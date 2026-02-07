<?php

namespace App\Exports;

use App\Models\Team;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeamsExport implements FromCollection, WithHeadings
{
    protected $eventId;

    public function __construct($eventId = null)
    {
        $this->eventId = $eventId; // null = export all events
    }

    public function collection()
    {
        $query = Team::with(['players.scores', 'event']);

        if ($this->eventId) {
            $query->where('event_id', $this->eventId);
        }

        $teams = $query->get();

        // Calculate total points per team
        $teams = $teams->map(function ($team) {
            $totalPoints = $team->players->sum(function ($player) {
                return $player->scores->sum('points');
            });

            $team->total_points = $totalPoints;
            return $team;
        });

        // Sort by total_points desc and assign rank
        $rank = 1;
        $teams = $teams->sortByDesc('total_points')->map(function ($team) use (&$rank) {
            $team->rank = $rank++;
            return $team;
        });

        // Convert each team into a flat array row
        $rows = collect();
        foreach ($teams as $team) {
            $rows->push([
                'Event Name'     => $team->event?->name ?? 'N/A',
                'Team Name'      => $team->team_name,
                'Members Count'  => $team->players->count(),
                'Players Emails' => $team->players->pluck('email')->implode(', '),
                'Total Points'   => $team->total_points ?? 0,
                'Rank'           => $team->rank ?? 0,
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['Event Name', 'Team Name', 'Members Count', 'Players Emails', 'Total Points', 'Rank'];
    }
}
