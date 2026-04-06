<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $coach = Role::firstOrCreate(['name' => 'coach']);

        // Assign all permissions to admin
        $admin->syncPermissions(Permission::all());

        // Coach limited permissions
        $coach->syncPermissions([
            'create_player',
            'create_team',
            'create_score',
            'edit_score'
        ]);

        // Assign admin role to specific user
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        if ($adminUser) {
            $adminUser->assignRole($admin); 
        }
    }
}