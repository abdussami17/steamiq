<?php

namespace App\Models;

use App\Models\Event;
use App\Models\Score;
use App\Models\Team;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['event_id','team_id','name','profile','email'];

    public function team() { return $this->belongsTo(Team::class); }
    public function event() { return $this->belongsTo(Event::class); }
    public function scores() { return $this->hasMany(Score::class); }

}
