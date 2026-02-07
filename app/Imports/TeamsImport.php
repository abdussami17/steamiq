<?php

namespace App\Imports;

use App\Models\Team;
use App\Models\Player;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Validation\ValidationException;

class TeamsImport implements ToCollection
{
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function collection(Collection $rows)
    {
        // Remove header row if present
        $header = $rows->first();
        if(strtolower($header[0]) === 'team name') {
            $rows->shift();
        }

        foreach ($rows as $row) {
            $teamName = trim($row[0] ?? '');
            $playerEmails = trim($row[1] ?? '');

            if (!$teamName || !$playerEmails) continue;

            $emails = array_map('trim', explode(',', $playerEmails));

            // Find players by email for this event
            $players = Player::where('event_id', $this->eventId)
                             ->whereIn('email', $emails)
                             ->pluck('id')
                             ->toArray();

            if(empty($players)) {
                // Optionally skip or throw exception
                continue;
            }

            // Create team
            $team = Team::create([
                'team_name' => $teamName,
                'event_id' => $this->eventId,
            ]);

            // Attach players
            $team->players()->attach($players);
        }
    }
}
