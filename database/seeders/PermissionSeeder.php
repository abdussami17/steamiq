<?php 

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            ['name' => 'create_event', 'label' => 'Create Event'],
            ['name' => 'edit_event', 'label' => 'Edit Event'],
            ['name' => 'delete_event', 'label' => 'Delete Event'],
            ['name' => 'duplicate_event', 'label' => 'Duplicate Event'],
            ['name' => 'create_organization', 'label' => 'Create Organization'],
            ['name' => 'edit_organization', 'label' => 'Edit Organization'],
            ['name' => 'delete_organization', 'label' => 'Delete Organization'],
            ['name' => 'create_group', 'label' => 'Create Group'],
            ['name' => 'edit_group', 'label' => 'Edit Group'],
            ['name' => 'delete_group', 'label' => 'Delete Group'],
            ['name' => 'create_subgroup', 'label' => 'Create Subgroup'],
            ['name' => 'edit_subgroup', 'label' => 'Edit Subgroup'],
            ['name' => 'delete_subgroup', 'label' => 'Delete Subgroup'],
            ['name' => 'create_team', 'label' => 'Create Team'],
            ['name' => 'edit_team', 'label' => 'Edit Team'],
            ['name' => 'delete_team', 'label' => 'Delete Team'],
            ['name' => 'import_team', 'label' => 'Import Team'],
            ['name' => 'create_player', 'label' => 'Create Player'],

            ['name' => 'delete_player', 'label' => 'Delete Player'],

            ['name' => 'create_score', 'label' => 'Create Score'],
            ['name' => 'edit_score', 'label' => 'Edit Score'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['label' => $perm['label']]
            );
        }
    }
}