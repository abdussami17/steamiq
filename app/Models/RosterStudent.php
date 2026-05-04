<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RosterStudent extends Model
{
    protected $fillable = [
        'roster_id',
        'student_id',
        'attendance_status',
    ];

    /**
     * Each record belongs to a Roster
     */
    public function roster(): BelongsTo
    {
        return $this->belongsTo(Roster::class);
    }

    /**
     * Each record belongs to a Student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Helper: check if present
     */
    public function isPresent(): bool
    {
        return $this->attendance_status === 'present';
    }

    /**
     * Helper: check if absent
     */
    public function isAbsent(): bool
    {
        return $this->attendance_status === 'absent';
    }

    /**
     * Helper: not marked yet
     */
    public function isPending(): bool
    {
        return $this->attendance_status === null;
    }
}