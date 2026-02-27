<?php

namespace App\Models;

use App\Models\Event;
use App\Models\Group;
use App\Models\Organization;
use App\Models\Player;
use App\Models\Score;
use App\Models\Student;
use App\Models\SubGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_name',
        'event_id',
        'sub_group_id',
        'profile',
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

public function organization(){
    return $this->belongsTo(Organization::class);
}

public function groups(){
    return $this->hasMany(Group::class);
}

public function subgroup() { return $this->belongsTo(SubGroup::class,'sub_group_id'); }
public function students() { return $this->hasMany(Student::class,'team_id'); }
public function scores() { return $this->hasMany(Score::class); }
}
