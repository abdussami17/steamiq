<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusAssignment extends Model
{
    protected $fillable = [
        'assignable_id',
        'assignable_type',
        'points',
    ];

    // Polymorphic relation (optional future use)
    public function assignable()
    {
        return $this->morphTo();
    }
}