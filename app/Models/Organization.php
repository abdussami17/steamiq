<?php

namespace App\Models;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;


    protected $fillable = ['name','organization_type','email','profile','event_id'];
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
    

    
}
