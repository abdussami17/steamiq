<?php

namespace App\Models;

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
}
