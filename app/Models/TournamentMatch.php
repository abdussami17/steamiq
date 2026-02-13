<?php
namespace App\Models;

use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Model;

class TournamentMatch extends Model
{
    protected $fillable = [
        'tournament_id','team_a_id','team_b_id','winner_team_id','round_no','status','game_title','format','pin','scheduled_at'
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function teamA()
    {
        return $this->belongsTo(Team::class,'team_a_id');
    }

    public function teamB()
    {
        return $this->belongsTo(Team::class,'team_b_id');
    }

    public function winner()
    {
        return $this->belongsTo(Team::class,'winner_team_id');
    }
}
