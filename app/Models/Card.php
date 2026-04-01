<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = ['type','negative_points'];

    protected $casts = [
        'negative_points' => 'integer',
    ];
    public function assignments()
    {
        return $this->hasMany(CardAssignment::class);
    }
}