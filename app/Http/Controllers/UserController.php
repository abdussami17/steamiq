<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{




    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
    
        $request->validate([
            'role'        => 'nullable',
            'permissions' => 'nullable|array',
            'password'    => 'nullable|string|min:6|confirmed',
        ]);
    
        // Update basic fields
        $user->name = $request->name;
        $user->username = $request->username;
    
        // Update password only if entered
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
    
        $user->save();
    
        // Assign role
        if ($request->filled('role')) {

            // Assign selected role
            $user->syncRoles([$request->role]);
        
        } else {
        
            // Remove all roles
            $user->syncRoles([]);
        }
        // Assign direct permissions
        $user->syncPermissions($request->permissions ?? []);
    
        return redirect()->back()->with('success', 'User Updated Successfully');
    }
    public function bulkDelete(Request $request)
{
    $ids = $request->ids;

    if (!is_array($ids) || empty($ids)) {
        return response()->json(['success' => false]);
    }

    User::whereIn('id', $ids)
        ->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'admin');
        })
        ->delete();

    return response()->json(['success' => true]);
}
}