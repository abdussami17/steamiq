<?php

namespace App\Http\Controllers;

use App\Models\Event;
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
}
