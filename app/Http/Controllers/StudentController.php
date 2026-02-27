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

class StudentController extends Controller
{
    public function index()
    {
        $events = Event::where('status', 'live')
            ->select('id', 'name')
            ->get();
        $teams = Team::select('id','team_name')->get();   
        return view('students.index', compact('events','teams'));
    }
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
            $team = Team::findOrFail($request->team_id);
            $eventId = $team->event_id;

            foreach($request->students as $studentData){
                $profilePath = null;
                if(isset($studentData['profile'])){
                    $profilePath = $studentData['profile']->store('students', 'public');
                }

                Student::create([
                    'name' => $studentData['name'],
                    'email' => $studentData['email'] ?? null,
                    'profile' => $profilePath,
                    'team_id' => $team->id,
                    'event_id' => $eventId
                ]);
            }

            DB::commit();
            return back()->with('success', 'Students added successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to add students.');
        }
    }

    public function leaderboard($eventId)
    {
        $categories = SteamCategory::orderBy('id')->get(); // dynamic
    
        $students = Student::with([
            'team.subgroup',
            'scores.challengeActivity'
        ])
        ->where('event_id', $eventId)
        ->get();
    
        $rows = [];
    
        foreach ($students as $student) {
    
            $scoreMap = $student->scores->keyBy('steam_category_id');
    
            $row = [
                'id' => $student->id,
                'student' => $student->name,
                'team' => $student->team->team_name ?? 'N/A',
                'activity' => optional($student->scores->first()?->challengeActivity)->name ?? 'N/A',
                'total' => 0
            ];
    
            foreach ($categories as $cat) {
                $points = (int) optional($scoreMap->get($cat->id))->points ?? 0;
    
                $row[$cat->name] = $points;
                $row['total'] += $points;
            }
    
            $rows[] = $row;
        }
    
        // sort + rank
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
