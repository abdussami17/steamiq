<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\ChallengeActivity;
use App\Models\Challenges;
use App\Models\Event;
use App\Models\Matches;
use App\Models\Organization;
use App\Models\Player;
use App\Models\SteamCategory;
use App\Models\Student;
use App\Models\SubGroup;
use App\Models\Team;
use App\Models\TournamentSetting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index()
    {
      
        $allplayers = Student::all();

        $events = Event::where('status', '!=', 'closed')->orderBy('start_date', 'asc')->get();
        $allevents = Event::with([
            'tournamentSetting',
            'activities',
            'organizations.groups.teams.students', // teams directly under group
            'organizations.groups.subgroups.teams.students' // teams under subgroup
        ])->get();

   
        $organizations = Organization::all(); // fetch all
        $groups = \App\Models\Group::with('organization')->get();
        $subgroups = SubGroup::with('group', 'event')->get();
        $teams = Team::select('id','name')->get();
        $steamCategories = SteamCategory::all();
        $cards = Card::all();
        $assignables = [
            'team'   => $teams,
            'player' => $allplayers,
            'group'  => $groups
        ];

        $activities = ChallengeActivity::with('event')->get();
        return view('events.index', compact('cards','assignables','steamCategories' ,'teams','activities', 'subgroups', 'groups', 'organizations', 'allevents', 'events', 'allplayers'));
    }


