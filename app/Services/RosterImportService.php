<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Group;
use App\Models\Organization;
use App\Models\Roster;
use App\Models\RosterStudent;
use App\Models\Student;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Role;

class RosterImportService
{
    /**
     * Required columns in the uploaded Excel/CSV file.
     */
    private const REQUIRED_COLUMNS = [
        'name',
        'age',
        'grade',
        'gender', 
        'shirt_size',
        'team',
        'group',     
        'coach',
        'organization',
        'pod',   
        'division'
    ];

    /**
     * All expected columns (optional ones default gracefully).
     */
    private const ALL_COLUMNS = [
        'name',
        'age',
        'grade',
        'gender', 
        'player_email', 
        'shirt_size',
        'team',
        'group',
        'subgroup',   
        'coach',
        'organization',
        'pod',   
        'division'
    ];

    /**
     * Import a roster file for a given event.
     *
     * @param  UploadedFile  $file
     * @param  int           $eventId
     * @return array{total_rows:int, inserted:int, duplicates:int, failed:array}
     */
    public function import(UploadedFile $file, int $eventId): array
    {
        $event = Event::findOrFail($eventId);

        $rows   = $this->parseFile($file);
        $report = [
            'total_rows' => count($rows),
            'inserted'   => 0,
            'duplicates' => 0,
            'failed'     => [],
        ];

        // Cache rosters & teams created this run to avoid redundant queries
        $rosterCache = [];  // key: "{event_id}_{organization_id}"
        $teamCache   = [];  // key: "{organization_id}_{team_name}"

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // Row 1 = header

            try {
                $validationError = $this->validateRow($row, $rowNumber);
                Log::info('📌 FINAL ROW BEFORE INSERT:', $row);
                if ($validationError) {
                    $report['failed'][] = $validationError;
                    continue;
                }

                DB::transaction(function () use (
                    $row, $event, $rowNumber,
                    &$rosterCache, &$teamCache, &$report
                )  {
                    $coachId = null;
                    // --- 1. Resolve Organization ---
                    $organization = $this->resolveOrganization($row['organization'], $event->id);

                    // --- 2. Resolve Coach ---
                    if (!empty($row['coach'])) {
                        $coach = $this->resolveCoach($row['coach']);
                        $coachId = $coach->id;
                    
                        // attach to organization
                        if ($organization->coach_id === null) {
                            $organization->coach_id = $coachId;
                            $organization->save();
                        }
                    }
                    $group = $this->resolveGroup($row['group'], $organization, $row['pod'] ?? null);

                    // 1. Resolve SubGroup (optional)
                    $subGroup = null;
                    if (!empty($row['subgroup'])) {
                        $subGroup = $this->resolveSubGroup($row['subgroup'], $group);
                    }
                    
                    // 2. Team cache key updated
                    $teamCacheKey = "{$organization->id}_{$group->id}_{$subGroup?->id}_{$row['team']}";
                    
                    if (!isset($teamCache[$teamCacheKey])) {
                        $teamCache[$teamCacheKey] = $this->resolveTeam(
                            $row['team'],
                            $group,
                            $row['division'] ?? null,
                            $subGroup
                        );
                    }
                    
                    $team = $teamCache[$teamCacheKey];

                    // --- 4. Duplicate Student Check ---
                    $isDuplicate = Student::where('name', $row['name'])
                        ->where('team_id', $team->id)
                        ->exists();

                    if ($isDuplicate) {
                        $report['duplicates']++;
                        return; // skip — do not insert, do not fail
                    }

                    // --- 5. Create Student ---
                    $student = Student::create([
                        'name'       => $row['name'],
                        'email'       => $row['player_email'] ?? null, 
                        'age'        => $row['age']        ?? null,
                        'grade'      => $row['grade']      ?? null,
                        'gender'     => $row['gender'] ?? null, 
                        'shirt_size' => $row['shirt_size'] ?? null,
                        'team_id'    => $team->id,
                    ]);

                    // --- 6. Resolve Roster (one per event + organization) ---
                    $rosterCacheKey = "{$event->id}_{$organization->id}";
                    if (!isset($rosterCache[$rosterCacheKey])) {
                        $rosterCache[$rosterCacheKey] = $this->resolveRoster(
                            $event->id,
                            $organization->id,
                            $coachId
                        );
                    }
                    $roster = $rosterCache[$rosterCacheKey];

                    // --- 7. Create RosterStudent Pivot ---
                    RosterStudent::firstOrCreate([
                        'roster_id'  => $roster->id,
                        'student_id' => $student->id,
                    ], [
                        'attendance_status' => null,
                    ]);

                    $report['inserted']++;
                });

            } catch (\Throwable $e) {
                Log::error("RosterImport row {$rowNumber} failed", [
                    'row'       => $row,
                    'exception' => $e->getMessage(),
                ]);

                $report['failed'][] = [
                    'row'    => $rowNumber,
                    'reason' => 'Unexpected error: ' . $e->getMessage(),
                ];
            }
        }

        return $report;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PARSING
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Parse an xlsx or csv file into an array of associative row arrays.
     */
    private function parseFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path      = $file->getRealPath();

        if ($extension === 'csv') {
            return $this->parseCsv($path);
        }

