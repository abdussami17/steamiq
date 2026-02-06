<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Player;
use App\Models\Challenges;
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
                       $challenges = Challenges::all();
    
        return view('events.index', compact('players', 'events','challenges','allplayers'));
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
    
        return redirect()->back()->with('success', 'Event created successfully.');
    }
    

}
