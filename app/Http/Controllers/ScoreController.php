<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Player;
use App\Models\Challenges;
use App\Models\Scores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

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

            return back()->with('success', 'Score added successfully');

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


    public function scoresData()
    {
        $scores = Scores::with(['player', 'challenge'])
            ->orderByDesc('created_at')
            ->get();

        $scores = $scores->map(function ($s) {
            return [
                'id' => $s->id,
                'player' => $s->player->name ?? 'N/A',
                'player_email' => $s->player->email ?? 'N/A',
                'pillar' => $s->challenge->pillar_type ?? 'N/A',
                'category' => $s->challenge->name ?? 'N/A',
                'points' => $s->points ?? 0,
                'date' => $s->created_at->format('Y-m-d'),
            ];
        });

        return response()->json($scores);
    }

    public function view($id)
    {
        $score = Score::with(['player', 'challenge'])->find($id);
    
        if (!$score) {
            return response()->json(['error' => 'Score not found'], 404);
        }
    
        return response()->json([
            'id' => $score->id,
            'player_id' => $score->player->id ?? null,
            'player' => $score->player->name ?? 'N/A',
            'pillar_id' => $score->challenge->id ?? null,
            'pillar' => $score->challenge->pillar_type ?? 'N/A',
            'category' => $score->challenge->name ?? 'N/A',
            'points' => $score->points,
            'date' => $score->created_at->format('Y-m-d'),
        ]);
    }
    

    // Update score
    public function update(Request $request, Scores $score)
    {
        $request->validate([
            'points' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'pillar' => 'required|string|max:255',
        ]);

        // Update challenge if needed
        $challenge = Challenges::firstOrCreate([
            'name' => $request->category,
            'pillar_type' => $request->pillar
        ]);

        $score->update([
            'challenge_id' => $challenge->id,
            'points' => $request->points,
        ]);

        return response()->json(['success' => true, 'message' => 'Score updated successfully.']);
    }

    // Delete score
    public function destroy(Scores $score)
    {
        $score->delete();
        return response()->json(['success' => true, 'message' => 'Score deleted successfully.']);
    }
}
