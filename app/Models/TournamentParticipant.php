<?php
namespace App\Models;

use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Model;

class TournamentParticipant extends Model
{
    protected $table = 'tournament_participants';
    protected $fillable = ['tournament_id','team_id','seed'];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
