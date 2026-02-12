

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
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#importScoresModal">
                <i data-lucide="download"></i> Bulk Import
            </button>
            
            <button class="btn btn-secondary" onclick="fetchScores()">
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
            <tbody id="scoresTableBody"></tbody>
        </table>
    </div>
</div>


<div id="schedule-tab" class="tab-content">
    <div class="spreadsheet-container">
        <div class="spreadsheet-toolbar">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#matchModal">
                <i data-lucide="plus"></i> Schedule Match
            </button>
      
            <a href="{{ route('matches.export.all') }}" class="btn btn-secondary">
                <i data-lucide="upload"></i> Export All Schedule
            </a>
            
            
        </div>
        <div class="schedule-grid" id="scheduleGrid">
            <!-- Matches will be dynamically loaded here -->
        </div>
    </div>
</div>

      
    </section>


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
@include('events.modals.edit-score')
@include('events.modals.import-scores')
@include('events.modals.edit-challenge')
@include('events.modals.add-match')
@include('events.modals.match-pin')
@include('events.modals.add-round')
@include('events.score-script')
@include('events.challenge-script')
@include('events.matches-script')






