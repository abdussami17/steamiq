<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardAssignment;
use Illuminate\Http\Request;

use App\Models\Score;
use App\Models\Team;
use App\Models\Student;
use App\Models\Group;
use Illuminate\Support\Facades\DB;


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
        $request->validate([
            'assignable_type' => 'required|in:organization,group,team,student',
            'assignable_id'   => 'required|exists:' . $this->getTableName($request->assignable_type) . ',id',
            'card_id'         => 'required|exists:cards,id',
        ]);
    
        $card = Card::findOrFail($request->card_id);
    
        $exists = CardAssignment::where('assignable_id', $request->assignable_id)
            ->where('assignable_type', $request->assignable_type)
            ->where('card_id', $request->card_id)
            ->exists();
    
        if ($exists) {
            return back()->with('error', 'This card is already assigned to the selected entity!');
        }
    
        DB::transaction(function () use ($request, $card) {
    
            // Create assignment
            CardAssignment::create([
                'assignable_id'   => $request->assignable_id,
                'assignable_type' => $request->assignable_type,
                'card_id'         => $request->card_id,
            ]);
    
            // Apply score effect
            $this->applyCardEffect($card, $request->assignable_type, $request->assignable_id);
        });
    
        return back()->with('success', 'Card assigned successfully!');
    }
    private function applyCardEffect($card, $type, $id)
    {
        $points = $card->type === 'red' ? 0 : -abs($card->negative_points);
    
        // Resolve affected IDs
        $teamIds = [];
        $studentIds = [];
    
        if ($type === 'organization') {
    
            $teamIds = Team::whereIn('group_id', function ($q) use ($id) {
                $q->select('id')->from('groups')->where('organization_id', $id);
            })->pluck('id')->toArray();
    
            $studentIds = Student::whereIn('team_id', $teamIds)->pluck('id')->toArray();
        }
    
        elseif ($type === 'group') {
    
            $teamIds = Team::where('group_id', $id)->pluck('id')->toArray();
            $studentIds = Student::whereIn('team_id', $teamIds)->pluck('id')->toArray();
        }
    
        elseif ($type === 'team') {
    
            $teamIds = [$id];
            $studentIds = Student::where('team_id', $id)->pluck('id')->toArray();
        }
    
        elseif ($type === 'student') {
    
            $studentIds = [$id];
        }
    
        // ✅ APPLY EFFECT
    
        if ($card->type === 'red') {
    
            // set ALL related scores to 0
            if (!empty($teamIds)) {
                Score::whereIn('team_id', $teamIds)->update(['points' => 0]);
            }
    
            if (!empty($studentIds)) {
                Score::whereIn('student_id', $studentIds)->update(['points' => 0]);
            }
    
        } else {
    
            // deduct points
            if (!empty($teamIds)) {
                Score::whereIn('team_id', $teamIds)
                    ->update([
                        'points' => DB::raw("points + ($points)")
                    ]);
            }

            if (!empty($studentIds)) {
                Score::whereIn('student_id', $studentIds)
                    ->update([
                        'points' => DB::raw("points + ($points)")
                    ]);
            }
        }
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