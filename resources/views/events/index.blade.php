

@extends('layouts.app')
@section('title', 'Events - SteamIQ')


@section('content')
<div class="container">
    <!-- Active Events -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="icon">
                    <i data-lucide="trophy"></i>
                </span>
                Events Management
            </h2>
            <button class="btn btn-primary" data-bs-target="#createEventModal" data-bs-toggle="modal">
                <i data-lucide="plus"></i>
                <span>Create New Event</span>
            </button>
        </div>
        
        <div class="card-grid">
            @forelse($allevents as $allevent)
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title">{{ $allevent->name }}</h3>
                            <p style="color: var(--text-dim); font-size: 0.9rem; margin-top: 0.25rem;">
                                {{ ucfirst($allevent->event_type) }}
                            </p>
                        </div>
                        <span class="badge badge-{{ $allevent->status }}">{{ ucfirst($allevent->status) }}</span>
                    </div>
                    <div class="stats-grid">
                        <div class="stat">
                            <div class="stat-label">Teams</div>
                            <div class="stat-value" style="font-size: 1.3rem;">
                                {{ $allevent->teams->count() ?: 'N/A' }}
                            </div>
                        </div>
                        <div class="stat">
                            <div class="stat-label">Players</div>
                            <div class="stat-value" style="font-size: 1.3rem;">
                                {{ $allevent->teams->sum(fn($t)=>$t->players->count()) ?: 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <div class="card-actions">
                        <button class="btn btn-primary" style="flex:1;"
                            onclick="openEventModal({{ $allevent->id }})">
                            View Event
                        </button>
                    </div>
                </div>
            @empty
                <p>No events available</p>
            @endforelse
        </div>

        
    </section>

    <!-- Event Operations Spreadsheet -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="icon">
                    <i data-lucide="clipboard-list"></i>
                </span>
                Event Operations
            </h2>
        </div>

        <div class="tabs">
            <button class="tab active" onclick="switchTab('teams')">Teams</button>
            <button class="tab" onclick="switchTab('scores')">Scores</button>
            <button class="tab" onclick="switchTab('schedule')">Schedule</button>
            <button class="tab" onclick="switchTab('challenges')">Challenges</button>

        </div>

        <!-- Teams Tab -->
        <div id="teams-tab" class="tab-content active">
            <div class="spreadsheet-container">
                <div class="spreadsheet-toolbar">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_team">
                        <i data-lucide="plus"></i> Add Team
                    </button>
                    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#importTeamsModal">
                        <i data-lucide="download"></i> Import CSV
                    </button>
                    <a href="{{ route('teams.export') }}" class="btn btn-secondary">
                        <i data-lucide="upload"></i> Export Teams
                    </a>
                    
                      
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Team ID</th>
                            <th>Team Name</th>
                            <th>Members</th>
                            <th>Total Points</th>
                            <th>Rank</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="teamsTableBody"></tbody>
                    @include('events.team-script')

                </table>
            </div>
        </div>

           <!-- Challenges Tab -->
           <div id="challenges-tab" class="tab-content">
            <div class="spreadsheet-container">
                <div class="spreadsheet-toolbar">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createChallengeModal">
                        <i data-lucide="plus"></i> Add Challenge
                    </button>
                  
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Challenge ID</th>
                            <th>Pillar Type</th>
                            <th>Name</th>
                            <th>Max Points</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" value="01" readonly></td>
                            <td><input type="text" value="Brain Games"></td>
                            <td>Basket Ball</td>
                           <td>
1200
                           </td>
                            <td>
                                <div style="display: flex; gap: 0.25rem;">
                                    <button class="btn btn-icon btn-view" onclick="viewTeamDetails('T001')" title="View">
                                        <i data-lucide="eye"></i>
                                    </button>
                                    <button class="btn btn-icon btn-edit" onclick="openTeamModal('edit', 'T001')" title="Edit">
                                        <i data-lucide="edit-2"></i>
                                    </button>
                                    <button class="btn btn-icon btn-delete" onclick="confirmDelete('team', 'T001', 'Team Alpha')" title="Delete">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                       
                      
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Scores Tab -->
        <div id="scores-tab" class="tab-content">
            <div class="spreadsheet-container">
                <div class="spreadsheet-toolbar">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scoreModal">
                        <i data-lucide="plus"></i> Add Score
                    </button>
                    <button class="btn btn-secondary">
                        <i data-lucide="download"></i> Bulk Import
                    </button>
                    <button class="btn btn-secondary">
                        <i data-lucide="refresh-cw"></i> Recalculate
                    </button>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>CAM Pillar</th>
                            <th>Category/Game</th>
                            <th>Points</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Alex Johnson</td>
                            <td>Brain Games</td>
                            <td><input type="text" value="Science"></td>
                            <td><input type="number" value="150"></td>
                            <td>2026-02-01</td>
                            <td>
                                <div style="display: flex; gap: 0.25rem;">
                                    <button class="btn btn-icon btn-edit" onclick="editScore('S001')" title="Edit">
                                        <i data-lucide="edit-2"></i>
                                    </button>
                                    <button class="btn btn-icon btn-delete" onclick="confirmDelete('score', 'S001', 'Science - 150pts')" title="Delete">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Maria Garcia</td>
                            <td>E-Gaming</td>
                            <td><input type="text" value="AquaBall Clash"></td>
                            <td><input type="number" value="200"></td>
                            <td>2026-02-01</td>
                            <td>
                                <div style="display: flex; gap: 0.25rem;">
                                    <button class="btn btn-icon btn-edit" onclick="editScore('S002')" title="Edit">
                                        <i data-lucide="edit-2"></i>
                                    </button>
                                    <button class="btn btn-icon btn-delete" onclick="confirmDelete('score', 'S002', 'AquaBall - 200pts')" title="Delete">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>James Chen</td>
                            <td>Playground Games</td>
                            <td><input type="text" value="Beach Balling"></td>
                            <td><input type="number" value="180"></td>
                            <td>2026-02-02</td>
                            <td>
                                <div style="display: flex; gap: 0.25rem;">
                                    <button class="btn btn-icon btn-edit" onclick="editScore('S003')" title="Edit">
                                        <i data-lucide="edit-2"></i>
                                    </button>
                                    <button class="btn btn-icon btn-delete" onclick="confirmDelete('score', 'S003', 'Beach Balling - 180pts')" title="Delete">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Schedule Tab -->
        <div id="schedule-tab" class="tab-content">
            <div class="spreadsheet-container">
                <div class="spreadsheet-toolbar">
                    <button class="btn btn-primary" onclick="openModal('matchModal')">
                        <i data-lucide="plus"></i> Schedule Match
                    </button>
                    <button class="btn btn-secondary">
                        <i data-lucide="shuffle"></i> Auto-Generate
                    </button>
                    <button class="btn btn-secondary">
                        <i data-lucide="upload"></i> Export Schedule
                    </button>
                </div>
                <div class="schedule-grid">
                    <div class="schedule-item">
                        <div class="schedule-time">
                            <div style="font-size: 0.8rem; color: var(--text-dim);">FEB 02</div>
                            <div>14:00</div>
                        </div>
                        <div class="schedule-match">
                            <div class="schedule-match-title">Match #1 - Semifinals</div>
                            <div class="schedule-match-teams">Team Alpha vs Team Beta</div>
                        </div>
                        <div class="schedule-actions">
                            <button class="btn btn-icon btn-edit" onclick="editMatch('M001')" title="Edit">
                                <i data-lucide="edit-2"></i>
                            </button>
                            <button class="btn btn-icon btn-delete" onclick="confirmDelete('match', 'M001', 'Match #1')" title="Delete">
                                <i data-lucide="trash-2"></i>
                            </button>
                            <button class="btn btn-primary" onclick="generateMatchPIN('M001')">PIN</button>
                        </div>
                    </div>
                    <div class="schedule-item">
                        <div class="schedule-time">
                            <div style="font-size: 0.8rem; color: var(--text-dim);">FEB 02</div>
                            <div>15:30</div>
                        </div>
                        <div class="schedule-match">
                            <div class="schedule-match-title">Match #2 - Semifinals</div>
                            <div class="schedule-match-teams">Team Gamma vs Team Delta</div>
                        </div>
                        <div class="schedule-actions">
                            <button class="btn btn-icon btn-edit" onclick="editMatch('M002')" title="Edit">
                                <i data-lucide="edit-2"></i>
                            </button>
                            <button class="btn btn-icon btn-delete" onclick="confirmDelete('match', 'M002', 'Match #2')" title="Delete">
                                <i data-lucide="trash-2"></i>
                            </button>
                            <button class="btn btn-primary" onclick="generateMatchPIN('M002')">PIN</button>
                        </div>
                    </div>
                    <div class="schedule-item">
                        <div class="schedule-time">
                            <div style="font-size: 0.8rem; color: var(--text-dim);">FEB 03</div>
                            <div>16:00</div>
                        </div>
                        <div class="schedule-match">
                            <div class="schedule-match-title">Championship Final</div>
                            <div class="schedule-match-teams">TBD vs TBD</div>
                        </div>
                        <div class="schedule-actions">
                            <button class="btn btn-icon btn-edit" onclick="editMatch('M003')" title="Edit">
                                <i data-lucide="edit-2"></i>
                            </button>
                            <button class="btn btn-icon btn-delete" onclick="confirmDelete('match', 'M003', 'Championship')" title="Delete">
                                <i data-lucide="trash-2"></i>
                            </button>
                            <button class="btn btn-primary" onclick="generateMatchPIN('M003')">PIN</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      
    </section>

    <!-- Leaderboard -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="icon">
                    <i data-lucide="award"></i>
                </span>
                Event Leaderboard
            </h2>
            <button class="btn btn-secondary" id="exportLeaderboard">Export Rankings</button>
        </div>
    
        <div class="form-group">
            <label class="form-label">Select Event <span class="text-danger">*</span></label>
            <select class="form-select" id="selectEvent">
                <option value="" hidden>--Select Event--</option>
            </select>
        </div>
    
        <div class="leaderboard" id="leaderboardBody">
            <div class="leaderboard-item"><div>Loading...</div></div>
        </div>
    </section>
    
    
    @include('events.leaderboard-script')
</div>
@endsection



@include('events.modals.create-team')
@include('events.modals.create-event')
@include('events.modals.create-challenge')
@include('events.modals.create-scores')
@include('events.modals.view-event')
@include('events.modals.import-team')
@include('events.modals.edit-team')
@include('events.modals.view-team')




