<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'type',
        'description',
        'user_id',
        'data',
    ];  
    
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
