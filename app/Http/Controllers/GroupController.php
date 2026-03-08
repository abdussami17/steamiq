<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
            'organization_id'    => 'required|exists:organizations,id',
        ]);
    
        Group::create([
            'group_name' => $request->group_name,
            'organization_id'    => $request->organization_id,
        ]);
    
        return back()->with('success', 'Group created successfully.')->header('Content-Type', 'text/html');
    }
    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);
    
        $data = $request->validate([
            'group_name' => 'required|string|max:255',
            'organization_id'    => 'required|exists:organizations,id',
        ]);
    
        $group->update($data);
    
        return back()->with('success', 'Group updated successfully');
    }

    public function destroy(Group $group)
{
    $group->delete();

    return back()->with('success', 'Group deleted successfully.');
}

}
