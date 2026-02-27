<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengeActivity extends Model
{
    protected $fillable = ['event_id','name','description'];
    public function event() { return $this->belongsTo(Event::class); }
    public function scores() { return $this->hasMany(Score::class); }
}
