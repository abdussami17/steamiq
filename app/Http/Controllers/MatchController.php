<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Matches;
use App\Models\MatchRound;
use Illuminate\Http\Request;
use App\Models\EsportsPoints;
use App\Exports\ScheduleExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class MatchController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id'=>'required|exists:events,id',
            'match_name'=>'required|string|max:255',
            'team_a'=>'required|different:team_b',
            'team_b'=>'required',
            'game_title'=>'nullable|string|max:255',
            'format'=>'required|in:single,bo3,bo5,custom',
            'win_required'=>'nullable|integer|min:1',
            'date'=>'required|date',
            'time'=>'required'
        ]);

        if($validator->fails())
            return response()->json(['errors'=>$validator->errors()],422);

        $winRequired = match($request->format){
            'single'=>1,
            'bo3'=>2,
            'bo5'=>3,
            'custom'=>$request->win_required ?? 1,
        };

        $match = Matches::create([
            'event_id'=>$request->event_id,
            'match_name'=>$request->match_name,
            'team_a_id'=>$request->team_a,
            'team_b_id'=>$request->team_b,
            'game_title'=>$request->game_title,
            'format'=>$request->format,
            'win_required'=>$winRequired,
            'date'=>$request->date,
            'time'=>$request->time
        ]);

        return response()->json(['success'=>true,'message'=>'Match created successfully','match'=>$match]);
    }

    public function generatePin($id)
    {
        $match = Matches::findOrFail($id);
    
        // If PIN already exists, just return it
        if ($match->pin) {
            return response()->json([
                'success' => true,
                'pin' => $match->pin,
                'message' => 'PIN already generated for this match.'
            ]);
        }
    
        // Generate unique 6-digit numeric PIN
        $pin = rand(100000, 999999);
        while (Matches::where('pin', $pin)->exists()) {
            $pin = rand(100000, 999999);
        }
    
        $match->pin = $pin;
        $match->save();
    
        return response()->json([
            'success' => true,
            'pin' => $pin,
            'message' => 'PIN generated successfully.'
        ]);
    }
    



    public function fetch()
    {
        $matches = Matches::with(['teamA','teamB'])->latest()->get();
        return response()->json($matches);
    }

    public function destroy($id)
    {
        $match = Matches::findOrFail($id);
        $match->delete();
        return response()->json(['success'=>true,'message'=>'Match deleted']);
    }

    public function fetchTeams(Request $request)
    {
        // Optional: filter by event if event_id is passed
        $eventId = $request->query('event_id');

        $teams = Team::when($eventId, function($query, $eventId) {
                return $query->where('event_id', $eventId);
            })
            ->get(['id', 'team_name']);

        return response()->json($teams);
    }

    public function addRound(Request $request, $matchId)
    {
        try {
            // Validate input
            $request->validate([
                'winner_team_id' => 'required|exists:teams,id',
            ]);
    
            // Load match with rounds
            $match = Matches::with('rounds')->findOrFail($matchId);
    
            // Max rounds validation
            $maxRounds = $match->win_required * 2 - 1;
            if ($match->rounds->count() >= $maxRounds) {
                return response()->json([
                    'success' => false,
                    'errors' => ['Maximum rounds reached']
                ], 400);
            }
    
            // Create new round
            $roundNo = $match->rounds->count() + 1;
            $round = MatchRound::create([
                'match_id' => $matchId,
                'round_no' => $roundNo,
                'winner_team_id' => $request->winner_team_id
            ]);
    
            // Reload rounds to recalc wins
            $match->load('rounds');
    
            $winsA = $match->rounds->where('winner_team_id', $match->team_a_id)->count();
            $winsB = $match->rounds->where('winner_team_id', $match->team_b_id)->count();
    
            $winner = null;
            if ($winsA >= $match->win_required) $winner = $match->team_a_id;
            if ($winsB >= $match->win_required) $winner = $match->team_b_id;
    
            // If match completed, assign winner and points
            if ($winner) {
    
                $match->winner_team_id = $winner;
                $match->status = 'completed';
                $match->save();
    
                // ✅ Only create points if event_id and winner exist
                if ($match->event_id && $winner) {
                    EsportsPoints::create([
                        'event_id' => $match->event_id,
                        'team_id' => $winner,
                        'points' => 10, // configurable
                        'match_id' => $match->id
                    ]);
                } else {
                    \Log::warning('EsportsPoints not created: missing event_id or winner', [
                        'match_id' => $match->id,
                        'event_id' => $match->event_id,
                        'winner' => $winner
                    ]);
                }
            }
    
            return response()->json([
                'success' => true,
                'round' => $round,
                'winner' => $winner
            ]);
    
        } catch (\Throwable $e) {
            \Log::error('AddRound Error', [
                'matchId' => $matchId,
                'input' => $request->all(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'errors' => ['Server error: '.$e->getMessage()]
            ], 500);
        }
    }

    public function exportAllSchedule()
    {
        $fileName = 'all_events_schedule_'.date('Ymd_His').'.xlsx';
    
        return Excel::download(new ScheduleExport(), $fileName);
    }
        
    

}
