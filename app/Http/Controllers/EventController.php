<?php

namespace App\Http\Controllers;

use App\Models\BracketMatch;
use App\Models\Card;
use App\Models\ChallengeActivity;
use App\Models\Score;
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

    public function updateStatus(Request $request, Event $event): JsonResponse
    {
        $request->validate(['status' => 'required|in:draft,live,closed']);
        $event->update(['status' => $request->status]);
        return response()->json(['success' => true, 'status' => $event->status]);
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



public function getWinnerTeams(Event $event): JsonResponse
{
    $event->load([
        'organizations.groups.teams',
        'organizations.groups.subgroups.teams',
    ]);

    $teams = $event->organizations->flatMap->groups->flatMap(function ($group) {
        $direct   = $group->teams->map(fn($t) => [
            'id'           => $t->id,
            'name'         => $t->name,
            'group_name'   => $group->name,
            'subgroup_name'=> null,
            'org_name'     => $group->organization->name ?? null,
        ]);
        $fromSubs = $group->subgroups->flatMap(fn($sub) =>
            $sub->teams->map(fn($t) => [
                'id'           => $t->id,
                'name'         => $t->name,
                'group_name'   => $group->name,
                'subgroup_name'=> $sub->name,
                'org_name'     => $group->organization->name ?? null,
            ])
        );
        return $direct->concat($fromSubs);
    })->unique('id')->values();

    // Also find teams that have reached the grand final (last-round bracket match)
    $finalTeams = collect();
    $grandFinal = \App\Models\BracketMatch::where('event_id', $event->id)
        ->whereIn('phase', ['grand_final', 'pod_final'])
        ->orderByRaw("CASE WHEN phase='grand_final' THEN 0 ELSE 1 END")
        ->orderBy('round_no', 'desc')
        ->orderBy('match_no')
        ->first();

    if ($grandFinal) {
        $teamIds = array_filter([$grandFinal->team_a_id, $grandFinal->team_b_id]);
        if (count($teamIds)) {
            $dbTeams = \App\Models\Team::whereIn('id', $teamIds)->get();
            $finalTeams = $dbTeams->map(fn($t) => [
                'id'           => $t->id,
                'name'         => $t->name,
                'group_name'   => null,
                'subgroup_name'=> null,
                'org_name'     => null,
            ])->values();
        }
    }

    return response()->json([
        'success'         => true,
        'teams'           => $teams,
        'final_teams'     => $finalTeams,
        'current_winner'  => $event->winner_team_id,
    ]);
}

public function setWinner(Request $request, Event $event): JsonResponse
{
    $request->validate(['winner_team_id' => 'required|exists:teams,id']);

    try {
        $event->update(['winner_team_id' => $request->winner_team_id]);

        // If caller requested closure, also close event and return aggregated stats
        if ($request->has('close') && $request->close) {
            $event->update(['status' => 'closed']);

            // collect team models (teams under orgs/groups/subgroups)
            $teamModels = $event->organizations->flatMap->groups->flatMap(function ($group) {
                $direct   = $group->teams;
                $fromSubs = $group->subgroups->flatMap(fn($s) => $s->teams);
                return $direct->concat($fromSubs);
            })->unique('id')->values();

            $participants = $teamModels->flatMap->students->count();

            // total points
            $totalPoints = \App\Models\Score::where('event_id', $event->id)->sum('points');

            // helper to pull top N by activity type
            $topByType = function ($type) use ($event) {
                return \App\Models\Score::where('event_id', $event->id)
                    ->whereHas('challengeActivity', fn($q) => $q->where('activity_type', $type))
                    ->with(['student','team','challengeActivity'])
                    ->orderBy('points', 'desc')
                    ->take(10)
                    ->get()
                    ->map(fn($s) => [
                        'points' => $s->points,
                        'student' => $s->student?->name,
                        'team' => $s->team?->name,
                        'activity' => $s->challengeActivity?->display_name ?? null,
                    ])->values()->toArray();
            };

            $stats = [
                'participants' => $participants,
                'total_points' => (int) $totalPoints,
                'top10_brain' => $topByType('brain'),
                'top10_playground' => $topByType('playground'),
                'top10_egaming' => $topByType('egaming'),
                'top10_esports' => $topByType('esports'),
            ];

            // overall top score
            $top = \App\Models\Score::where('event_id', $event->id)
                ->with(['student','team','challengeActivity'])
                ->orderBy('points','desc')
                ->first();
            $stats['top_score'] = $top ? [
                'points' => $top->points,
                'student' => $top->student?->name,
                'team' => $top->team?->name,
                'activity' => $top->challengeActivity?->display_name ?? null,
            ] : null;

            $winner = \App\Models\Team::find($request->winner_team_id);

            return response()->json(['success' => true, 'message' => 'Event closed and winner set.', 'stats' => $stats, 'winner' => $winner]);
        }

        return response()->json(['success' => true, 'message' => 'Winner saved successfully.']);
    } catch (\Throwable $e) {
        \Log::error('Set winner error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Failed to save winner.'], 500);
    }
}
    /**
     * Return the aggregated results/stats for an event (used to re-open the summary modal).
     */
    public function results(Event $event): JsonResponse
    {
        try {
            $event->load([
                'organizations.groups.teams',
                'organizations.groups.subgroups.teams',
            ]);

            // collect team models (teams under orgs/groups/subgroups)
            $teamModels = $event->organizations->flatMap->groups->flatMap(function ($group) {
                $direct   = $group->teams;
                $fromSubs = $group->subgroups->flatMap(fn($s) => $s->teams);
                return $direct->concat($fromSubs);
            })->unique('id')->values();

            $participants = $teamModels->flatMap->students->count();

            // total points
            $totalPoints = \App\Models\Score::where('event_id', $event->id)->sum('points');

            // helper to pull top N by activity type
            $topByType = function ($type) use ($event) {
                return \App\Models\Score::where('event_id', $event->id)
                    ->whereHas('challengeActivity', fn($q) => $q->where('activity_type', $type))
                    ->with(['student','team','challengeActivity'])
                    ->orderBy('points', 'desc')
                    ->take(10)
                    ->get()
                    ->map(fn($s) => [
                        'points' => $s->points,
                        'student' => $s->student?->name,
                        'team' => $s->team?->name,
                        'activity' => $s->challengeActivity?->display_name ?? null,
                    ])->values()->toArray();
            };

            $stats = [
                'participants' => $participants,
                'total_points' => (int) $totalPoints,
                'top10_brain' => $topByType('brain'),
                'top10_playground' => $topByType('playground'),
                'top10_egaming' => $topByType('egaming'),
                'top10_esports' => $topByType('esports'),
            ];

            // overall top score
            $top = \App\Models\Score::where('event_id', $event->id)
                ->with(['student','team','challengeActivity'])
                ->orderBy('points','desc')
                ->first();
            $stats['top_score'] = $top ? [
                'points' => $top->points,
                'student' => $top->student?->name,
                'team' => $top->team?->name,
                'activity' => $top->challengeActivity?->display_name ?? null,
            ] : null;

            $winner = $event->winner_team_id ? \App\Models\Team::find($event->winner_team_id) : null;

            return response()->json(['success' => true, 'stats' => $stats, 'winner' => $winner]);
        } catch (\Throwable $e) {
            \Log::error('Fetch results error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch results.'], 500);
        }
    }







    // =========================================================================
    // BRACKET — DB-backed, interactive, pod-based
    // =========================================================================

    public function bracket(Event $event): JsonResponse
    {
        $event->load([
            'tournamentSetting',
            'activities',
            'organizations.groups.teams',
            'organizations.groups.subgroups.teams',
        ]);

        $ts   = $event->tournamentSetting;
        $type = $ts->tournament_type ?? 'single_elimination';

        // Auto-init if no matches exist in DB yet
        if (!BracketMatch::where('event_id', $event->id)->exists()) {
            $this->initBracketMatches($event);
        }

        // Load all bracket matches with team relations
        $allMatches = BracketMatch::where('event_id', $event->id)
            ->with(['teamA', 'teamB', 'winner'])
            ->orderBy('pod')->orderBy('division')->orderBy('phase')
            ->orderBy('round_no')->orderBy('match_no')
            ->get();

        // ── Organise into pods → phases → rounds ─────────────────────────────
        $podsMap      = [];  // [podName => [phaseKey => [round_no => [matches]]]]
        $grandFinalRd = [];  // [round_no => [matches]]

        foreach ($allMatches as $m) {
            $matchData = $this->formatBracketMatch($m);

            if ($m->phase === 'grand_final') {
                $grandFinalRd[$m->round_no][$m->match_no] = $matchData;
            } else {
                $pod      = $m->pod      ?? 'General';
                $div      = $m->division ?? 'Cross';
                $phaseKey = $m->phase === 'pod_semifinal'
                    ? 'pod_final'
                    : (strtolower($div) . '_qualification');

                if (!isset($podsMap[$pod][$phaseKey])) {
                    $podsMap[$pod][$phaseKey] = [
                        'key'      => $phaseKey,
                        'label'    => $m->phase === 'pod_semifinal'
                            ? 'Pod Final'
                            : ucfirst(strtolower($div)) . ' Division',
                        'division' => $m->phase === 'pod_semifinal' ? null : $div,
                        'rounds'   => [],
                    ];
                }
                $podsMap[$pod][$phaseKey]['rounds'][$m->round_no][$m->match_no] = $matchData;
            }
        }

        // ── Format pods array ─────────────────────────────────────────────────
        $phaseOrder = [
            'primary_qualification' => 0,
            'junior_qualification'  => 1,
            'pod_final'             => 2,
        ];
        $pods = [];
        foreach ($podsMap as $podName => $phases) {
            uksort($phases, fn($a, $b) => ($phaseOrder[$a] ?? 99) <=> ($phaseOrder[$b] ?? 99));
            $formattedPhases = [];

            foreach ($phases as $phaseData) {
                $rounds = [];
                ksort($phaseData['rounds']);
                $maxRound = max(array_keys($phaseData['rounds']));

                foreach ($phaseData['rounds'] as $rNo => $rMatches) {
                    ksort($rMatches);
                    $cnt = count($rMatches);
                    $roundName = match (true) {
                        $rNo === $maxRound && $cnt === 1 => 'Final',
                        $rNo === $maxRound - 1 && $cnt <= 2 => 'Semi-Finals',
                        $rNo === $maxRound - 2 && $cnt <= 4 => 'Quarter-Finals',
                        default => 'Round ' . $rNo,
                    };
                    $rounds[] = [
                        'round_no'   => $rNo,
                        'round_name' => $roundName,
                        'matches'    => array_values($rMatches),
                    ];
                }
                $formattedPhases[] = array_merge($phaseData, ['rounds' => $rounds]);
            }

            $pods[] = [
                'name'   => $podName,
                'label'  => $podName . ' Pod',
                'phases' => $formattedPhases,
            ];
        }

        // ── Grand Final ───────────────────────────────────────────────────────
        $grandFinal = null;
        if (!empty($grandFinalRd)) {
            ksort($grandFinalRd);
            $gfRounds = [];
            foreach ($grandFinalRd as $rNo => $rMatches) {
                ksort($rMatches);
                $gfRounds[] = [
                    'round_no'   => $rNo,
                    'round_name' => 'Grand Final',
                    'matches'    => array_values($rMatches),
                ];
            }
            $grandFinal = ['rounds' => $gfRounds];
        }

        // Flat team list for UI
        $realTeams = $event->organizations->flatMap->groups->flatMap(function ($group) {
            $direct   = $group->teams->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'division' => $t->division]);
            $fromSubs = $group->subgroups->flatMap(
                fn($sub) => $sub->teams->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'division' => $t->division])
            );
            return $direct->concat($fromSubs);
        })->unique('id')->values();

        return response()->json([
            'success'     => true,
            'event'       => $event->only('id', 'name', 'type', 'location', 'status', 'start_date', 'end_date'),
            'setting'     => $ts,
            'activities'  => $event->activities,
            'type'        => $type,
            'editable'    => auth()->check(),
            'pods'        => $pods,
            'grand_final' => $grandFinal,
            'teams'       => $realTeams->values(),
        ]);
    }

    // ── Force re-initialise bracket ───────────────────────────────────────────
    public function bracketBoard()
    {
        $events = Event::orderBy('start_date', 'desc')->get(['id', 'name', 'type', 'status', 'start_date']);
        return view('bracket.index', compact('events'));
    }

    public function bracketInit(Request $request, Event $event): JsonResponse
    {
        try {
            BracketMatch::where('event_id', $event->id)->delete();

            // Also clear any bracket scores previously synced to the scores table
            $bracketActivity = ChallengeActivity::where('event_id', $event->id)
                ->where('name', 'Tournament Bracket')->first();
            if ($bracketActivity) {
                Score::where('event_id', $event->id)
                    ->where('challenge_activity_id', $bracketActivity->id)
                    ->delete();
            }

            $event->load(['organizations.groups.teams', 'organizations.groups.subgroups.teams']);
            $this->initBracketMatches($event);
            return response()->json(['success' => true, 'message' => 'Bracket initialised.']);
        } catch (\Throwable $e) {
            \Log::error('bracketInit error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to initialise bracket.'], 500);
        }
    }

    // ── Update a single match (set score / winner) ────────────────────────────
    public function bracketUpdateMatch(Request $request, Event $event, BracketMatch $match): JsonResponse
    {
        if ($match->event_id !== $event->id) {
            return response()->json(['success' => false, 'message' => 'Match not found.'], 404);
        }

        $request->validate([
            'winner_team_id' => 'nullable|exists:teams,id',
            'team_a_score'   => 'nullable|integer',
            'team_b_score'   => 'nullable|integer',
        ]);

        try {
            DB::transaction(function () use ($request, $match) {
                $winnerId  = $request->winner_team_id ?? null;
                $prevWinner = $match->winner_team_id;

                $match->update([
                    'winner_team_id' => $winnerId,
                    'team_a_score'   => $request->has('team_a_score') ? $request->team_a_score : $match->team_a_score,
                    'team_b_score'   => $request->has('team_b_score') ? $request->team_b_score : $match->team_b_score,
                    'status'         => $winnerId ? 'completed' : 'pending',
                ]);

                if ($match->next_match_id) {
                    $next = BracketMatch::find($match->next_match_id);
                    if ($next) {
                        // If winner changed or was cleared, also clear any deeper advancement
                        if ($prevWinner && $prevWinner !== $winnerId) {
                            // Clear old winner from next match if it was there
                            if ($match->next_is_team_a && $next->team_a_id === $prevWinner) {
                                $next->update(['team_a_id' => $winnerId, 'winner_team_id' => null, 'status' => 'pending']);
                            } elseif (!$match->next_is_team_a && $next->team_b_id === $prevWinner) {
                                $next->update(['team_b_id' => $winnerId, 'winner_team_id' => null, 'status' => 'pending']);
                            }
                        } elseif ($winnerId) {
                            // Advance winner
                            if ($match->next_is_team_a) {
                                $next->update(['team_a_id' => $winnerId]);
                            } else {
                                $next->update(['team_b_id' => $winnerId]);
                            }
                        } elseif (!$winnerId) {
                            // Undo: clear slot in next match
                            if ($match->next_is_team_a) {
                                $next->update(['team_a_id' => null, 'winner_team_id' => null, 'status' => 'pending']);
                            } else {
                                $next->update(['team_b_id' => null, 'winner_team_id' => null, 'status' => 'pending']);
                            }
                        }
                    }
                }
            });

            $match->refresh()->load(['teamA', 'teamB', 'winner']);

            // Sync cumulative bracket points into the scores table (outside transaction, non-fatal)
            $this->syncBracketScores($event);

            return response()->json(['success' => true, 'match' => $this->formatBracketMatch($match)]);
        } catch (\Throwable $e) {
            \Log::error('bracketUpdateMatch error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update match.'], 500);
        }
    }

    // ── Sync bracket match totals → scores table ──────────────────────────────
    private function syncBracketScores(Event $event): void
    {
        try {
            // Ensure there is a dedicated challenge activity for bracket points
            $activity = ChallengeActivity::firstOrCreate(
                ['event_id' => $event->id, 'name' => 'Tournament Bracket'],
                [
                    'activity_or_mission' => 'activity',
                    'activity_type'       => 'esports',
                    'esports_type'        => 'Tournament Bracket',
                    'point_structure'     => 'per_team',
                    'max_score'           => 99999,
                ]
            );

            // Sum each team's points across all bracket matches in this event
            $matches = BracketMatch::where('event_id', $event->id)->get();
            $teamPoints = [];
            foreach ($matches as $m) {
                if ($m->team_a_id !== null && $m->team_a_score !== null) {
                    $teamPoints[$m->team_a_id] = ($teamPoints[$m->team_a_id] ?? 0) + (int) $m->team_a_score;
                }
                if ($m->team_b_id !== null && $m->team_b_score !== null) {
                    $teamPoints[$m->team_b_id] = ($teamPoints[$m->team_b_id] ?? 0) + (int) $m->team_b_score;
                }
            }

            // Upsert one Score row per team for this activity
            foreach ($teamPoints as $teamId => $points) {
                Score::updateOrCreate(
                    [
                        'event_id'             => $event->id,
                        'challenge_activity_id' => $activity->id,
                        'team_id'              => $teamId,
                        'student_id'           => null,
                    ],
                    ['points' => $points]
                );
            }
        } catch (\Throwable $e) {
            \Log::error('syncBracketScores error: ' . $e->getMessage());
        }
    }

    // ── Internal: format a BracketMatch for the API response ─────────────────
    private function formatBracketMatch(BracketMatch $m): array
    {
        $ta = $m->teamA;
        $tb = $m->teamB;
        return [
            'id'             => $m->id,
            'round_no'       => $m->round_no,
            'match_no'       => $m->match_no,
            'team_a'         => $ta ? ['id' => $ta->id, 'name' => $ta->name, 'division' => $ta->division] : null,
            'team_b'         => $tb ? ['id' => $tb->id, 'name' => $tb->name, 'division' => $tb->division] : null,
            'team_a_score'   => $m->team_a_score,
            'team_b_score'   => $m->team_b_score,
            'winner_team_id' => $m->winner_team_id,
            'winner_name'    => $m->winner?->name,
            'status'         => $m->status,
            'next_match_id'  => $m->next_match_id,
            'next_is_team_a' => $m->next_is_team_a,
            'is_bye_a'       => ($m->team_a_id === null && $tb !== null),
            'is_bye_b'       => ($m->team_b_id === null && $ta !== null),
        ];
    }

    // ── Internal: initialise bracket match records in DB ─────────────────────
    private function initBracketMatches(Event $event): void
    {
        // Collect teams by pod (group->pod) and division
        $podTeams = [];  // ['Red' => ['Primary' => [...], 'Junior' => []], 'Blue' => [...]]

        foreach ($event->organizations as $org) {
            foreach ($org->groups as $group) {
                $podName = $group->pod ?? $group->group_name;
                if (!isset($podTeams[$podName])) {
                    $podTeams[$podName] = ['Primary' => [], 'Junior' => [], 'Other' => []];
                }
                foreach ($group->teams as $team) {
                    $div = in_array($team->division, ['Primary', 'Junior']) ? $team->division : 'Other';
                    $podTeams[$podName][$div][] = ['id' => $team->id, 'name' => $team->name];
                }
                foreach ($group->subgroups as $sub) {
                    foreach ($sub->teams as $team) {
                        $div = in_array($team->division, ['Primary', 'Junior']) ? $team->division : 'Other';
                        $podTeams[$podName][$div][] = ['id' => $team->id, 'name' => $team->name];
                    }
                }
            }
        }

        DB::transaction(function () use ($event, $podTeams) {
            // Count active pods (pods that have at least one team)
            $activePods = array_filter($podTeams, fn($t) =>
                !empty($t['Primary']) || !empty($t['Junior']) || !empty($t['Other'])
            );

            // Create Grand Final if multiple pods
            $grandFinal = null;
            if (count($activePods) >= 2) {
                $grandFinal = BracketMatch::create([
                    'event_id' => $event->id,
                    'pod'      => null,
                    'division' => null,
                    'phase'    => 'grand_final',
                    'round_no' => 1,
                    'match_no' => 0,
                    'status'   => 'pending',
                ]);
            }

            $podIdx = 0;
            foreach ($activePods as $podName => $divTeams) {
                $hasPrimary = !empty($divTeams['Primary']);
                $hasJunior  = !empty($divTeams['Junior']);
                $hasOther   = !empty($divTeams['Other']);

                $allTeams = array_merge(
                    $divTeams['Primary'],
                    $divTeams['Junior'],
                    $divTeams['Other']
                );

                $bothDivs = ($hasPrimary || $hasOther) && $hasJunior;

                // Pod Final (cross-division): primary champ vs junior champ
                $podFinal = null;
                if ($bothDivs) {
                    $podFinal = BracketMatch::create([
                        'event_id'       => $event->id,
                        'pod'            => $podName,
                        'division'       => null,
                        'phase'          => 'pod_semifinal',
                        'round_no'       => 1,
                        'match_no'       => 0,
                        'status'         => 'pending',
                        'next_match_id'  => $grandFinal?->id,
                        'next_is_team_a' => $grandFinal ? ($podIdx % 2 === 0) : null,
                    ]);
                }

                // Determine where each division's final feeds into
                $primNextId  = $podFinal?->id ?? $grandFinal?->id;
                $primNextIsA = $podFinal ? true : ($podIdx % 2 === 0 ? true : false);

                $junNextId   = $podFinal?->id ?? $grandFinal?->id;
                $junNextIsA  = $podFinal ? false : ($podIdx % 2 === 0 ? true : false);

                if ($hasPrimary) {
                    $this->buildElimBracket($event->id, $podName, 'Primary', 'qualification', $divTeams['Primary'], $primNextId, $primNextIsA);
                }
                if ($hasJunior) {
                    $this->buildElimBracket($event->id, $podName, 'Junior', 'qualification', $divTeams['Junior'], $junNextId, $junNextIsA);
                }
                if ($hasOther && !$hasPrimary) {
                    // Treat "Other" as primary-side if no primary teams
                    $this->buildElimBracket($event->id, $podName, 'Primary', 'qualification', $divTeams['Other'], $primNextId, $primNextIsA);
                }

                $podIdx++;
            }
        });
    }

    // ── Internal: build a single-elimination bracket for a set of teams ───────
    private function buildElimBracket(
        int     $eventId,
        string  $pod,
        string  $division,
        string  $phase,
        array   $teams,
        ?int    $nextMatchId,
        ?bool   $nextIsTeamA
    ): void {
        if (empty($teams)) return;

        // Pad to next power of 2 with BYE slots
        $size = max(2, (int) pow(2, ceil(log(max(count($teams), 2), 2))));
        while (count($teams) < $size) {
            $teams[] = ['id' => null, 'name' => 'BYE'];
        }

        $numRounds = (int) log($size, 2);

        // Insert from final (round $numRounds) → first round (round 1) so IDs are available for chaining
        $matchIdsByRound = [];

        for ($r = $numRounds; $r >= 1; $r--) {
            $matchCount          = (int) ($size / pow(2, $r));
            $matchIdsByRound[$r] = [];

            for ($m = 0; $m < $matchCount; $m++) {
                if ($r === $numRounds) {
                    $nMatchId = $nextMatchId;
                    $nIsTeamA = $nextIsTeamA;
                } else {
                    $nMatchId = $matchIdsByRound[$r + 1][intdiv($m, 2)] ?? null;
                    $nIsTeamA = ($m % 2 === 0);
                }

                $teamAId = null;
                $teamBId = null;
                if ($r === 1) {
                    $teamAId = $teams[$m * 2]['id']     ?? null;
                    $teamBId = $teams[$m * 2 + 1]['id'] ?? null;
                }

                $match = BracketMatch::create([
                    'event_id'       => $eventId,
                    'pod'            => $pod,
                    'division'       => $division,
                    'phase'          => $phase,
                    'round_no'       => $r,
                    'match_no'       => $m,
                    'team_a_id'      => $teamAId,
                    'team_b_id'      => $teamBId,
                    'status'         => 'pending',
                    'next_match_id'  => $nMatchId,
                    'next_is_team_a' => $nIsTeamA,
                ]);

                $matchIdsByRound[$r][$m] = $match->id;

                // Auto-advance BYE: if exactly one side is null, auto-win
                if ($r === 1 && ($teamAId === null) !== ($teamBId === null)) {
                    $autoWinner = $teamAId ?? $teamBId;
                    $match->update(['winner_team_id' => $autoWinner, 'status' => 'completed']);
                    if ($nMatchId) {
                        $next = BracketMatch::find($nMatchId);
                        if ($next) {
                            $nIsTeamA
                                ? $next->update(['team_a_id' => $autoWinner])
                                : $next->update(['team_b_id' => $autoWinner]);
                        }
                    }
                }
            }
        }
    }


    public function bulkDelete(Request $request)
{
    $ids = $request->ids;

    if (!is_array($ids) || empty($ids)) {
        return response()->json(['success' => false]);
    }

    Event::whereIn('id', $ids)->delete();

    return response()->json(['success' => true]);
}

}
