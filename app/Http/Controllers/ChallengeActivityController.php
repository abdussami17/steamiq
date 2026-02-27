<?php

namespace App\Http\Controllers;

use App\Models\ChallengeActivity;
use Illuminate\Http\Request;

class ChallengeActivityController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id'    => 'required|exists:events,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ChallengeActivity::create($validated);

        return back()->with('success', 'Activity created successfully.');
    }

    // Get single activity (for edit)
public function show(ChallengeActivity $activity)
{
    return response()->json($activity);
}

// Update activity
public function update(Request $request, ChallengeActivity $activity)
{
    $validated = $request->validate([
        'event_id'    => 'required|exists:events,id',
        'name'        => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $activity->update($validated);

    return response()->json(['success' => true, 'message' => 'Activity updated successfully.']);
}

// Delete activity
public function destroy(ChallengeActivity $activity)
{
    $activity->delete();
    return response()->json(['success' => true, 'message' => 'Activity deleted successfully.']);
}
}
