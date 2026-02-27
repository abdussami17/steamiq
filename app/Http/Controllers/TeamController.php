<?php

namespace App\Http\Controllers;

use App\Exports\TeamsExport;
use App\Imports\TeamsImport;
use App\Models\Player; 
use App\Models\Score;
use App\Models\Student;
use App\Models\SubGroup;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_name'    => 'required|string|max:255',
            'sub_group_id' => 'required|exists:sub_groups,id',
            'profile'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        DB::beginTransaction();
    
        try {
    
            $subgroup = SubGroup::with('event')->findOrFail($validated['sub_group_id']);
    
            $profilePath = null;
    
            if ($request->hasFile('profile')) {
                $profilePath = $request->file('profile')->store('teams', 'public');
            }
    
            Team::create([
                'team_name'    => $validated['team_name'],
                'sub_group_id' => $subgroup->id,
                'event_id'     => $subgroup->event->id,
                'profile'      => $profilePath,
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
            // 1) Load teams + subgroup
            // =============================
            $teams = Team::with(['subgroup'])
                ->get();

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
            // 4) Build rows
            // =============================
            $rows = $teams->map(function ($team) use ($pointsMap, $membersMap) {

                return [
                    'id' => $team->id,
                    'team_name' => $team->team_name ?? 'N/A',
                    'subgroup_name' => $team->subgroup->name ?? 'N/A',
                    'members_count' => $membersMap[$team->id] ?? 0,
                    'total_points' => $pointsMap[$team->id] ?? 0,
                    'profile' => $team->profile ?? null,
                ];
            });

            // =============================
            // 5) Sort by points DESC
            // =============================
            $rows = $rows->sortByDesc('total_points')->values();

            // =============================
            // 6) Assign rank (CORRECT way)
            // =============================
            $rows = $rows->map(function ($row, $index) {
                $row['rank'] = $index + 1;
                return $row;
            });

            return response()->json($rows);

        } catch (\Throwable $e) {

            \Log::error('Teams Data Error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([]);
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
    public function import(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'file'     => 'required|file|mimes:xlsx,csv',
        ]);
    
        $eventId = $request->event_id;
    
        Excel::import(new class($eventId) implements \Maatwebsite\Excel\Concerns\ToCollection {
            protected $eventId;
            public function __construct($eventId) { $this->eventId = $eventId; }
    
            public function collection(\Illuminate\Support\Collection $rows)
            {
                foreach ($rows as $row) {
                    $teamName = trim($row[0] ?? '');
                    $emails   = trim($row[1] ?? '');
    
                    if (!$teamName || !$emails) continue;
    
                    // Create team
                    $team = Team::create([
                        'team_name' => $teamName,
                        'event_id'  => $this->eventId,
                    ]);
    
                    // Split emails and fetch IDs
                    $emailArray = array_map('trim', explode(',', $emails));
                    $playerIds  = Player::whereIn('email', $emailArray)->pluck('id')->toArray();
    
                    if ($playerIds) {
                        $team->players()->attach($playerIds);
                    }
                }
            }
        }, $request->file('file'));
    
        return back()->with('success', 'Teams imported successfully.');
    }




    public function view(Team $team)
    {
        $team->load([
            'event',
            'students.scores.challengeActivity'
        ]);
    
        $members = $team->students->map(function ($student) {
    
            // 🔥 calculate total properly
            $totalPoints = (int) $student->scores->sum('points');
    
            return [
                'id' => $student->id,
                'name' => $student->name ?? 'N/A',
                'email' => $student->email ?? 'N/A',
                'total_points' => $totalPoints, // ALWAYS exists
                'scores' => $student->scores->map(function ($score) {
                    return [
                        'challenge' => $score->challengeActivity->name ?? 'N/A',
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
    // Update team
    public function edit(Team $team)
    {
        $team->load('subgroup.event');
    
        return response()->json([
            'team' => [
                'id' => $team->id,
                'team_name' => $team->team_name,
                'sub_group_id' => $team->sub_group_id,
                'event_id' => $team->event_id,
                'profile' => $team->profile ? asset('storage/' . $team->profile) : null
            ]
        ]);
    }
    
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'team_name'    => 'required|string|max:255',
            'sub_group_id' => 'required|exists:sub_groups,id',
            'profile'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        DB::beginTransaction();
        try {
            $subgroup = SubGroup::with('event')->findOrFail($validated['sub_group_id']);
    
            $profilePath = $team->profile;
            if ($request->hasFile('profile')) {
                $profilePath = $request->file('profile')->store('teams', 'public');
            }
    
            $team->update([
                'team_name'    => $validated['team_name'],
                'sub_group_id' => $subgroup->id,
                'event_id'     => $subgroup->event->id,
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
        $team->players()->detach();
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
        \App\Models\Team::select('id','team_name')->get()
    );
}

}
