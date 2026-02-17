<?php

namespace App\Http\Controllers;

use App\Models\Challenges;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Player;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index() {
       
        $players = Player::whereDoesntHave('teams')
                         ->orderBy('name')
                         ->get();
        $allplayers = Player::all();
       
        $events = Event::where('status', '!=', 'closed')
                       ->orderBy('start_date', 'asc') 
                       ->get();
                       $allevents = Event::orderByRaw("
                       CASE status
                           WHEN 'live' THEN 1
                           WHEN 'close' THEN 2
                           WHEN 'draft' THEN 3
                           ELSE 4
                       END ASC
                   ")->get();

                       $challenges = Challenges::all();
                       $organizations = Organization::all(); // fetch all
                       $groups = \App\Models\Group::with('team')->latest()->get();
            
    
        return view('events.index', compact('players','groups','organizations','allevents','events','challenges','allplayers'));
    }
    


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'event_type' => 'required|in:match,tournament,season_tracking',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:draft,live,closed',
            'notes' => 'nullable|string',
        ]);
    
        if ($data['event_type'] === 'season_tracking' && empty($data['end_date'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['end_date' => 'End date is required for Season Tracking events.']);
        }
    
        Event::create([
            'name' => $data['name'],
            'event_type' => $data['event_type'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'location' => $data['location'] ?? null,
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
            'registration_count' => 0,
        ]);
    
        return redirect()
        ->back()
        ->with('success', 'Event created successfully.')
        ->header('Content-Type', 'text/html');
    
    }
    public function show(Event $event)
    {
        $event->load([
            'teams.players',
            'challenges'
        ]);
           // Count of players with at least one score recorded
           $completedPlayers = \App\Models\Scores::where('event_id', $event->id)
           ->whereIn('player_id', $event->teams->pluck('players.*.id')->flatten())
           ->distinct('player_id')
           ->count('player_id');
    
        return response()->json([
            'id' => $event->id,
            'name' => $event->name ?? 'N/A',
            'event_type' => $event->event_type ?? 'N/A',
            'status' => $event->status ?? 'N/A',
            'start_date' => $event->start_date ? date('M d, Y', strtotime($event->start_date)) : 'N/A',
            'end_date' => $event->end_date ? date('M d, Y', strtotime($event->end_date)) : 'N/A',
            'location' => $event->location ?? 'N/A',
            'completed_players' => $completedPlayers,
            'notes' => $event->notes ?? '-',
            'teams' => $event->teams->map(function($team){
                return [
                    'id' => $team->id,
                    'team_name' => $team->team_name ?? 'N/A',
                    'players' => $team->players->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->name ?? 'N/A'
                    ])
                ];
            }),
            'challenges' => $event->challenges->map(function($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name ?? 'N/A',
                    'pillar_type' => $c->pillar_type ?? 'N/A',
                    'sub_category' => $c->pillar_type === 'brain' ? ($c->sub_category ?? 'N/A') : null
                ];
            })
            
        ]);
    }
    


}
