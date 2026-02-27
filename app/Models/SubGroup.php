<?php

namespace App\Models;

use App\Models\Event;
use App\Models\Group;
use App\Models\Team;
use Illuminate\Database\Eloquent\Model;

class SubGroup extends Model
{
   protected $fillable = [
    'name',
    'event_id',
    'group_id'
   ];


   public function group() { return $this->belongsTo(Group::class); }
   public function event() { return $this->belongsTo(Event::class); }
   public function teams() { return $this->hasMany(Team::class); }
}
