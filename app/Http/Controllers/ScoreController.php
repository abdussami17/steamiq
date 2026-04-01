<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Score;
use App\Models\Student;
use App\Models\Team;
use App\Models\ChallengeActivity;
use App\Models\SteamCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScoreController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'challenge_activity_id' => 'required|exists:challenge_activities,id',
            'student_id' => 'required_without:team_id|nullable|exists:students,id',
            'team_id' => 'required_without:student_id|nullable|exists:teams,id',
            'points' => 'required|numeric|min:1',
        ]);
    
        if (!$request->student_id && !$request->team_id) {
            return response()->json([
                'success'=>false,
                'message'=>'Please select student or team'
            ],422);
        }
    
        $activity = ChallengeActivity::find($request->challenge_activity_id);
    
        if (!$activity) {
            return response()->json([
                'success'=>false,
                'message'=>'Invalid activity'
            ],404);
        }
  
        if ($request->points > $activity->max_score) {
            return response()->json([
                'success'=>false,
                'message'=>"Points cannot exceed max score ({$activity->max_score})"
            ],422);
        }
    
        DB::beginTransaction();
    
        try {
    
            
            $existingScore = Score::where('event_id', $request->event_id)
                ->where('challenge_activity_id', $request->challenge_activity_id)
                ->when($request->student_id, fn($q)=>$q->where('student_id', $request->student_id))
                ->when($request->team_id, fn($q)=>$q->where('team_id', $request->team_id))
                ->first();
    
            if ($existingScore) {
    
             
                $existingScore->update([
                    'points' => $request->points
                ]);
    
                $type = 'update';
    
            } else {
    
              
                Score::create([
                    'event_id' => $request->event_id,
                    'challenge_activity_id' => $request->challenge_activity_id,
                    'student_id' => $request->student_id,
                    'team_id' => $request->team_id,
                    'points' => $request->points,
                ]);
    
                $type = 'create';
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'type' => $type, 
                'message' => $type === 'update'
                    ? 'Score updated successfully'
                    : 'Score assigned successfully'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Score store error: '.$e->getMessage());
    
            return response()->json([
                'success'=>false,
                'message'=>'Failed to save points'
            ],500);
        }
    }
    public function getExistingScore(Request $request)
    {
        
    
        $query = Score::where('event_id', $request->event_id)
            ->where('challenge_activity_id', $request->challenge_activity_id);
    
        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }
    
        if ($request->team_id) {
            $query->where('team_id', $request->team_id);
        }
    
      
    
        $score = $query->first();
    
     
    
        return response()->json([
            'points' => $score ? $score->points : null
        ]);
    }

    public function getEventStudents(Event $event)
    {
        $students = $event->organizations()
            ->with([
                'groups.teams.students',      // Teams directly under group
                'groups.subgroups.teams.students' // Teams under subgroups
            ])
            ->get()
            ->flatMap(function ($org) {
                return $org->groups->flatMap(function ($group) {
                    $directTeams    = $group->teams->flatMap->students;       // direct teams
                    $subgroupTeams  = $group->subgroups->flatMap->teams->flatMap->students; // subgroup teams
                    return $directTeams->concat($subgroupTeams);
                });
            })
            ->unique('id') // remove duplicates if any
            ->map(fn($student) => [
                'id' => $student->id,
                'name' => $student->name
            ])
            ->sortBy('name')
            ->values();
    
        return response()->json($students);
    }
    
    public function getEventTeams(Event $event)
    {
        $teams = $event->organizations()
            ->with([
                'groups.teams',      // Teams directly under group
                'groups.subgroups.teams' // Teams under subgroups
            ])
            ->get()
            ->flatMap(function ($org) {
                return $org->groups->flatMap(function ($group) {
                    $directTeams    = $group->teams;                         // direct teams
                    $subgroupTeams  = $group->subgroups->flatMap->teams;     // subgroup teams
                    return $directTeams->concat($subgroupTeams);
                });
            })
            ->unique('id') // remove duplicates
            ->map(fn($team) => [
                'id' => $team->id,
                'name' => $team->name
            ])
            ->values();
    
        return response()->json($teams);
    }
    // Fetch activities for an event
    public function getEventActivities(Event $event)
    {
        // fetch all relevant fields
        $activities = $event->activities()
            ->select('id', 'name', 'brain_type', 'esports_type', 'egaming_type', 'badge_name')
            ->orderBy('id')
            ->get();
    
        return response()->json($activities);
    }

    // Fetch STEAM categories
    public function getSteamCategories()
    {
        return response()->json(SteamCategory::select('id','name')->orderBy('id')->get());
    }


    public function getEventOrganizations(Event $event)
    {
        return response()->json(
            $event->organizations()->select('id','name')->get()
        );
    }
    
    public function getOrganizationGroups($id)
    {
        return response()->json(
            \App\Models\Group::where('organization_id',$id)->select('id','group_name')->get()
        );
    }
    
    public function getGroupSubgroups($id)
    {
        return response()->json(
            \App\Models\SubGroup::where('group_id',$id)->select('id','name')->get()
        );
    }
    public function getFilteredStudents(Request $request)
{
    $query = Student::query();

    $query->whereHas('team.group.organization', function ($q) use ($request) {
        if ($request->event_id) $q->where('event_id', $request->event_id);
    });

    if ($request->group_id) {
        $query->whereHas('team', fn($q) => $q->where('group_id', $request->group_id));
    }

    if ($request->sub_group_id) {
        $query->whereHas('team', fn($q) => $q->where('sub_group_id', $request->sub_group_id));
    }

    return response()->json(
        $query->select('id','name')->get()
    );
}
public function getFilteredTeams(Request $request)
{
    $query = Team::query();

    if ($request->group_id) {
        $query->where('group_id', $request->group_id);
    }

    if ($request->sub_group_id) {
        $query->where('sub_group_id', $request->sub_group_id);
    }

    return response()->json(
        $query->select('id','name')->get()
    );
}
public function fetchScores(Request $request)
{
    $event_id = $request->event_id;

    if (!$event_id) {
        return response()->json(['error' => 'Event not selected'], 422);
    }

    $event = Event::with('organizations.groups.teams.students')->find($event_id);

    if (!$event) {
        return response()->json(['error' => 'Event not found'], 404);
    }

    $table = [];

    foreach ($event->organizations as $org) {
        foreach ($org->groups as $group) {

            foreach ($group->teams as $team) {

                // Fetch team scores with challengeActivity
                $teamScores = Score::with('challengeActivity')
                    ->where('event_id', $event_id)
                    ->where('team_id', $team->id)
                    ->whereNull('student_id')
                    ->get();

                foreach ($teamScores as $score) {
                    $table[] = [
                        'type'     => 'team',
                        'id'       => $score->team_id,
                        'name'     => $team->name,
                        'activity' => $score->challengeActivity->display_name ?? 'N/A',
                        'total'    => $score->points,
                    ];
                }

                foreach ($team->students as $student) {

                    // Fetch student scores with challengeActivity
                    $studentScores = Score::with('challengeActivity')
                        ->where('event_id', $event_id)
                        ->where('student_id', $student->id)
                        ->get();

                    foreach ($studentScores as $score) {
                        $table[] = [
                            'type'     => 'student',
                            'id'       => $score->student_id,
                            'name'     => $student->name,
                            'activity' => $score->challengeActivity->display_name ?? 'N/A',
                            'total'    => $score->points,
                        ];
                    }
                }
            }
        }
    }

    // Sort by total points descending and assign rank
    $table = collect($table)
        ->sortByDesc('total')
        ->values()
        ->map(function ($row, $index) {
            $row['rank'] = $index + 1;
            return $row;
        });

    return response()->json([
        'table' => $table
    ]);
}
    public function updateScore(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|integer',
                'team_id' => 'nullable|integer',
                'student_id' => 'nullable|integer',
                'category_id' => 'required|integer',
                'points' => 'required|numeric|min:0|max:1200' // Changed max to 1200
            ]);

            // Find or create the score
            $score = Score::updateOrCreate(
                [
                    'event_id' => $validated['event_id'],
                    'team_id' => $validated['team_id'],
                    'student_id' => $validated['student_id'],
                    'steam_category_id' => $validated['category_id']
                ],
                [
                    'points' => $validated['points']
                ]
            );

            // Calculate new totals
            $total = $this->calculateTotal($validated['event_id'], $validated['team_id'], $validated['student_id']);

            return response()->json([
                'success' => true,
                'message' => 'Score updated successfully',
                'total' => $total
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating score: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkUpdate(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|integer',
                'updates' => 'required|array',
                'updates.*.team_id' => 'nullable|integer',
                'updates.*.student_id' => 'nullable|integer',
                'updates.*.category_id' => 'required|integer',
                'updates.*.points' => 'required|numeric|min:0|max:1200' // Changed max to 1200
            ]);

            $updatedTotals = [];

            foreach ($validated['updates'] as $update) {
                $score = Score::updateOrCreate(
                    [
                        'event_id' => $validated['event_id'],
                        'team_id' => $update['team_id'],
                        'student_id' => $update['student_id'],
                        'steam_category_id' => $update['category_id']
                    ],
                    [
                        'points' => $update['points']
                    ]
                );

                // Track updated totals
                $key = ($update['student_id'] ? 'student_' : 'team_') . ($update['student_id'] ?? $update['team_id']);
                $updatedTotals[$key] = $this->calculateTotal(
                    $validated['event_id'], 
                    $update['team_id'], 
                    $update['student_id']
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Bulk update completed successfully',
                'totals' => $updatedTotals
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error in bulk update: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateTotal($event_id, $team_id, $student_id = null)
    {
        $categories = SteamCategory::all();
        $total = 0;

        foreach ($categories as $category) {
            $score = Score::where('event_id', $event_id)
                ->where('steam_category_id', $category->id)
                ->when($student_id, function($query) use ($student_id) {
                    return $query->where('student_id', $student_id);
                }, function($query) use ($team_id) {
                    return $query->where('team_id', $team_id)->whereNull('student_id');
                })
                ->first();
            
            $total += $score ? $score->points : 0;
        }

        return $total;
    }


    /*
|--------------------------------------------------------------------------
| ADD THIS METHOD to App\Http\Controllers\ScoreController
|--------------------------------------------------------------------------
| This endpoint is called by the leaderboard inline-edit feature.
| It looks up a ChallengeActivity by its display_name for the given event,
| then upserts the score for the given team or student.
|
| Route (add to web.php inside the admin middleware group):
|   Route::post('/scores/update-by-name', [ScoreController::class, 'updateScoreByName']);
*/
 
    /**
     * Update (or create) a score identified by the activity's display_name.
     *
     * Called by the leaderboard inline-edit cell.
     *
     * POST /scores/update-by-name
     * {
     *   event_id:              int,
     *   activity_display_name: string,   ← matches getDisplayNameAttribute()
     *   team_id:               int|null,
     *   student_id:            int|null,
     *   points:                numeric,
     * }
     */
    public function updateScoreByName(Request $request)
    {
        $request->validate([
            'event_id'              => 'required|integer|exists:events,id',
            'activity_display_name' => 'required|string',
            'team_id'               => 'nullable|integer|exists:teams,id',
            'student_id'            => 'nullable|integer|exists:students,id',
            'points'                => 'required|numeric|min:0',
        ]);
 
        if (!$request->team_id && !$request->student_id) {
            return response()->json([
                'success' => false,
                'message' => 'A team_id or student_id is required.',
            ], 422);
        }
 
        /*
         * Resolve the ChallengeActivity whose display_name matches.
         *
         * getDisplayNameAttribute() returns one of:
         *   badge_name          (when activity_or_mission === 'mission')
         *   brain_type          (when activity_type === 'brain')
         *   egaming_type        (when activity_type === 'egaming')
         *   esports_type        (when activity_type === 'esports')
         *   'Playground'        (when activity_type === 'playground')
         *   name                (fallback)
         *
         * We cannot filter by computed attribute in SQL, so we load all
         * activities for the event and match in PHP.
         */
        $displayName = $request->activity_display_name;
 
        $activity = \App\Models\ChallengeActivity::where('event_id', $request->event_id)
            ->get()
            ->first(fn($a) => $a->display_name === $displayName);
 
        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => "Activity \"{$displayName}\" not found for this event.",
            ], 404);
        }
 
        /* Validate points don't exceed max_score */
        if ($request->points > $activity->max_score) {
            return response()->json([
                'success' => false,
                'message' => "Points cannot exceed max score ({$activity->max_score}) for this activity.",
            ], 422);
        }
 
        \DB::beginTransaction();
 
        try {
            \App\Models\Score::updateOrCreate(
                [
                    'event_id'             => $request->event_id,
                    'challenge_activity_id'=> $activity->id,
                    'team_id'              => $request->team_id,
                    'student_id'           => $request->student_id,
                ],
                [
                    'points' => $request->points,
                ]
            );
 
            \DB::commit();
 
            return response()->json([
                'success' => true,
                'message' => 'Score updated successfully.',
            ]);
 
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('updateScoreByName error: ' . $e->getMessage());
 
            return response()->json([
                'success' => false,
                'message' => 'Failed to save score.',
            ], 500);
        }
    }
}