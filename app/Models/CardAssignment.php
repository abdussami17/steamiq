<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardAssignment extends Model
{
    // Fixed syntax: = array
    protected $fillable = [
        'card_id',
        'assignable_id',
        'assignable_type'
    ];

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function player()
    {
        return $this->belongsTo(Student::class);
    }
}