<?php

namespace App\Imports;

use App\Models\Group;
use App\Models\Organization;
use App\Models\Student;
use App\Models\SubGroup;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeamsImport implements ToCollection, WithHeadingRow
{
    public array $imported = [];
    public array $failed = [];

    public int $teamsCreated = 0;
    public int $teamsExisting = 0;
    public int $studentsAdded = 0;
    public int $totalRows = 0;

    public function collection(Collection $rows): void
    {
        $this->totalRows = $rows->count();

        $teamRegistry = [];

        foreach ($rows as $index => $row) {

            $rowNumber = $index + 2;

            $data = array_map(
                fn ($v) => is_string($v) ? trim($v) : $v,
                $row->toArray()
            );

            $teamName     = $data['team_name']     ?? '';
            $orgName      = $data['organization']  ?? '';
            $groupName    = $data['group']          ?? '';
            $subName      = $data['subgroup']       ?? '';
            $division     = $data['division']       ?? '';
            $studentName  = $data['player_name']  ?? '';
            $studentEmail = $data['player_email'] ?? '';

            $rowErrors = [];

            // ── Required field validation ──────────────────────────────
            if (!$teamName)  $rowErrors[] = 'team_name is required.';
            if (!$orgName)   $rowErrors[] = 'organization is required.';
            if (!$groupName) $rowErrors[] = 'group is required.';
            if (!$division)  $rowErrors[] = 'division is required.';

            $division = ucfirst(strtolower($division));

            if (!in_array($division, ['Junior', 'Primary'])) {
                $rowErrors[] = 'division must be Junior or Primary.';
            }

            // ── Parse student names & emails ───────────────────────────
            // Names: split by comma, filter empty strings
            $studentNames = array_values(
                array_filter(
                    array_map('trim', explode(',', (string) $studentName)),
                    fn($v) => $v !== ''
                )
            );

            // Emails: split by comma, keep empties so indexes stay aligned,
            // but trim each value so "  " becomes ""
            $rawEmails = array_map('trim', explode(',', (string) $studentEmail));

            // If the entire email cell was blank, rawEmails will be ['']
            // which we treat as no emails at all
            $emailCellEmpty = ($studentEmail === '' || $studentEmail === null);

            // ── Student / email cross-validation ──────────────────────
            if ($studentNames) {

                // Validate any emails that were actually supplied
                foreach ($rawEmails as $idx => $email) {
                    if ($email === '') continue; // blank slot is allowed
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $rowErrors[] = "Invalid email at position " . ($idx + 1) . ": {$email}";
                    }
                }

            } elseif (!$emailCellEmpty) {
                // Emails given but no names
                $rowErrors[] = 'player_name is required when player_email is provided.';
            }

            // ── Fail row early if validation errors ───────────────────
            if ($rowErrors) {
                Log::warning('ROW VALIDATION FAILED', [
                    'row'    => $rowNumber,
                    'data'   => $data,
                    'errors' => $rowErrors,
                ]);

                $this->failed[] = [
                    'row'       => $rowNumber,
                    'team_name' => $teamName,
                    'errors'    => $rowErrors,
                ];
                continue;
            }

            // ── Organization lookup ────────────────────────────────────
            $organization = Organization::whereRaw('LOWER(name) = ?', [strtolower($orgName)])->first();
            if (!$organization) {
                Log::warning('ORG NOT FOUND', compact('rowNumber', 'orgName'));
                $this->failed[] = [
                    'row'       => $rowNumber,
                    'team_name' => $teamName,
                    'errors'    => ["Organization '{$orgName}' not found."],
                ];
                continue;
            }

            // ── Group lookup ───────────────────────────────────────────
            $group = Group::where('organization_id', $organization->id)
                ->whereRaw('LOWER(group_name) = ?', [strtolower($groupName)])
                ->first();

            if (!$group) {
                Log::warning('GROUP NOT FOUND', compact('rowNumber', 'groupName'));
                $this->failed[] = [
                    'row'       => $rowNumber,
                    'team_name' => $teamName,
                    'errors'    => ["Group '{$groupName}' not found."],
                ];
                continue;
            }

            // ── Subgroup lookup (optional) ─────────────────────────────
            $subGroupId = null;
            if ($subName) {
                $sub = SubGroup::where('group_id', $group->id)
                    ->whereRaw('LOWER(name) = ?', [strtolower($subName)])
                    ->first();

                if (!$sub) {
                    $this->failed[] = [
                        'row'       => $rowNumber,
                        'team_name' => $teamName,
                        'errors'    => ["Subgroup '{$subName}' not found."],
                    ];
                    continue;
                }
                $subGroupId = $sub->id;
            }

            // ── Team upsert inside a transaction ──────────────────────
            $teamKey = strtolower(trim($teamName));

            DB::beginTransaction();

            try {

                if (!isset($teamRegistry[$teamKey])) {

                    $existing = Team::where('name', $teamName)
                        ->where('group_id', $group->id)
                        ->first();

                    if ($existing) {
                        $team  = $existing;
                        $isNew = false;
                        $this->teamsExisting++;
                    } else {
                        $team = Team::create([
                            'name'         => $teamName,
                            'group_id'     => $group->id,
                            'sub_group_id' => $subGroupId,
                            'division'     => $division,
                        ]);
                        $isNew = true;
                        $this->teamsCreated++;
                    }

                    $teamRegistry[$teamKey] = [
                        'team'         => $team,
                        'meta'         => compact('orgName', 'groupName', 'division'),
                        'result_index' => count($this->imported),
                    ];

                    $this->imported[] = [
                        'team_name'      => $teamName,
                        'division'       => $division,
                        'group'          => $groupName,
                        'students_added' => 0,
                        'is_new'         => $isNew,
                    ];

                } else {

                    // Same team name appeared before — make sure metadata matches
                    $meta = $teamRegistry[$teamKey]['meta'];

                    if (
                        strtolower($meta['orgName'])   !== strtolower($orgName)   ||
                        strtolower($meta['groupName']) !== strtolower($groupName) ||
                        strtolower($meta['division'])  !== strtolower($division)
                    ) {
                        DB::rollBack();

                        $this->failed[] = [
                            'row'       => $rowNumber,
                            'team_name' => $teamName,
                            'errors'    => ['Conflict: team already seen with different organization/group/division.'],
                        ];
                        continue;
                    }
                }

                $team        = $teamRegistry[$teamKey]['team'];
                $resultIndex = $teamRegistry[$teamKey]['result_index'];

                // ── Insert students ────────────────────────────────────
                foreach ($studentNames as $i => $name) {

                    // Email at same index — or null if not supplied
                    $email = (!$emailCellEmpty && isset($rawEmails[$i]) && $rawEmails[$i] !== '')
                        ? strtolower($rawEmails[$i])
                        : null;

                    // Duplicate check:
                    //   • if email present  → match by email within team
                    //   • if email absent   → match by name  within team
                    if ($email !== null) {
                        $exists = Student::where('team_id', $team->id)
                            ->whereRaw('LOWER(email) = ?', [$email])
                            ->exists();
                    } else {
                        $exists = Student::where('team_id', $team->id)
                            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                            ->exists();
                    }

                    if (!$exists) {
                        Student::create([
                            'team_id' => $team->id,
                            'name'    => $name,
                            'email'   => $email,   // null when not provided
                        ]);

                        $this->studentsAdded++;
                        $this->imported[$resultIndex]['students_added']++;
                    }
                }

                DB::commit();

            } catch (\Throwable $e) {

                DB::rollBack();

                Log::error('ROW ERROR', [
                    'row'   => $rowNumber,
                    'error' => $e->getMessage(),
                ]);

                $this->failed[] = [
                    'row'       => $rowNumber,
                    'team_name' => $teamName,
                    'errors'    => [$e->getMessage()],
                ];
            }
        }
    }

    public function result(): array
    {
        return [
            'summary' => [
                'total_rows'     => $this->totalRows,
                'teams_created'  => $this->teamsCreated,
                'teams_existing' => $this->teamsExisting,
                'students_added' => $this->studentsAdded,
                'failed_rows'    => count($this->failed),
            ],
            'imported' => $this->imported,
            'failed'   => $this->failed,
        ];
    }
}