<?php

namespace App\Http\Controllers;

use App\Models\ChallengeActivity;
use App\Models\Challenges;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Player;
use App\Models\SubGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
                       $groups = \App\Models\Group::with('event')->get();
                       $subgroups = SubGroup::with('group','event')->get();
                       $activities = ChallengeActivity::with('event')->get();
        return view('events.index', compact('players','activities','subgroups','groups','organizations','allevents','events','challenges','allplayers'));
    }
    


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'organization_id' => 'required|exists:organizations,id',
            'event_type' => 'required|in:Brain Games,Playground Games,Esports',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:draft,live,closed',
        ], [
            'name.required' => 'Event name is required.',
            'organization_id.required' => 'Organization selection is required.',
            'organization_id.exists' => 'Selected organization is invalid.',
            'start_date.required' => 'Start date is required.',
            'end_date.after_or_equal' => 'End date must be after start date.',
        ]);
    
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $validator->errors()->first());
        }
    
        $data = $validator->validated();
    
        Event::create([
            ...$data,
            'registration_count' => 0,
        ]);
    
        return redirect()->back()->with('success', 'Event created successfully.');
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
