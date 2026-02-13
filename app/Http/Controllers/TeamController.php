<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Player; 
use App\Exports\TeamsExport;
use App\Imports\TeamsImport;
use Illuminate\Http\Request;
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
        $request->validate([
            'team_name'       => 'required|string|max:255',
            'event_id'        => 'required|exists:events,id',
            'organization_id' => 'required|exists:organizations,id',
            'players'         => 'required|array|min:1',
            'players.*'       => 'exists:players,id'
        ]);
    
        $team = Team::create([
            'team_name'       => $request->team_name,
            'event_id'        => $request->event_id,
            'organization_id' => $request->organization_id,
        ]);
    
        $team->players()->attach($request->players);
    
        return back()->with('success', 'Team created successfully.');
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
                'total_points' => $totalPoints ?: 0
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
    public function update(Request $request, Team $team)
    {
        $request->validate([
            'team_name' => 'required|string|max:255',
            'players' => 'required|array|min:1',
            'players.*' => 'exists:players,id'
        ]);

        $team->update(['team_name' => $request->team_name]);
        $team->players()->sync($request->players);

        return response()->json(['success' => true, 'message' => 'Team updated successfully.']);
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
