<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Event;
use App\Models\Player;

use App\Models\TeamMember;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index()
    {
        $events = Event::where('status', 'live')
        ->select('id', 'name')
        ->get();
        
    
    
        return view('players.index', compact('events'));
    }
    

    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'player_name'  => 'required|string|max:255',
            'player_email' => 'required|email|unique:players,email',
            'assign_team'  => 'nullable|exists:teams,id',
            'event_id'     => 'nullable|exists:events,id',
        ]);
    
        $player = Player::create([
            'name'     => $validated['player_name'],
            'email'    => $validated['player_email'],
            'event_id' => $validated['event_id'] ?? 1,
        ]);
    
        if (!empty($validated['assign_team'])) {
            TeamMember::create([
                'player_id' => $player->id,
                'team_id'   => $validated['assign_team'],
            ]);
        }
    
        return redirect()->back()->with('success', 'Player added successfully!');
    }
    



}
