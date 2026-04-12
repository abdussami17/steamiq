<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Score;
use App\Models\SteamCategory;
use App\Models\Student;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $addingCount = collect($request->students)
            ->filter(fn($s) => !empty($s['name']))
            ->count();

            
            if ($currentCount + $addingCount > $maxPlayers) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot add more than {$maxPlayers} players per team."
                ], 422);
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
            return response()->json([
                'success' => true,
                'message' => 'Player added successfully.'
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            return back()->with('error', 'Failed to add Players.');
        }
    }
    public function leaderboard(Request $request, $eventId)
    {
        try {
            $organizationId = $request->organization_id;
    
            $students = Student::with([
                'team.subgroup.group.organization',
                'scores.challengeActivity',
                
            ])
            ->whereHas('team.group.organization', function ($q) use ($eventId, $organizationId) {
                $q->where('event_id', $eventId);
                if ($organizationId) $q->where('id', $organizationId);
            })
            ->get();
    
            $rows = [];
    
            foreach ($students as $student) {
                $team = $student->team;
                $subgroup = $team->subgroup ?? null;
    
                $totalPoints = 0;
                $activityName = '';

                foreach ($student->scores as $score) {
                    $points = (int) $score->points;
                    $totalPoints += $points;
                
                    if (!$activityName && $score->challengeActivity) {
                        // 1️⃣ Format name: replace underscores and capitalize words
                        $formattedName = str_replace('_', ' ', $score->challengeActivity->display_name);
                        $formattedName = ucwords($formattedName);
                
                        // 2️⃣ Append correct description based on activity_type
                        $description = '';
                        $type = strtolower($score->challengeActivity->activity_type);
                
                        if ($type === 'brain') {
                            $description = $score->challengeActivity->brain_description ?? '';
                        } elseif (in_array($type, ['playground', 'esports', 'egaming'])) {
                            $description = $score->challengeActivity->egaming_description ?? '';
                        }
                
                        // Combine name + dash + description (if description exists)
                        $activityName = $description ? "{$formattedName} - {$description}" : $formattedName;
                    }
                }
    
                
              
    
                $rows[] = [
                    'id' => $student->id,
                    'student' => $student->name,
                    'team' => $team->name ?? 'N/A',
                    'activity' => $activityName,
                    'total' => $totalPoints
                ];
            }
    
            $rows = collect($rows)->sortByDesc('total')->values();
            foreach ($rows as $i => $r) {
                $r['rank'] = $i + 1;
                $rows[$i] = $r;
            }
    
            // ===============================
            // Fetch logged-in user permissions
            // ===============================
            $userPermissions = Auth::user()->getAllPermissions()->pluck('name')->toArray();
    
            return response()->json([
                'rows' => $rows,
                'permissions' => $userPermissions
            ]);
    
        } catch (\Throwable $e) {
            \Log::error('Leaderboard Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'rows' => [],
                'permissions' => []
            ], 500);
        }
    }



// Delete player
public function destroy($id)
{


    $player = Student::find($id);
    if (!$player) {
        return response()->json([
            'success' => false,
            'message' => 'Player not found'
        ], 404);
    }

    $player->delete();

    return response()->json([
        'success' => true,
        'message' => 'Player deleted successfully'
    ]);
}
public function edit($id)
{
    $player = Student::with('team.group.organization')->find($id);

    if (!$player) {
        return response()->json([
            'success' => false,
            'message' => 'Player not found'
        ], 404);
    }

    $teams = Team::with('group.organization')
        ->select('id', 'name', 'group_id')
        ->get();

    return response()->json([
        'success' => true,
        'data' => [
            'id' => $player->id,
            'name' => $player->name,
            'team_id' => $player->team_id,
            'organization_name' => optional($player->team->group->organization)->name
        ],
        'teams' => $teams
    ]);
}
public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'team_id' => 'required|exists:teams,id',
    ]);

    $player = Student::find($id);

    if (!$player) {
        return response()->json([
            'success' => false,
            'message' => 'Player not found'
        ], 404);
    }

    $player->update([
        'name' => $request->name,
        'team_id' => $request->team_id,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Player updated successfully'
    ]);
}

public function bulkDelete(Request $request)
{
    $ids = $request->ids;

    if (!is_array($ids) || empty($ids)) {
        return response()->json(['success' => false]);
    }

    Student::whereIn('id', $ids)->delete();

    return response()->json(['success' => true]);
}


}
