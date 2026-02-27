<?php

namespace App\Http\Controllers;

use App\Exports\TeamsExport;
use App\Imports\TeamsImport;
use App\Models\Player; 
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
    
   

    public function teamsData()
    {
        $teams = Team::with([
            'players.scores',
            'event'
        ])->get();
    
        $teams = $teams->map(function ($team) {
    
            $memberCount = $team->players->count();
    
            $totalPoints = $team->players->sum(function ($player) {
                return $player->scores->sum('points');
            });
    
            return [
                'id' => $team->id,
                'team_name' => $team->team_name ?? 'N/A',
                'members_count' => $memberCount ?: 0,
                'total_points' => $totalPoints ?: 0,
                'profile' => $team->profile
            ];
        });
    
        $teams = collect($teams)->sortByDesc('total_points')->values();
    
        $rank = 1;
        $teams = $teams->map(function ($team) use (&$rank) {
            $team['rank'] = $rank++;
            return $team;
        });
    
        return response()->json($teams);
    }
    public function export(Request $request)
    {
        $eventId = $request->input('event_id');

        // If user did not select an event, export all events
        return Excel::download(new TeamsExport($eventId), 'teams.xlsx');
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




    // View team details (for modal)
    public function view(Team $team)
    {
        $team->load(['event', 'players.scores.challenge']); // eager load

        return response()->json([
            'team' => $team,
            'members' => $team->players->map(function($player){
                return [
                    'id' => $player->id,
                    'name' => $player->name,
                    'email' => $player->email,
                    'scores' => $player->scores->map(function($score){
                        return [
                            'challenge' => $score->challenge->name ?? 'N/A',
                            'pillar' => $score->challenge->pillar_type ?? 'N/A',
                            'points' => $score->points ?? 0
                        ];
                    })
                ];
            }),
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
