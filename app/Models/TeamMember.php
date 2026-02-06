<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamMember extends Model
{
        
    use HasFactory;

    protected $table = 'team_members';
    protected $fillable = ['player_id', 'team_id'];
    public $timestamps = true;
}
