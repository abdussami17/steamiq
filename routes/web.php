<?php

use App\Exports\LeaderboardExport;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeActivityController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubGroupController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TournamentController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'registerLoginPage'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Admin routes
    Route::middleware('admin')->group(function () {

        // Players routes
        Route::get('/student', [StudentController::class, 'index'])->name('student.index');
        Route::post('/student/store', [StudentController::class, 'store'])->name('student.store');
        Route::post('/players/import', [PlayerController::class, 'import'])->name('players.import');
        Route::get('/players/{player}/edit', [PlayerController::class, 'edit'])->name('players.edit');
        Route::post('/players/{player}/update', [PlayerController::class, 'update'])->name('players.update');
        Route::delete('/players/{player}', [PlayerController::class, 'destroy'])->name('players.destroy');

        // Teams routes
        Route::post('/teams/store', [TeamController::class, 'store'])->name('teams.store');
        Route::get('/teams-data', [TeamController::class, 'teamsData'])->name('teams.data');
        Route::get('/view/{team}', [TeamController::class, 'view'])->name('teams.view');
        Route::get('/teams/{team}', [TeamController::class, 'edit'])->name('teams.edit'); // JSON for edit modal
        Route::post('/teams/update/{team}', [TeamController::class, 'update'])->name('teams.update'); // update handler
        Route::delete('/delete/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
        Route::get('/teams-export', [TeamController::class, 'export'])->name('teams.export');
        Route::post('/teams-import', [TeamController::class, 'import'])->name('teams.import');
        Route::post('/teams/bulk-delete', [TeamController::class, 'bulkDelete'])
        ->name('teams.bulkDelete');
        // Events routes
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::post('/events/store', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])
    ->name('events.destroy');
        Route::get('/events/{event}/players', [TeamController::class, 'playersByEvent'])->name('teams.get_players');
        Route::get('/events/{event}/teams', function (\App\Models\Event $event) {
            $teams = \App\Models\Team::where('event_id', $event->id)->get(['id', 'team_name']);
            return response()->json(['teams' => $teams]);
        })->name('events.teams');

        // Tournaments routes
        
        Route::get('/tournaments', [TournamentController::class,'index'])->name('tournaments.index');
        Route::get('/tournaments/create', [TournamentController::class,'create'])->name('tournaments.create');
        Route::post('/tournaments', [TournamentController::class,'store'])->name('tournaments.store');
        Route::post('/tournament-match/{match}/winner', [TournamentController::class,'setWinner']);
        Route::post('/tournament-match/{match}/pin', [TournamentController::class,'generatePIN']);
        // Challenges routes
        // Route::post('/challenges/store', [ChallengeController::class, 'store'])->name('challenges.store');
        // Route::get('/challenges/fetch', [ChallengeController::class, 'fetch'])->name('challenges.fetch');
        // Route::get('/challenges/edit/{challenge}', [ChallengeController::class, 'edit'])->name('challenges.edit');
        // Route::post('/challenges/update/{challenge}', [ChallengeController::class, 'update'])->name('challenges.update');
        // Route::delete('/challenges/delete/{challenge}', [ChallengeController::class, 'destroy'])->name('challenges.destroy');

        // Activity Routes
        Route::post('/activities/store', [ChallengeActivityController::class, 'store'])->name('activities.store');
        Route::get('/activities/{activity}', [ChallengeActivityController::class, 'show']);
        Route::post('/activities/{activity}/update', [ChallengeActivityController::class, 'update']);
        Route::delete('/activities/{activity}/delete', [ChallengeActivityController::class, 'destroy']);
  // Score routes
  Route::prefix('scores')->name('scores.')->group(function () {
    Route::post('/', [ScoreController::class, 'store'])->name('store');
});
Route::post('/scores/fetch', [App\Http\Controllers\ScoreController::class, 'fetchScores']);
Route::post('/scores/update', [App\Http\Controllers\ScoreController::class, 'updateScore']);
Route::post('/scores/bulk-update', [App\Http\Controllers\ScoreController::class, 'bulkUpdate']);

// API routes for AJAX dropdowns
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/events/{event}/students', [ScoreController::class, 'getEventStudents'])->name('events.students');
    Route::get('/events/{event}/teams', [ScoreController::class, 'getEventTeams'])->name('events.teams');
    Route::get('/events/{event}/activities', [ScoreController::class, 'getEventActivities'])->name('events.activities');
    Route::get('/steam-categories', [ScoreController::class, 'getSteamCategories'])->name('steam.categories');
});   
Route::post('/matches', [MatchController::class,'store'])->name('matches.store');
Route::post('/matches/{id}/generate-pin', [MatchController::class,'generatePin'])->name('matches.generatePin');
Route::post('/matches/{id}/round', [MatchController::class,'addRound'])->name('matches.addRound');
Route::get('/matches/fetch', [MatchController::class,'fetch'])->name('matches.fetch');
Route::delete('/matches/{id}', [MatchController::class,'destroy'])->name('matches.destroy');
Route::get('/matches/teams', [MatchController::class, 'fetchTeams'])->name('matches.teams');
// Add round
Route::post('/matches/{match}/round', [MatchController::class, 'addRound'])->name('matches.round');
Route::get('/matches/export/all', [MatchController::class, 'exportAllSchedule'])
    ->name('matches.export.all');



    // Organization Routes
    Route::post('/organization/store',[OrganizationController::class,'store'])->name('organizations.store');
    Route::delete('/organizations/{organization}', 
    [OrganizationController::class, 'destroy']
)->name('organizations.destroy');
Route::get('/organizations/list', [TeamController::class, 'list']);
// Update organization
Route::post('/organizations/update/{id}', [OrganizationController::class, 'update'])->name('organizations.update');
// Groupes Routes
Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
Route::post('/groups/store', [GroupController::class, 'store'])->name('groups.store');
Route::post('/groups/update/{id}', [GroupController::class, 'update'])->name('groups.update');
Route::get('/teams/list', [TeamController::class, 'listTeam'])->name('teams.list');
Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');

Route::post('/subgroups/store', [SubGroupController::class, 'store'])->name('subgroups.store');
Route::delete('/subgroups/{subgroup}', [SubGroupController::class, 'destroy'])->name('subgroups.destroy');
Route::get('subgroup/fetch/{id}', [SubGroupController::class, 'show'])->name('subgroup.fetch');
Route::put('subgroup/update/{subgroup}', [SubGroupController::class, 'update'])->name('subgroup.update'); // Update


        // Leaderboard routes
        Route::get('/leaderboard-events', [LeaderboardController::class, 'events'])->name('leaderboard.events');
        Route::get('/leaderboard-data', [LeaderboardController::class, 'data'])->name('leaderboard.data');
        Route::get('/event/{event}/students-leaderboard', [StudentController::class, 'leaderboard']);
        Route::post('/score/update-inline', [StudentController::class, 'updateScoreInline']);

        Route::get('/leaderboard-export', function (Request $request) {
            $eventId = $request->input('event_id');
            if (!$eventId || $eventId === '-- Select Event --') {
                return redirect()->back()->with('error', 'Please select a valid event.');
            }
            return Excel::download(new LeaderboardExport($eventId), 'leaderboard.xlsx');
        });
    });
});
