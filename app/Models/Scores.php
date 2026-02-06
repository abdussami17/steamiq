<?php

namespace App\Models;

use App\Models\Event;
use App\Models\Player;
use App\Models\Challenges;
use Illuminate\Database\Eloquent\Model;

class Scores extends Model
{
    protected $table = 'scores';

    protected $fillable = [
        'event_id',
        'player_id',
        'challenge_id',
        'points',
        'score_date',
        'notes',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function challenge()
    {
        return $this->belongsTo(Challenges::class, 'challenge_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
