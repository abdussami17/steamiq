<?php 

namespace App\Services;

use App\Models\Event;
use App\Models\Score;
use App\Models\SteamCategory;

class LeaderboardService
{
    public function build($eventId)
    {
        // Load event with full hierarchy and students' scores
        $event = Event::with([
            'organizations.groups.subgroups.teams.students.scores'
        ])->findOrFail($eventId);

        $categories = SteamCategory::all(); // id and name

        $rows = [];

        foreach ($event->organizations as $org) {
            foreach ($org->groups as $group) {
                foreach ($group->subgroups as $subgroup) {
                    foreach ($subgroup->teams as $team) {

                        // TEAM ROW (aggregate)
                        $teamScores = $team->scores->whereNull('student_id')->keyBy('steam_category_id');

                        $rows[] = $this->makeRow(
                            'team',
                            $event,
                            $org,
                            $group,
                            $subgroup,
                            $team->name,
                            null,
                            $team->id,
                            $teamScores,
                            $categories
                        );

                        // STUDENT ROWS
                        foreach ($team->students as $student) {
                            $studentScores = $student->scores->keyBy('steam_category_id');

                            $rows[] = $this->makeRow(
                                'student',
                                $event,
                                $org,
                                $group,
                                $subgroup,
                                $team->name,
                                $student->name,
                                $student->id,
                                $studentScores,
                                $categories
                            );
                        }
                    }
                }
            }
        }

        // Sort by total descending
        $rows = collect($rows)->sortByDesc('total')->values();

        // Assign ranks with tie handling
        $rank = 1;
        $previousPoints = null;
        foreach ($rows as $index => $row) {
            if ($previousPoints !== null && $row['total'] === $previousPoints) {
                $row['rank'] = $rows[$index - 1]['rank']; // tie
            } else {
                $row['rank'] = $rank;
            }
            $previousPoints = $row['total'];
            $rank++;
            $rows[$index] = $row;
        }

        return [$rows, $categories];
    }

    private function makeRow($type, $event, $org, $group, $sub, $teamName, $studentName, $id, $scores, $categories)
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
            'organization' => $org->name ?? 'N/A',
            'group' => $group->group_name ?? 'N/A',
            'subgroup' => $sub->name ?? 'N/A',
            'team_name' => $teamName ?? 'N/A',
            'student_name' => $studentName ?? 'N/A',
            'scores' => $points,
            'total' => $total
        ];
    }
}