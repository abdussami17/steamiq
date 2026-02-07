<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Player; 
use Illuminate\Http\Request;

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
            'team_name' => 'required|string|max:255',
            'event_id'  => 'required|exists:events,id', 
            'players'   => 'required|array|min:1',
            'players.*' => 'exists:players,id'
        ]);
    
        $team = Team::create([
            'team_name' => $request->team_name,
            'event_id'  => $request->event_id,
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
    

}
