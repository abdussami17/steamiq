<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BonusAssignment;

class AssignBonusController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'assignable_type' => 'required|string',
            'assignable_id'   => 'required|integer',
            'points'          => 'required|integer|min:1',
        ]);

        BonusAssignment::create([
            'assignable_type' => $request->assignable_type,
            'assignable_id'   => $request->assignable_id,
            'points'          => $request->points,
        ]);

        return redirect()->back()->with('success', 'Bonus assigned successfully!');
    }
}   