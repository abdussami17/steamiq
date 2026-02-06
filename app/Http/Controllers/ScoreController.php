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
}
