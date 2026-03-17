<?php

namespace App\Models;

use App\Models\Event;
use App\Models\Organization;
use App\Models\SubGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id','group_name','pod'];
    public function event() { return $this->belongsTo(Event::class); }
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'group_id'); // direct teams under group
    }
    
    public function subgroups() { return $this->hasMany(SubGroup::class); }
}
