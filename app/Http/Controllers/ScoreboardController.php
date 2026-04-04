<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organization;
use App\Models\ChallengeActivity;
use Illuminate\Http\Request;

class ScoreboardController extends Controller
{
    public function index(Request $request)
    {
        $events          = Event::orderBy('start_date', 'desc')->get();
        $selectedEventId = $request->get('event_id', $events->first()?->id);
        $selectedEvent   = $events->firstWhere('id', $selectedEventId);

        $primaryData = [];
        $juniorData  = [];
        $activities  = collect();

        if ($selectedEvent) {
            $activities  = ChallengeActivity::where('event_id', $selectedEvent->id)->get();
            $primaryData = $this->buildDivisionData($selectedEvent, 'Primary', $activities);
            $juniorData  = $this->buildDivisionData($selectedEvent, 'Junior',  $activities);
        }

        return view('scoreboard.index', compact(
            'events', 'selectedEvent', 'selectedEventId',
            'primaryData', 'juniorData', 'activities'
        ));
    }

    public function getData(Request $request)
    {
        $event = Event::find($request->get('event_id'));
        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        $activities  = ChallengeActivity::where('event_id', $event->id)->get();
        $primaryData = $this->buildDivisionData($event, 'Primary', $activities);
        $juniorData  = $this->buildDivisionData($event, 'Junior',  $activities);

        return response()->json([
            'event'      => ['id' => $event->id, 'name' => $event->name, 'type' => $event->type],
            'activities' => $activities->map(fn($a) => ['id' => $a->id, 'name' => $a->display_name]),
            'primary'    => $primaryData,
            'junior'     => $juniorData,
        ]);
    }

    private function buildDivisionData(Event $event, string $division, $activities): array
    {
        $activityIds = $activities->pluck('id');

        $organizations = Organization::where('event_id', $event->id)
            ->with([
                'groups'               => fn($q) => $q->orderBy('id'),
                'groups.teams'         => fn($q) => $q->where('division', $division)->orderBy('id'),
                'groups.teams.students',
                'groups.teams.scores',
            ])
            ->get();

        $rows = [];

        foreach ($organizations as $org) {
            foreach ($org->groups->sortBy('id') as $group) {
                $teamsInDiv = $group->teams->sortBy('id');
                if ($teamsInDiv->isEmpty()) continue;

                foreach ($teamsInDiv as $team) {
                    $scoresByActivity = $team->scores
                        ->whereIn('challenge_activity_id', $activityIds)
                        ->groupBy('challenge_activity_id')
                        ->map(fn($s) => $s->sum('points'));

                    $rows[] = [
                        'team_no'         => $team->id,
                        'team_name'       => $team->display_name,
                        'members'         => $team->students->pluck('name')->implode(', '),
                        'division'        => $team->division,
                        'org_name'        => $org->name,
                        'group_id'        => $group->id,
                        'group_name'      => $group->name ?? ('Group ' . $group->id),
                        'activity_scores' => $scoresByActivity->toArray(),
                        'total_points'    => $scoresByActivity->sum(),
                        'flag_totals'     => 0,
                        'rank'            => null,
                    ];
                }
            }
        }

        if (empty($rows)) return [];

        // Rank by points descending
        $ranked = collect($rows)
            ->sortByDesc('total_points')
            ->values()
            ->map(function ($row, $i) {
                $row['rank'] = $i + 1;
                return $row;
            });

        // Display: rank ASC (rank 1 at top), ties by group then team_no
        return $ranked
            ->sortBy([['rank', 'asc'], ['group_id', 'asc'], ['team_no', 'asc']])
            ->values()
            ->toArray();
    }
}