<?php

namespace App\Http\Controllers;

use App\Models\ChallengeActivity;
use App\Models\Challenges;
use App\Models\Event;
use App\Models\Matches;
use App\Models\Organization;
use App\Models\Player;
use App\Models\SteamCategory;
use App\Models\SubGroup;
use App\Models\TournamentSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function index()
    {
        $players = Player::whereDoesntHave('teams')->orderBy('name')->get();
        $allplayers = Player::all();

        $events = Event::where('status', '!=', 'closed')->orderBy('start_date', 'asc')->get();
        $allevents = Event::with([
            'tournamentSetting',
            'activities',
            'organizations.groups.subgroups.teams.students'
        ])->get();

        $challenges = Challenges::all();
        $organizations = Organization::all(); // fetch all
        $groups = \App\Models\Group::with('organization')->get();
        $subgroups = SubGroup::with('group', 'event')->get();

        $steamCategories = SteamCategory::all();


        $activities = ChallengeActivity::with('event')->get();
        return view('events.index', compact('players','steamCategories' ,'activities', 'subgroups', 'groups', 'organizations', 'allevents', 'events', 'challenges', 'allplayers'));
    }
    public function store(Request $r)
    {
        $r->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:esports,xr',
            'location' => 'required|string|max:255',
            'brain_type' => 'required_if:brain_enabled,1|string|max:255|nullable',
            'brain_score' => 'required_if:brain_enabled,1|numeric|nullable',
        
            'activities' => 'nullable|array',
            'activities.*.type' => 'required|string|max:255',
            'activities.*.score' => 'required|numeric|min:0',
        ]);
        DB::transaction(function () use ($r) {

            $event = Event::create([
                'name' => $r->name,
                'type' => $r->type,
                'location' => $r->location,
                'start_date' => $r->start_date,
                'end_date' => $r->end_date,
                'status' => $r->status ?? 'draft'
            ]);

            TournamentSetting::create([
                'event_id' => $event->id,
            
                // Brain settings only for esports
                'brain_enabled' => $r->type === 'esports' ? ($r->brain_enabled ?? 0) : 0,
                'brain_type' => $r->type === 'esports' && $r->brain_enabled ? $r->brain_type : null,
                'brain_score' => $r->type === 'esports' && $r->brain_enabled ? $r->brain_score : null,
            
                // Game settings only for esports
                'game' => $r->type === 'esports' ? $r->game : null,
                'players_per_team' => $r->type === 'esports' ? $r->players_per_team : null,
                'match_rule' => $r->type === 'esports' ? $r->match_rule : null,
            
                // IMPORTANT FIX
                'points_win' => $r->type === 'esports' ? ($r->points_win ?? 0) : 0,
                'points_draw' => $r->type === 'esports' ? ($r->points_draw ?? 0) : 0,
            
                'tournament_type' => $r->type === 'esports'
                ? $r->esports_tournament_type
                : $r->xr_tournament_type,
        
        'number_of_teams' => $r->type === 'esports'
                ? $r->esports_number_of_teams
                : $r->xr_number_of_teams,
            ]);
            if ($r->filled('activities')) {
                foreach ($r->activities as $a) {
                    ChallengeActivity::create([
                        'event_id' => $event->id,
                        'name' => $a['type'],    // guaranteed to exist
                        'max_score' => $a['score'],  // guaranteed numeric
                    ]);
                }
            }
        });

        return redirect()
            ->route('events.index')
            ->with('success', 'Event created successfully');
    }
    public function loadBracket($id)
{
    $event = Event::with('tournamentSetting')->findOrFail($id);

    $teamsCount = $event->tournamentSetting->number_of_teams;
    $teams = []; 
    for ($i = 1; $i <= $teamsCount; $i++) {
        $teams[] = "Team " . $i;
    }

    $bracket = $this->generateBracket($teams);

    // Return **HTML partial** for modal
    return view('events.partials.bracket', compact('bracket'));
}
    private function generateBracket($teams)
    {
        $rounds = [];
        $roundTeams = $teams;
    
        while (count($roundTeams) > 1) {
            $matches = [];
            for ($i = 0; $i < ceil(count($roundTeams) / 2); $i++) {
                $matches[] = [
                    'team1' => $roundTeams[$i*2] ?? null,
                    'team2' => $roundTeams[$i*2+1] ?? null,
                    'winner' => null
                ];
            }
            $rounds[] = $matches;
    
            // Prepare next round placeholders
            $roundTeams = array_fill(0, count($matches), null);
        }
    
        return $rounds;
    }



    public function destroy(Event $event)
    {
        try {
            $event->delete();
            return redirect()->back()->with('success', 'Event deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Event deletion error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete event.');
        }
    }


    public function edit(Event $event): JsonResponse
{
    try {
        // Load related tournament settings and activities
        $event->load('tournamentSetting', 'activities');

        return response()->json([
            'success' => true,
            'data' => $event
        ]);
    } catch (\Throwable $e) {
        \Log::error('Event edit fetch error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch event data.'
        ], 500);
    }
}

public function update(Request $request, Event $event): JsonResponse
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'type' => 'required|in:esports,xr',
        'location' => 'required|string|max:255',
        'start_date' => 'required|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|in:draft,live,closed',
        'brain_enabled' => 'nullable|boolean',
        'brain_type' => 'nullable|string|max:255|required_if:brain_enabled,1',
        'brain_score' => 'nullable|numeric|required_if:brain_enabled,1',
        'game' => 'nullable|string|max:255',
        'players_per_team' => 'nullable|numeric',
        'match_rule' => 'nullable|string|max:50',
        'points_win' => 'nullable|numeric',
        'points_draw' => 'nullable|numeric',
        'tournament_type' => 'nullable|string|max:50',
        'number_of_teams' => 'nullable|numeric',
        'activities' => 'nullable|array',
        'activities.*.type' => 'required|string|max:255',
        'activities.*.score' => 'required|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::transaction(function () use ($request, $event) {

            // Update event basic info
            $event->update([
                'name' => $request->name,
                'type' => $request->type,
                'location' => $request->location,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status
            ]);

            // Update tournament settings
            $tournament = $event->tournamentSetting;
            $tournament->update([
                'brain_enabled' => $request->brain_enabled ?? 0,
                'brain_type' => $request->brain_enabled ? $request->brain_type : null,
                'brain_score' => $request->brain_enabled ? $request->brain_score : null,
                'game' => $request->game ?? null,
                'players_per_team' => $request->players_per_team ?? null,
                'match_rule' => $request->match_rule ?? null,
                'points_win' => $request->points_win ?? 0,
                'points_draw' => $request->points_draw ?? 0,
                'tournament_type' => $request->tournament_type ?? null,
                'number_of_teams' => $request->number_of_teams ?? null
            ]);

            // Remove old activities
            $event->activities()->delete();

            // Add new activities
            if ($request->filled('activities')) {
                foreach ($request->activities as $a) {
                    ChallengeActivity::create([
                        'event_id' => $event->id,
                        'name' => $a['type'],
                        'max_score' => $a['score'],
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully.'
        ]);
    } catch (\Throwable $e) {
        \Log::error('Event update error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to update event.'
        ], 500);
    }
}

public function show(Event $event)
{
    $event->load([
        'organizations.groups.subgroups.teams.students',
        'activities'
    ]);

    return response()->json($event);
}

 
}
