<?php

namespace App\Exports;

use App\Models\Score;
use App\Models\Student;
use App\Models\Team;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TeamsExport implements
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
    WithStyles
{
    public function collection()
    {
        // Teams with relations
        $teams = Team::with([
            'subgroup.group',
            'group',
        ])->get();

        // Points map
        $pointsMap = Score::selectRaw('team_id, SUM(points) as total_points')
            ->groupBy('team_id')
            ->pluck('total_points', 'team_id');

        // Members count
        $membersMap = Student::selectRaw('team_id, COUNT(*) as total')
            ->groupBy('team_id')
            ->pluck('total', 'team_id');

        // Build rows
        $rows = $teams->map(function ($team) use ($pointsMap, $membersMap) {

            $totalPoints = $pointsMap[$team->id] ?? 0;

            return [
                'team_id' => $team->id,
                'team_name' => $team->display_name ?? 'N/A',
                'division' => $team->division ?? 'N/A',

                'group' => $team->subgroup
                    ? $team->subgroup->group->group_name
                    : ($team->group->group_name ?? 'N/A'),

                'subgroup' => $team->subgroup->name ?? 'N/A',

                'pod' => $team->subgroup
                    ? $team->subgroup->group->pod
                    : ($team->group->pod ?? 'N/A'),

                'members' => $membersMap[$team->id] ?? 0,

                'total_points' => $totalPoints,
            ];
        });

        // Sort same as frontend
        $rows = $rows->sort(function ($a, $b) {

            if ($b['total_points'] != $a['total_points']) {
                return $b['total_points'] <=> $a['total_points'];
            }

            return strtolower($a['team_name']) <=> strtolower($b['team_name']);
        })->values();

        // Add rank
        $rows = $rows->map(function ($row, $index) {

            $row['rank'] = $index + 1;

            return [
                'Rank' => $row['rank'],
                'Team ID' => $row['team_id'],
                'Team Name' => $row['team_name'],
                'Division' => $row['division'],
                'Group' => $row['group'],
                'Sub Group' => $row['subgroup'],
                'POD' => strtoupper($row['pod']),
                'Members' => $row['members'],
                'Total Points' => $row['total_points'],
            ];
        });

        return new Collection($rows);
    }

    public function headings(): array
    {
        return [
            'Rank',
            'Team ID',
            'Team Name',
            'Division',
            'Group',
            'Sub Group',
            'POD',
            'Members',
            'Total Points',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 13,
                ],
            ],
        ];
    }
}