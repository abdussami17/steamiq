<?php

namespace App\Http\Controllers;

use App\Models\SubGroup;
use Illuminate\Http\Request;

class SubGroupController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'group_id' => 'required|exists:groups,id',
            'event_id' => 'required|exists:events,id',
        ]);
    
        SubGroup::create($data);
    
        return back()->with('success', 'Sub group created successfully');
    }
    public function destroy(SubGroup $subgroup)
    {
        $subgroup->delete();
    
        return back()->with('success', 'Sub Group deleted successfully.');
    }
    public function show($id)
    {
        $subgroup = SubGroup::with('group.event')->find($id);
    
        if (!$subgroup) {
            return response()->json(['error' => 'SubGroup not found'], 404);
        }
    
        // Make sure to return the correct field names
        return response()->json([
            'id' => $subgroup->id,
            'name' => $subgroup->name,           // This should match 'name' in your DB
            'subgroup_name' => $subgroup->name,  // Alternative field name for compatibility
            'group_id' => $subgroup->group_id,
            'event_id' => optional($subgroup->group->event)->id,  // Fixed: event comes from group
            'event_name' => optional($subgroup->group->event)->name,
        ]);
    }

    public function update(Request $request, SubGroup $subgroup)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'group_id' => 'required|exists:groups,id',
            'event_id' => 'nullable|exists:events,id',
        ]);

        $subgroup->update([
            'name' => $request->name,
            'group_id' => $request->group_id,
            'event_id' => $request->event_id,
        ]);

        return redirect()->back()->with('success', 'Sub Group updated successfully.');
    }
}
