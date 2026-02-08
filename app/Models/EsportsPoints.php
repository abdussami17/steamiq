<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class EsportsPoints extends Model
{
    
    protected $table = 'team_esport_points';

    protected $fillable = [
        'event_id',
        'team_id',
        'match_id',
        'points',
    ];
}
