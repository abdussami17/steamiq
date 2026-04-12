<?php

namespace App\Models;


use App\Models\Activity;
use App\Models\ChallengeActivity;
use App\Models\Group;
use App\Models\Matches;

use App\Models\Score;
use App\Models\Student;
use App\Models\SubGroup;
use App\Models\Team;
use App\Models\TournamentSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Event extends Model
{
    use HasFactory;

    protected $fillable = ['name','type','location','start_date','end_date','status','winner_team_id'];
    protected $dates = [
        'start_date',
        'end_date'
    ];

    public function tournament(){ return $this->hasOne(TournamentSetting::class); }
    public function tournamentSetting()
    {
        return $this->hasOne(TournamentSetting::class);
    }
    
    public function activities()
    {
        return $this->hasMany(ChallengeActivity::class);
    }
    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }
    public function teams(){ return $this->hasMany(Team::class); }
    public function matches(){ return $this->hasMany(Matches::class); }
    
}
