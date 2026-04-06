<?php

namespace App\Http\Controllers;

use App\Models\ChallengeActivity;
use App\Models\Event;
use App\Models\Group;
use App\Models\Organization;
use App\Models\Score;
use App\Models\SteamCategory;
use App\Models\Student;
use App\Models\SubGroup;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScoreController extends Controller
{
    /* =========================================================
       INDEX
    ========================================================= */
    public function index()
    {
        $events = Event::all();
        return view('scores.index', compact('events'));
    }

    /* =========================================================
       STORE  (Add Score modal)
    ========================================================= */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'challenge_activity_id' => 'required|exists:challenge_activities,id',
            'student_id' => 'nullable|exists:students,id',
            'team_id' => 'nullable|exists:teams,id',
            'points' => 'required|numeric|min:0',
        ]);

        if (!$request->student_id && !$request->team_id) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Please select a student or team.',
                ],
                422,
            );
        }

        $activity = ChallengeActivity::find($request->challenge_activity_id);
        if (!$activity) {
            return response()->json(['success' => false, 'message' => 'Invalid activity.'], 404);
        }

        if ($request->points > $activity->max_score) {
            return response()->json(
                [
                    'success' => false,
                    'message' => "Points cannot exceed max score ({$activity->max_score}).",
                ],
                422,
            );
        }

        DB::beginTransaction();
        try {
            $score = Score::updateOrCreate(
                [
                    'event_id' => $request->event_id,
                    'challenge_activity_id' => $request->challenge_activity_id,
                    'student_id' => $request->student_id,
                    'team_id' => $request->team_id,
                ],
                ['points' => $request->points],
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $score->wasRecentlyCreated ? 'Score assigned successfully.' : 'Score updated successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Score store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save score.'], 500);
        }
    }

    /* =========================================================
       UPDATE BY ACTIVITY ID  (inline + bulk edit)
       POST /scores/update-by-id
    ========================================================= */
    public function updateById(Request $request)
    {
        $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'challenge_activity_id' => 'required|integer|exists:challenge_activities,id',
            'team_id' => 'nullable|integer|exists:teams,id',
            'student_id' => 'nullable|integer|exists:students,id',
            'points' => 'required|numeric|min:0',
        ]);

        if (!$request->team_id && !$request->student_id) {
            return response()->json(['success' => false, 'message' => 'team_id or student_id required.'], 422);
        }

        if (!auth()->check() || auth()->user()->role != 1) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $activity = ChallengeActivity::find($request->challenge_activity_id);
        if (!$activity) {
            return response()->json(['success' => false, 'message' => 'Activity not found.'], 404);
        }

        if ($request->points > $activity->max_score) {
            return response()->json(
                [
                    'success' => false,
                    'message' => "Points cannot exceed max score ({$activity->max_score}) for \"{$activity->display_name}\".",
                ],
                422,
            );
        }

        DB::beginTransaction();
        try {
            Score::updateOrCreate(
                [
                    'event_id' => $request->event_id,
                    'challenge_activity_id' => $request->challenge_activity_id,
                    'team_id' => $request->team_id ?? null,
                    'student_id' => $request->student_id ?? null,
                ],
                ['points' => $request->points],
            );

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Score saved.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('updateById error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save score.'], 500);
        }
    }

    /* =========================================================
       BONUS
       POST /scores/bonus
    ========================================================= */
    public function bonus(Request $request)
    {
        $request->validate([
            'event_id' => 'required|integer|exists:events,id',
            'target_type' => 'required|in:organization,group,subgroup,team,player',
            'bonus_points' => 'required|integer|min:1',
            'organization_id' => 'nullable|integer',
            'group_id' => 'nullable|integer',
            'sub_group_id' => 'nullable|integer',
            'team_id' => 'nullable|integer',
            'student_id' => 'nullable|integer',
        ]);

        if (!auth()->check() || auth()->user()->role != 1) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $eventId = $request->event_id;
        $bonusPts = $request->bonus_points;

        // Resolve team IDs and student IDs in scope
        $teamIds = collect();
        $studentIds = collect();

        switch ($request->target_type) {
            case 'organization':
                $teamIds = Team::whereHas('group', fn($q) => $q->where('organization_id', $request->organization_id))->pluck('id');
                $studentIds = Student::whereIn('team_id', $teamIds)->pluck('id');
                break;
            case 'group':
                $teamIds = Team::where('group_id', $request->group_id)->pluck('id');
                $studentIds = Student::whereIn('team_id', $teamIds)->pluck('id');
                break;
            case 'subgroup':
                $teamIds = Team::where('sub_group_id', $request->sub_group_id)->pluck('id');
                $studentIds = Student::whereIn('team_id', $teamIds)->pluck('id');
                break;
            case 'team':
                $teamIds = collect([$request->team_id]);
                $studentIds = Student::where('team_id', $request->team_id)->pluck('id');
                break;
            case 'player':
                $studentIds = collect([$request->student_id]);
                break;
        }

        DB::beginTransaction();
        try {
            $updated = 0;

            // Add bonus to team scores
            if ($teamIds->isNotEmpty()) {
                $updated += Score::where('event_id', $eventId)->whereIn('team_id', $teamIds)->whereNull('student_id')->increment('bonus_points', $bonusPts);
            }

            // Add bonus to student scores
            if ($studentIds->isNotEmpty()) {
                $updated += Score::where('event_id', $eventId)->whereIn('student_id', $studentIds)->increment('bonus_points', $bonusPts);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$bonusPts} bonus point(s) added to {$updated} score record(s).",
                'updated' => $updated,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Bonus error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to assign bonus.'], 500);
        }
    }





    /* =========================================================
       DROPDOWN HELPERS
    ========================================================= */
   /**
     * Get organizations for an event
     */
    public function getEventOrganizations(Event $event)
    {
        return response()->json(
            $event->organizations()
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
        );
    }
 
    /**
     * Get groups for an organization
     */
    public function getOrganizationGroups($id)
    {
        return response()->json(
            Group::where('organization_id', $id)
                ->select('id', 'group_name')
                ->orderBy('group_name')
                ->get()
        );
    }
 
    /**
     * Get subgroups for a group
     */
    public function getGroupSubgroups($id)
    {
        return response()->json(
            SubGroup::where('group_id', $id)
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
        );
    }
 
    /**
     * Get filtered students by group/subgroup
     * Used when "Player" is selected in "Assign To" dropdown
     */
    public function getFilteredStudents(Request $request)
    {
        $query = Student::query()->select('id', 'name', 'team_id');
 
        if ($request->group_id) {
            $query->whereHas('team', function($q) use ($request) {
                $q->where('group_id', $request->group_id);
            });
        }
        
        if ($request->sub_group_id) {
            $query->whereHas('team', function($q) use ($request) {
                $q->where('sub_group_id', $request->sub_group_id);
            });
        }
 
        return response()->json(
            $query->orderBy('name')->get()
        );
    }
 
    /**
     * Get filtered teams by group/subgroup
     * Used when "Team" is selected in "Assign To" dropdown
     */
    public function getFilteredTeams(Request $request)
    {
        $query = Team::query()->select('id', 'name');
        
        if ($request->group_id) {
            $query->where('group_id', $request->group_id);
        }
        
        if ($request->sub_group_id) {
            $query->where('sub_group_id', $request->sub_group_id);
        }
        
        return response()->json(
            $query->orderBy('name')->get()
        );
    }
 
    /**
     * Get students for a specific team
     * Alternative method if you need to fetch students by team directly
     */
    public function getTeamStudents($teamId)
    {
        return response()->json(
            Student::where('team_id', $teamId)
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
        );
    }
 
    /**
     * Get all activities for an event
     * Used to populate the Activity dropdown
     */
    public function getEventActivities(Event $event)
    {
        return response()->json(
            $event->activities()
                ->select([
                    'id', 
                    'event_id', 
                    'name', 
                    'max_score', 
                    'activity_or_mission', 
                    'activity_type', 
                    'badge_name', 
                    'brain_type', 
                    'brain_description', 
                    'point_structure', 
                    'esports_type', 
                    'esports_players', 
                    'esports_structure', 
                    'esports_description', 
                    'egaming_type', 
                    'egaming_mode', 
                    'egaming_structure', 
                    'egaming_description', 
                    'playground_description', 
                    'created_at', 
                    'updated_at'
                ])
                ->orderBy('id')
                ->get()
        );
    }
 
    /**
     * Get existing score for a student/team + activity combination
     * Pre-fills the points field if a score already exists
     */
    public function getExistingScore(Request $request)
    {
        $query = Score::where('event_id', $request->event_id)
            ->where('challenge_activity_id', $request->challenge_activity_id);
 
        if ($request->student_id) {
            $query->where('student_id', $request->student_id)->whereNull('team_id');
        }
        
        if ($request->team_id) {
            $query->where('team_id', $request->team_id)->whereNull('student_id');
        }
 
        $score = $query->first();
        
        return response()->json([
            'points' => $score ? $score->points : null
        ]);
    }
 
    /**
     * Get Steam categories
     */
    public function getSteamCategories()
    {
        return response()->json(
            SteamCategory::select('id', 'name')
                ->orderBy('id')
                ->get()
        );
    }
    /* =========================================================
       LEADERBOARD EVENTS LIST
    ========================================================= */
    public function events()
    {
        return response()->json(Event::orderByDesc('start_date')->get(['id', 'name']));
    }

    /* =========================================================
       LEADERBOARD DATA
    ========================================================= */
    public function data(Request $request)
    {
        $eventId = $request->event_id;
        if (!$eventId) {
            return response()->json(['categories' => [], 'rows' => []]);
        }

        try {
            $event = Event::with(['organizations.groups.teams.scores.challengeActivity', 'organizations.groups.teams.students.scores.challengeActivity', 'organizations.groups.subgroups.teams.scores.challengeActivity', 'organizations.groups.subgroups.teams.students.scores.challengeActivity'])->findOrFail($eventId);

            // Build activity map: display_name => ['slug'=>..., 'id'=>..., 'max_score'=>...]
            $activityMap = [];

            foreach ($event->organizations as $org) {
                foreach ($org->groups as $group) {
                    $allTeams = $group->teams->merge($group->subgroups->flatMap(fn($sg) => $sg->teams));
                    foreach ($allTeams as $team) {
                        foreach ($team->scores as $score) {
                            if ($score->student_id === null && $score->challengeActivity) {
                                $this->registerActivity($activityMap, $score->challengeActivity);
                            }
                        }
                        foreach ($team->students as $student) {
                            foreach ($student->scores as $score) {
                                if ($score->challengeActivity) {
                                    $this->registerActivity($activityMap, $score->challengeActivity);
                                }
                            }
                        }
                    }
                }
            }

            $categoryNames = array_keys($activityMap);

            // Build blocks
            $blocks = [];
            foreach ($event->organizations as $org) {
                foreach ($org->groups as $group) {
                    foreach ($group->teams as $team) {
                        if ($team->sub_group_id) {
                            continue;
                        }
                        $blocks[] = $this->buildBlock($event, $org, $group, null, $team, $categoryNames);
                    }
                    foreach ($group->subgroups as $subgroup) {
                        foreach ($subgroup->teams as $team) {
                            $blocks[] = $this->buildBlock($event, $org, $group, $subgroup, $team, $categoryNames);
                        }
                    }
                }
            }

            // Rank — only teams with grand_total > 0
            $sorted = collect($blocks)->sortByDesc(fn($b) => $b['team']['grand_total'])->values();
            $rank = 1;
            $prevGT = null;
            $prevRank = 1;
            $rankMap = [];

            foreach ($sorted as $block) {
                $gt = $block['team']['grand_total'];
                if ($gt <= 0) {
                    $rankMap[$block['team']['id']] = null; // no rank for zero
                    continue;
                }
                if ($prevGT !== null && $gt === $prevGT) {
                    $rankMap[$block['team']['id']] = $prevRank;
                } else {
                    $rankMap[$block['team']['id']] = $rank;
                    $prevRank = $rank;
                }
                $prevGT = $gt;
                $rank++;
            }

            foreach ($blocks as &$block) {
                $block['team']['rank'] = $rankMap[$block['team']['id']] ?? null;
            }
            unset($block);

            usort($blocks, fn($a, $b) => $b['team']['grand_total'] <=> $a['team']['grand_total']);

            $rows = [];
            foreach ($blocks as $block) {
                $rows[] = $block['team'];
                foreach ($block['students'] as $s) {
                    $rows[] = $s;
                }
            }

            // Categories payload — include activity id and max_score
            $categories = array_values(
                array_map(
                    fn($name, $meta) => [
                        'name' => $name,
                        'type' => $meta['slug'],
                        'id' => $meta['id'],
                        'max_score' => $meta['max_score'],
                    ],
                    array_keys($activityMap),
                    array_values($activityMap),
                ),
            );

            return response()->json([
                'categories' => $categories,
                'rows' => array_values($rows),
            ]);
        } catch (\Throwable $e) {
            Log::error('Leaderboard data error', ['message' => $e->getMessage()]);
            return response()->json(['categories' => [], 'rows' => []]);
        }
    }

    /* =========================================================
       PRIVATE HELPERS
    ========================================================= */
    private function registerActivity(array &$map, ChallengeActivity $activity): void
    {
        $dn = $activity->display_name;
        if (empty($dn) || array_key_exists($dn, $map)) {
            return;
        }
        $map[$dn] = [
            'slug' => $this->activitySlug($activity),
            'id' => $activity->id,
            'max_score' => $activity->max_score ?? 9999,
        ];
    }

    private function activitySlug(ChallengeActivity $activity): string
    {
        if ($activity->activity_or_mission === 'mission') {
            return 'mission';
        }
        return match ($activity->activity_type) {
            'egaming' => 'egaming',
            'esports' => 'esports',
            'playground' => 'playground',
            'brain' => $this->brainSlug((string) ($activity->brain_type ?? '')),
            default => 'other',
        };
    }

    private function brainSlug(string $brainType): string
    {
        $n = strtolower($brainType);
        if (str_contains($n, 'science')) {
            return 'science';
        }
        if (str_contains($n, 'tech')) {
            return 'technology';
        }
        if (str_contains($n, 'engineer')) {
            return 'engineering';
        }
        if (str_contains($n, 'eng')) {
            return 'engineering';
        }
        if (str_contains($n, 'art')) {
            return 'art';
        }
        if (str_contains($n, 'math')) {
            return 'math';
        }
        return 'other';
    }

    private function buildBlock($event, $org, $group, $subgroup, $team, array $categoryNames): array
    {
        // Team-level scores (student_id IS NULL)
        $teamLookup = [];
        $bonusLookup = [];

        foreach ($team->scores as $score) {
            if ($score->student_id !== null || !$score->challengeActivity) {
                continue;
            }
            $name = $score->challengeActivity->display_name;
            if (!$name) {
                continue;
            }
            $teamLookup[$name] = ($teamLookup[$name] ?? 0) + (int) ($score->points ?? 0);
            $bonusLookup[$name] = ($bonusLookup[$name] ?? 0) + (int) ($score->bonus_points ?? 0);
        }

        $teamScores = [];
        $teamBonus = [];
        $teamPoints = 0;
        $teamBonusTot = 0;

        foreach ($categoryNames as $cat) {
            $pts = $teamLookup[($name = $cat)] ?? 0;
            $bonus = $bonusLookup[$cat] ?? 0;
            $teamScores[$cat] = $pts;
            $teamBonus[$cat] = $bonus;
            $teamPoints += $pts;
            $teamBonusTot += $bonus;
        }

        // Student rows
        $studentRows = [];
        $playerPoints = 0;
        $playerBonus = 0;

        foreach ($team->students as $student) {
            $sLookup = [];
            $sBonusL = [];

            foreach ($student->scores as $score) {
                if (!$score->challengeActivity) {
                    continue;
                }
                $name = $score->challengeActivity->display_name;
                if (!$name) {
                    continue;
                }
                $sLookup[$name] = ($sLookup[$name] ?? 0) + (int) ($score->points ?? 0);
                $sBonusL[$name] = ($sBonusL[$name] ?? 0) + (int) ($score->bonus_points ?? 0);
            }

            $studentScores = [];
            $studentBonus = [];
            $studentTotal = 0;
            $studentBonusT = 0;

            foreach ($categoryNames as $cat) {
                $pts = $sLookup[$cat] ?? 0;
                $bonus = $sBonusL[$cat] ?? 0;
                $studentScores[$cat] = $pts;
                $studentBonus[$cat] = $bonus;
                $studentTotal += $pts;
                $studentBonusT += $bonus;
            }

            $playerPoints += $studentTotal;
            $playerBonus += $studentBonusT;

            $studentRows[] = [
                'type' => 'student',
                'id' => $student->id,
                'event' => $event->name,
                'organization' => $org->name ?? 'N/A',
                'group' => $group->group_name ?? '-',
                'subgroup' => $subgroup->name ?? '-',
                'team_name' => $team->name,
                'division' => $team->division ?? '-',
                'student_name' => $student->name,
                'scores' => $studentScores,
                'bonus' => $studentBonus,
                'total_bonus' => $studentBonusT,
                'total_points' => $studentTotal + $studentBonusT,
                'rank' => null,
            ];
        }

        $grandTotal = $teamPoints + $teamBonusTot + $playerPoints + $playerBonus;

        return [
            'team' => [
                'type' => 'team',
                'id' => $team->id,
                'event' => $event->name,
                'organization' => $org->name ?? 'N/A',
                'group' => $group->group_name ?? '-',
                'subgroup' => $subgroup->name ?? '-',
                'team_name' => $team->name,
                'division' => $team->division ?? '-',
                'student_name' => null,
                'scores' => $teamScores,
                'bonus' => $teamBonus,
                'team_points' => $teamPoints,
                'team_bonus' => $teamBonusTot,
                'player_points' => $playerPoints,
                'player_bonus' => $playerBonus,
                'total_bonus' => $teamBonusTot + $playerBonus,
                'grand_total' => $grandTotal,
                'rank' => null,
            ],
            'students' => $studentRows,
        ];
    }
}
