<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tournament;
use App\Models\TournamentParticipant;
use App\Models\TournamentMatch;
use App\Models\Team;

class TournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::with([
            'participants.team',
            'matches.teamA',
            'matches.teamB'
        ])->get();

        return view('tournament.index', compact('tournaments'));
    }

    public function create()
    {
        $events = \App\Models\Event::where('type','tournament')->get();
        return view('tournament.create', compact('events'));
    }

    public function store(Request $request)
    {
        $tournament = Tournament::create($request->only('name','type','scheduled_at','event_id'));

        // Assign participants
        foreach($request->team_ids as $index=>$teamId){
            TournamentParticipant::create([
                'tournament_id'=>$tournament->id,
                'team_id'=>$teamId,
                'seed'=>$index+1
            ]);
        }

        // Generate first round matches automatically
        $this->generateBracket($tournament);

        return redirect()->route('tournaments.index')->with('success','Tournament created!')->header('Content-Type', 'text/html');
    }

    private function generateBracket(Tournament $tournament)
    {
        $teams = $tournament->participants()->orderBy('seed')->pluck('team_id')->toArray();
        $numTeams = count($teams);

        $roundMatches = [];
        $i = 0;
        while($i < $numTeams){
            $teamA = $teams[$i];
            $teamB = $teams[$i+1] ?? null; // bye if odd
            TournamentMatch::create([
                'tournament_id'=>$tournament->id,
                'team_a_id'=>$teamA,
                'team_b_id'=>$teamB,
                'round_no'=>1,
                'status'=>'pending',
                'format'=>'single'
            ]);
            $i += 2;
        }
    }

    public function setWinner(Request $request, $matchId)
    {
        $match = TournamentMatch::findOrFail($matchId);
        $match->winner_team_id = $request->winner_team_id;
        $match->status = 'completed';
        $match->save();

        // TODO: advance winner to next round automatically

        return response()->json(['success'=>true]);
    }

    public function generatePIN($matchId)
    {
        $match = TournamentMatch::findOrFail($matchId);
    
        $match->pin = rand(1000,9999);
        $match->save();
    
        return response()->json([
            'success' => true,
            'pin' => $match->pin
        ]);
    }
    
    
    
}
