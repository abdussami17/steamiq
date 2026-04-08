<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\SubGroup;
use Illuminate\Http\Request;

class SubGroupController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'group_id' => 'required|exists:groups,id',

        ]);
    
        SubGroup::create($data);
    
        return redirect()->back()->with('active_tab', 'teams-tab')->with('success', 'SubGroup created successfully!');
    }
    public function destroy(SubGroup $subgroup)
    {
        $subgroup->delete();
    
        return back()->with('success', 'Sub Group deleted successfully.');
    }
    public function show($id)
    {
        $subgroup = SubGroup::find($id);
    
        if (!$subgroup) {
            return response()->json(['error' => 'SubGroup not found'], 404);
        }
    
        // Make sure to return the correct field names
        return response()->json([
            'id' => $subgroup->id,
            'name' => $subgroup->name,           // This should match 'name' in your DB
            'subgroup_name' => $subgroup->name,  // Alternative field name for compatibility
            'group_id' => $subgroup->group_id,

        ]);
    }

    public function update(Request $request, SubGroup $subgroup)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'group_id' => 'required|exists:groups,id',
  
        ]);

        $subgroup->update([
            'name' => $request->name,
            'group_id' => $request->group_id,
  
        ]);

        return redirect()->back()->with('success', 'Sub Group updated successfully.');
    }

    public function getGroupByOrganization($orgId)
{
    $groups = Group::where('organization_id', $orgId)
        ->get(['id', 'group_name']);

    return response()->json($groups);
}
}
