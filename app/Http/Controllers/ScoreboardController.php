<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organization;
use App\Models\ChallengeActivity;
use App\Models\BonusAssignment;
use App\Models\CardAssignment;
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

        // Eager load teams, students and scores for this division
        $organizations = Organization::where('event_id', $event->id)
            ->with([
                'groups'               => fn($q) => $q->orderBy('id'),
                'groups.teams'         => fn($q) => $q->where('division', $division)->orderBy('id'),
                'groups.teams.students.scores',
                'groups.teams.scores',
            ])
            ->get();

        $rows = [];

        // Collect ids for bulk lookup
        $allTeamIds = [];
        $allStudentIds = [];
        $groupTeamsMap = [];
        $orgTeamsMap = [];
        $studentTeamMap = [];

        foreach ($organizations as $org) {
            $orgTeamsMap[$org->id] = [];
            foreach ($org->groups->sortBy('id') as $group) {
                $teamsInDiv = $group->teams->sortBy('id');
                if ($teamsInDiv->isEmpty()) continue;

                $groupTeamsMap[$group->id] = [];

                foreach ($teamsInDiv as $team) {
                    $allTeamIds[] = $team->id;
                    $orgTeamsMap[$org->id][] = $team->id;
                    $groupTeamsMap[$group->id][] = $team->id;
                    $studentIds = [];
                    foreach ($team->students as $student) {
                        $allStudentIds[] = $student->id;
                        $studentIds[] = $student->id;
                        $studentTeamMap[$student->id] = $team->id;
                    }
                    // store for later
                    $team->__student_ids = $studentIds;
                }
            }
        }

        // Bonus assignments map (team & student)
        $teamBonusMap = [];
        $studentBonusMap = [];
        if (!empty($allTeamIds)) {
            $teamBonusMap = BonusAssignment::where('assignable_type', 'team')
                ->whereIn('assignable_id', $allTeamIds)
                ->selectRaw('assignable_id, SUM(points) as total')
                ->groupBy('assignable_id')
                ->pluck('total', 'assignable_id')
                ->toArray();
        }
        if (!empty($allStudentIds)) {
            $studentBonusMap = BonusAssignment::where('assignable_type', 'student')
                ->whereIn('assignable_id', $allStudentIds)
                ->selectRaw('assignable_id, SUM(points) as total')
                ->groupBy('assignable_id')
                ->pluck('total', 'assignable_id')
                ->toArray();
        }

        // Also include bonus assignments applied at group or organization level
        $groupBonusMap = [];
        $orgBonusMap = [];
        if (!empty($groupTeamsMap)) {
            $groupIds = array_keys($groupTeamsMap);
            $groupBonusMap = BonusAssignment::where('assignable_type', 'group')
                ->whereIn('assignable_id', $groupIds)
                ->selectRaw('assignable_id, SUM(points) as total')
                ->groupBy('assignable_id')
                ->pluck('total', 'assignable_id')
                ->toArray();
        }
        if (!empty($orgTeamsMap)) {
            $orgIds = array_keys($orgTeamsMap);
            $orgBonusMap = BonusAssignment::where('assignable_type', 'organization')
                ->whereIn('assignable_id', $orgIds)
                ->selectRaw('assignable_id, SUM(points) as total')
                ->groupBy('assignable_id')
                ->pluck('total', 'assignable_id')
                ->toArray();
        }

        // Card assignments affecting these teams (team, student, group, organization)
        $cardAssignments = CardAssignment::with('card')
            ->where(function ($q) use ($allTeamIds, $allStudentIds, $groupTeamsMap, $orgTeamsMap) {
                if (!empty($allTeamIds)) {
                    $q->orWhere(function ($q2) use ($allTeamIds) {
                        $q2->where('assignable_type', 'team')->whereIn('assignable_id', $allTeamIds);
                    });
                }
                if (!empty($allStudentIds)) {
                    $q->orWhere(function ($q3) use ($allStudentIds) {
                        $q3->where('assignable_type', 'student')->whereIn('assignable_id', $allStudentIds);
                    });
                }
                if (!empty($groupTeamsMap)) {
                    $groupIds = array_keys($groupTeamsMap);
                    $q->orWhere(function ($q4) use ($groupIds) {
                        $q4->where('assignable_type', 'group')->whereIn('assignable_id', $groupIds);
                    });
                }
                if (!empty($orgTeamsMap)) {
                    $orgIds = array_keys($orgTeamsMap);
                    $q->orWhere(function ($q5) use ($orgIds) {
                        $q5->where('assignable_type', 'organization')->whereIn('assignable_id', $orgIds);
                    });
                }
            })->get();

        // Build team -> cards map
        $teamCardMap = [];
        foreach ($allTeamIds as $tid) $teamCardMap[$tid] = [];

        foreach ($cardAssignments as $ca) {
            $atype = $ca->assignable_type;
            $aid = $ca->assignable_id;
            $cardMeta = $ca->card ? ['assignment_id' => $ca->id, 'card_id' => $ca->card->id, 'type' => $ca->card->type, 'negative_points' => $ca->card->negative_points] : ['assignment_id' => $ca->id, 'card_id' => null, 'type' => 'unknown', 'negative_points' => 0];

            if ($atype === 'team' && isset($teamCardMap[$aid])) {
                $exists = false;
                foreach ($teamCardMap[$aid] as $ex) {
                    if (($ex['assignment_id'] ?? null) === $ca->id) { $exists = true; break; }
                }
                if (!$exists) $teamCardMap[$aid][] = $cardMeta;
            } elseif ($atype === 'student') {
                $tid = $studentTeamMap[$aid] ?? null;
                if ($tid) {
                    $exists = false;
                    foreach ($teamCardMap[$tid] as $ex) {
                        if (($ex['assignment_id'] ?? null) === $ca->id) { $exists = true; break; }
                    }
                    if (!$exists) $teamCardMap[$tid][] = $cardMeta;
                }
            } elseif ($atype === 'group') {
                foreach ($groupTeamsMap[$aid] ?? [] as $tid) {
                    $exists = false;
                    foreach ($teamCardMap[$tid] as $ex) {
                        if (($ex['assignment_id'] ?? null) === $ca->id) { $exists = true; break; }
                    }
                    if (!$exists) $teamCardMap[$tid][] = $cardMeta;
                }
            } elseif ($atype === 'organization') {
                foreach ($orgTeamsMap[$aid] ?? [] as $tid) {
                    $exists = false;
                    foreach ($teamCardMap[$tid] as $ex) {
                        if (($ex['assignment_id'] ?? null) === $ca->id) { $exists = true; break; }
                    }
                    if (!$exists) $teamCardMap[$tid][] = $cardMeta;
                }
            }
        }

        // Now build rows with totals that include bonus points and card counts
        foreach ($organizations as $org) {
            foreach ($org->groups->sortBy('id') as $group) {
                $teamsInDiv = $group->teams->sortBy('id');
                if ($teamsInDiv->isEmpty()) continue;

                foreach ($teamsInDiv as $team) {
                    // Team-level scores (include bonus_points on score records)
                    $scoresByActivity = [];
                    foreach ($team->scores as $s) {
                        if ($activityIds->isNotEmpty() && !in_array($s->challenge_activity_id, $activityIds->toArray())) continue;
                        $aid = $s->challenge_activity_id;
                        $scoresByActivity[$aid] = ($scoresByActivity[$aid] ?? 0) + (int) ($s->points ?? 0) + (int) ($s->bonus_points ?? 0);
                    }

                    $teamPoints = array_sum($scoresByActivity);

                    // Student totals
                    $playerPoints = 0;
                    $playerBonus = 0; // student bonus_points already included per-score, but keep consistent naming
                    foreach ($team->students as $student) {
                        foreach ($student->scores as $ss) {
                            if ($activityIds->isNotEmpty() && !in_array($ss->challenge_activity_id, $activityIds->toArray())) continue;
                            $playerPoints += (int) ($ss->points ?? 0) + (int) ($ss->bonus_points ?? 0);
                        }
                        // BonusAssignment for student
                        $playerBonus += (int) ($studentBonusMap[$student->id] ?? 0);
                    }

                    // BonusAssignment for team — include group & organization level assignments
                    $teamBonusAssignment = (int) ($teamBonusMap[$team->id] ?? 0)
                        + (int) ($groupBonusMap[$group->id] ?? 0)
                        + (int) ($orgBonusMap[$org->id] ?? 0);

                    // Grand total for display
                    $grandTotal = $teamPoints + $playerPoints + $teamBonusAssignment;

                    $cardList = $teamCardMap[$team->id] ?? [];
                    $flagCount = count($cardList);

                    $rows[] = [
                        'team_no'         => $team->id,
                        'team_name'       => $team->display_name,
                        'members'         => $team->students->pluck('name')->implode(', '),
                        'division'        => $team->division,
                        'org_name'        => $org->name,
                        'group_id'        => $group->id,
                        'group_name'      => $group->name ?? ('Group ' . $group->id),
                        'activity_scores' => $scoresByActivity,
                        'total_points'    => $grandTotal,
                        'flag_totals'     => $flagCount,
                        'cards'           => $cardList,
                        'rank'            => null,
                    ];
                }
            }
        }

        if (empty($rows)) return [];

        // Rank by total_points descending
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