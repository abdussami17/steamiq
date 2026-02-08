<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Team;
use App\Models\EsportsPoints;

class LeaderboardExport implements FromView
{
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function view(): View
    {
        // Fetch teams with player scores
        $teams = Team::with(['players' => function($q){
            $q->with(['scores' => fn($sq) => $sq->with('challenge')]);
        }])->where('event_id', $this->eventId)->get();

        // Fetch match esports points for this event
        $esportsPoints = EsportsPoints::where('event_id', $this->eventId)
            ->get()
            ->groupBy('team_id');

        $teams = $teams->map(function($team) use ($esportsPoints) {
            $brain = $play = $egame = $esports = 0;

            // Sum player scores
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

            // Add match esports points
            if (isset($esportsPoints[$team->id])) {
                $esports += $esportsPoints[$team->id]->sum('points');
            }

            $team->brain   = $brain;
            $team->play    = $play;
            $team->egame   = $egame;
            $team->esports = $esports;
            $team->total   = $brain + $play + $egame + $esports;

            return $team;
        });

        // Sort by total points
        $teams = $teams->sortByDesc('total')->values();

        // Assign rank
        $rank = 1;
        foreach ($teams as $team) {
            $team->rank = $rank++;
        }

        return view('leaderboard.export', [
            'teams' => $teams
        ]);
    }
}
