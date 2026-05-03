<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array'
        ], [
            'name.required' => 'Role name is required',
            
        ]);

        try {
            // Check if role already exists
            if (Role::where('name', $request->name)->exists()) {
                return redirect()->back()->with('error', 'Role already exists!');
            }

            // Create role
            $role = Role::create(['name' => $request->name]);

            // Sync permissions if provided
            if ($request->permissions) {
                $validPermissions = Permission::whereIn('name', $request->permissions)->pluck('name')->toArray();
                $role->syncPermissions($validPermissions);
            }

            return redirect()->back()->with('success', 'Role Created Successfully');

        } catch (QueryException $e) {
            // Catch database errors (e.g. duplicate entry)
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Catch all other errors
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
    public function update(Request $request, $id)
{
    try {
        $role = Role::findOrFail($id);

        // Guard admin role
        if($role->name === 'admin') {
            return redirect()->back()->with('error', 'Admin role cannot be edited.');
        }

        // Validate
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array'
        ]);

        // Update name
        $role->name = $request->name;
        $role->save();

        // Sync permissions
        $validPermissions = $request->permissions ? Permission::whereIn('name', $request->permissions)->pluck('name')->toArray() : [];
        $role->syncPermissions($validPermissions);

        return redirect()->back()->with('success', 'Role updated successfully.');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}



public function destroy($id)
{
    try {
        return DB::transaction(function () use ($id) {

            $role = Role::findOrFail($id);

            if ($role->name === 'admin') {
                return back()->with('error', 'Admin role cannot be deleted.');
            }

            User::role($role->name)->get()->each(function ($user) use ($role) {
                $user->removeRole($role->name);
            });

            $role->syncPermissions([]);
            $role->delete();

            return back()->with('success', 'Role deleted successfully.');
        });

    } catch (\Throwable $e) {
        return back()->with('error', 'Something went wrong.');
    }
}
public function bulkDelete(Request $request)
{
    $ids = $request->ids;

    if (!is_array($ids) || empty($ids)) {
        return response()->json(['success' => false]);
    }

    // Never delete admin role
    Role::whereIn('id', $ids)
        ->where('name', '!=', 'admin')
        ->delete();

    return response()->json(['success' => true]);
}
}