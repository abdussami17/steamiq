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
    public function index()
    {
        $events = Event::select('id', 'name')
        ->get();
        $teams = Team::select('id','name')->get();   
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
    
            foreach ($request->students as $studentData) {
    
                $profilePath = null;
    
                if (isset($studentData['profile'])) {
    
                    $file = $studentData['profile'];
    
                    $extension = $file->getClientOriginalExtension() ?: $file->extension();
                    $filename = time().'_'.Str::random(8).'.'.$extension;
    
                    $destinationDir = public_path('storage/players');
    
                    if (!is_dir($destinationDir)) {
                        mkdir($destinationDir, 0755, true);
                    }
    
                    $file->move($destinationDir, $filename);
    
                    $profilePath = 'players/'.$filename;
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
            Log::error($e->getMessage());
    
            return back()->with('error', 'Failed to add Players.');
        }
    }

    public function leaderboard($eventId)
    {
        $categories = SteamCategory::orderBy('id')->get(); // dynamic
    
        // Fetch students belonging to the event via team -> group -> event
        $students = Student::with([
            'team.subgroup.group.organization',
            'scores.challengeActivity'
        ])
        ->whereHas('team.group.organization', function ($q) use ($eventId) {
            $q->where('event_id', $eventId);
        })
        ->get();
    
        $rows = [];
    
        foreach ($students as $student) {
    
            $scoreMap = $student->scores->keyBy('steam_category_id');
    
            $team = $student->team;
            $subgroup = $team->subgroup ?? null;
    
            $row = [
                'id' => $student->id,
                'student' => $student->name,
                'team' => $team->name ?? 'N/A',
                'subgroup' => $subgroup->name ?? 'N/A', // optional subgroup
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
    
        // sort by total points DESC + assign rank
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
