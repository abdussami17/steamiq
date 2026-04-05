<?php

namespace App\Http\Controllers;

use App\Exports\TeamsExport;
use App\Imports\TeamsImport;
use App\Models\Group;
use App\Models\Player; 
use App\Models\Score;
use App\Models\Student;
use App\Models\SubGroup;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;




class TeamController extends Controller
{
    public function playersByEvent($eventId)
    {
        $players = Player::where('event_id', $eventId)
            ->select('id','name')
            ->get();

        return response()->json($players);
    }

    public function getGroups($orgId)
{
    $groups = Group::where('organization_id', $orgId)->get();
    return response()->json($groups);
}

public function getTeams($groupId)
{
    $teams = Team::where('group_id', $groupId)->get();
    return response()->json($teams);
}
    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_name' => 'required|string|max:255',
            'division' => 'required|in:Junior,Primary',
            'group_id' => 'required|exists:groups,id',
            'sub_group_id' => 'nullable|exists:sub_groups,id',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        
        DB::beginTransaction();
        
        try {
        
            $subgroupId = $validated['sub_group_id'] ?? null;
        
            $profilePath = null;
        
            if ($request->hasFile('profile')) {
        
                $file = $request->file('profile');
        
                $extension = $file->getClientOriginalExtension() ?: $file->extension();
                $filename = time().'_'.Str::random(8).'.'.$extension;
        
                $destinationDir = public_path('storage/teams');
        
                if (!is_dir($destinationDir)) {
                    mkdir($destinationDir, 0755, true);
                }
        
                $file->move($destinationDir, $filename);
        
                $profilePath = 'teams/'.$filename;
            }
        
            Team::create([
                'name' => $validated['team_name'],
                'group_id' => $validated['group_id'],
                'sub_group_id' => $subgroupId,
                'division' => $validated['division'],
                'profile' => $profilePath
            ]);
        
            DB::commit();
        
            return back()->with('success', 'Team created successfully.');
        
        } catch (\Throwable $e) {
        
            DB::rollBack();
            Log::error($e->getMessage());
        
            return back()->withInput()->with('error', 'Failed to create team.');
        }
    }
    
   
    public function teamsData(Request $request)
    {
        try {
            // =============================
            // 1) Load teams + subgroup + card assignments
            // =============================
            $teams = Team::with([
                'subgroup.group',
                'group',
                'cards.card' // ensure card details are loaded
            ])->get();
    
            // =============================
            // 2) Get total points per team (SCORES TABLE ONLY)
            // =============================
            $pointsMap = Score::selectRaw('team_id, SUM(points) as total_points')
                ->groupBy('team_id')
                ->pluck('total_points', 'team_id');
    
            // =============================
            // 3) Count students per team
            // =============================
            $membersMap = Student::selectRaw('team_id, COUNT(*) as total')
                ->groupBy('team_id')
                ->pluck('total', 'team_id');
    
            // =============================
            // 4) Build rows with total points adjusted for negative points
            // =============================
            $rows = $teams->map(function ($team) use ($pointsMap, $membersMap) {
    
                $assignedCards = $team->cards ?? collect();
    
                $negativePoints = $assignedCards->sum(function($assignment) {
                    return $assignment->card->negative_points ?? 0;
                });
    
                $basePoints = $pointsMap[$team->id] ?? 0;
    
                if ($assignedCards->isEmpty()) {
                    $totalPoints = $basePoints;
                } else {
                    $totalPoints = $negativePoints == 0 ? 0 : $basePoints - $negativePoints;
                    $totalPoints = max(0, $totalPoints); // avoid negative total
                }
    
                return [
                    'id' => $team->id,
                    'name' => $team->display_name ?? 'N/A',
                    'pod' => $team->subgroup
                        ? $team->subgroup->group->pod
                        : $team->group->pod ?? 'N/A',
                    'subgroup_name' => $team->subgroup->name ?? 'N/A',
                    'division' => $team->division ?? 'N/A',
                    'group_name' => $team->subgroup
                        ? $team->subgroup->group->group_name
                        : ($team->group->group_name ?? 'N/A'),
                    'members_count' => $membersMap[$team->id] ?? 0,
                    'total_points' => $totalPoints,
                    'profile' => $team->profile ?? null,
                ];
            });
    
            // =============================
            // 5) Sort by total_points DESC
            // =============================
            $rows = $rows->sortByDesc('total_points')->values();
    
            // =============================
            // 6) Assign rank
            // =============================
            $rows = $rows->map(function ($row, $index) {
                $row['rank'] = $index + 1;
                return $row;
            });
    
            // =============================
            // 7) Send user permissions once
            // =============================
            $userPermissions = Auth::user()->getAllPermissions()->pluck('name')->toArray();
    
            return response()->json([
                'teams' => $rows,
                'permissions' => $userPermissions
            ]);
    
        } catch (\Throwable $e) {
            \Log::error('Teams Data Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'teams' => [],
                'permissions' => []
            ]);
        }
    }
    public function export(Request $request)
    {
        $eventId = $request->event_id;
    
        return Excel::download(
            new TeamsExport($eventId),
            'teams.xlsx'
        );
    }
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
    
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false]);
        }
    
        Team::whereIn('id', $ids)->delete();
    
        return response()->json(['success' => true]);
    }




    public function view(Team $team)
    {
        // Eager load students, their scores, and the associated activity
        $team->load([
            'students.scores.challengeActivity'
        ]);
    
        // Map members with scores and total
        $members = $team->students->map(function ($student) {
    
            $totalPoints = (int) $student->scores->sum('points');
    
            return [
                'id' => $student->id,
                'name' => $student->name ?? 'N/A',
                'email' => $student->email ?? 'N/A',
                'total_points' => $totalPoints,
                'scores' => $student->scores->map(function ($score) {
                    return [
                        'activity' => $score->challengeActivity->name ?? 'N/A',
                        'points' => (int) ($score->points ?? 0)
                    ];
                })->values()
            ];
        })->values();
    
        return response()->json([
            'team' => $team,
            'members' => $members
        ]);
    }
  public function edit(Team $team)
{
    // Eager load subgroup and group relationships
    $team->load('subgroup.group', 'group.organization');

    // Load all organizations for dropdown
    $organizations = \App\Models\Organization::select('id', 'name')->get();

    // Load all groups for the team's organization
    $groups = \App\Models\Group::where('organization_id', $team->group->organization_id ?? null)
        ->select('id', 'group_name', 'organization_id')
        ->get();

    // Load subgroups for the team’s current group
    $subgroups = \App\Models\SubGroup::where('group_id', $team->group_id)
        ->select('id', 'name', 'group_id')
        ->get();

    return response()->json([
        'team' => [
            'id' => $team->id,
            'name' => $team->name,
            'division' => $team->division,
            'organization_id' => $team->group->organization_id ?? null,
            'group_id' => $team->group_id,
            'sub_group_id' => $team->sub_group_id,
            'profile' => $team->profile ? asset('storage/' . $team->profile) : null
        ],
        'organizations' => $organizations,
        'groups' => $groups,
        'subgroups' => $subgroups
    ]);
}
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'team_name'    => 'required|string|max:255',
            'division'     => 'required|in:Junior,Primary',
            'group_id'     => 'required|exists:groups,id',
            'sub_group_id' => 'nullable|exists:sub_groups,id', // optional now
            'profile'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        DB::beginTransaction();
        try {
            $subgroupId = null;
            if(!empty($validated['sub_group_id'])){
                $subgroup = SubGroup::where('id', $validated['sub_group_id'])
                    ->where('group_id', $validated['group_id'])
                    ->firstOrFail(); // ensure subgroup belongs to group
                $subgroupId = $subgroup->id;
            }
    
            $profilePath = null;
        
            if ($request->hasFile('profile')) {
        
                $file = $request->file('profile');
        
                $extension = $file->getClientOriginalExtension() ?: $file->extension();
                $filename = time().'_'.Str::random(8).'.'.$extension;
        
                $destinationDir = public_path('storage/teams');
        
                if (!is_dir($destinationDir)) {
                    mkdir($destinationDir, 0755, true);
                }
        
                $file->move($destinationDir, $filename);
        
                $profilePath = 'teams/'.$filename;
            }
    
            $team->update([
                'name'         => $validated['team_name'],
                'group_id'     => $validated['group_id'],
                'sub_group_id' => $subgroupId,
                'division'     => $validated['division'],
                'profile'      => $profilePath,
            ]);
    
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Team updated successfully.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update team.']);
        }
    }
    // Delete team
    public function destroy(Team $team)
    {
        
        $team->delete();

        return response()->json(['success' => true, 'message' => 'Team deleted successfully.']);
    }


    public function list()
{
    return response()->json(
        \App\Models\Organization::select('id','name')->get()
    );
}


