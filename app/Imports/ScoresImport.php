<?php

namespace App\Imports;

use App\Models\Scores;
use App\Models\Player;
use App\Models\Challenges;
use Maatwebsite\Excel\Concerns\ToModel;

class ScoresImport implements ToModel
{
    public function model(array $row)
    {
        // Column 0 = player email, 1 = challenge name, 2 = points
        $email = trim($row[0] ?? '');
        $challengeName = trim($row[1] ?? '');
        $points = $row[2] ?? null;

        // Find player
        $player = Player::where('email', $email)->first();
        if (!$player) return null; // Skip if player doesn't exist

        $eventId = $player->event_id;

        // Find challenge in the same event as player
        $challenge = Challenges::where('name', $challengeName)
                               ->where('event_id', $eventId)
                               ->first();
        if (!$challenge) return null; // Skip if challenge doesn't belong to the player's event

        // Points validation
        if (!is_numeric($points) || $points < 0 || $points > $challenge->max_points) {
            return null; // Skip invalid points
        }

        // Insert or update the score
        return Scores::updateOrCreate([
            'event_id' => $eventId,
            'player_id' => $player->id,
            'challenge_id' => $challenge->id
        ], [
            'points' => $points
        ]);
    }
}
