<?php

namespace App\Models;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'group_name'
    ];

    public function team(){
        return $this->belongsTo(Team::class);
    }
}
