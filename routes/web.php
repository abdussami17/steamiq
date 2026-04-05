<?php

use App\Exports\LeaderboardExport;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ChallengeActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ScoreboardController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubGroupController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TournamentController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;

// =============================================================================
// GUEST ROUTES (Public Access)
// =============================================================================
Route::middleware('guest')->group(function () {
    Route::get('/sign-in', [AuthController::class, 'registerLoginPage'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Dashboard accessible to everyone
Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/leaderboard/top-teams', [LeaderboardController::class, 'fetchTopThreeTeams'])->name('leaderboard.fetchTopThreeTeams');
Route::get('/leaderboard/top-players', [LeaderboardController::class, 'fetchTopThreePlayers'])->name('leaderboard.fetchTopThreePlayers');
Route::post('/scores/update-by-name', [ScoreController::class, 'updateScoreByName'])->name('scores.updateByName');
Route::get('/leaderboard-events', [LeaderboardController::class, 'events'])->name('leaderboard.events');
Route::get('/leaderboard-data', [LeaderboardController::class, 'data'])->name('leaderboard.data');
// =============================================================================
// AUTHENTICATED ROUTES
// =============================================================================
Route::middleware('auth')->group(function () {
    // -------------------------------------------------------------------------
    // Authentication & Dashboard
    // -------------------------------------------------------------------------
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // =========================================================================
    // ADMIN ROUTES
    // =========================================================================
    Route::middleware('admin')->group(function () {
        // Roles
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');

        // Permissions
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');

        // ---------------------------------------------------------------------
        // Student / Player Management
        // ---------------------------------------------------------------------
        Route::get('/student', [StudentController::class, 'index'])->name('student.index');
        Route::post('/student/store', [StudentController::class, 'store'])->name('student.store');
        Route::post('/players/import', [PlayerController::class, 'import'])->name('players.import');
        Route::get('/players/{player}/edit', [PlayerController::class, 'edit'])->name('players.edit');
        Route::post('/players/{player}/update', [PlayerController::class, 'update'])->name('players.update');
        Route::delete('/players/{player}', [PlayerController::class, 'destroy'])->name('players.destroy');

        // ---------------------------------------------------------------------
        // Team Management
        // ---------------------------------------------------------------------
        Route::post('/teams/store', [TeamController::class, 'store'])->name('teams.store');
        Route::get('/teams-data', [TeamController::class, 'teamsData'])->name('teams.data');
        Route::get('/view/{team}', [TeamController::class, 'view'])->name('teams.view');
        Route::get('/teams/{team}', [TeamController::class, 'edit'])->name('teams.edit');
        Route::post('/teams/update/{team}', [TeamController::class, 'update'])->name('teams.update');
        Route::delete('/delete/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
        Route::get('/teams-export', [TeamController::class, 'export'])->name('teams.export');
        Route::post('/teams-import', [TeamController::class, 'import'])->name('teams.import');
        Route::post('/teams/bulk-delete', [TeamController::class, 'bulkDelete'])->name('teams.bulkDelete');
        Route::get('/teams/import/template', [App\Http\Controllers\TeamController::class, 'importTemplate'])->name('teams.import.template');
        Route::post('/teams/import', [App\Http\Controllers\TeamController::class, 'import'])->name('teams.import');
        // ---------------------------------------------------------------------
        // Event Management
        // ---------------------------------------------------------------------
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::get('/events/{event}/bracket', [EventController::class, 'bracket'])->name('events.bracket');
        Route::post('/events/store', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        Route::post('/events/{event}/duplicate', [EventController::class, 'duplicate'])->name('events.duplicate');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::post('/events/{event}/update', [EventController::class, 'update'])->name('events.update');
        Route::get('/events/{event}/players', [TeamController::class, 'playersByEvent'])->name('teams.get_players');
        Route::get('/events/{event}/teams', function (\App\Models\Event $event) {
            $teams = \App\Models\Team::where('event_id', $event->id)->get(['id', 'team_name']);
            return response()->json(['teams' => $teams]);
        })->name('events.teams');

        // ---------------------------------------------------------------------
        // Tournament Management
        // ---------------------------------------------------------------------
        Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
        Route::get('/tournaments/create', [TournamentController::class, 'create'])->name('tournaments.create');
        Route::post('/tournaments', [TournamentController::class, 'store'])->name('tournaments.store');
        Route::post('/tournament-match/{match}/winner', [TournamentController::class, 'setWinner']);
        Route::post('/tournament-match/{match}/pin', [TournamentController::class, 'generatePIN']);

        // ---------------------------------------------------------------------
        // Challenge Activity Management
        // ---------------------------------------------------------------------
        Route::post('/activities/store', [ChallengeActivityController::class, 'store'])->name('activities.store');
        Route::get('/activities/{activity}', [ChallengeActivityController::class, 'show']);
        Route::post('/activities/{activity}/update', [ChallengeActivityController::class, 'update']);
        Route::delete('/activities/{activity}/delete', [ChallengeActivityController::class, 'destroy']);

        // ---------------------------------------------------------------------
        // Score Management
        // ---------------------------------------------------------------------
        Route::prefix('scores')
            ->name('scores.')
            ->group(function () {
                Route::post('/', [ScoreController::class, 'store'])->name('store');
            });
        Route::get('/scoring', [ScoreController::class, 'index'])->name('scoring.index');
        Route::post('/scores/create', [ScoreController::class, 'store'])->name('scores.store');
        Route::get('/scores/existing', [ScoreController::class, 'getExistingScore'])->name('scores.existing');
        Route::post('/scores/fetch', [App\Http\Controllers\ScoreController::class, 'fetchScores']);
        Route::post('/scores/update', [App\Http\Controllers\ScoreController::class, 'updateScore']);
        Route::post('/scores/bulk-update', [App\Http\Controllers\ScoreController::class, 'bulkUpdate']);

        // ---------------------------------------------------------------------
        // Card Management
        // ---------------------------------------------------------------------
        Route::get('/cards', [CardController::class, 'index'])->name('cards.index');
        Route::post('/cards/store', [CardController::class, 'store'])->name('cards.store');
        Route::get('/cards/{card}/edit', [CardController::class, 'edit'])->name('cards.edit');
        Route::post('/cards/{card}/update', [CardController::class, 'update'])->name('cards.update');
        Route::delete('/cards/{card}/delete', [CardController::class, 'destroy'])->name('cards.delete');
        Route::post('/card-assignments', [CardController::class, 'assignCard'])->name('card.assignments.store');

        // ---------------------------------------------------------------------
        // API Routes (AJAX Dropdowns)
        // ---------------------------------------------------------------------
        Route::prefix('api')
            ->name('api.')
            ->group(function () {
                Route::get('/events/{event}/students', [ScoreController::class, 'getEventStudents'])->name('events.students');
                Route::get('/events/{event}/teams', [ScoreController::class, 'getEventTeams'])->name('events.teams');
                Route::get('/events/{event}/activities', [ScoreController::class, 'getEventActivities'])->name('events.activities');
                Route::get('/steam-categories', [ScoreController::class, 'getSteamCategories'])->name('steam.categories');
            });

        // ---------------------------------------------------------------------
        // Match Management
        // ---------------------------------------------------------------------
        Route::post('/matches', [MatchController::class, 'store'])->name('matches.store');
        Route::post('/matches/{id}/generate-pin', [MatchController::class, 'generatePin'])->name('matches.generatePin');
        Route::post('/matches/{id}/round', [MatchController::class, 'addRound'])->name('matches.addRound');
        Route::get('/matches/fetch', [MatchController::class, 'fetch'])->name('matches.fetch');
        Route::delete('/matches/{id}', [MatchController::class, 'destroy'])->name('matches.destroy');
        Route::get('/matches/teams', [MatchController::class, 'fetchTeams'])->name('matches.teams');
        Route::post('/matches/{match}/round', [MatchController::class, 'addRound'])->name('matches.round');
        Route::get('/matches/export/all', [MatchController::class, 'exportAllSchedule'])->name('matches.export.all');

        // ---------------------------------------------------------------------
        // Organization & Hierarchy Routes
        // ---------------------------------------------------------------------
        Route::get('/event/{eventId}/organizations', [EventController::class, 'getOrganizations']);
        Route::get('/organization/{orgId}/groups', [GroupController::class, 'getByOrganization']);

        Route::post('/organization/store', [OrganizationController::class, 'store'])->name('organizations.store');
        Route::delete('/organizations/{organization}', [OrganizationController::class, 'destroy'])->name('organizations.destroy');
        Route::post('/organizations/update/{id}', [OrganizationController::class, 'update'])->name('organizations.update');
        Route::get('/organizations/list', [TeamController::class, 'list']);

        // ---------------------------------------------------------------------
        // Group Management
        // ---------------------------------------------------------------------
        Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
        Route::post('/groups/store', [GroupController::class, 'store'])->name('groups.store');
        Route::post('/groups/update/{id}', [GroupController::class, 'update'])->name('groups.update');
        Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');
        Route::get('/groups/{group}/subgroups', [GroupController::class, 'subgroups']);
        Route::get('/teams/list', [TeamController::class, 'listTeam'])->name('teams.list');
        Route::get('/get-groups/{orgId}', [TeamController::class, 'getGroups']);
        Route::get('/get-teams/{groupId}', [TeamController::class, 'getTeams']);

        // ---------------------------------------------------------------------
        // SubGroup Management
        // ---------------------------------------------------------------------
        Route::post('/subgroups/store', [SubGroupController::class, 'store'])->name('subgroups.store');
        Route::delete('/subgroups/{subgroup}', [SubGroupController::class, 'destroy'])->name('subgroups.destroy');
        Route::get('subgroup/fetch/{id}', [SubGroupController::class, 'show'])->name('subgroup.fetch');
        Route::put('subgroup/update/{subgroup}', [SubGroupController::class, 'update'])->name('subgroup.update');

        // ---------------------------------------------------------------------
        // Filtered Data Routes (Hierarchy Dropdowns)
        // ---------------------------------------------------------------------
        Route::get('/events/{event}/organizations', [ScoreController::class, 'getEventOrganizations']);
        Route::get('/organizations/{id}/groups', [ScoreController::class, 'getOrganizationGroups']);
        Route::get('/groups/{id}/subgroups', [ScoreController::class, 'getGroupSubgroups']);

        Route::get('/students', [ScoreController::class, 'getFilteredStudents']);
        Route::get('/teams', [ScoreController::class, 'getFilteredTeams']);

        // ---------------------------------------------------------------------
        // Scoreboard Routes
        //
        Route::get('/scoreboard', [ScoreboardController::class, 'index'])->name('scoreboard.index');
        Route::get('/scoreboard/data', [ScoreboardController::class, 'getData'])->name('scoreboard.data');

        // ---------------------------------------------------------------------
        // Settings Routes
        //

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/profile/update', [SettingController::class, 'updateProfile'])->name('profile.update');
        Route::get('/settings/activities/fetch', [SettingController::class, 'fetchChallengeActivities'])->name('settings.activities.fetch');

        // ---------------------------------------------------------------------
        // Leaderboard Routes
        // ---------------------------------------------------------------------

        Route::get('/event/{event}/students-leaderboard', [StudentController::class, 'leaderboard']);
        Route::post('/score/update-inline', [StudentController::class, 'updateScoreInline']);
        Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

        // ---------------------------------------------------------------------
        // Leaderboard Export
        // ---------------------------------------------------------------------
        Route::get('/leaderboard-export', function (Request $request) {
            $eventId = $request->input('event_id');
            if (!$eventId || $eventId === '-- Select Event --') {
                return redirect()->back()->with('error', 'Please select a valid event.');
            }
            return Excel::download(new LeaderboardExport($eventId), 'leaderboard.xlsx');
        });
    });
});
