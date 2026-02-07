<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Score;

class LeaderboardController extends Controller
{
    // Fetch all events for dropdown
    public function events()
    {
        $events = \App\Models\Event::orderByDesc('start_date')->get(['id','name']);
        return response()->json($events);
    }

    // Fetch leaderboard for a specific event
    public function data(Request $request)
    {
        try {
            $eventId = $request->event_id;

            if (!$eventId) {
                return response()->json([], 200);
            }

            // Fetch all teams for this event
            $teams = Team::with(['players' => function($q) use($eventId) {
                $q->where('event_id', $eventId)
                  ->with(['scores' => function($sq) use($eventId) {
                      $sq->where('event_id', $eventId)
                         ->with('challenge');
                  }]);
            }])->where('event_id', $eventId)
              ->get();

            // Calculate scores per team
            $teams = $teams->map(function($team) {
                $brain = 0;
                $play = 0;
                $egame = 0;
                $esports = 0;

                foreach ($team->players as $player) {
                    foreach ($player->scores as $score) {
                        $pillar = $score->challenge?->pillar_type ?? null;
                        $points = $score->points ?? 0;

                        if ($pillar === 'brain') $brain += $points;
                        elseif ($pillar === 'playground') $play += $points;
                        elseif ($pillar === 'egame') $egame += $points;
                        elseif ($pillar === 'esports') $esports += $points;
                    }
                }

                $team->brain   = $brain;
                $team->play    = $play;
                $team->egame   = $egame;
                $team->esports = $esports;
                $team->total   = $brain + $play + $egame + $esports;

                return $team;
            });

            // Rank teams by total points
            $teams = $teams->sortByDesc('total')->values();

            $rank = 1;
            foreach ($teams as $team) {
                $team->rank = $rank++;
            }

            return response()->json($teams);

        } catch (\Throwable $e) {
            // Log if needed: \Log::error($e->getMessage());
            return response()->json([], 200); // never break UI
        }
    }
}
