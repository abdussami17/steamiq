<?php

namespace App\Observers;

use App\Models\CardAssignment;
use App\Models\Activity;
use App\Models\Group;
use App\Models\Team;
use App\Models\Student;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;

class CardAssignmentObserver
{
    public function created(CardAssignment $assignment)
    {
        // Get card
        $card = $assignment->card;

        // Resolve assignable model
        $targetName = 'Unknown';

        switch ($assignment->assignable_type) {
            case 'organization':
                $model = Organization::find($assignment->assignable_id);
                $targetName = $model->name ?? 'N/A';
                break;

            case 'group':
                $model = Group::find($assignment->assignable_id);
                $targetName = $model->group_name ?? 'N/A';
                break;

            case 'team':
                $model = Team::find($assignment->assignable_id);
                $targetName = $model->name ?? 'N/A';
                break;

            case 'student':
                $model = Student::find($assignment->assignable_id);
                $targetName = $model->name ?? 'N/A';
                break;
        }

        Activity::create([
            'type' => 'card_assigned',
            'description' => "Card '{$card->type}' assigned to {$assignment->assignable_type} '{$targetName}'",
            'user_id' => Auth::id(),
            'data' => json_encode([
                'card_id' => $assignment->card_id,
                'card_type' => $card->type,
                'assignable_type' => $assignment->assignable_type,
                'assignable_id' => $assignment->assignable_id,
                'assignable_name' => $targetName
            ])
        ]);
    }
}