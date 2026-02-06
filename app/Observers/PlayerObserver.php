<?php
namespace App\Observers;

use App\Models\Player;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class PlayerObserver
{
    public function created(Player $player)
    {
        Activity::create([
            'type' => 'player_created',
            'description' => "New player '{$player->name}' added",
            'user_id' => Auth::id(),
            'data' => json_encode(['player_id' => $player->id])
        ]);
    }

    public function updated(Player $player)
    {
        $changes = $player->getChanges(); // changed attributes
        Activity::create([
            'type' => 'player_updated',
            'description' => "Player '{$player->name}' updated",
            'user_id' => Auth::id(),
            'data' => json_encode($changes)
        ]);
    }

    public function deleted(Player $player)
    {
        Activity::create([
            'type' => 'player_deleted',
            'description' => "Player '{$player->name}' deleted",
            'user_id' => Auth::id(),
            'data' => json_encode(['player_id' => $player->id])
        ]);
    }
}
