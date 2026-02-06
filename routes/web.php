<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\ScoreController;


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
    

    });
});
