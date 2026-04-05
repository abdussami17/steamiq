<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardAssignment;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index()
    {
        $cards = Card::all();
        return view('card.index', compact('cards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:yellow,orange,red',
            'negative_points' => 'nullable|integer|min:0',
        ]);

        if(Card::where('type', $request->type)->exists()){
            return back()->with('error', 'Card of this type already exists.');
        }

        $points = $request->type === 'red' ? 0 : $request->negative_points;

        Card::create([
            'type' => $request->type,
            'negative_points' => $points,
        ]);

        return back()->with('success', 'Card added successfully.');
    }

    public function edit(Card $card)
    {
        return view('card.edit', compact('card'));
    }

    public function update(Request $request, Card $card)
    {
        $request->validate([
            'type' => 'required|in:yellow,orange,red',
            'negative_points' => 'nullable|integer|min:0',
        ]);

        if(Card::where('type', $request->type)->where('id', '!=', $card->id)->exists()){
            return back()->with('error', 'Another card with this type already exists.');
        }

        $points = $request->type === 'red' ? 0 : $request->negative_points;

        $card->update([
            'type' => $request->type,
            'negative_points' => $points,
        ]);

        return back()->with('success', 'Card updated successfully.');
    }

    public function destroy(Card $card)
    {
        $card->delete();
        return back()->with('success', 'Card deleted successfully.');
    }

public function assignCard(Request $request)
{
    // Validate input
    $request->validate([
        'assignable_type' => 'required|in:organization,group,team,student',
        'assignable_id'   => 'required|exists:' . $this->getTableName($request->assignable_type) . ',id',
        'card_id'         => 'required|exists:cards,id',
    ]);

    // Check if already assigned
    $exists = CardAssignment::where('assignable_id', $request->assignable_id)
        ->where('assignable_type', $request->assignable_type)
        ->where('card_id', $request->card_id)
        ->exists();

    if ($exists) {
        return redirect()->back()->with('error', 'This card is already assigned to the selected entity!');
    }

    // Create assignment
    CardAssignment::create([
        'assignable_id'   => $request->assignable_id,   
        'assignable_type' => $request->assignable_type,
        'card_id'         => $request->card_id,
    ]);

    return redirect()->back()->with('success', 'Card assigned successfully!');
}

private function getTableName($type)
{
    $map = [
        'organization' => 'organizations',
        'group' => 'groups',
        'team' => 'teams',
        'student' => 'students',
    ];
    
    return $map[$type] ?? $type;
}
}