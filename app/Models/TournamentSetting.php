<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentSetting extends Model
{
    protected $fillable = [
        'event_id','brain_enabled','brain_type','brain_score','brain_powerup',
        'game','players_per_team','match_rule','points_win','points_draw',
        'tournament_type','number_of_teams'
    ];


    public function event()
{
    return $this->belongsTo(Event::class);
}
}
