<?php

namespace App\Models;

use App\Models\Player;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_name',
        'event_id',
    ];

    // Team has multiple players (many-to-many)
    public function players()
    {
        return $this->belongsToMany(Player::class, 'team_members', 'team_id', 'player_id')
                    ->withTimestamps();
    }

    // Team belongs to an event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function matchesA()
{
    return $this->hasMany(MatchModel::class, 'team_a_id');
}

public function matchesB()
{
    return $this->hasMany(MatchModel::class, 'team_b_id');
}

}
