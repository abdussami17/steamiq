<?php
namespace App\Models;

use App\Models\Scores;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'event_id',
        'profile',
    ];

    // Event relationship
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Player belongs to many teams via team_members
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members');
    }

    // Optional: team_members pivot table (if needed)
    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }

    // Scores relationship
    public function scores()
    {
        return $this->hasMany(Scores::class);
    }
}
