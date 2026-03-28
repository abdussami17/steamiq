<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Score;
use App\Models\SteamCategory;
use App\Models\Student;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    
    public function store(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'students' => 'required|array|min:1',
            'students.*.name' => 'required|string|max:255',
            'students.*.email' => 'nullable|email|max:255',
            'students.*.profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        DB::beginTransaction();
    
        try {
            // Load team with relations up to Event -> TournamentSetting
            $team = Team::with(['subgroup.group.organization.event.tournamentSetting', 'students'])->findOrFail($request->team_id);
    
            // Get actual Event model
            $event = $team->eventRelation();
            if (!$event) {
                return back()->with('error', 'Event not found for this team.');
            }
    
            // Get TournamentSetting for this event
            $tournamentSetting = $event->tournamentSetting;
            $maxPlayers = $tournamentSetting->players_per_team ?? 2; // fallback to 2
    
           
            // Current players
            $currentCount = $team->students()->count();
            $addingCount = count($request->students);

            
            if ($currentCount + $addingCount > $maxPlayers) {
                return back()->with('popup_error', "Cannot add more than {$maxPlayers} players per team.");
            }
    
            foreach ($request->students as $studentData) {
                $profilePath = null;
    
                if (isset($studentData['profile'])) {
                    $file = $studentData['profile'];
                    $extension = $file->getClientOriginalExtension() ?: $file->extension();
                    $filename = time() . '_' . \Str::random(8) . '.' . $extension;
    
                    $destinationDir = public_path('storage/players');
                    if (!is_dir($destinationDir)) mkdir($destinationDir, 0755, true);
    
                    $file->move($destinationDir, $filename);
                    $profilePath = 'players/' . $filename;
                }
    
                Student::create([
                    'name' => $studentData['name'],
                    'email' => $studentData['email'] ?? null,
                    'profile' => $profilePath,
                    'team_id' => $team->id,
                ]);
            }
    
            DB::commit();
            return back()->with('success', 'Player added successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            return back()->with('error', 'Failed to add Players.');
        }
    }
    public function leaderboard(Request $request, $eventId)
    {
        $categories = SteamCategory::orderBy('id')->get();
        $organizationId = $request->organization_id;
    
        $students = Student::with([
            'team.subgroup.group.organization',
            'scores.challengeActivity'
        ])
        ->whereHas('team.group.organization', function ($q) use ($eventId, $organizationId) {
            $q->where('event_id', $eventId);
    
            if ($organizationId) {
                $q->where('id', $organizationId);
            }
        })
        ->get();
    
        $rows = [];
    
        foreach ($students as $student) {
    
            $scoreMap = $student->scores->keyBy('steam_category_id');
            $team = $student->team;
            $subgroup = $team->subgroup ?? null;
    
            // Determine activity name using your getDisplayNameAttribute logic
            $activityName = optional($student->scores->first()?->challengeActivity)?->display_name ?? 'N/A';
    
            $row = [
                'id' => $student->id,
                'student' => $student->name,
                'team' => $team->name ?? 'N/A',
                'subgroup' => $subgroup->name ?? 'N/A',
                'activity' => $activityName,
                'total' => 0
            ];
    
            foreach ($categories as $cat) {
                $points = (int) optional($scoreMap->get($cat->id))->points ?? 0;
                $row[$cat->name] = $points;
                $row['total'] += $points;
            }
    
            $rows[] = $row;
        }
    
        $rows = collect($rows)->sortByDesc('total')->values();
    
        $rank = 1;
        foreach ($rows as $i => $r) {
            $r['rank'] = $rank++;
            $rows[$i] = $r;
        }
    
        return response()->json([
            'categories' => $categories->pluck('name'),
            'rows' => $rows
        ]);
    }
    public function updateScoreInline(Request $request)
{
    $studentId = $request->student_id;
    $categoryName = $request->category;
    $points = (int) $request->points;

    $category = \App\Models\SteamCategory::where('name', $categoryName)->first();

    Score::updateOrCreate(
        [
            'student_id' => $studentId,
            'steam_category_id' => $category->id
        ],
        [
            'points' => $points
        ]
    );

    return response()->json(['success' => true]);
}
}
