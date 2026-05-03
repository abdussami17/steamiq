<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function store(Request $request)
    {
        // Validate admin input (human-friendly name)
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name'
        ]);
    
        // Human-friendly label jo admin type kare
        $label = $request->name;
    
        // Backend-safe slug (snake_case)
        $slug = Str::snake($label); // Example: "Create Event" => "create_event"
    try {
      // Create permission
      Permission::create([
        'name' => $slug,
       'label' => $label  
    ]);
    return redirect()->back()->with('success', 'Permission Created Successfully');
    } catch (PermissionAlreadyExists $e) {
        return redirect()->back()->with('error', 'Permission "' . $request->name . '" already exists.');
    }
        
    

    }


    public function update(Request $request, $id)
    {
        try {
            $permission = Permission::findOrFail($id);

            // Validate the input
            $request->validate([
                'name' => 'required|string|max:255|unique:permissions,name,' . $id
            ]);

            // Update label (human-friendly name)
            $label = $request->name;

            // Update the permission
            $permission->update([
                'label' => $label
            ]);

            return redirect()->back()->with('success', 'Permission updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $permission = Permission::findOrFail($id);

            // Remove permission from all roles and users automatically (Spatie handles via pivot tables)
            $permission->delete();

            return redirect()->back()->with('success', 'Permission deleted successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}