<?php

namespace App\Exports;

use App\Models\Team;
use App\Models\Score;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeamsExport implements FromCollection, WithHeadings
{
    protected $eventId;

    public function __construct($eventId = null)
    {
        $this->eventId = $eventId;
    }

    public function collection()
    {
        // =============================
        // 1) Base query (same as table)
        // =============================
        $query = Team::with(['subgroup']);

        if ($this->eventId) {
            $query->where('event_id', $this->eventId);
        }

        $teams = $query->get();

        // =============================
        // 2) Points sum from scores table
        // =============================
        $pointsMap = Score::selectRaw('team_id, SUM(points) as total_points')
            ->groupBy('team_id')
            ->pluck('total_points', 'team_id');

        // =============================
        // 3) Students count
        // =============================
        $membersMap = Student::selectRaw('team_id, COUNT(*) as total')
            ->groupBy('team_id')
            ->pluck('total', 'team_id');

        // =============================
        // 4) Build rows
        // =============================
        $rows = $teams->map(function ($team) use ($pointsMap, $membersMap) {
            return [
                'id' => $team->id,
                'team_name' => $team->team_name ?? 'N/A',
                'subgroup' => $team->subgroup->name ?? 'N/A',
                'members' => $membersMap[$team->id] ?? 0,
                'points' => $pointsMap[$team->id] ?? 0,
                'profile' => $team->profile ?? 'N/A',
            ];
        });

        // =============================
        // 5) Sort DESC
        // =============================
        $rows = $rows->sortByDesc('points')->values();

        // =============================
        // 6) Rank
        // =============================
        $rows = $rows->map(function ($row, $index) {
            $row['rank'] = $index + 1;
            return $row;
        });

        // =============================
        // 7) Format for Excel
        // =============================
        return $rows->map(function ($row) {
            return [
                $row['profile'] ?? 'N/A',
                $row['id'],
                $row['team_name'],
                $row['subgroup'],
                $row['members'],
                $row['points'],
                $row['rank'],
            ];
        });
    }

    // =============================
    // SAME HEADINGS AS TABLE
    // =============================
    public function headings(): array
    {
        return [
            'Avatar',
            'Team ID',
            'Team Name',
            'Sub Group',
            'Members',
            'Total Points',
            'Rank',
        ];
    }
}