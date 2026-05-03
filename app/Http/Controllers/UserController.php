<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{




    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => 'required',
            'permissions' => 'nullable|array'
        ]);

        // Assign role
        $user->syncRoles([$request->role]);

        // Assign direct permissions
        $user->syncPermissions($request->permissions ?? []);

        return redirect()->back()->with('success','User Updated Successfully');
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