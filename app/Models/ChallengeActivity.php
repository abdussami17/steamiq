<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallengeActivity extends Model
{
    protected $fillable = [
        'event_id', 'max_score',
        'activity_or_mission', 'activity_type', 'badge_name',
        'brain_type', 'brain_description', 'point_structure',
        'esports_type', 'esports_players', 'esports_structure', 'esports_description',
        'egaming_type', 'egaming_mode', 'egaming_structure', 'egaming_description',
        'playground_description',
    ];

    public function getDisplayNameAttribute()
{
    if ($this->activity_or_mission === 'mission') {
        return $this->badge_name;
    }

    return match ($this->activity_type) {
        'brain'      => $this->brain_type,
        'egaming'    => $this->egaming_type,
        'esports'    => $this->esports_type,
        'playground' => 'Playground',
        default      => $this->name,
    };
}
    public function event() { return $this->belongsTo(Event::class); }
    public function scores() { return $this->hasMany(Score::class); }
}
