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
            // Load event with nested relationships
            $event = Event::with([
                'organization',
                'groups.subgroups.teams.students'
            ])->findOrFail($eventId);
    
            // Map categories from steam_categories table
            $categories = SteamCategory::pluck('name')->toArray();
    
            $rows = [];
    
            foreach ($event->groups as $group) {
                foreach ($group->subgroups as $subgroup) {
                    foreach ($subgroup->teams as $team) {
                        // Fetch team scores
                        $teamScores = Score::where('event_id', $eventId)
                            ->where('team_id', $team->id)
                            ->whereNull('student_id')
                            ->get()
                            ->keyBy('steam_category_id');
    
                        // Prepare team row
                        $teamRow = [
                            'type' => 'team',
                            'id' => $team->id,
                            'event' => $event->name,
                            'organization' => $event->organization->name ?? 'N/A',
                            'group' => $group->group_name ?? '-',
                            'subgroup' => $subgroup->name ?? '-',
                            'team_name' => $team->team_name,
                            'student_name' => null,
                            'scores' => [],
                            'total_points' => 0
                        ];
    
                        foreach ($categories as $catName) {
                            $steamCat = SteamCategory::where('name', $catName)->first();
                            $points = optional($teamScores->get($steamCat->id))->points ?? 0;
                            $teamRow['scores'][$catName] = $points;
                            $teamRow['total_points'] += $points;
                        }
    
                        $rows[] = $teamRow;
    
                        // Students
                        foreach ($team->students as $student) {
                            $studentScores = Score::where('event_id', $eventId)
                                ->where('student_id', $student->id)
                                ->get()
                                ->keyBy('steam_category_id');
    
                            $studentRow = [
                                'type' => 'student',
                                'id' => $student->id,
                                'event' => $event->name,
                                'organization' => $event->organization->name ?? 'N/A',
                                'group' => $group->group_name ?? '-',
                                'subgroup' => $subgroup->name ?? '-',
                                'team_name' => $team->team_name,
                                'student_name' => $student->name,
                                'scores' => [],
                                'total_points' => 0
                            ];
    
                            foreach ($categories as $catName) {
                                $steamCat = SteamCategory::where('name', $catName)->first();
                                $points = optional($studentScores->get($steamCat->id))->points ?? 0;
                                $studentRow['scores'][$catName] = $points;
                                $studentRow['total_points'] += $points;
                            }
    
                            $rows[] = $studentRow;
                        }
                    }
                }
            }
    
            // Sort by total points descending
            $rows = collect($rows)->sortByDesc('total_points')->values();
    
            // Assign proper ranks
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
                'categories' => $categories,
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