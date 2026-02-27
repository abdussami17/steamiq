<?php

namespace App\Models;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;


    protected $fillable = ['name','type','email','profile'];
    public function events() { return $this->hasMany(Event::class); }
    

    
}