public function getOrganizations($eventId)
{
    $orgs = Organization::where('event_id', $eventId)->get();

    return response()->json($orgs);
}
    public function store(Request $r)
    {
        $r->validate([
            'name'         => 'required|string|max:255',
            'type'         => 'required|in:esports,xr',
            'location'     => 'required|string|max:255',
            'brain_type'   => 'nullable|required_if:brain_enabled,1|string|max:255',
            'brain_score'  => 'nullable|required_if:brain_enabled,1|numeric',
    
            'activities'                       => 'nullable|array',
            'activities.*.activity_or_mission' => 'required|in:activity,mission',
            'activities.*.activity_type'       => 'required_if:activities.*.activity_or_mission,activity|nullable|in:brain,esports,egaming,playground',
            'activities.*.badge_name'          => 'required_if:activities.*.activity_or_mission,mission|nullable|string|max:255',
            'activities.*.max_score'           => 'required|numeric|min:0',
            'activities.*.point_structure'     => 'nullable|in:per_team,per_player',
    
            'activities.*.brain_type'          => 'nullable|string|max:255',
            'activities.*.brain_description'   => 'nullable|string|max:500',
    
            'activities.*.esports_type'        => 'nullable|string|max:255',
            'activities.*.esports_players'     => 'nullable|string|max:50',
            'activities.*.esports_structure'   => 'nullable|string|max:50',
            'activities.*.esports_description' => 'nullable|string|max:500',
    
            'activities.*.egaming_type'        => 'nullable|string|max:255',
            'activities.*.egaming_mode'        => 'nullable|string|max:255',
            'activities.*.egaming_structure'   => 'nullable|string|max:255',
            'activities.*.egaming_description' => 'nullable|string|max:500',
    
            'activities.*.playground_description' => 'nullable|string|max:500',
        ]);
    
        DB::transaction(function () use ($r) {
    
            $event = Event::create([
                'name'       => $r->name,
                'type'       => $r->type,
                'location'   => $r->location,
                'start_date' => $r->start_date,
                'end_date'   => $r->end_date,
                'status'     => $r->status ?? 'draft',
            ]);
    
            $isEsports = $r->type === 'esports';
    
            TournamentSetting::create([
                'event_id'        => $event->id,
                'brain_enabled'   => $isEsports ? ($r->brain_enabled ?? 0) : 0,
                'brain_type'      => $isEsports && $r->brain_enabled ? $r->brain_type : null,
                'brain_score'     => $isEsports && $r->brain_enabled ? $r->brain_score : null,
                'game'            => $isEsports ? $r->game : null,
                'players_per_team'=> $isEsports ? $r->players_per_team : $r->xr_players_per_team,
                'match_rule'      => $isEsports ? $r->match_rule : null,
                'points_win'      => $isEsports ? ($r->points_win ?? 0) : 0,
                'points_draw'     => $isEsports ? ($r->points_draw ?? 0) : 0,
                'tournament_type' => $isEsports ? $r->esports_tournament_type : $r->xr_tournament_type,
                'number_of_teams' => $isEsports ? $r->esports_number_of_teams : $r->xr_number_of_teams,
            ]);
            if ($isEsports && $r->brain_enabled) {

                ChallengeActivity::create([
                    'event_id'            => $event->id,
                    'name'                => $r->brain_type,
                    'max_score'           => $r->brain_score,
                    'activity_or_mission' => 'activity',
                    'activity_type'       => 'brain',
            
                    'brain_type'          => $r->brain_type,
                    'brain_description'   => null,
            
                    'point_structure'     => 'per_team'
                ]);
            }
    
            if ($r->filled('activities')) {
                foreach ($r->activities as $a) {
                    ChallengeActivity::create([
                        'event_id'             => $event->id,
                        'name'                 => $a['activity_type'] ?? $a['badge_name'] ?? null,
                        'max_score'            => $a['max_score'] ?? 0,
                        'activity_or_mission'  => $a['activity_or_mission'],
                        'activity_type'        => $a['activity_type'] ?? null,
                        'badge_name'           => $a['badge_name'] ?? null,
                        'point_structure'      => $a['point_structure'] ?? null,
    
                        'brain_type'           => $a['brain_type'] ?? null,
                        'brain_description'    => $a['brain_description'] ?? null,
    
                        'esports_type'         => $a['esports_type'] ?? null,
                        'esports_players'      => $a['esports_players'] ?? null,
                        'esports_structure'    => $a['esports_structure'] ?? null,
                        'esports_description'  => $a['esports_description'] ?? null,
    
                        'egaming_type'         => $a['egaming_type'] ?? null,
                        'egaming_mode'         => $a['egaming_mode'] ?? null,
                        'egaming_structure'    => $a['egaming_structure'] ?? null,
                        'egaming_description'  => $a['egaming_description'] ?? null,
    
                        'playground_description' => $a['playground_description'] ?? null,
                    ]);
                }
            }
        });
    
        return redirect()->back()->with('success', 'Event created successfully');
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
            $event->load('tournamentSetting', 'activities');
            return response()->json(['success' => true, 'data' => $event]);
        } catch (\Throwable $e) {
            \Log::error('Event edit fetch error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch event data.'], 500);
        }
    }
    
    public function update(Request $request, Event $event): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'type'         => 'required|in:esports,xr',
            'location'     => 'required|string|max:255',
            'status'       => 'required|in:draft,live,closed',
    
            'brain_type'   => 'nullable|string|max:255|required_if:brain_enabled,1',
            'brain_score'  => 'nullable|numeric|required_if:brain_enabled,1',
    
            'activities'                              => 'nullable|array',
            'activities.*.activity_or_mission'        => 'required|in:activity,mission',
            'activities.*.activity_type'              => 'nullable|in:brain,esports,egaming,playground',
            'activities.*.badge_name'                 => 'nullable|string|max:255',
            'activities.*.max_score'                  => 'nullable|numeric|min:0',
            'activities.*.point_structure'            => 'nullable|in:per_team,per_player',
            'activities.*.brain_type'                 => 'nullable|string|max:255',
            'activities.*.brain_description'          => 'nullable|string|max:500',
            'activities.*.esports_type'               => 'nullable|string|max:255',
            'activities.*.esports_players'            => 'nullable|string|max:50',
            'activities.*.esports_structure'          => 'nullable|string|max:50',
            'activities.*.esports_description'        => 'nullable|string|max:500',
            'activities.*.egaming_type'               => 'nullable|string|max:255',
            'activities.*.egaming_mode'               => 'nullable|string|max:255',
            'activities.*.egaming_structure'          => 'nullable|string|max:255',
            'activities.*.egaming_description'        => 'nullable|string|max:500',
            'activities.*.playground_description'     => 'nullable|string|max:500',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
    
        try {
            DB::transaction(function () use ($request, $event) {
    
                $isEsports = $request->type === 'esports';
    
                $event->update([
                    'name'       => $request->name,
                    'type'       => $request->type,
                    'location'   => $request->location,
                    'start_date' => $request->start_date,
                    'end_date'   => $request->end_date,
                    'status'     => $request->status,
                ]);
    
                $event->tournamentSetting->update([
                    'brain_enabled'    => $isEsports ? ($request->brain_enabled ?? 0) : 0,
                    'brain_type'       => $isEsports && $request->brain_enabled ? $request->brain_type : null,
                    'brain_score'      => $isEsports && $request->brain_enabled ? $request->brain_score : null,
                    'game'             => $isEsports ? $request->game : null,
                    'players_per_team' => $isEsports ? $request->players_per_team : $request->xr_players_per_team,
                    'match_rule'       => $isEsports ? $request->match_rule : null,
                    'points_win'       => $isEsports ? ($request->points_win ?? 0) : 0,
                    'points_draw'      => $isEsports ? ($request->points_draw ?? 0) : 0,
                    'tournament_type'  => $isEsports ? $request->esports_tournament_type : $request->xr_tournament_type,
                    'number_of_teams'  => $isEsports ? $request->esports_number_of_teams : $request->xr_number_of_teams,
                ]);
    
                $event->activities()->delete();
    
                if ($isEsports && $request->brain_enabled) {
                    ChallengeActivity::create([
                        'event_id'            => $event->id,
                        'name'                => $request->brain_type,
                        'max_score'           => $request->brain_score,
                        'activity_or_mission' => 'activity',
                        'activity_type'       => 'brain',
                        'brain_type'          => $request->brain_type,
                        'brain_description'   => null,
                        'point_structure'     => 'per_team'
                    ]);
                }
    
                if ($request->filled('activities')) {
                    foreach ($request->activities as $a) {
                        ChallengeActivity::create([
                            'event_id'               => $event->id,
                            'name'                   => $a['activity_type'] ?? $a['badge_name'] ?? null,
                            'max_score'              => $a['max_score'] ?? 0,
                            'activity_or_mission'    => $a['activity_or_mission'],
                            'activity_type'          => $a['activity_type'] ?? null,
                            'badge_name'             => $a['badge_name'] ?? null,
                            'point_structure'        => $a['point_structure'] ?? null,
                            'brain_type'             => $a['brain_type'] ?? null,
                            'brain_description'      => $a['brain_description'] ?? null,
                            'esports_type'           => $a['esports_type'] ?? null,
                            'esports_players'        => $a['esports_players'] ?? null,
                            'esports_structure'      => $a['esports_structure'] ?? null,
                            'esports_description'    => $a['esports_description'] ?? null,
                            'egaming_type'           => $a['egaming_type'] ?? null,
                            'egaming_mode'           => $a['egaming_mode'] ?? null,
                            'egaming_structure'      => $a['egaming_structure'] ?? null,
                            'egaming_description'    => $a['egaming_description'] ?? null,
                            'playground_description' => $a['playground_description'] ?? null,
                        ]);
                    }
                }
    
            });
    
            return response()->json(['success' => true, 'message' => 'Event updated successfully.']);
    
        } catch (\Throwable $e) {
            \Log::error('Event update error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update event.'], 500);
        }
    }
    public function duplicate(Event $event)
{
    try {
        DB::transaction(function () use ($event, &$newEvent) {

            $newEvent = $event->replicate();
            $newEvent->name = $event->name . ' (Copy)';
            $newEvent->status = 'draft';
            $newEvent->push();

            if ($event->tournamentSetting) {
                $newSetting = $event->tournamentSetting->replicate();
                $newSetting->event_id = $newEvent->id;
                $newSetting->save();
            }

            foreach ($event->activities as $activity) {
                $newActivity = $activity->replicate();
                $newActivity->event_id = $newEvent->id;
                $newActivity->save();
            }

        });

        return response()->json([
            'success' => true,
            'message' => 'Event duplicated successfully'
        ]);

    } catch (\Throwable $e) {
        \Log::error('Duplicate event error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to duplicate event'
        ], 500);
    }
}
public function show(Event $event)
{
    $event->load([
        'activities',
        'tournamentSetting',
        'organizations.groups.teams.students',        
        'organizations.groups.subgroups.teams.students' 
    ]);

    return response()->json($event);
}










