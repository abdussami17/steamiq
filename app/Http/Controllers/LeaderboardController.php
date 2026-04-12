<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Score;
use App\Models\Student;
use App\Models\Team;
use App\Models\ChallengeActivity;
use App\Models\BonusAssignment;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{


    public function index(){

return view('leaderboard.index');

    }
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
             * ── COLLECT UNIQUE ACTIVITY DISPLAY NAMES + TYPES ────────────────
             * We store an ordered map: displayName => slug
             * so each category column carries the correct CSS slug from
             * the model's own type fields — not guessed from the string.
             *
             * Order: team-level scores first, then student-level.
             * Deduplication preserves first-seen order.
             */
            $activityMap = []; // [ displayName => ['slug' => ..., 'id' => ...] ]  (insertion-ordered)

            foreach ($event->organizations as $org) {
                foreach ($org->groups as $group) {

                    $allTeams = $group->teams->merge(
                        $group->subgroups->flatMap(fn($sg) => $sg->teams)
                    );

                    foreach ($allTeams as $team) {
                        // Team-level scores (student_id IS NULL)
                        foreach ($team->scores as $score) {
                            if ($score->student_id === null && $score->challengeActivity) {
                                $dn   = $score->challengeActivity->display_name;
                                $slug = $this->activitySlug($score->challengeActivity);
                                if (!empty($dn) && !array_key_exists($dn, $activityMap)) {
                                    $activityMap[$dn] = ['slug' => $slug, 'id' => $score->challengeActivity->id];
                                }
                            }
                        }
                        // Student-level scores
                        foreach ($team->students as $student) {
                            foreach ($student->scores as $score) {
                                if ($score->challengeActivity) {
                                    $dn   = $score->challengeActivity->display_name;
                                    $slug = $this->activitySlug($score->challengeActivity);
                                    if (!empty($dn) && !array_key_exists($dn, $activityMap)) {
                                        $activityMap[$dn] = ['slug' => $slug, 'id' => $score->challengeActivity->id];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // category display names in order (used as score keys in buildBlock)
            $categoryNames = array_keys($activityMap); // keys are still display names

            /*
             * ── BUILD BLOCKS ──────────────────────────────────────────────────
             */
            $blocks = [];

            // Preload bonus assignments for this event and fold group/org bonuses into per-team totals
            $allTeamIds = [];
            $allStudentIds = [];
            $groupTeamsMap = [];
            $orgTeamsMap = [];
            foreach ($event->organizations as $org) {
                $orgTeamsMap[$org->id] = [];
                foreach ($org->groups as $group) {
                    $groupTeamsMap[$group->id] = [];
                    $teams = $group->teams->merge($group->subgroups->flatMap(fn($sg) => $sg->teams));
                    foreach ($teams as $team) {
                        $allTeamIds[] = $team->id;
                        $orgTeamsMap[$org->id][] = $team->id;
                        $groupTeamsMap[$group->id][] = $team->id;
                        foreach ($team->students as $student) {
                            $allStudentIds[] = $student->id;
                        }
                    }
                }
            }

            $teamBonusMapRaw = [];
            $studentBonusMap = [];
            if (!empty($allTeamIds)) {
                $teamBonusMapRaw = BonusAssignment::where('assignable_type', 'team')
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

            // fold into per-team map
            $teamBonusMap = [];
            foreach ($event->organizations as $org) {
                foreach ($org->groups as $group) {
                    $teams = $group->teams->merge($group->subgroups->flatMap(fn($sg) => $sg->teams));
                    foreach ($teams as $team) {
                        $tid = $team->id;
                        $teamBonusMap[$tid] = (int) ($teamBonusMapRaw[$tid] ?? 0)
                            + (int) ($groupBonusMap[$group->id] ?? 0)
                            + (int) ($orgBonusMap[$org->id] ?? 0);
                    }
                }
            }

            foreach ($event->organizations as $org) {
                foreach ($org->groups as $group) {

                    // Direct-group teams (no sub_group_id)
                    foreach ($group->teams as $team) {
                        if ($team->sub_group_id) continue;
                        $blocks[] = $this->buildBlock($event, $org, $group, null, $team, $categoryNames, $teamBonusMap, $studentBonusMap);
                    }

                    // Subgroup teams
                    foreach ($group->subgroups as $subgroup) {
                        foreach ($subgroup->teams as $team) {
                                $blocks[] = $this->buildBlock($event, $org, $group, $subgroup, $team, $categoryNames, $teamBonusMap, $studentBonusMap);
                            }
                    }
                }
            }

            /*
             * ── ASSIGN RANKS by grand_total (tie-aware) ───────────────────────
             */
            $sorted = collect($blocks)
                ->sortByDesc(fn($b) => $b['team']['grand_total'])
                ->values();

            $rank     = 1;
            $prevGT   = null;
            $prevRank = 1;
            $rankMap  = [];

            foreach ($sorted as $block) {
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

            // Inject ranks and then re-sort blocks by grand_total DESC for display
            foreach ($blocks as &$block) {
                $block['team']['rank'] = $rankMap[$block['team']['id']] ?? null;
            }
            unset($block);

            // ── SORT blocks by grand_total descending so the table rows are
            //    always shown in ranking order (rank 1 at top, etc.)
            usort($blocks, fn($a, $b) => $b['team']['grand_total'] <=> $a['team']['grand_total']);

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

            /*
             * ── BUILD categories payload ──────────────────────────────────────
             * Return each category as {name, type} so the frontend can apply
             * the correct colour without guessing from the display-name string.
             */
            $categories = array_values(array_map(
                fn($name, $data) => ['name' => $name, 'type' => $data['slug'], 'id' => $data['id']],
                array_keys($activityMap),
                array_values($activityMap)
            ));

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

    // -------------------------------------------------------------------------
    // Activity slug helper
    // -------------------------------------------------------------------------

    /**
     * Return the CSS slug for a ChallengeActivity, mirroring exactly the same
     * logic as getDisplayNameAttribute() in the model:
     *
     *   activity_or_mission === 'mission'  →  mission
     *   activity_type === 'brain'          →  science / technology / engineering / art / math
     *   activity_type === 'egaming'        →  egaming
     *   activity_type === 'esports'        →  esports
     *   activity_type === 'playground'     →  playground
     *   default                            →  other
     */
    private function activitySlug(ChallengeActivity $activity): string
    {
        if ($activity->activity_or_mission === 'mission') {
            return 'mission';
        }

        return match ($activity->activity_type) {
            'egaming'    => 'egaming',
            'esports'    => 'esports',
            'playground' => 'playground',
            'brain'      => $this->brainSlug((string)($activity->brain_type ?? '')),
            default      => 'other',
        };
    }

    /**
     * Map a brain_type label to a STEAM CSS slug.
     * Falls back to 'other' if the label doesn't match a known category.
     */
    private function brainSlug(string $brainType): string
    {
        $n = strtolower($brainType);

        if (str_contains($n, 'science'))                         return 'science';
        if (str_contains($n, 'tech'))                            return 'technology';
        if (str_contains($n, 'engineer') || str_contains($n, 'eng')) return 'engineering';
        if (str_contains($n, 'art'))                             return 'art';
        if (str_contains($n, 'math'))                            return 'math';

        return 'other';
    }

    // -------------------------------------------------------------------------
    // Build one block: team row + student rows
    // -------------------------------------------------------------------------

    private function buildBlock($event, $org, $group, $subgroup, $team, array $categoryNames, array $teamBonusMap = [], array $studentBonusMap = []): array
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
        foreach ($categoryNames as $cat) {
            $pts = $teamLookup[$cat] ?? 0;
            $teamScores[$cat] = $pts;
            $teamPoints      += $pts;
        }

        // Include any bonus assignments for team and students
        $teamBonusAssignment = (int) ($teamBonusMap[$team->id] ?? 0);

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
            foreach ($categoryNames as $cat) {
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
                'total_points' => $studentTotal + (int) ($studentBonusMap[$student->id] ?? 0),
                'bonus_assignment' => (int) ($studentBonusMap[$student->id] ?? 0),
                'rank'         => null,
            ];
        }

        $grandTotal = $teamPoints + $playerPoints + $teamBonusAssignment;

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
            'bonus_assignment' => $teamBonusAssignment,
            'rank'          => null, // filled after rank computation
        ];

        return [
            'team'     => $teamRow,
            'students' => $studentRows,
        ];
    }

    // -------------------------------------------------------------------------
    // Top-3 helpers (unchanged)
    // -------------------------------------------------------------------------

    public function fetchTopThreeTeams(Request $request)
    {
        $eventId = $request->event_id;

        $teams = Team::with(['subgroup.group', 'group', 'cards.card'])
            ->whereHas('group.organization', fn($q) => $q->where('event_id', $eventId))
            ->get();

        $pointsMap = Score::where('event_id', $eventId)
            ->selectRaw('team_id, SUM(points) as total_points')
            ->groupBy('team_id')
            ->pluck('total_points', 'team_id');

        $membersMap = Student::selectRaw('team_id, COUNT(*) as total')
            ->whereHas('team.group.organization', fn($q) => $q->where('event_id', $eventId))
            ->groupBy('team_id')
            ->pluck('total', 'team_id');

        $rows = $teams->map(function ($team) use ($pointsMap, $membersMap) {
            $assignedCards  = $team->cards ?? collect();
            $negativePoints = $assignedCards->sum(fn($a) => $a->card->negative_points ?? 0);
            $basePoints     = $pointsMap[$team->id] ?? 0;
            $totalPoints    = max(0, $basePoints - $negativePoints);

            return [
                'id'           => $team->id,
                'avatar'       => $team->profile ?? null,
                'name'         => $team->display_name ?? 'N/A',
                'pod'          => $team->subgroup ? $team->subgroup->group->pod : ($team->group->pod ?? 'N/A'),
                'division'     => $team->division ?? 'N/A',
                'total_points' => $totalPoints,
                'rank'         => 0,
            ];
        })->sortByDesc('total_points')->take(3)->values();

        $rows->transform(fn($row, $index) => array_merge($row, ['rank' => $index + 1]));

        return response()->json($rows);
    }

    public function fetchTopThreePlayers(Request $request)
    {
        $eventId = $request->event_id;

        $students = Student::with([
            'team.subgroup.group.organization',
            'scores.challengeActivity',
            'cards',
        ])->whereHas('team.group.organization', fn($q) => $q->where('event_id', $eventId))
          ->get();

        $rows = $students->map(function ($student) {
            $team          = $student->team;
            $totalPoints   = $student->scores->sum(fn($s) => (int)$s->points);
            $totalNegative = optional($student->cards)->sum('negative_points') ?? 0;
            $totalPoints   = max(0, $totalPoints - $totalNegative);

            return [
                'id'           => $student->id,
                'avatar'       => $student->avatar ?? null,
                'name'         => $student->name,
                'team'         => $team->name ?? 'N/A',
                'total_points' => $totalPoints,
                'rank'         => 0,
            ];
        })->sortByDesc('total_points')->take(3)->values();

        $rows->transform(fn($row, $index) => array_merge($row, ['rank' => $index + 1]));

        return response()->json($rows);
    }
}
