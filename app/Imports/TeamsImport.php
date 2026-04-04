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

            $teamName     = $data['team_name'] ?? '';
            $orgName      = $data['organization'] ?? '';
            $groupName    = $data['group'] ?? '';
            $subName      = $data['subgroup'] ?? '';
            $division     = $data['division'] ?? '';
            $studentName  = $data['student_name'] ?? '';
            $studentEmail = $data['student_email'] ?? '';

            $rowErrors = [];

            // Required validation
            if (!$teamName)  $rowErrors[] = 'team_name is required.';
            if (!$orgName)   $rowErrors[] = 'organization is required.';
            if (!$groupName) $rowErrors[] = 'group is required.';
            if (!$division)  $rowErrors[] = 'division is required.';

            $division = ucfirst(strtolower($division));

            if (!in_array($division, ['Junior', 'Primary'])) {
                $rowErrors[] = "division must be Junior or Primary.";
            }
            // Students parse
            $studentNames  = array_values(array_filter(array_map('trim', explode(',', $studentName))));
            $studentEmails = array_values(array_filter(array_map('trim', explode(',', $studentEmail))));

            if ($studentNames && !$studentEmails) {
                $rowErrors[] = 'student_email required with student_name.';
            }

            if ($studentNames && $studentEmails) {
                if (count($studentNames) !== count($studentEmails)) {
                    $rowErrors[] = 'student_name and email count mismatch.';
                } else {
                    foreach ($studentEmails as $email) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $rowErrors[] = "Invalid email: {$email}";
                        }
                    }
                }
            }

            if (!$studentNames && $studentEmails) {
                $rowErrors[] = 'student_name required when email provided.';
            }

            // ❗ FIXED: properly closed block
            if ($rowErrors) {
                Log::warning('ROW VALIDATION FAILED', [
                    'row' => $rowNumber,
                    'data' => $data,
                    'errors' => $rowErrors
                ]);

                $this->failed[] = [
                    'row' => $rowNumber,
                    'team_name' => $teamName,
                    'errors' => $rowErrors,
                ];
                continue;
            }

            // Organization
            $organization = Organization::whereRaw('LOWER(name)=?', [strtolower($orgName)])->first();
            if (!$organization) {

                Log::warning('ORG NOT FOUND', compact('rowNumber', 'orgName'));

                $this->failed[] = [
                    'row' => $rowNumber,
                    'team_name' => $teamName,
                    'errors' => ["Organization '{$orgName}' not found"]
                ];
                continue;
            }

            // Group
            $group = Group::where('organization_id', $organization->id)
                ->whereRaw('LOWER(group_name)=?', [strtolower($groupName)])
                ->first();

            if (!$group) {

                Log::warning('GROUP NOT FOUND', compact('rowNumber', 'groupName'));

                $this->failed[] = [
                    'row' => $rowNumber,
                    'team_name' => $teamName,
                    'errors' => ["Group '{$groupName}' not found"]
                ];
                continue;
            }

            // Subgroup
            $subGroupId = null;
            if ($subName) {
                $sub = SubGroup::where('group_id', $group->id)
                    ->whereRaw('LOWER(name)=?', [strtolower($subName)])
                    ->first();

                if (!$sub) {
                    $this->failed[] = [
                        'row' => $rowNumber,
                        'team_name' => $teamName,
                        'errors' => ["Subgroup '{$subName}' not found"]
                    ];
                    continue;
                }
                $subGroupId = $sub->id;
            }

            $teamKey = strtolower($teamName);

            DB::beginTransaction();

            try {

                if (!isset($teamRegistry[$teamKey])) {

                    $existing = Team::where('name', $teamName)
                        ->where('group_id', $group->id)
                        ->first();

                    if ($existing) {
                        $team = $existing;
                        $isNew = false;
                        $this->teamsExisting++;
                    } else {
                        $team = Team::create([
                            'name' => $teamName,
                            'group_id' => $group->id,
                            'sub_group_id' => $subGroupId,
                            'division' => $division,
                        ]);
                        $isNew = true;
                        $this->teamsCreated++;
                    }

                    $teamRegistry[$teamKey] = [
                        'team' => $team,
                        'meta' => compact('orgName', 'groupName', 'division'),
                        'result_index' => count($this->imported)
                    ];

                    $this->imported[] = [
                        'team_name' => $teamName,
                        'division' => $division,
                        'group' => $groupName,
                        'students_added' => 0,
                        'is_new' => $isNew
                    ];
                } else {

                    $meta = $teamRegistry[$teamKey]['meta'];

                    if (
                        strtolower($meta['orgName']) !== strtolower($orgName) ||
                        strtolower($meta['groupName']) !== strtolower($groupName) ||
                        strtolower($meta['division']) !== strtolower($division)
                    ) {
                        DB::rollBack();

                        $this->failed[] = [
                            'row' => $rowNumber,
                            'team_name' => $teamName,
                            'errors' => ['Conflict with previous rows']
                        ];
                        continue;
                    }
                }

                $team = $teamRegistry[$teamKey]['team'];
                $indexResult = $teamRegistry[$teamKey]['result_index'];

                foreach ($studentNames as $i => $name) {

                    $email = strtolower($studentEmails[$i]);

                    $exists = Student::where('team_id', $team->id)
                        ->whereRaw('LOWER(email)=?', [$email])
                        ->exists();

                    if (!$exists) {
                        Student::create([
                            'team_id' => $team->id,
                            'name' => $name,
                            'email' => $email
                        ]);

                        $this->studentsAdded++;
                        $this->imported[$indexResult]['students_added']++;
                    }
                }

                DB::commit();

            } catch (\Throwable $e) {

                DB::rollBack();

                Log::error('ROW ERROR', [
                    'row' => $rowNumber,
                    'error' => $e->getMessage()
                ]);

                $this->failed[] = [
                    'row' => $rowNumber,
                    'team_name' => $teamName,
                    'errors' => [$e->getMessage()]
                ];
            }
        }
    }

    public function result(): array
    {
        return [
            'summary' => [
                'total_rows' => $this->totalRows,
                'teams_created' => $this->teamsCreated,
                'teams_existing' => $this->teamsExisting,
                'students_added' => $this->studentsAdded,
                'failed_rows' => count($this->failed),
            ],
            'imported' => $this->imported,
            'failed' => $this->failed,
        ];
    }
}