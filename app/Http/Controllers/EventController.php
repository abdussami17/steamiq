<?php

namespace App\Http\Controllers;

use App\Models\ChallengeActivity;
use App\Models\Challenges;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Player;
use App\Models\SubGroup;
use Carbon\Carbon;
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
        try {
            $event->load([
                'teams.students',
                'challengeactivity'
            ]);
    
            // Count of students with at least one score
            $studentIds = $event->teams->flatMap(fn($t) => $t->students->pluck('id'));
            $completedStudents = \App\Models\Score::where('event_id', $event->id)
                ->whereIn('student_id', $studentIds)
                ->distinct('student_id')
                ->count('student_id');
    
            return response()->json([
                'id' => $event->id,
                'name' => $event->name ?? 'N/A',
                'event_type' => $event->event_type ?? 'N/A',
                'status' => $event->status ?? 'N/A',
                'start_date' => $event->start_date ? Carbon::parse($event->start_date)->format('M d, Y') : 'N/A',
                'end_date' => $event->end_date ? Carbon::parse($event->end_date)->format('M d, Y') : 'N/A',
                'location' => $event->location ?? 'N/A',
                'completed_students' => $completedStudents,
                'notes' => $event->notes ?? '-',
                'teams' => $event->teams->map(fn($team) => [
                    'id' => $team->id,
                    'team_name' => $team->team_name ?? 'N/A',
                    'students' => $team->students->map(fn($s) => [
                        'id' => $s->id,
                        'name' => $s->name ?? 'N/A'
                    ])
                ]),
                'challenges' => $event->challengeactivity->map(fn($c) => [
                    'id' => $c->id,
                    'name' => $c->name ?? 'N/A'
                ])
            ]);
        } catch (\Throwable $e) {
            \Log::error('Event modal fetch error: '.$e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
    public function destroy(Event $event)
    {
        try {
            $event->delete();
            return redirect()->back()->with('success', 'Event deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Event deletion error: '.$e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete event.');
        }
    }

}
