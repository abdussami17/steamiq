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
            'team_id'    => 'required|exists:teams,id',
        ]);
    
        Group::create([
            'group_name' => $request->group_name,
            'team_id'    => $request->team_id,
        ]);
    
        return back()->with('success', 'Group created successfully.');
    }


    public function destroy(Group $group)
{
    $group->delete();

    return back()->with('success', 'Group deleted successfully.');
}

}
