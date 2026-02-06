<?php
namespace App\Http\Controllers;

use App\Models\Challenges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChallengeController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id'     => 'required|exists:events,id',
                'pillar_type'  => 'required|in:brain,playground,egame',
                'name'         => 'required|string|max:255',
                'max_points'   => 'required|integer|min:1',
                'sub_category' => 'nullable|string|max:255',
            ]);

            $event = \App\Models\Event::find($validated['event_id']);
            if ($event->status === 'closed') {
                throw ValidationException::withMessages([
                    'event_id' => 'Cannot add challenge to a closed event.'
                ]);
            }

            $exists = Challenges::where('event_id', $validated['event_id'])
                ->where('name', $validated['name'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'name' => 'A challenge with this name already exists for the selected event.'
                ]);
            }

            if ($validated['pillar_type'] === 'brain') {
                if (empty($validated['sub_category']) || $validated['sub_category'] === '--Select Category--') {
                    throw ValidationException::withMessages([
                        'sub_category' => 'Sub-category is required for Brain challenges.'
                    ]);
                }
            } else {
                $validated['sub_category'] = null;
            }

            DB::transaction(function () use ($validated) {
                Challenges::create($validated);
            });

            return back()->with('success', 'Challenge created successfully');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong. Please try again.')->withInput();
        }
    }
}
