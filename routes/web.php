<?php

use App\Exports\LeaderboardExport;
use App\Http\Controllers\AssignBonusController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardAssignApiController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ChallengeActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\RosterController;
use App\Http\Controllers\RosterStudentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScoreboardController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubGroupController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

// =============================================================================
// UTILITY
// =============================================================================

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return 'Cache Cleared!';
});

// =============================================================================
// PUBLIC — QR CHECK-IN (No auth required — scanned by mobile devices in field)
// MUST be defined BEFORE any wildcard or auth-wrapped roster routes
// =============================================================================

// GET  /checkin  → renders the check-in blade page (shows spinner → result)
Route::get('/checkin', [RosterController::class, 'checkinPage'])->name('checkin.page');

// POST /checkin  → processes the check-in AJAX request from the blade page
Route::post('/checkin', [RosterController::class, 'checkin'])->name('checkin');

// =============================================================================
// GUEST ROUTES (Unauthenticated only — redirects away if already logged in)
// =============================================================================

Route::middleware('guest')->group(function () {
    Route::get('/sign-in',   [AuthController::class, 'registerLoginPage'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// =============================================================================
// PUBLIC ROUTES (No login required — read-only data / display pages)
// =============================================================================

Route::get('/',                [DashboardController::class,  'index'])->name('dashboard.index');
Route::get('/scoreboard',      [ScoreboardController::class, 'index'])->name('scoreboard.index');
Route::get('/scoreboard/data', [ScoreboardController::class, 'getData'])->name('scoreboard.data');

Route::get('/bracket',                   [EventController::class, 'bracketBoard'])->name('bracket.index');
Route::get('/events/{event}/bracket',    [EventController::class, 'bracket'])->name('events.bracket');

Route::get('/leaderboard/top-teams',     [LeaderboardController::class, 'fetchTopThreeTeams'])->name('leaderboard.fetchTopThreeTeams');
Route::get('/leaderboard/top-players',   [LeaderboardController::class, 'fetchTopThreePlayers'])->name('leaderboard.fetchTopThreePlayers');
Route::get('/leaderboard-event',         [LeaderboardController::class, 'events'])->name('leaderboard.events');
Route::get('/leaderboard-datas',         [LeaderboardController::class, 'data'])->name('leaderboard.data');

// =============================================================================
// AUTHENTICATED ROUTES
// =============================================================================

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // =========================================================================
    // ADMIN ROUTES
    // =========================================================================

    Route::middleware('admin')->group(function () {

        // ---------------------------------------------------------------------
        // Bonus
        // ---------------------------------------------------------------------
        Route::post('/assign-bonus', [AssignBonusController::class, 'store'])->name('bonus.assign.store');

        // ---------------------------------------------------------------------
        // Score Management
        // ---------------------------------------------------------------------
        Route::get('/scoring',              [ScoreController::class, 'index'])->name('scoring.index');
        Route::post('/scores',              [ScoreController::class, 'store'])->name('scores.store');
        Route::post('/scores/create',       [ScoreController::class, 'store']);                          // backward-compat alias
        Route::post('/scores/update-by-id', [ScoreController::class, 'updateById'])->name('scores.updateById');
        Route::get('/scores/existing',      [ScoreController::class, 'getExistingScore'])->name('scores.existing');
        Route::get('/leaderboard-events',   [ScoreController::class, 'events'])->name('scores.events');
        Route::get('/leaderboard-data',     [ScoreController::class, 'data'])->name('scores.data');

        Route::get('/leaderboard-export', function (Request $request) {
            $eventId = $request->input('event_id');
            if (! $eventId || $eventId === '-- Select Event --') {
                return redirect()->back()->with('error', 'Please select a valid event.');
            }
            return Excel::download(new LeaderboardExport($eventId), 'leaderboard.xlsx');
        });

        // ---------------------------------------------------------------------
        // API — Ajax dropdown helpers (score modals)
        // ---------------------------------------------------------------------
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/events/{event}/students',   [ScoreController::class, 'getEventStudents'])->name('events.students');
            Route::get('/events/{event}/teams',      [ScoreController::class, 'getEventTeams'])->name('events.teams');
            Route::get('/events/{event}/activities', [ScoreController::class, 'getEventActivities'])->name('events.activities');
            Route::get('/steam-categories',          [ScoreController::class, 'getSteamCategories'])->name('steam.categories');
            Route::get('/teams/{team}/students',     [ScoreController::class, 'getTeamStudents'])->name('teams.students');

            // Card assign helpers
            Route::get('organizations/list',         [CardAssignApiController::class, 'organizations'])->name('organizations.list');
            Route::get('groups/list',                [CardAssignApiController::class, 'groups'])->name('groups.list');
            Route::get('teams/list',                 [CardAssignApiController::class, 'teams'])->name('teams.list');
            Route::get('teams/by-group/{group}',     [CardAssignApiController::class, 'teamsByGroup']);
            Route::get('students/by-team/{team}',    [CardAssignApiController::class, 'studentsByTeam']);
        });

        // ---------------------------------------------------------------------
        // Hierarchy dropdowns (used by modals)
        // ---------------------------------------------------------------------
        Route::get('/events/{event}/organizations',  [ScoreController::class, 'getEventOrganizations']);
        Route::get('/organizations/{id}/groups',     [ScoreController::class, 'getOrganizationGroups']);
        Route::get('/groups/{id}/subgroups',         [ScoreController::class, 'getGroupSubgroups']);
        Route::get('/students',                      [ScoreController::class, 'getFilteredStudents']);  // ?group_id=&sub_group_id=
        Route::get('/teams',                         [ScoreController::class, 'getFilteredTeams']);     // ?group_id=&sub_group_id=
        Route::get('/team/{team}/students',          [ScoreController::class, 'getTeamStudents']);

        // ---------------------------------------------------------------------
        // User Management
        // ---------------------------------------------------------------------
        Route::get('/users',           [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}',      [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}',   [SettingController::class, 'destroyUser'])->name('setting.users.destroy');
        Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulkDelete');

        // ---------------------------------------------------------------------
        // Roles & Permissions
        // ---------------------------------------------------------------------
        Route::post('/roles',              [RoleController::class,       'store'])->name('roles.store');
        Route::put('/roles/{id}',          [RoleController::class,       'update'])->name('roles.update');
        Route::delete('/roles/{id}',       [RoleController::class,       'destroy'])->name('roles.destroy');
        Route::post('/roles/bulk-delete',  [RoleController::class,       'bulkDelete'])->name('roles.bulkDelete');
        Route::post('/permissions',        [PermissionController::class,  'store'])->name('permissions.store');
        Route::delete('/permissions/{id}', [PermissionController::class,  'destroy'])->name('permissions.destroy');

        // ---------------------------------------------------------------------
        // Student / Player Management
        // ---------------------------------------------------------------------
        Route::get('/student',                    [StudentController::class, 'index'])->name('student.index');
        Route::post('/student/store',             [StudentController::class, 'store'])->name('student.store');
        Route::post('/players/import',            [StudentController::class, 'import'])->name('student.import');
        Route::get('/players/{player}/edit',      [StudentController::class, 'edit'])->name('student.edit');
        Route::post('/players/{player}/update',   [StudentController::class, 'update'])->name('student.update');
        Route::delete('/player-destroy/{player}', [StudentController::class, 'destroy'])->name('student.destroy');
        Route::post('/players/bulk-delete',       [StudentController::class, 'bulkDelete'])->name('students.bulk.delete');

        // ---------------------------------------------------------------------
        // Team Management
        // ---------------------------------------------------------------------
        Route::get('/teams/export', [TeamController::class, 'export'])
        ->name('teams.export');
        Route::post('/teams/store',          [TeamController::class, 'store'])->name('teams.store');
        Route::get('/teams-data',            [TeamController::class, 'teamsData'])->name('teams.data');
        Route::get('/view/{team}',           [TeamController::class, 'view'])->name('teams.view');
        Route::get('/teams/{team}',          [TeamController::class, 'edit'])->name('teams.edit');
        Route::post('/teams/update/{team}',  [TeamController::class, 'update'])->name('teams.update');
        Route::delete('/delete/{team}',      [TeamController::class, 'destroy'])->name('teams.destroy');

        Route::post('/teams-import',         [TeamController::class, 'import'])->name('teams.import');
        Route::post('/teams/bulk-delete',    [TeamController::class, 'bulkDelete'])->name('teams.bulkDelete');
        Route::get('/teams/import/template', [TeamController::class, 'importTemplate'])->name('teams.import.template');
        Route::get('/teams/list',            [TeamController::class, 'listTeam'])->name('teams.list');
        Route::get('/organizations/list',    [TeamController::class, 'list']);
        Route::get('/get-groups/{orgId}',    [TeamController::class, 'getGroups']);
        Route::get('/get-teams/{groupId}',   [TeamController::class, 'getTeams']);

        // ---------------------------------------------------------------------
        // Event Management
        // ---------------------------------------------------------------------
        Route::get('/events',                                              [EventController::class, 'index'])->name('events.index');
        Route::post('/events/store',                                       [EventController::class, 'store'])->name('events.store');
        Route::post('/events/bulk-delete',                                 [EventController::class, 'bulkDelete'])->name('events.bulkDelete');
        Route::get('/events/{event}',                                      [EventController::class, 'show'])->name('events.show');
        Route::get('/events/{event}/edit',                                 [EventController::class, 'edit'])->name('events.edit');
        Route::post('/events/{event}/update',                              [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}',                                   [EventController::class, 'destroy'])->name('events.destroy');
        Route::patch('/events/{event}/status',                             [EventController::class, 'updateStatus'])->name('events.updateStatus');
        Route::post('/events/{event}/duplicate',                           [EventController::class, 'duplicate'])->name('events.duplicate');
        Route::post('/events/{event}/bracket/init',                        [EventController::class, 'bracketInit'])->name('events.bracket.init');
        Route::post('/events/{event}/bracket/matches/{match}',             [EventController::class, 'bracketUpdateMatch'])->name('events.bracket.match.update');
        Route::get('/events/{event}/players',                              [TeamController::class,  'playersByEvent'])->name('teams.get_players');
        Route::get('/events/{event}/teams',                                function (\App\Models\Event $event) {
            $teams = \App\Models\Team::where('event_id', $event->id)->get(['id', 'team_name']);
            return response()->json(['teams' => $teams]);
        })->name('events.teams');
        Route::get('/events/{event}/winner-teams',                         [EventController::class, 'getWinnerTeams']);
        Route::post('/events/{event}/set-winner',                          [EventController::class, 'setWinner']);
        Route::get('/events/{event}/results',                              [EventController::class, 'results']);
        Route::get('/event/{eventId}/organizations',                       [EventController::class, 'getOrganizations']);
        Route::get('/event/{event}/students-leaderboard',                  [StudentController::class, 'leaderboard']);

        // ---------------------------------------------------------------------
        // Tournament Management
        // ---------------------------------------------------------------------
        Route::get('/tournaments',                      [TournamentController::class, 'index'])->name('tournaments.index');
        Route::get('/tournaments/create',               [TournamentController::class, 'create'])->name('tournaments.create');
        Route::post('/tournaments',                     [TournamentController::class, 'store'])->name('tournaments.store');
        Route::post('/tournament-match/{match}/winner', [TournamentController::class, 'setWinner']);
        Route::post('/tournament-match/{match}/pin',    [TournamentController::class, 'generatePIN']);

        // ---------------------------------------------------------------------
        // Challenge Activity Management
        // ---------------------------------------------------------------------
        Route::post('/activities/store',               [ChallengeActivityController::class, 'store'])->name('activities.store');
        Route::get('/activities/{activity}',           [ChallengeActivityController::class, 'show']);
        Route::post('/activities/{activity}/update',   [ChallengeActivityController::class, 'update']);
        Route::delete('/activities/{activity}/delete', [ChallengeActivityController::class, 'destroy']);

        // ---------------------------------------------------------------------
        // Card Management
        // ---------------------------------------------------------------------
        Route::get('/cards',                            [CardController::class, 'index'])->name('cards.index');
        Route::post('/cards/store',                     [CardController::class, 'store'])->name('cards.store');
        Route::get('/cards/{card}/edit',                [CardController::class, 'edit'])->name('cards.edit');
        Route::post('/cards/{card}/update',             [CardController::class, 'update'])->name('cards.update');
        Route::delete('/cards/{card}/delete',           [CardController::class, 'destroy'])->name('cards.delete');
        Route::post('/card-assignments',                [CardController::class, 'assignCard'])->name('card.assignments.store');
        Route::delete('/card-assignments/{assignment}', [CardController::class, 'unassignCard'])->name('card.assignments.destroy');

        // ---------------------------------------------------------------------
        // Match Management
        // ---------------------------------------------------------------------
        Route::post('/matches',                   [MatchController::class, 'store'])->name('matches.store');
        Route::post('/matches/{id}/generate-pin', [MatchController::class, 'generatePin'])->name('matches.generatePin');
        Route::post('/matches/{id}/round',        [MatchController::class, 'addRound'])->name('matches.addRound');
        Route::get('/matches/fetch',              [MatchController::class, 'fetch'])->name('matches.fetch');
        Route::delete('/matches/{id}',            [MatchController::class, 'destroy'])->name('matches.destroy');
        Route::get('/matches/teams',              [MatchController::class, 'fetchTeams'])->name('matches.teams');
        Route::post('/matches/{match}/round',     [MatchController::class, 'addRound'])->name('matches.round');
        Route::get('/matches/export/all',         [MatchController::class, 'exportAllSchedule'])->name('matches.export.all');

        // ---------------------------------------------------------------------
        // Organization Management
        // ---------------------------------------------------------------------
        Route::post('/organization/store',              [OrganizationController::class, 'store'])->name('organizations.store');
        Route::post('/organizations/update/{id}',       [OrganizationController::class, 'update'])->name('organizations.update');
        Route::delete('/organizations/{organization}',  [OrganizationController::class, 'destroy'])->name('organizations.destroy');
        Route::post('/organizations/bulk-delete',       [OrganizationController::class, 'bulkDelete'])->name('organizations.bulkDelete');

        // ---------------------------------------------------------------------
        // Group Management
        // ---------------------------------------------------------------------
        Route::post('/groups',               [GroupController::class, 'store'])->name('groups.store');
        Route::post('/groups/update/{id}',   [GroupController::class, 'update'])->name('groups.update');
        Route::delete('/groups/{group}',     [GroupController::class, 'destroy'])->name('groups.destroy');
        Route::get('/groups/{group}/subgroups', [GroupController::class, 'subgroups']);
        Route::post('/groups/bulk-delete',   [GroupController::class, 'bulkDelete'])->name('groups.bulkDelete');
        Route::get('/organization/{orgId}/groups', [GroupController::class, 'getByOrganization']);

        // ---------------------------------------------------------------------
        // SubGroup Management
        // ---------------------------------------------------------------------
        Route::post('/subgroups/store',            [SubGroupController::class, 'store'])->name('subgroups.store');
        Route::delete('/subgroups/{subgroup}',     [SubGroupController::class, 'destroy'])->name('subgroups.destroy');
        Route::get('/subgroup/fetch/{id}',         [SubGroupController::class, 'show'])->name('subgroup.fetch');
        Route::put('/subgroup/update/{subgroup}',  [SubGroupController::class, 'update'])->name('subgroup.update');
        Route::get('/get-org-groups/{orgId}',      [SubGroupController::class, 'getGroupByOrganization']);
        Route::post('/subgroups/bulk-delete',      [SubGroupController::class, 'bulkDelete'])->name('subgroups.bulkDelete');

        // ---------------------------------------------------------------------
        // Leaderboard (admin view)
        // ---------------------------------------------------------------------
        Route::get('/leaderboard',                 [LeaderboardController::class, 'index'])->name('leaderboard.index');
        Route::post('/score/update-inline',        [StudentController::class, 'updateScoreInline']);

        // ---------------------------------------------------------------------
        // Settings
        // ---------------------------------------------------------------------
        Route::get('/settings',                   [SettingController::class, 'index'])->name('settings.index');
        Route::post('/profile/update',            [SettingController::class, 'updateProfile'])->name('profile.update');
        Route::get('/settings/activities/fetch',  [SettingController::class, 'fetchChallengeActivities'])->name('settings.activities.fetch');

        // =====================================================================
        // ROSTER MANAGEMENT (Phase 1 + Phase 2)
        // Static/named routes MUST come before wildcard {roster} routes
        // =====================================================================
        Route::prefix('rosters')->name('rosters.')->group(function () {

            // ------------------------------------------------------------------
            // Static routes (no wildcard) — defined FIRST
            // ------------------------------------------------------------------

            // Roster listing page (UI)
            Route::get('/',        [RosterController::class, 'index'])->name('index');

            // Inside the rosters prefix group, after existing static routes:
Route::get('/export/game-cards', [RosterController::class, 'exportGameCards'])->name('export.game-cards');
            // AJAX: fetch roster list data
            Route::get('/list',    [RosterController::class, 'list'])->name('list');
            Route::get('/roster/sample-template', [RosterController::class, 'download'])
            ->name('sample.template');
            // Import roster (Excel / CSV upload)
            Route::post('/import', [RosterController::class, 'import'])->name('import');

            // ------------------------------------------------------------------
            // Wildcard routes — defined AFTER static routes
            // ------------------------------------------------------------------

            // View single roster detail (AJAX)
            Route::get('/{roster}',                  [RosterController::class, 'show'])->name('show');

            // Generate field packet: PDF + QR  (status: draft → ready)
            Route::post('/{roster}/generate-packet', [RosterController::class, 'generateFieldPacket'])->name('generate-packet');

            // Fetch QR code data for modal viewer
            Route::get('/{roster}/qr',               [RosterController::class, 'showQr'])->name('qr');
        });

        // ---------------------------------------------------------------------
        // Roster Student Management
        // ---------------------------------------------------------------------
        Route::prefix('roster-students')->name('roster-students.')->group(function () {

            // Update a single student's attendance status
            Route::patch('/{rosterStudent}/attendance', [RosterStudentController::class, 'updateAttendance'])->name('attendance');
        });

    }); // end admin middleware

}); // end auth middleware