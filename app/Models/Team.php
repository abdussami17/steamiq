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
        'name',
        'sub_group_id',
        'profile',
        'group_id',
        'division'
    ];


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

public function group()
{
    return $this->belongsTo(Group::class);
}
public function getDisplayNameAttribute()
{
    $suffixMap = [
        'Primary' => '_P',
        'Junior'  => '_J'
    ];

    $suffix = $suffixMap[$this->division] ?? '';

    return $this->name . $suffix;
}

public function subgroup() { return $this->belongsTo(SubGroup::class,'sub_group_id'); }
public function students() { return $this->hasMany(Student::class,'team_id'); }
public function scores() { return $this->hasMany(Score::class); }
public function challengeActivity(){return $this->hasMany(ChallengeActivity::class);}
}
