<?php

namespace App\Models;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'address',
        'email',
        'profile'
    ];
    public function teams(){
        return $this->hasMany(Team::class);
    }
    

    
}
