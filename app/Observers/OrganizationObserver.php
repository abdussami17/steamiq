<?php

namespace App\Observers;

use App\Models\Activity;
use App\Models\Organization;

class OrganizationObserver
{
    /**
     * Handle the Organization "created" event.
     */
    public function created(Organization $organization)
    {
        Activity::create([
            'user_id' => auth()->id(),
            'description' => "New Organization '{$organization->name}' added",
            'type'    => 'organization_created',
            'data' => json_encode(['organization_id' => $organization->id])
        ]);
    }

    /**
     * Handle the Organization "updated" event.
     */
    public function updated(Organization $organization): void
    {
        Activity::create([
            'user_id' => auth()->id(),
            'description' => "New Organization '{$organization->name}' updated",
            'type'    => 'organization_updated',
            'data' => json_encode(['organization_id' => $organization->id])
        ]);
    }

    /**
     * Handle the Organization "deleted" event.
     */
    public function deleted(Organization $organization): void
    {
        //
    }

    /**
     * Handle the Organization "restored" event.
     */
    public function restored(Organization $organization): void
    {
        //
    }

    /**
     * Handle the Organization "force deleted" event.
     */
    public function forceDeleted(Organization $organization): void
    {
        //
    }
}