        return $this->parseXlsx($path);
    }

    private function parseCsv(string $path): array
    {
        $rows    = [];
        $headers = null;

        if (($handle = fopen($path, 'r')) === false) {
            throw new \RuntimeException('Could not open CSV file.');
        }

        while (($line = fgetcsv($handle)) !== false) {
            if ($headers === null) {
                Log::info('📌 CSV HEADERS:', $headers);
                $headers = array_map([$this, 'normalizeHeader'], $line);
                continue;
                Log::info('📌 RAW CSV ROW:', $line);
            }
            $rows[] = $this->mapRowToHeaders($headers, $line);
        }

        fclose($handle);
        return $rows;
    }

    private function parseXlsx(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet       = $spreadsheet->getActiveSheet();
        $data        = $sheet->toArray(null, true, true, false);

        if (empty($data)) {
            return [];
        }

        $headers = array_map([$this, 'normalizeHeader'], array_shift($data));
        Log::info('📌 XLSX HEADERS:', $headers);
        $rows    = [];

        foreach ($data as $line) {
            Log::info('📌 RAW XLSX ROW:', $line);
            // Skip completely empty rows
            if (empty(array_filter($line, fn($v) => $v !== null && $v !== ''))) {
                continue;
            }
            $rows[] = $this->mapRowToHeaders($headers, $line);
        }

        return $rows;
    }

    /**
     * Zip header names with row values into an associative array,
     * normalising all known column names and trimming string values.
     */
    private function mapRowToHeaders(array $headers, array $values): array
    {
        $combined = array_combine($headers, array_pad($values, count($headers), null));

        $normalised = [];
        foreach (self::ALL_COLUMNS as $col) {
            $value            = $combined[$col] ?? null;
            $normalised[$col] = is_string($value) ? trim($value) : $value;
            Log::info('📌 MAPPED ROW:', $normalised);
        }

        return $normalised;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // VALIDATION
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Validate a single row. Returns a failed-row array or null if valid.
     */
    private function validateRow(array $row, int $rowNumber): ?array
    {
        $missing = [];
    
        foreach (self::REQUIRED_COLUMNS as $col) {
            if (!isset($row[$col]) || trim($row[$col]) === '') {
                $missing[] = $col;
            }
        }
        if (!isset($row['division']) || trim($row['division']) === '') {
            return [
                'row' => $rowNumber,
                'reason' => 'Missing required field(s): division',
            ];
        }
        
        if (!isset($row['pod']) || trim($row['pod']) === '') {
            return [
                'row' => $rowNumber,
                'reason' => 'Missing required field(s): pod',
            ];
        }
    
        if (!empty($missing)) {
            return [
                'row'    => $rowNumber,
                'reason' => 'Missing required field(s): ' . implode(', ', $missing),
            ];
        }
    
        // Age strict numeric
        if (!is_numeric($row['age'])) {
            return [
                'row'    => $rowNumber,
                'reason' => 'Age must be a valid number',
            ];
        }
    
        return null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RESOLVERS
    // ─────────────────────────────────────────────────────────────────────────

    private function resolveOrganization(string $name, int $eventId): Organization
    {
        return Organization::firstOrCreate(
            ['name' => $name, 'event_id' => $eventId],
            ['name' => $name, 'event_id' => $eventId]
        );
    }

    private function resolveCoach(string $name): User
    {
        // Attempt to find by name; use a safe email derived from name
        $email = $this->nameToEmail($name);

        $coach = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => bcrypt('password')]
        );

        // Ensure role exists and is assigned
        $role = Role::firstOrCreate(['name' => 'coach', 'guard_name' => 'web']);
        if (!$coach->hasRole('coach')) {
            $coach->assignRole($role);
        }

        return $coach;
    }
    private function resolveTeam(string $teamName, Group $group, ?string $division = null, $subGroup = null): Team
    {
        return Team::firstOrCreate(
            [
                'name' => trim($teamName),
                'group_id' => $group->id,
                'sub_group_id' => $subGroup?->id,
            ],
            [
                'division' => strtolower(trim($division ?: 'primary')),
            ]
        );
    }
    private function resolveSubGroup(string $name, Group $group)
{
    return \App\Models\SubGroup::firstOrCreate(
        [
            'name' => trim($name),
            'group_id' => $group->id,
        ]
    );
}

    private function resolveGroup(string $groupName, Organization $organization, ?string $pod = null): Group
    {
        return Group::firstOrCreate(
            [
                'organization_id' => $organization->id,
                'group_name'      => trim($groupName),
            ],
            [
                'pod' => trim($pod ?: 'red'),
            ]
        );
    }

private function resolveRoster(int $eventId, int $organizationId, ?int $coachId = null): Roster
{
    $roster = Roster::firstOrCreate(
        [
            'event_id' => $eventId,
            'organization_id' => $organizationId,
        ],
        [
            'status' => 'draft',
        ]
    );

    // IMPORTANT: update coach if missing
    if ($coachId && !$roster->coach_id) {
        $roster->coach_id = $coachId;
        $roster->save();
    }

    return $roster;
}

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────


    private function normalizeHeader(string $h): string
    {
        $h = strtolower(trim($h));
        $h = str_replace(' ', '_', $h);
    
        // alias mapping
        return match($h) {
            'group_name' => 'group',
            'team_name' => 'team',
            'division_name' => 'division',
            'pod_name' => 'pod',
            'player_email' => 'player_email',
            'gender_name' => 'gender',
            'sub_group' => 'subgroup',
            'sub_group_name' => 'subgroup',
            default => $h
        };
    }


    /**
     * Derive a deterministic email from a coach name for firstOrCreate lookups.
     * e.g. "John Smith" → "john.smith.coach@steamiq.local"
     */
    private function nameToEmail(string $name): string
    {
        $slug = \Str::slug($name, '.');
        return "{$slug}.coach@steamiq.local";
    }
}