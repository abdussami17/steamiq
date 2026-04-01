<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function events()
    {
        $events = Event::orderByDesc('start_date')->get(['id', 'name']);
        return response()->json($events);
    }

    public function data(Request $request)
    {
        $eventId = $request->event_id;
        if (!$eventId) return response()->json(['categories' => [], 'rows' => []]);

        try {
            /*
             * ── EAGER LOAD ────────────────────────────────────────────────────
             */
            $event = Event::with([
                'organizations.groups.teams.scores.challengeActivity',
                'organizations.groups.teams.students.scores.challengeActivity',
                'organizations.groups.subgroups.teams.scores.challengeActivity',
                'organizations.groups.subgroups.teams.students.scores.challengeActivity',
            ])->findOrFail($eventId);

            /*
             * ── COLLECT UNIQUE ACTIVITY DISPLAY NAMES ────────────────────────
             * Order: team-level scores first, then student-level.
             * Deduplication preserves first-seen order.
             */
            $activityNames = collect();

            foreach ($event->organizations as $org) {
                foreach ($org->groups as $group) {

                    $allTeams = $group->teams->merge(
                        $group->subgroups->flatMap(fn($sg) => $sg->teams)
                    );

                    foreach ($allTeams as $team) {
                        foreach ($team->scores as $score) {
                            if ($score->student_id === null && $score->challengeActivity) {
                                $activityNames->push($score->challengeActivity->display_name);
                            }
                        }
                        foreach ($team->students as $student) {
                            foreach ($student->scores as $score) {
                                if ($score->challengeActivity) {
                                    $activityNames->push($score->challengeActivity->display_name);
                                }
                            }
                        }
                    }
                }
            }

            $categories = $activityNames->filter(fn($v) => !empty($v))
                ->unique()
                ->values()
                ->toArray();

            /*
             * ── BUILD BLOCKS ──────────────────────────────────────────────────
             *
             * PRIMARY SORT: group → team insertion order (natural hierarchy).
             * RANK is assigned by grand_total DESC (tie-aware) AFTER blocks
             * are collected, but the ROW ORDER stays group → team.
             */
            $blocks = [];

            foreach ($event->organizations as $org) {
                foreach ($org->groups as $group) {

                    // Direct-group teams (no sub_group_id)
                    foreach ($group->teams as $team) {
                        if ($team->sub_group_id) continue;
                        $blocks[] = $this->buildBlock($event, $org, $group, null, $team, $categories);
                    }

                    // Subgroup teams
                    foreach ($group->subgroups as $subgroup) {
                        foreach ($subgroup->teams as $team) {
                            $blocks[] = $this->buildBlock($event, $org, $group, $subgroup, $team, $categories);
                        }
                    }
                }
            }

            /*
             * ── ASSIGN RANKS by grand_total (tie-aware) ───────────────────────
             * Ranking is separate from display order.
             * We compute ranks then inject them back into blocks.
             */
            $sorted = collect($blocks)
                ->sortByDesc(fn($b) => $b['team']['grand_total'])
                ->values();

            $rank     = 1;
            $prevGT   = null;
            $prevRank = 1;
            $rankMap  = []; // team_id -> rank

            foreach ($sorted as $idx => $block) {
                $gt = $block['team']['grand_total'];
                if ($prevGT !== null && $gt === $prevGT) {
                    $rankMap[$block['team']['id']] = $prevRank;
                } else {
                    $rankMap[$block['team']['id']] = $rank;
                    $prevRank = $rank;
                }
                $prevGT = $gt;
                $rank++;
            }

            // Inject ranks back (blocks remain in group→team order)
            foreach ($blocks as &$block) {
                $block['team']['rank'] = $rankMap[$block['team']['id']] ?? null;
            }
            unset($block);

            /*
             * ── FLATTEN to rows array ─────────────────────────────────────────
             */
            $rows = [];
            foreach ($blocks as $block) {
                $rows[] = $block['team'];
                foreach ($block['students'] as $s) {
                    $rows[] = $s;
                }
            }

            return response()->json([
                'categories' => $categories,
                'rows'       => array_values($rows),
            ]);

        } catch (\Throwable $e) {
            \Log::error('Leaderboard fetch error', [
                'event_id' => $eventId,
                'message'  => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);
            return response()->json(['categories' => [], 'rows' => []]);
        }
    }

    /**
     * Build one block: team row + student rows.
     */
    private function buildBlock($event, $org, $group, $subgroup, $team, $categories): array
    {
        // Team-level score lookup: display_name → summed points (student_id IS NULL)
        $teamLookup = [];
        foreach ($team->scores as $score) {
            if ($score->student_id !== null) continue;
            if (!$score->challengeActivity) continue;
            $name = $score->challengeActivity->display_name;
            if (!$name) continue;
            $teamLookup[$name] = ($teamLookup[$name] ?? 0) + (int)($score->points ?? 0);
        }

        $teamScores = [];
        $teamPoints = 0;
        foreach ($categories as $cat) {
            $pts = $teamLookup[$cat] ?? 0;
            $teamScores[$cat] = $pts;
            $teamPoints      += $pts;
        }

        // Student rows
        $studentRows  = [];
        $playerPoints = 0;

        foreach ($team->students as $student) {

            $studentLookup = [];
            foreach ($student->scores as $score) {
                if (!$score->challengeActivity) continue;
                $name = $score->challengeActivity->display_name;
                if (!$name) continue;
                $studentLookup[$name] = ($studentLookup[$name] ?? 0) + (int)($score->points ?? 0);
            }

            $studentScores = [];
            $studentTotal  = 0;
            foreach ($categories as $cat) {
                $pts = $studentLookup[$cat] ?? 0;
                $studentScores[$cat] = $pts;
                $studentTotal       += $pts;
            }

            $playerPoints += $studentTotal;

            $studentRows[] = [
                'type'         => 'student',
                'id'           => $student->id,
                'event'        => $event->name,
                'organization' => $org->name ?? 'N/A',
                'group'        => $group->group_name ?? '-',
                'subgroup'     => $subgroup->name ?? '-',
                'team_name'    => $team->name,
                'division'     => $team->division ?? '-',
                'student_name' => $student->name,
                'scores'       => $studentScores,
                'total_points' => $studentTotal,
                'rank'         => null,
            ];
        }

        $grandTotal = $teamPoints + $playerPoints;

        $teamRow = [
            'type'          => 'team',
            'id'            => $team->id,
            'event'         => $event->name,
            'organization'  => $org->name ?? 'N/A',
            'group'         => $group->group_name ?? '-',
            'subgroup'      => $subgroup->name ?? '-',
            'team_name'     => $team->name,
            'division'      => $team->division ?? '-',
            'student_name'  => null,
            'scores'        => $teamScores,
            'team_points'   => $teamPoints,
            'player_points' => $playerPoints,
            'total_points'  => $grandTotal,
            'grand_total'   => $grandTotal,
            'rank'          => null, // filled after rank computation above
        ];

        return [
            'team'     => $teamRow,
            'students' => $studentRows,
        ];
    }
}