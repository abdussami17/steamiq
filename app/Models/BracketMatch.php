<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BracketMatch extends Model
{
    protected $fillable = [
        'event_id', 'pod', 'division', 'phase',
        'round_no', 'match_no',
        'team_a_id', 'team_b_id',
        'team_a_score', 'team_b_score',
        'winner_team_id', 'status',
        'next_match_id', 'next_is_team_a',
    ];

    protected $casts = [
        'next_is_team_a' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function teamA(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_a_id');
    }

    public function teamB(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_b_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'winner_team_id');
    }

    public function nextMatch(): BelongsTo
    {
        return $this->belongsTo(BracketMatch::class, 'next_match_id');
    }
}
