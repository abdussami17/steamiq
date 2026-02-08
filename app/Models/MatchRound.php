<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatchRound extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'round_no',
        'winner_team_id',
    ];



    public function match() { return $this->belongsTo(Matches::class,'match_id'); }
}
