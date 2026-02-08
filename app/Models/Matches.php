<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Matches extends Model
{


    use HasFactory;

    protected $fillable = [
        'event_id',
        'match_name',
        'team_a_id',
        'team_b_id',
        'game_title',
        'format',
        'win_required',
        'date',
        'time',
        'pin',
        'winner_team_id',
        'status',
    ];


    public function event() { return $this->belongsTo(Event::class); }
    public function teamA() { return $this->belongsTo(Team::class,'team_a_id'); }
    public function teamB() { return $this->belongsTo(Team::class,'team_b_id'); }
    public function rounds() { return $this->hasMany(MatchRound::class,'match_id'); }

}



