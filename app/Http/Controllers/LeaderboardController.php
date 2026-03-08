<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Score;
use App\Models\SteamCategory;
use App\Models\Team;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    // Fetch all events for dropdown
    public function events()
    {
        $events = Event::orderByDesc('start_date')->get(['id','name']);
        return response()->json($events);
    }

    public function data(Request $request)
    {
        $eventId = $request->event_id;
        if (!$eventId) return response()->json([]);
    
        try {
            // Load event with full hierarchy
            $event = Event::with([
                'organizations.groups.subgroups.teams.students.scores.challengeActivity'
            ])->findOrFail($eventId);
    
            // Map categories once
            $categories = SteamCategory::pluck('name', 'id')->toArray(); // [id => name]
    
            $rows = [];
    
            foreach ($event->organizations as $org) {
                foreach ($org->groups as $group) {
                    foreach ($group->subgroups as $subgroup) {
                        foreach ($subgroup->teams as $team) {
    
                            // TEAM ROW (aggregate)
                            $teamRow = [
                                'type' => 'team',
                                'id' => $team->id,
                                'event' => $event->name,
                                'organization' => $org->name ?? 'N/A',
                                'group' => $group->group_name ?? '-',
                                'subgroup' => $subgroup->name ?? '-',
                                'team_name' => $team->name,
                                'student_name' => null,
                                'scores' => [],
                                'total_points' => 0
                            ];
    
                            // Calculate team scores (sum of students or team-only)
                            foreach ($categories as $catId => $catName) {
                                $points = optional($team->scores->whereNull('student_id')->where('steam_category_id', $catId)->first())->points ?? 0;
                                $teamRow['scores'][$catName] = $points;
                                $teamRow['total_points'] += $points;
                            }
    
                            $rows[] = $teamRow;
    
                            // STUDENT ROWS
                            foreach ($team->students as $student) {
    
                                $studentRow = [
                                    'type' => 'student',
                                    'id' => $student->id,
                                    'event' => $event->name,
                                    'organization' => $org->name ?? 'N/A',
                                    'group' => $group->group_name ?? '-',
                                    'subgroup' => $subgroup->name ?? '-',
                                    'team_name' => $team->name,
                                    'student_name' => $student->name,
                                    'scores' => [],
                                    'total_points' => 0
                                ];
    
                                foreach ($categories as $catId => $catName) {
                                    $points = optional($student->scores->where('steam_category_id', $catId)->first())->points ?? 0;
                                    $studentRow['scores'][$catName] = $points;
                                    $studentRow['total_points'] += $points;
                                }
    
                                $rows[] = $studentRow;
                            }
                        }
                    }
                }
            }
    
            // Sort by total points descending
            $rows = collect($rows)->sortByDesc('total_points')->values();
    
            // Assign ranks properly (tie handling)
            $rank = 1;
            $previousPoints = null;
            foreach ($rows as $index => $row) {
                if ($previousPoints !== null && $row['total_points'] == $previousPoints) {
                    $row['rank'] = $rows[$index - 1]['rank']; // tie
                } else {
                    $row['rank'] = $rank;
                }
                $previousPoints = $row['total_points'];
                $rank++;
                $rows[$index] = $row;
            }
    
            return response()->json([
                'categories' => array_values($categories), // return names
                'rows' => $rows
            ]);
    
        } catch (\Throwable $e) {
            \Log::error('Leaderboard fetch error', [
                'event_id' => $eventId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([]);
        }
    }
}