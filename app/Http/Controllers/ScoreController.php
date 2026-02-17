<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Player;
use App\Models\Scores;
use App\Models\Challenges;
use Illuminate\Http\Request;
use App\Imports\ScoresImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;

class ScoreController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id'     => 'required|exists:events,id',
                'player_id'    => 'required|exists:players,id',
                'challenge_id' => 'required|exists:challenges,id',
                'points'       => 'required|integer|min:0',
            ]);

            $event = Event::findOrFail($validated['event_id']);
            $player = Player::findOrFail($validated['player_id']);
            $challenge = Challenges::findOrFail($validated['challenge_id']);

            if ($player->event_id != $event->id) {
                throw ValidationException::withMessages([
                    'player_id' => 'Selected player does not belong to this event.'
                ]);
            }

            if ($challenge->event_id != $event->id) {
                throw ValidationException::withMessages([
                    'challenge_id' => 'Selected challenge does not belong to this event.'
                ]);
            }

            if ($validated['points'] < 0 || $validated['points'] > $challenge->max_points) {
                throw ValidationException::withMessages([
                    'points' => 'Points must be between 0 and ' . $challenge->max_points
                ]);
            }

            $exists = Scores::where('player_id', $player->id)
                            ->where('challenge_id', $challenge->id)
                            ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'player_id' => 'Score already exists for this player and challenge.'
                ]);
            }

            DB::transaction(function () use ($validated) {
                Scores::create($validated);
            });

            return back()->with('success', 'Score added successfully')
            ->header('Content-Type', 'text/html');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Log the full error message and stack trace
            Log::error('ScoreController@store error: '.$e->getMessage(), [
                'exception' => $e
            ]);

            // Optionally, also return the message for local debugging (remove in production)
            return back()->with('error', 'Something went wrong: '.$e->getMessage())->withInput();
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx'
        ]);
    
        $file = $request->file('file');
    
        try {
            Excel::import(new ScoresImport(), $file);
    
            return back()->with('success', 'Scores imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: '.$e->getMessage());
        }
    }
    
    public function scoresData()
    {
        $scores = Scores::with(['player','challenge'])
            ->latest()
            ->get();

        return response()->json(
            $scores->map(function ($s) {
                return [
                    'id' => $s->id,
                    'player' => $s->player->name ?? 'N/A',
                    'pillar' => $s->challenge->pillar_type ?? 'N/A',
                    'category' => $s->challenge->name ?? 'N/A',
                    'points' => $s->points,
                    'date' => $s->created_at->format('Y-m-d'),
                ];
            })
        );
    }


        public function edit(Scores $score)
{


    return response()->json([
        'id' => $score->id,
        'points' => $score->points
    ]);
}



public function update(Request $request, Scores $score)
{
    try {
        $request->validate([
            'points' => 'required|numeric|min:0'
        ]);

        $score->points = $request->points;
        $score->save();

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        \Log::error('Score update failed: '.$e->getMessage());
        return response()->json(['success'=>false,'error'=>$e->getMessage()],500);
    }
}





public function destroy(Scores $score)
{
    $score->delete();
    return response()->json(['success' => true]);
}

}
