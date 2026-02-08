<?php

namespace App\Models;

use App\Models\Matches;
use App\Models\Challenges;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'event_type',
        'start_date',
        'end_date',
        'location',
        'registration_count',
        'status',
    ];

    // Event has many teams
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    // Event has many players (optional, if players register before teams)
    public function players()
    {
        return $this->hasMany(Player::class);
    }

    // Event has many scores (if scores are tracked per event)
    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function challenges()
    {
        return $this->hasMany(Challenges::class);
    }

    public function matches()
{
    return $this->hasMany(Matches::class);
}
public function tournaments()
    {
        return $this->hasMany(Tournament::class);
    }
    
}
