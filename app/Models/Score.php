<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $table = 'scores';
    protected $fillable = [
        'event_id',
        'student_id',
        'team_id',
        'challenge_activity_id',
        'steam_category_id',
        'points',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function challengeActivity()
    {
        return $this->belongsTo(ChallengeActivity::class, 'challenge_activity_id');
    }

    public function steamCategory()
    {
        return $this->belongsTo(SteamCategory::class, 'steam_category_id');
    }
}