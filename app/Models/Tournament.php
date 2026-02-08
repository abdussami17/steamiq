<?php

namespace App\Models;

use App\Models\Event;
use App\Models\TournamentMatch;
use App\Models\TournamentParticipant;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $fillable = ['event_id','name','type','scheduled_at'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function participants()
    {
        return $this->hasMany(TournamentParticipant::class);
    }

    public function matches()
    {
        return $this->hasMany(TournamentMatch::class);
    }
}