public function bracket(Event $event): JsonResponse
{
    $event->load('tournamentSetting', 'activities');
    $ts       = $event->tournamentSetting;
    $numTeams = (int) ($ts->number_of_teams ?? 0);
    $type     = $ts->tournament_type ?? 'single_elimination';

    $teams  = collect(range(1, max($numTeams, 2)))->map(fn($n) => ['seed' => $n, 'name' => 'TBD']);
    $rounds = match($type) {
        'round_robin'        => $this->buildRoundRobin($teams),
        'double_elimination' => $this->buildDoubleElimination($teams),
        default              => $this->buildSingleElimination($teams),
    };

    return response()->json([
        'success'    => true,
        'event'      => $event->only('id', 'name', 'type', 'location', 'status', 'start_date', 'end_date'),
        'setting'    => $ts,
        'activities' => $event->activities,
        'type'       => $type,
        'rounds'     => $rounds,
    ]);
}

private function buildSingleElimination(\Illuminate\Support\Collection $teams): array
{
    $slots = $teams->toArray();
    $size  = max(2, (int) pow(2, ceil(log(max(count($slots), 2), 2))));
    while (count($slots) < $size) $slots[] = ['seed' => null, 'name' => 'BYE'];

    $rounds  = [];
    $current = array_chunk($slots, 2);
    $num     = 1;

    while (count($current) >= 1) {
        $rounds[] = [
            'name'    => match(true) {
                count($current) === 1 => 'Final',
                count($current) === 2 => 'Semi-Finals',
                count($current) === 4 => 'Quarter-Finals',
                default               => 'Round ' . $num,
            },
            'bracket' => 'Winners',
            'matches' => $current,
        ];
        if (count($current) <= 1) break;
        $current = array_chunk(array_fill(0, (int)(count($current) / 2), [['seed' => null, 'name' => 'TBD'], ['seed' => null, 'name' => 'TBD']]), 1);
        $current = array_map(fn($m) => $m[0], $current);
        $current = array_chunk($current, 2);
        $num++;
    }

    return $rounds;
}

private function buildRoundRobin(\Illuminate\Support\Collection $teams): array
{
    $slots   = $teams->toArray();
    $matches = [];
    for ($i = 0; $i < count($slots); $i++)
        for ($j = $i + 1; $j < count($slots); $j++)
            $matches[] = [$slots[$i], $slots[$j]];

    return [['name' => 'Round Robin', 'bracket' => 'Round Robin', 'matches' => $matches]];
}

private function buildDoubleElimination(\Illuminate\Support\Collection $teams): array
{
    $winners = $this->buildSingleElimination($teams);
    $losers  = [['name' => 'Losers Round 1', 'bracket' => 'Losers', 'matches' => $winners[0]['matches'] ?? []]];
    $grand   = [['name' => 'Grand Final',    'bracket' => 'Grand Final', 'matches' => [[['seed' => null, 'name' => 'TBD'], ['seed' => null, 'name' => 'TBD']]]]];

    return array_merge(
        array_map(fn($r) => array_merge($r, ['bracket' => 'Winners']), $winners),
        $losers,
        $grand
    );
}
 
}
