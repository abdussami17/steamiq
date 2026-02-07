<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\LeaderboardController;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Exports\LeaderboardExport;


Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/', [AuthController::class, 'registerLoginPage'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
   
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::middleware('admin')->group(function () {
      
        Route::get('/players', [PlayerController::class, 'index'])->name('player.index');
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
        Route::post('/add-player', [PlayerController::class, 'store'])->name('player.store');
        Route::post('/teams/store', [TeamController::class, 'store'])->name('teams.store');
        Route::post('/events/store', [EventController::class, 'store'])->name('events.store');
        Route::post('/challenges/store', [ChallengeController::class, 'store'])->name('challenges.store');
        Route::post('/scores/store', [ScoreController::class,'store'])->name('scores.store');
        Route::get('/events/{event}/players', [TeamController::class, 'playersByEvent'])->name('teams.get_players');
        Route::get('/event/{event}/leaderboard', [PlayerController::class, 'getPlayersLeaderboard'])
     ->name('players.leaderboard');
     Route::post('/players/import', [PlayerController::class,'import'])->name('players.import');
     Route::get('/players/{player}/edit', [PlayerController::class,'edit'])->name('players.edit');
     Route::post('/players/{player}/update', [PlayerController::class,'update'])->name('players.update');
     Route::delete('/players/{player}', [PlayerController::class,'destroy'])->name('players.destroy');
     
    
     Route::get('/events/{event}/teams', function(\App\Models\Event $event) {
        $teams = \App\Models\Team::where('event_id', $event->id)->get(['id','team_name']);
        return response()->json(['teams' => $teams]);
    })->name('events.teams');
    Route::get('/events/{event}', [EventController::class,'show'])->name('events.show');
// Route to fetch all events for the dropdown
Route::get('/leaderboard-events', [LeaderboardController::class, 'events'])
    ->name('leaderboard.events');

// Route to fetch leaderboard data for a specific event
Route::get('/leaderboard-data', [LeaderboardController::class, 'data'])
    ->name('leaderboard.data');
    
    Route::get('/leaderboard-export', function(Request $request){
        $eventId = $request->input('event_id'); // <-- safe
        if(!$eventId || $eventId === '-- Select Event --') {
            return redirect()->back()->with('error', 'Please select a valid event.');
        }
    
        return Excel::download(new LeaderboardExport($eventId), 'leaderboard.xlsx');
    });


    Route::get('/teams-export', [TeamController::class, 'export'])->name('teams.export');
Route::post('/teams-import', [TeamController::class, 'import'])->name('teams.import');
Route::get('/view/{team}', [TeamController::class, 'view'])->name('teams.view');
Route::post('/update/{team}', [TeamController::class, 'update'])->name('teams.update');
Route::delete('/delete/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
Route::get('/teams-data', [TeamController::class, 'teamsData'])->name('teams.data');


Route::get('/scores-data', [ScoreController::class, 'scoresData'])->name('scores.data');
Route::get('/scores/view/{score}', [ScoreController::class, 'view'])->name('scores.view');
Route::post('/scores/update/{score}', [ScoreController::class, 'update'])->name('scores.update');
Route::delete('/scores/delete/{score}', [ScoreController::class, 'destroy'])->name('scores.destroy');
    });
});
