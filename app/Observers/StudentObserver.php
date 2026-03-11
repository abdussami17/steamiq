<?php
namespace App\Observers;

use App\Models\Student;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class StudentObserver
{
    public function created(Student $Student)
    {
        Activity::create([
            'type' => 'Player_created',
            'description' => "New Player '{$Student->name}' added",
            'user_id' => Auth::id(),
            'data' => json_encode(['Student_id' => $Student->id])
        ]);
    }

    public function updated(Student $Student)
    {
        $changes = $Student->getChanges(); // changed attributes
        Activity::create([
            'type' => 'Player_updated',
            'description' => "Player '{$Student->name}' updated",
            'user_id' => Auth::id(),
            'data' => json_encode($changes)
        ]);
    }

    public function deleted(Student $Student)
    {
        Activity::create([
            'type' => 'Player_deleted',
            'description' => "Player '{$Student->name}' deleted",
            'user_id' => Auth::id(),
            'data' => json_encode(['Student_id' => $Student->id])
        ]);
    }
}
