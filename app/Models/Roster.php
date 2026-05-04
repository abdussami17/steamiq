<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Roster extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'organization_id',
        'status',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Students on this roster via the roster_students pivot.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'roster_students')
                    ->withPivot('attendance_status')
                    ->withTimestamps();
    }

    /**
     * Direct pivot rows (useful for attendance updates).
     */
    public function rosterStudents(): HasMany
    {
        return $this->hasMany(RosterStudent::class);
    }

    // ── Status helpers ────────────────────────────────────────────────────────

    public function isDraft(): bool    { return $this->status === 'draft'; }
    public function isReady(): bool    { return $this->status === 'ready'; }
    public function isCheckedIn(): bool { return $this->status === 'checked-in'; }
}