<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class challenges extends Model
{
 // Mass assignment - ye columns ko allow karta hai create()/update() me
 protected $fillable = [
    'event_id',
    'pillar_type',
    'sub_category',
    'name',
    'max_points'
];

// Optional: relationship to Event
public function event()
{
    return $this->belongsTo(Event::class);
}
}
