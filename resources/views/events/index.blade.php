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
                            <div class="d-flex ">
                                <span class="badge me-2 badge-{{ $allevent->status }}">
                                    {{ ucfirst($allevent->status) }}
                                </span>
            
                                <form action="{{ route('events.destroy', $allevent->id) }}" method="POST"
                                      style="display:inline; height:0"
                                      onsubmit="return confirm('Are you sure you want to delete this event?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                                        <i data-lucide="trash" class="text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
            
                        <div class="stats-grid">
            
                            @php
                                // Merge all teams under groups + subgroups and remove duplicates
                                $allTeams = $allevent->organizations
                                    ->flatMap->groups
                                    ->flatMap(function ($group) {
                                        return $group->teams->concat($group->subgroups->flatMap->teams);
                                    })
                                    ->unique('id');
                            @endphp
            
                            {{-- TEAMS --}}
                            <div class="stat">
                                <div class="stat-label">Teams</div>
                                <div class="stat-value" style="font-size:1.3rem;">
                                    {{ $allTeams->count() ?: 'N/A' }}
                                </div>
                            </div>
            
                            {{-- PLAYERS --}}
                            <div class="stat">
                                <div class="stat-label">Players</div>
                                <div class="stat-value" style="font-size:1.3rem;">
                                    {{ $allTeams->flatMap->students->count() ?: 'N/A' }}
                                </div>
                            </div>
            
                            {{-- GROUPS --}}
                            <div class="stat">
                                <div class="stat-label">Groups</div>
                                <div class="stat-value" style="font-size:1.3rem;">
                                    {{ $allevent->organizations->flatMap->groups->count() ?: 'N/A' }}
                                </div>
                            </div>
            
                            {{-- SUBGROUPS --}}
                            <div class="stat">
                                <div class="stat-label">Sub Groups</div>
                                <div class="stat-value" style="font-size:1.3rem;">
                                    {{ $allevent->organizations->flatMap->groups->flatMap->subgroups->count() ?: 'N/A' }}
                                </div>
                            </div>
            
                        </div>
            
                        <div class="card-actions">
                            <button class="btn btn-primary" style="flex:1;" onclick="openEventModal({{ $allevent->id }})">
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

            @php
                $activeTab = session('active_tab') ?? 'events-tab';
            @endphp

            <div class="tabs" id="eventTabs">
                <button class="tab {{ $activeTab == 'events-tab' ? 'active' : '' }}"
                    onclick="switchTab('events')">Events</button>
                <button class="tab {{ $activeTab == 'organizations-tab' ? 'active' : '' }}"
                    onclick="switchTab('organizations')">Organizations</button>
                <button class="tab {{ $activeTab == 'groups-tab' ? 'active' : '' }}"
                    onclick="switchTab('groups')">Groups</button>
                <button class="tab {{ $activeTab == 'subgroup-tab' ? 'active' : '' }}" onclick="switchTab('subgroup')">Sub
                    Group</button>
                <button class="tab {{ $activeTab == 'teams-tab' ? 'active' : '' }}"
                    onclick="switchTab('teams')">Teams</button>
                <button class="tab {{ $activeTab == 'scores-tab' ? 'active' : '' }}"
                    onclick="switchTab('scores')">Scores</button>
            </div>
            @if (session('active_tab'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const tabs = document.getElementById('eventTabs');
                        if (tabs) {
                            // scroll to the tabs area smoothly
                            tabs.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    });
                </script>
            @endif


            <!-- Events Tab -->
            <div id="events-tab" class="tab-content {{ $activeTab == 'events-tab' ? 'active show' : '' }}">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEventModal">
                            <i data-lucide="plus"></i> Add Event
                        </button>

                    </div>


                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>

                                <th>Event Name</th>
                                <th>Event Type</th>
                                <th>Location</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>

                                <th>Brain Game</th>
                                <th>Game Settings</th>
                                <th>Tournament</th>
                                <th>Activities</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($allevents as $allevent)
                                <tr>

                                    <td>
                                        {{ $allevent->id ?: 'N/A' }}
                                    </td>
                                    {{-- <td>{{ $allevent->organization->name ?: 'N/A' }}</td> --}}
                                    <td>{{ $allevent->name ?: 'N/A' }}</td>
                                    {{-- Name --}}
                                    <td>
                                        @if ($allevent->type === 'esports')
                                            STEAM ESports
                                        @elseif($allevent->type === 'xr')
                                            STEAM XR Sports
                                        @else
                                            STEAM {{ $allevent->type ?: 'N/A' }}
                                        @endif
                                    </td>
                                    <td>{{ $allevent->location ?: 'N/A' }}</td>
                                    <td>{{ $allevent->start_date ?: 'N/A' }}</td>
                                    <td>{{ $allevent->end_date ?: 'N/A' }}</td>
                                    <td>
                                        @php
                                            $status = $allevent->status;
                                            $map = [
                                                'live' => ['label' => 'LIVE', 'class' => 'badge-live'],
                                                'close' => ['label' => 'CLOSED', 'class' => 'badge-close'],
                                                'draft' => ['label' => 'DRAFT', 'class' => 'badge-draft'],
                                            ];
                                        @endphp

                                        @if ($status && isset($map[$status]))
                                            <span class="badge {{ $map[$status]['class'] }}">
                                                {{ $map[$status]['label'] }}
                                            </span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    {{-- Brain Game --}}
                                    <td>
                                        @if ($allevent->tournamentSetting && $allevent->tournamentSetting->brain_enabled)
                                            Type: {{ $allevent->tournamentSetting->brain_type }}<br>
                                            Score: {{ $allevent->tournamentSetting->brain_score }}<br>
                                        @else
                                            N/A
                                        @endif
                                    </td>

                                    {{-- Game Settings --}}
                                    <td>
                                        @if ($allevent->tournamentSetting)
                                            Game: {{ $allevent->tournamentSetting->game ?? '-' }}<br>
                                            Players/Team: {{ $allevent->tournamentSetting->players_per_team ?? '-' }}<br>
                                            Match Rule: {{ $allevent->tournamentSetting->match_rule ?? '-' }}<br>
                                            Points Win: {{ $allevent->tournamentSetting->points_win ?? '-' }}<br>
                                            Points Draw: {{ $allevent->tournamentSetting->points_draw ?? '-' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>

                                    {{-- Tournament Settings --}}
                                    <td>
                                        @if ($allevent->tournamentSetting)
                                            Type: {{ $allevent->tournamentSetting->tournament_type ?? '-' }}<br>
                                            Teams: {{ $allevent->tournamentSetting->number_of_teams ?? '-' }}
                                        @else
                                            N/A
                                        @endif
                                    </td>

                                    <td>
                                        @if ($allevent->activities->count())
                                            <ul class="mb-0">
                                                @foreach ($allevent->activities as $activity)
                                                    <li>{{ $activity->display_name }} - Score: {{ $activity->max_score }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display:flex;gap:0.25rem;">

                                            <button class="btn btn-icon btn-edit"
                                                onclick="openEditEventModal({{ $allevent->id }})">
                                                <i data-lucide="edit-2"></i>
                                            </button>

                                            <form action="{{ route('events.destroy', $allevent->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this event?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-icon btn-delete">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                            </form>
                                            <button class="btn btn-icon btn-view"
                                                onclick="openBracketModal({{ $allevent->id }})">
                                                <i data-lucide="trophy"></i>
                                            </button>
                                        </div>
                                    </td>

                                </tr>

                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No Events available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <!-- Modal -->


                </div>
            </div>

            <!-- Organizations Tab -->
            <div id="organizations-tab" class="tab-content {{ $activeTab == 'organizations-tab' ? 'active show' : '' }}">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <button class="btn btn-primary" data-next-tab="groups-tab" data-bs-toggle="modal"
                            data-bs-target="#createOrganizationModal">
                            <i data-lucide="plus"></i> Add Organization
                        </button>

                    </div>


                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Email</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($organizations as $org)
                                <tr>


                                    <td>
                                        <img src="{{ $org->profile ? asset('storage/' . $org->profile) : asset('assets/avatar-default.png') }}"
                                            height="40" width="40" class="rounded-circle"
                                            style="object-fit: cover"
                                            onerror="this.src='{{ asset('assets/avatar-default.png') }}'">
                                    </td>
                                    <td>{{ $org->name ?: 'N/A' }}</td>

                                    <td>{{ $org->organization_type ?: 'N/A' }}</td>




                                    <td>{{ $org->email ?: 'N/A' }}</td>

                                    <td>
                                        <div style="display:flex;gap:0.25rem;">

                                            <button class="btn btn-icon btn-edit"
                                                onclick="openEditOrgModal({{ $org->id }}, '{{ addslashes($org->name) }}', '{{ addslashes($org->email) }}', '{{ $org->organization_type }}' , '{{ $org->event_id }}')">
                                                <i data-lucide="edit-2"></i>
                                            </button>

                                            <form action="{{ route('organizations.destroy', $org->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this organization?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-icon btn-delete">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                            </form>
                                        </div>


                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No organizations available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>


                </div>
            </div>

            <div id="groups-tab" class="tab-content {{ $activeTab == 'groups-tab' ? 'active show' : '' }}">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                            <i data-lucide="plus"></i> Add Group
                        </button>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Organization</th>
                                <th>Group Name</th>
                                <th>POD</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($groups as $group)
                                <tr>
                                    <td>{{ $group->id ?? 'N/A' }}</td>
                                    <td>
                                        {{ optional($organizations->firstWhere('id', $group->organization_id))->name ?? 'N/A' }}
                                    </td>
                                    <td>{{ $group->group_name ?? 'N/A' }}</td>
                                    <td class="text-uppercase">{{ $group->pod ?? 'N/A' }}</td>


                                    <td>
                                        <div style="display:flex;gap:0.25rem;">
                                            <button class="btn btn-icon btn-edit"
                                                onclick="openGroupEditModal(
                                            {{ $group->id ?? 'null' }},
                                            '{{ addslashes($group->group_name ?? 'N/A') }}',
                                            '{{ $group->pod ?? 'N/A' }}',
                                        
                                            {{ $group->organization_id }}
                                        )">
                                                <i data-lucide="pencil"></i>
                                            </button>

                                            <form action="{{ route('groups.destroy', $group->id ?? 0) }}" method="POST"
                                                onsubmit="return confirm('Delete this group?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-icon btn-delete">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-white py-4">
                                        No groups available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="subgroup-tab" class="tab-content {{ $activeTab == 'subgroup-tab' ? 'active show' : '' }}">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSubGroupModal">
                            <i data-lucide="plus"></i> Add Sub Group
                        </button>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Group Name</th>
                                <th>Sub Group Name</th>
                                <th>POD</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($subgroups as $subgrp)
                                <tr>
                                    <td>{{ $subgrp->id ?? 'N/A' }}</td>
                                    <td>{{ $subgrp->group->group_name ?? 'N/A' }}</td>
                                    <td>{{ $subgrp->name ?? 'N/A' }}</td>
                                    <td class="text-uppercase">{{ $subgrp->group->pod ?? 'N/A' }}</td>


                                    <td>
                                        <div style="display:flex;gap:0.25rem;">
                                            <button class="btn btn-icon btn-edit"
                                                onclick="openSubGroupEditModal({{ $subgrp->id }})">
                                                <i data-lucide="pencil"></i>
                                            </button>

                                            <form action="{{ route('subgroups.destroy', $subgrp->id ?? 0) }}"
                                                method="POST" onsubmit="return confirm('Delete this sub group?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-icon btn-delete">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-white py-4">
                                        No subgroups available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Teams Tab -->
            <div id="teams-tab" class="tab-content {{ $activeTab == 'teams-tab' ? 'active show' : '' }}">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_team">
                            <i data-lucide="plus"></i> Add Team
                        </button>
                        <button class="btn btn-danger" onclick="deleteSelectedTeams()">
                            Delete Selected
                        </button>
                        {{-- <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#importTeamsModal">
                            <i data-lucide="download"></i> Import CSV
                        </button> --}}
                        <a href="{{ route('teams.export') }}" class="btn btn-secondary">
                            <i data-lucide="upload"></i> Export Teams
                        </a>



                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAllTeams">
                                </th>
                                <th>Avatar</th>
                                <th>Team ID</th>
                                <th>Team Name</th>
                                <th>Division</th>
                                <th>Group</th>
                                <th>Sub Group</th>
                                <th>POD</th>
                                <th>Members</th>
                                <th>Total Points</th>
                                <th>Rank</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="teamsTableBody"></tbody>


                    </table>
                </div>
            </div>

            <!-- activites Tab -->
            {{-- <div id="activites-tab" class="tab-content">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createActivityModal">
                            <i data-lucide="plus"></i> Add Activity
                        </button>

                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Event</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($activities->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center">No data</td>
                                </tr>
                            @else
                                @foreach ($activities as $act)
                                    <tr>
                                        <td>{{ $act->id ?? 'N/A' }}</td>
                                        <td>{{ $act->event->name ?? 'N/A' }}</td>
                                        <td>{{ $act->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-icon btn-edit"
                                                    onclick="openEditActivityModal({{ $act->id }})">
                                                    <i data-lucide="edit-2"></i>
                                                </button>
                                                <button class="btn btn-icon btn-delete"
                                                    onclick="deleteActivity({{ $act->id }}, '{{ $act->name ?? 'N/A' }}')">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div> --}}

            <!-- Scores Tab -->
            <div id="scores-tab" class="tab-content">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar" id="scoreToolbar">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scoreModal">
                            <i data-lucide="plus"></i> Add Score
                        </button>
                        {{-- <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#importScoresModal">
                <i data-lucide="download"></i> Bulk Import
            </button> --}}
                        <button class="btn btn-secondary" onclick="fetchScores()">
                            <i data-lucide="refresh-cw"></i> Refresh
                        </button>
                    </div>

                    <select id="eventSelect" class="form-select mb-3">
                        <option value="">Select Event</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }}</option>
                        @endforeach
                    </select>

                    <div class="table-responsive">
                        <table class="data-table">
                            <thead id="scoreHead">
                                <tr>
                                    <th>Type</th>
                                    <th>Team</th>
                                    <th>Name</th>
                                    <th>Science</th>
                                    <th>Technology</th>
                                    <th>Engineering</th>
                                    <th>Art</th>
                                    <th>Math</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="scoreBody">
                                <tr>
                                    <td colspan="9">Select event to load scores...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>







            {{-- <div id="schedule-tab" class="tab-content">
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
            </div> --}}




        </section>


    </div>
@endsection



@push('modals')
    @include('events.modals.create-team')
    @include('events.modals.create-event')
    {{-- @include('events.modals.create-activity') --}}
    @include('events.modals.create-scores')
    @include('events.modals.view-event')
    @include('events.modals.import-team')
    @include('events.modals.edit-team')
    @include('events.modals.view-team')
    {{-- @include('events.modals.show-event') --}}
    @include('events.modals.bracket')




    @include('events.modals.edit-score')
    @include('events.modals.edit-event')
    {{-- @include('events.modals.import-scores') --}}
    {{-- @include('events.modals.edit-activity') --}}
    @include('events.modals.add-match')
    @include('events.modals.match-pin')
    @include('events.modals.add-round')
    @include('events.modals.create-organization')
    @include('events.modals.create-group')
    @include('events.modals.edit-organization')
    @include('events.modals.edit-group')
    @include('events.modals.create-subgroup')
    @include('events.modals.edit-subgroup')
@endpush

@push('scripts')
    @include('events.bracket-script')
    @include('events.team-script')
    @include('events.score-script')
    {{-- @include('events.matches-script') --}}
    @include('events.edit-event-script')
    @include('events.edit-subgroup-script')
@endpush
