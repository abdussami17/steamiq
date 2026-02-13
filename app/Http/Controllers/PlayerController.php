<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Event;
use App\Models\Player;
use App\Models\Scores;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;  
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class PlayerController extends Controller
{
    public function index()
    {
        $events = Event::where('status', 'live')
            ->select('id', 'name')
            ->get();
        return view('players.index', compact('events'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'player_name'  => 'required|string|max:255',
            'player_email' => 'required|email|unique:players,email',
            'assign_team'  => 'nullable|exists:teams,id',
            'event_id'     => 'nullable|exists:events,id',
    
            // NEW
            'profile'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        $profilePath = null;
    
        if ($request->hasFile('profile')) {
            $profilePath = $request->file('profile')
                                   ->store('players', 'public');
        }
    
        $player = Player::create([
            'name'     => $validated['player_name'],
            'email'    => $validated['player_email'],
            'event_id' => $validated['event_id'] ?? 1,
            'profile'  => $profilePath,
        ]);
    
        if (!empty($validated['assign_team'])) {
            TeamMember::create([
                'player_id' => $player->id,
                'team_id'   => $validated['assign_team'],
            ]);
        }
    
        return back()->with('success', 'Player added successfully!');
    }
    

    public function getPlayersLeaderboard($eventId)
    {
        $event = \App\Models\Event::findOrFail($eventId);

        $players = \App\Models\Player::with([
            'teams',
            'scores.challenge'
        ])->where('event_id', $eventId)->get();

        $result = $players->map(function($player) {
            $brain = $player->scores->where('challenge.pillar_type','brain')->sum('points');
            $playground = $player->scores->where('challenge.pillar_type','playground')->sum('points');
            $egaming = $player->scores->where('challenge.pillar_type','egame')->sum('points');
            $total = $brain + $playground + $egaming;

            return [
                'id' => $player->id,
                'name' => $player->name,
                'team' => $player->teams->first()?->team_name,
                'brain_points' => $brain,
                'playground_points' => $playground,
                'egaming_points' => $egaming,
                'total' => $total,
            ];
        });

        $sorted = $result->sortByDesc('total')->values();

        $rank = 1;
        foreach ($sorted as $player) {
            $player['rank'] = $rank++;
        }

        return response()->json($sorted);
    }

    public function import(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'file' => 'required|mimes:csv,xlsx,xls'
        ]);

        $eventId = $request->event_id;
        $rows = Excel::toArray([], $request->file('file'))[0];
        $header = array_map('strtolower', $rows[0]);
        unset($rows[0]);

        $total = 0;
        $inserted = 0;
        $duplicates = 0;
        $errors = 0;

        foreach ($rows as $row) {
            $rowData = array_combine($header, $row);
            $name = trim($rowData['name'] ?? '');
            $email = trim($rowData['email'] ?? '');

            if (!$name || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors++;
                continue;
            }

            if (Player::where('email', $email)->exists()) {
                $duplicates++;
                continue;
            }

            Player::create([
                'event_id' => $eventId,
                'name' => $name,
                'email' => $email
            ]);

            $inserted++;
            $total++;
        }

        return response()->json([
            'total' => $total,
            'inserted' => $inserted,
            'duplicates' => $duplicates,
            'errors' => $errors
        ]);
    }

    public function edit(Player $player)
    {
        $events = Event::where('status','live')->select('id','name')->get();
        $teams = Team::where('event_id', $player->event_id)->get();
        $playerTeam = $player->teams->first();

        return response()->json([
            'player' => [
                'id' => $player->id,
                'name' => $player->name,
                'email' => $player->email,
                'event_id' => $player->event_id,
                'team_id' => $playerTeam ? $playerTeam->id : null
            ],
            'events' => $events,
            'teams'  => $teams
        ]);
    }

    public function update(Request $request, Player $player)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:players,email,'.$player->id,
            'event_id' => 'required|exists:events,id',
            'team_id'  => 'nullable|exists:teams,id',
        ]);

        if(!empty($validated['team_id'])) {
            $team = Team::find($validated['team_id']);
            if($team->event_id != $validated['event_id']) {
                throw ValidationException::withMessages(['team_id' => 'Team must belong to same event']);
            }
        }

        DB::transaction(function() use($player, $validated){
            $player->update([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'event_id' => $validated['event_id'],
            ]);

            if(!empty($validated['team_id'])) {
                TeamMember::updateOrCreate(
                    ['player_id' => $player->id],
                    ['team_id' => $validated['team_id']]
                );
            } else {
                TeamMember::where('player_id', $player->id)->delete();
            }
        });

        return response()->json(['success' => true, 'message' => 'Player updated successfully']);
    }

    public function destroy(Player $player)
    {
        DB::transaction(function() use($player){
            TeamMember::where('player_id', $player->id)->delete();
            Scores::where('player_id', $player->id)->delete();
            $player->delete();
        });

        return response()->json(['success' => true, 'message' => 'Player deleted successfully']);
    }
}
