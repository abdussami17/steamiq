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
            'points' => 'required|array',
        ]);

        if (!$request->student_id && !$request->team_id) {
            return response()->json(['success'=>false,'message'=>'Please select student or team'],422);
        }

        DB::beginTransaction();
        try {
            foreach($request->points as $steamCategoryId => $pts){
                $pts = (int)$pts;
                if($pts <= 0) continue;

                Score::create([
                    'event_id' => $request->event_id,
                    'challenge_activity_id' => $request->challenge_activity_id,
                    'student_id' => $request->student_id,
                    'team_id' => $request->team_id,
                    'steam_category_id' => $steamCategoryId,
                    'points' => $pts,
                ]);
            }

            DB::commit();
            return response()->json(['success'=>true,'message'=>'Points assigned successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Score store error: '.$e->getMessage());
            return response()->json(['success'=>false,'message'=>'Failed to save points'],500);
        }
    }

    // Fetch students for an event
    public function getEventStudents(Event $event)
    {
        return response()->json($event->students()->select('id','name')->orderBy('name')->get());
    }

    // Fetch teams for an event
    public function getEventTeams(Event $event)
    {
        return response()->json($event->teams()->select('id','team_name as name')->orderBy('team_name')->get());
    }

    // Fetch activities for an event
    public function getEventActivities(Event $event)
    {
        return response()->json($event->challengeactivity()->select('id','name')->orderBy('name')->get());
    }

    // Fetch STEAM categories
    public function getSteamCategories()
    {
        return response()->json(SteamCategory::select('id','name')->orderBy('id')->get());
    }



    public function fetchScores(Request $request)
    {
        $event_id = $request->event_id;
    
        if (!$event_id) {
            return response()->json(['error' => 'Event not selected'], 422);
        }
    
        $teams = Team::with('students')->where('event_id', $event_id)->get();
        $categories = SteamCategory::all();
    
        $table = [];
    
        foreach ($teams as $team) {
    
            // Team row (aggregate)
            $teamScores = Score::where('event_id', $event_id)
                ->where('team_id', $team->id)
                ->whereNull('student_id')
                ->get()
                ->keyBy('steam_category_id');
    
            $row = [
                'type' => 'team',
                'id' => $team->id,
                'name' => $team->team_name,
                'team_name' => $team->team_name,
                'scores' => []
            ];
    
            foreach ($categories as $cat) {
                $row['scores'][$cat->id] = optional($teamScores->get($cat->id))->points ?? 0;
            }
    
            $table[] = $row;
    
            // Student rows
            foreach ($team->students as $student) {
                $studentScores = Score::where('event_id', $event_id)
                    ->where('student_id', $student->id)
                    ->get()
                    ->keyBy('steam_category_id');
    
                $srow = [
                    'type' => 'student',
                    'id' => $student->id,
                    'name' => $student->name,
                    'team_name' => $team->team_name,
                    'team_id' => $team->id,
                    'scores' => []
                ];
    
                foreach ($categories as $cat) {
                    $srow['scores'][$cat->id] = optional($studentScores->get($cat->id))->points ?? 0;
                }
    
                $table[] = $srow;
            }
        }
    
        return response()->json([
            'table' => $table,
            'categories' => $categories
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
}