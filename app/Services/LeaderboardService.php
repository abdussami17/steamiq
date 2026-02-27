<?php 

namespace App\Services;

use App\Models\Event;
use App\Models\Score;
use App\Models\SteamCategory;

class LeaderboardService
{
    public function build($eventId)
    {
        $event = Event::with([
            'organization',
            'groups.subgroups.teams.students'
        ])->findOrFail($eventId);

        $categories = SteamCategory::all();

        $rows = [];

        foreach ($event->groups as $group) {
            foreach ($group->subgroups as $subgroup) {
                foreach ($subgroup->teams as $team) {

                    $teamScores = Score::where('event_id', $eventId)
                        ->where('team_id', $team->id)
                        ->whereNull('student_id')
                        ->get()
                        ->keyBy('steam_category_id');

                    $rows[] = $this->makeRow(
                        'team',
                        $event,
                        $group,
                        $subgroup,
                        $team->team_name,
                        null,
                        $team->id,
                        $teamScores,
                        $categories
                    );

                    foreach ($team->students as $student) {
                        $studentScores = Score::where('event_id', $eventId)
                            ->where('student_id', $student->id)
                            ->get()
                            ->keyBy('steam_category_id');

                        $rows[] = $this->makeRow(
                            'student',
                            $event,
                            $group,
                            $subgroup,
                            $team->team_name,
                            $student->name,
                            $student->id,
                            $studentScores,
                            $categories
                        );
                    }
                }
            }
        }

        // sort
        $rows = collect($rows)->sortByDesc('total')->values();

       // rank (FIXED)
$rows = $rows->values()->map(function ($row, $index) {
    $row['rank'] = $index + 1;
    return $row;
});

        return [$rows, $categories];
    }

    private function makeRow($type,$event,$group,$sub,$teamName,$studentName,$id,$scores,$categories)
    {
        $total = 0;
        $points = [];

        foreach ($categories as $cat) {
            $p = optional($scores->get($cat->id))->points ?? 0;
            $points[$cat->name] = $p;
            $total += $p;
        }

        return [
            'type' => $type,
            'rank' => 0,
            'event' => $event->name ?? 'N/A',
            'organization' => $event->organization->name ?? 'N/A',
            'group' => $group->group_name ?? 'N/A',
            'subgroup' => $sub->name ?? 'N/A',
            'team_name' => $teamName ?? 'N/A',
            'student_name' => $studentName ?? 'N/A',
            'scores' => $points,
            'total' => $total
        ];
    }
}