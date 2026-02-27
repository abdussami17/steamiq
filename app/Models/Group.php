<?php

namespace App\Models;

use App\Models\Event;
use App\Models\SubGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['event_id','group_name'];
    public function event() { return $this->belongsTo(Event::class); }
    public function subgroups() { return $this->hasMany(SubGroup::class); }
}
