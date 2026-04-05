<?php 
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $coach = Role::firstOrCreate(['name' => 'coach']);

        // Assign all permissions to admin
        $admin->syncPermissions(Permission::all());

        // coach limited permissions
        $coach->syncPermissions([
            'create_player',
            'create_team',
            'create_score',
            'edit_score'
        ]);
    }
}