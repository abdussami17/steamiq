<?php
namespace App\Models;

use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'event_id',

    ];

   
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members');
    }
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