public function listTeam()
{
    return response()->json(
        \App\Models\Team::select('id','name')->get()
    );
}


// ================================================================
//  ADD these two methods to your existing TeamController.php
//  (they replace the old stub import() method)
// ================================================================

// ── 1.  POST /teams/import  ──────────────────────────────────────
/**
 * Handle the spreadsheet upload, run TeamsImport, return JSON result.
 */
public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,csv|max:5120',
    ]);

    try {
        $importer = new \App\Imports\TeamsImport();

        \Maatwebsite\Excel\Facades\Excel::import($importer, $request->file('file'));

        // ✅ FULL RESULT LOG
        \Log::info('IMPORT RESULT', $importer->result());

        // ❗ Failed rows separate log (easy debugging)
        \Log::warning('FAILED ROWS', [
            'failed' => $importer->failed
        ]);

        return response()->json($importer->result());

    } catch (\Throwable $e) {

        // ❌ GLOBAL ERROR LOG
        \Log::error('IMPORT FAILED COMPLETELY', [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => true,
            'message' => $e->getMessage()
        ], 500);
    }
}


// ── 2.  GET /teams/import/template  ─────────────────────────────
/**
 * Stream a pre-built sample .xlsx template so users know the format.
 */
public function importTemplate()
{
    // Build the template in-memory using a simple array writer
    // (no extra package required – uses PhpSpreadsheet which ships with laravel-excel)
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Teams Import');

    // ── Header row (bold) ──
    $headers = [
        'team_name',
        'organization',
        'group',
        'subgroup',
        'division',
        'student_name',
        'student_email',
    ];

    foreach ($headers as $col => $heading) {
        $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
        $sheet->setCellValue($cell, $heading);
        $sheet->getStyle($cell)->getFont()->setBold(true);
        $sheet->getStyle($cell)->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()->setRGB('D9E1F2');
    }

    // ── Sample rows ──
    $samples = [
        // Single student
        ['Eagles',  'Sunrise Academy', 'Group A', '',      'Junior',  'Ali Hassan',               'ali@example.com'],
        // Multiple students via comma on one row
        ['Tigers',  'Sunrise Academy', 'Group A', 'Pod 1', 'Primary', 'Sara Khan, John Doe',      'sara@example.com, john@example.com'],
        // Team only (no students)
        ['Falcons', 'City School',     'Group B', 'Pod 2', 'Junior',  '',                         ''],
        // Mix: repeat team row to add more students (both styles work)
        ['Eagles',  'Sunrise Academy', 'Group A', '',      'Junior',  'Mariam Ali, Usman Sheikh', 'mariam@example.com, usman@example.com'],
    ];

    foreach ($samples as $rowIdx => $rowData) {
        foreach ($rowData as $colIdx => $value) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1) . ($rowIdx + 2);
            $sheet->setCellValue($cell, $value);
        }
    }

    // ── Auto-width columns ──
    foreach (range('A', 'G') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // ── Notes sheet ──
    $notes = $spreadsheet->createSheet();
    $notes->setTitle('Instructions');
    $notesContent = [
        ['Column',         'Required?', 'Accepted values / notes'],
        ['team_name',      'Yes',       'Unique team name. You can also repeat the same name on multiple rows to add more students.'],
        ['organization',   'Yes',       'Must match an existing organization name exactly (case-insensitive).'],
        ['group',          'Yes',       'Must match an existing group name within that organization.'],
        ['subgroup',       'No',        'Leave blank if there is no subgroup. Must exist under the specified group.'],
        ['division',       'Yes',       'Allowed values: Junior  or  Primary'],
        ['student_name',   'No',        'One name, OR comma-separated: "Ali Hassan, Sara Khan". Leave blank for team-only rows.'],
        ['student_email',  'No',        'Required when student_name is given. Comma-separated in matching order: "ali@example.com, sara@example.com"'],
    ];
    foreach ($notesContent as $r => $row) {
        foreach ($row as $c => $val) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c + 1) . ($r + 1);
            $notes->setCellValue($cell, $val);
            if ($r === 0) {
                $notes->getStyle($cell)->getFont()->setBold(true);
            }
        }
    }
    foreach (['A','B','C'] as $col) {
        $notes->getColumnDimension($col)->setAutoSize(true);
    }

    // ── Stream download ──
    $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $filename = 'teams_import_template.xlsx';

    return response()->streamDownload(
        fn () => $writer->save('php://output'),
        $filename,
        [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]
    );
}

}
