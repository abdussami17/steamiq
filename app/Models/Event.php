<?php

namespace App\Models;


use App\Models\ChallengeActivity;
use App\Models\Group;
use App\Models\Organization;
use App\Models\Score;
use App\Models\Student;
use App\Models\SubGroup;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'organization_id',
        'event_type',
        'start_date',
        'end_date',
        'location',
        'registration_count',
        'status'
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function groups() { return $this->hasMany(Group::class); }
    public function subgroups() { return $this->hasMany(SubGroup::class); }
    public function teams() { return $this->hasMany(Team::class); }
    public function students() { return $this->hasMany(Student::class); }
    public function challengeactivity()
    {
        return $this->hasMany(\App\Models\ChallengeActivity::class);
    }
    public function scores() { return $this->hasMany(Score::class); }
}
