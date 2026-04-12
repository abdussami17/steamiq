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

            </div>

            <div class="card-grid events_grid">
                @forelse($allevents as $allevent)
                    <div class="events_card">
                        <div class="events_card-header">
                            <div>
                                <h3 class="events_card-title">{{ $allevent->name }}</h3>
                                <p style="color: var(--text-dim); font-size: 0.9rem; margin-top: 0.25rem;">
                                    {{ ucfirst($allevent->event_type) }}
                                </p>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                {{-- Status badge + inline edit --}}
                                <div class="ec-status-wrap" style="position:relative;">
                                    <span id="ec-badge-{{ $allevent->id }}"
                                        class="badge me-1 badge-{{ $allevent->status }}">
                                        {{ ucfirst($allevent->status) }}
                                    </span>
                                    @can('edit_event')
                                        <button type="button" class="btn btn-link p-0 m-0 align-baseline ec-status-btn"
                                            title="Change status" onclick="toggleStatusDrop({{ $allevent->id }}, this)">
                                            <i data-lucide="pencil" style="height: 13px;width:20px;color:#000"></i>
                                        </button>
                                        <div id="ec-drop-{{ $allevent->id }}" class="ec-status-drop d-none">
                                            @foreach (['draft', 'live', 'closed'] as $s)
                                                <button type="button"
                                                    class="ec-status-opt {{ $allevent->status === $s ? 'active' : '' }}"
                                                    data-event="{{ $allevent->id }}" data-status="{{ $s }}"
                                                    onclick="setEventStatus({{ $allevent->id }}, '{{ $s }}', this)">
                                                    <span class="ec-status-dot ec-dot-{{ $s }}"></span>
                                                    {{ ucfirst($s) }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @endcan
                                </div>

                                @can('delete_event')
                                    <form action="{{ route('events.destroy', $allevent->id) }}" method="POST"
                                        style="display:inline-flex; align-items:center; margin:0; padding:0;"
                                        onsubmit="return confirm('Are you sure you want to delete this event?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                                            <i data-lucide="trash" class="text-danger"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>

                        <div class="events_stats-grid">

                            @php
                                // Merge all teams under groups + subgroups and remove duplicates
                                $allTeams = $allevent->organizations->flatMap->groups
                                    ->flatMap(function ($group) {
                                        return $group->teams->concat($group->subgroups->flatMap->teams);
                                    })
                                    ->unique('id');
                            @endphp

                            {{-- TEAMS --}}
                            <div class="events_stat">
                                <div class="events_stat-label">Teams</div>
                                <div class="events_stat-value">
                                    {{ $allTeams->count() ?: 'N/A' }}
                                </div>
                            </div>

                            {{-- PLAYERS --}}
                            <div class="events_stat">
                                <div class="events_stat-label">Players</div>
                                <div class="events_stat-value">
                                    {{ $allTeams->flatMap->students->count() ?: 'N/A' }}
                                </div>
                            </div>

                            {{-- GROUPS --}}
                            <div class="events_stat">
                                <div class="events_stat-label">Groups</div>
                                <div class="events_stat-value">
                                    {{ $allevent->organizations->flatMap->groups->count() ?: 'N/A' }}
                                </div>
                            </div>

                            {{-- SUBGROUPS --}}
                            <div class="events_stat">
                                <div class="events_stat-label">Sub Groups</div>
                                <div class="events_stat-value">
                                    {{ $allevent->organizations->flatMap->groups->flatMap->subgroups->count() ?: 'N/A' }}
                                </div>
                            </div>

                        </div>
                        <div class="events_results_preview" id="event-results-{{ $allevent->id }}"></div>
                        <div class="events_card-actions">
                            <button class="btn btn-primary btn-main" onclick="openEventModal({{ $allevent->id }})">
                                View Event
                            </button>

                            <div class="action-icons">
                                <button class="btn btn-icon btn-view" title="Tournament Bracket"
                                    onclick="openBracketModal({{ $allevent->id }})">
                                    <i data-lucide="trophy"></i>
                                </button>
                                
                            </div>
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
                <button class="tab {{ $activeTab == 'players-tab' ? 'active' : '' }}"
                    onclick="switchTab('players')">Players</button>

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
                        <input type="text" id="eventSearch" class="form-input" placeholder="Search Event..."
                            style="width:300px;">
                        @can('create_event')
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEventModal">
                                <i data-lucide="plus"></i> Add Event
                            </button>
                        @endcan
                        @can('delete_event')
                        <button class="btn btn-danger" id="deleteSelectedEventsBtn" onclick="deleteSelectedEvents()">
                            Delete Selected (0)
                        </button>
                    @endcan

                    </div>


                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAllEvents">
                                </th>
                                <th>ID</th>

                                <th>Event Name</th>
                                <th>Event Type</th>
                                <th>Location</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>


                                <th>Game Settings</th>
                                <th>Tournament</th>
                                <th>Activities</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="eventTableBody">
                            @forelse($allevents as $allevent)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="event-checkbox" value="{{ $allevent->id }}">
                                    </td>
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
                                            $status = strtolower(trim($allevent->status)); 
                                    
                                            $map = [
                                                'live'   => ['label' => 'LIVE', 'class' => 'badge-live'],
                                                'closed' => ['label' => 'CLOSED', 'class' => 'badge-closed'], 
                                                'draft'  => ['label' => 'DRAFT', 'class' => 'badge-draft'],
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
                                            @can('edit_event')
                                                <button class="btn btn-icon btn-edit"
                                                    onclick="openEditEventModal({{ $allevent->id }})">
                                                    <i data-lucide="edit-2"></i>
                                                </button>
                                            @endcan
                                            @can('delete_event')
                                                <form action="{{ route('events.destroy', $allevent->id) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this event?');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-icon btn-delete">
                                                        <i data-lucide="trash-2"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                            @can('duplicate_event')
                                                <button class="btn btn-icon btn-copy"
                                                    onclick="duplicateEvent({{ $allevent->id }})">
                                                    <i data-lucide="copy"></i>
                                                </button>
                                            @endcan
                                            <button class="btn btn-icon btn-view"
                                                onclick="openBracketModal({{ $allevent->id }})">
                                                <i data-lucide="trophy"></i>
                                            </button>
                                            {{-- @if ($allevent->status === 'closed')
                                                <button class="btn btn-icon btn-summary" title="Event Summary"
                                                    onclick="openEventSummary({{ $allevent->id }})">
                                                    <i data-lucide="file-text"></i>
                                                </button>
                                            @endif --}}
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
                    @push('scripts')
                    @include("events.scripts.bulk-delete")
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                // Simple search by Team Name
                                document.getElementById('eventSearch').addEventListener('input', function() {
                                    const filter = this.value.toLowerCase();
                                    const rows = document.querySelectorAll('#eventTableBody tr');
                                    rows.forEach(row => {
                                        const cell = row.cells[1]; // Team Name column
                                        if (!cell) {
                                            row.style.display = 'none';
                                            return;
                                        }
                                        const name = cell.querySelector('input') ? cell.querySelector('input').value
                                            .toLowerCase() : cell.textContent.toLowerCase();
                                        row.style.display = name.includes(filter) ? '' : 'none';
                                    });
                                });
                            })
                        </script>
                    @endpush

                </div>
            </div>

            <!-- Organizations Tab -->
            <div id="organizations-tab" class="tab-content {{ $activeTab == 'organizations-tab' ? 'active show' : '' }}">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <input type="text" id="orgSearch" class="form-input" placeholder="Search Organization..."
                            style="width:300px;">
                        @can('create_organization')
                            <button class="btn btn-primary" data-next-tab="groups-tab" data-bs-toggle="modal"
                                data-bs-target="#createOrganizationModal">
                                <i data-lucide="plus"></i> Add Organization
                            </button>
                        @endcan
                        @can('delete_organization')
                        <button class="btn btn-danger" id="deleteSelectedOrgsBtn" onclick="deleteSelectedOrganizations()">
                            Delete Selected (0)
                        </button>
                    @endcan
                    </div>


                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAllOrgs">
                                </th>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Email</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="orgTableBody">
                            @forelse($organizations as $org)
                                <tr>

                                    <td>
                                        <input type="checkbox" class="org-checkbox" value="{{ $org->id }}">
                                    </td>
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

                                            @can('edit_organization')
                                                <button class="btn btn-icon btn-edit"
                                                    onclick="openEditOrgModal({{ $org->id }}, '{{ addslashes($org->name) }}', '{{ addslashes($org->email) }}', '{{ $org->organization_type }}' , '{{ $org->event_id }}')">
                                                    <i data-lucide="edit-2"></i>
                                                </button>
                                            @endcan
                                            @can('delete_organization')
                                                <form action="{{ route('organizations.destroy', $org->id) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this organization?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-icon btn-delete">
                                                        <i data-lucide="trash-2"></i>
                                                    </button>
                                                </form>
                                            @endcan
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
                    @push('scripts')
                    @include('organization.script')
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                // Simple search by Team Name
                                document.getElementById('orgSearch').addEventListener('input', function() {
                                    const filter = this.value.toLowerCase();
                                    const rows = document.querySelectorAll('#orgTableBody tr');
                                    rows.forEach(row => {
                                        const cell = row.cells[1]; // Team Name column
                                        if (!cell) {
                                            row.style.display = 'none';
                                            return;
                                        }
                                        const name = cell.querySelector('input') ? cell.querySelector('input').value
                                            .toLowerCase() : cell.textContent.toLowerCase();
                                        row.style.display = name.includes(filter) ? '' : 'none';
                                    });
                                });
                            })
                        </script>
                    @endpush

                </div>
            </div>

            {{-- Group Tab --}}

            <div id="groups-tab" class="tab-content {{ $activeTab == 'groups-tab' ? 'active show' : '' }}">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <input type="text" id="groupSearch" class="form-input" placeholder="Search Group..."
                            style="width:300px;">
                        @can('create_group')
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                                <i data-lucide="plus"></i> Add Group
                            </button>
                        @endcan
                        @can('delete_group')
                        <button class="btn btn-danger" id="deleteSelectedGroupsBtn" onclick="deleteSelectedGroups()">
                            Delete Selected (0)
                        </button>
                    @endcan
                        {{-- <a href="javascript:void(0)" class="btn btn-secondary assign-card-btn" data-type="group">
                            <i data-lucide="club"></i> Assign Cards
                        </a> --}}

                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>

                                <th>
                                    <input type="checkbox" id="selectAllGroups">
                                </th>
                                <th>ID</th>
                                <th>Organization</th>
                                <th>Group Name</th>
                                <th>POD</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="groupTableBody">
                            @forelse($groups as $group)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="group-checkbox" value="{{ $group->id }}">
                                    </td>
                                
                                    <td>{{ $group->id ?? 'N/A' }}</td>
                                    <td>
                                        {{ optional($organizations->firstWhere('id', $group->organization_id))->name ?? 'N/A' }}
                                    </td>
                                    <td>{{ $group->group_name ?? 'N/A' }}</td>
                                    <td class="text-uppercase">{{ $group->pod ?? 'N/A' }}</td>


                                    <td>
                                        <div style="display:flex;gap:0.25rem;">
                                            @can('edit_group')
                                                <button class="btn btn-icon btn-edit"
                                                    onclick="openGroupEditModal(
                                       {{ $group->id ?? 'null' }},
                                       '{{ addslashes($group->group_name ?? 'N/A') }}',
                                       '{{ $group->pod ?? 'N/A' }}',
                                   
                                       {{ $group->organization_id }}
                                   )">
                                                    <i data-lucide="pencil"></i>
                                                </button>
                                            @endcan

                                            @can('delete_group')
                                                <form action="{{ route('groups.destroy', $group->id ?? 0) }}" method="POST"
                                                    onsubmit="return confirm('Delete this group?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-icon btn-delete">
                                                        <i data-lucide="trash-2"></i>
                                                    </button>
                                                </form>
                                            @endcan
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
            @push('scripts')
            @include('groups.script')
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        // Simple search by Team Name
                        document.getElementById('groupSearch').addEventListener('input', function() {
                            const filter = this.value.toLowerCase();
                            const rows = document.querySelectorAll('#groupTableBody tr');
                            rows.forEach(row => {
                                const cell = row.cells[2]; // Team Name column
                                if (!cell) {
                                    row.style.display = 'none';
                                    return;
                                }
                                const name = cell.querySelector('input') ? cell.querySelector('input').value
                                    .toLowerCase() : cell.textContent.toLowerCase();
                                row.style.display = name.includes(filter) ? '' : 'none';
                            });
                        });
                    })
                </script>
            @endpush
            {{-- Subgroup Tab --}}

            <div id="subgroup-tab" class="tab-content {{ $activeTab == 'subgroup-tab' ? 'active show' : '' }}">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        @can('create_subgroup')
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSubGroupModal">
                                <i data-lucide="plus"></i> Add Sub Group
                            </button>
                        @endcan

    @can('delete_subgroup')
    <button class="btn btn-danger" id="deleteSelectedSubGroupsBtn" onclick="deleteSelectedSubGroups()">
        Delete Selected (0)
    </button>
@endcan

                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAllSubGroups">
                                </th>
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
                                    <td>
                                        <input type="checkbox" class="subgroup-checkbox" value="{{ $subgrp->id }}">
                                    </td>
                                    <td>{{ $subgrp->id ?? 'N/A' }}</td>
                                    <td>{{ $subgrp->group->group_name ?? 'N/A' }}</td>
                                    <td>{{ $subgrp->name ?? 'N/A' }}</td>
                                    <td class="text-uppercase">{{ $subgrp->group->pod ?? 'N/A' }}</td>


                                    <td>
                                        <div style="display:flex;gap:0.25rem;">
                                            @can('edit_subgroup')
                                                <button class="btn btn-icon btn-edit"
                                                    onclick="openSubGroupEditModal({{ $subgrp->id }})">
                                                    <i data-lucide="pencil"></i>
                                                </button>
                                            @endcan

                                            @can('delete_subgroup')
                                                <form action="{{ route('subgroups.destroy', $subgrp->id ?? 0) }}"
                                                    method="POST" onsubmit="return confirm('Delete this sub group?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-icon btn-delete">
                                                        <i data-lucide="trash-2"></i>
                                                    </button>
                                                </form>
                                            @endcan
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
                    @push('scripts')
                    @include('subgroups.scripts.bulk-edit')
                        
                    @endpush
                </div>
            </div>

            <!-- Teams Tab -->

            <div id="teams-tab" class="tab-content {{ $activeTab == 'teams-tab' ? 'active show' : '' }}">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <input type="text" id="teamSearch" class="form-input" placeholder="Search Team..."
                            style="width:300px;">
                        @can('create_team')
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_team">
                                <i data-lucide="plus"></i> Add Team
                            </button>
                        @endcan
                        @can('delete_team')
                            <button class="btn btn-danger" onclick="deleteSelectedTeams()">
                                Delete Selected
                            </button>
                        @endcan
                        @can('import_team')
                            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#importTeamsModal">
                                <i data-lucide="upload"></i> Import Teams
                            </button>
                        @endcan
                        {{-- <a href="{{ route('teams.export') }}" class="btn btn-secondary">
                            <i data-lucide="upload"></i> Export Teams
                        </a> --}}
                        {{-- <a href="javascript:void(0)" class="btn btn-secondary assign-card-btn" data-type="team">
                            <i data-lucide="club"></i> Assign Cards
                        </a> --}}



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
            <!-- Players Tab -->

            <div id="players-tab" class="tab-content {{ $activeTab == 'players-tab' ? 'active show' : '' }}">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <!-- Add this inside your spreadsheet-toolbar, beside buttons -->
                        <input type="text" id="playerSearch" class="form-input" placeholder="Search Player"
                            style="width:300px">
                        @can('create_player')
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                <i data-lucide="plus"></i> Add New Player
                            </button>
                        @endcan
                        @can('delete_player')
                        <button class="btn btn-danger" id="deleteSelectedPlayersBtn" onclick="deleteSelectedPlayers()">
                            Delete Selected (0)
                        </button>
                        @endcan

                        <button class="btn btn-secondary" onclick="loadLeaderboard()">
                            <i data-lucide="refresh-cw"></i> Refresh
                        </button>
                        {{-- <a href="javascript:void(0)" class="btn btn-secondary assign-card-btn" data-type="player">
                            <i data-lucide="club"></i> Assign Cards
                        </a> --}}


                    </div>
                    <div class="row g-3">
                        <!-- Event Filter -->
                        <div class="mb-4 col-md-4">
                            <label for="eventFilter" class="form-label">Select Event <span
                                    class="text-danger">*</span></label>
                            <select id="eventFilter" class="form-select">
                                <option hidden>-- Select Event --</option>
                                @foreach ($allevents as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4 col-md-4">
                            <label for="organizationFilter" class="form-label">Select Organization<span
                                    class="text-danger">*</span></label>
                            <select id="organizationFilter" class="form-select">
                                <option value="">-- Select Organization --</option>
                            </select>
                        </div>
                    </div>
                    <div id="playersGrid" style="width:100%; overflow:auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAllPlayers">
                                    </th>
                                    <th>Player</th>
                                    <th>Team</th>
                                    <th>Activity</th>
                                    <th>Total</th>
                                    <th>Rank</th>
                                    <th id="actionHeader" style="display:none;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="playersTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">Select event to load data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>











        </section>


    </div>
@endsection


@push('modals')
    {{-- Teams Modals --}}
    @include('teams.modals.create-team')
    @include('teams.modals.view-team')
    @include('teams.modals.import-team')
    @include('teams.modals.edit-team')

    {{-- Events Modals --}}

    @include('events.modals.view-event')
    @include('events.modals.edit-event')
    @include('events.modals.bracket')
    @include('events.modals.choose-winner')


    {{-- Matches Modals --}}
    @include('matches.modals.add-match')
    @include('matches.modals.match-pin')
    @include('matches.modals.add-round')

    {{-- Organization Modals --}}
    @include('organization.modals.create-organization')
    @include('organization.modals.edit-organization')

    {{-- Groups & Subgroups Modals --}}
    @include('groups.modals.create-group')
    @include('groups.modals.edit-group')
    @include('subgroups.modals.create-subgroup')
    @include('subgroups.modals.edit-subgroup')

    {{-- Students Modal --}}
    @include('students.modals.create-students')
    @include('students.modals.edit-students')

    {{-- Card Modal --}}
    @include('card.assign-card-modal')
@endpush
@push('styles')
    @include('events.style')
@endpush
@push('scripts')
    {{-- Event Scripts --}}
    @include('events.scripts.bracket-script')
    @include('events.scripts.edit-event-script')
    @include('events.scripts.duplicate-event-script')

    @include('events.scripts.winner-script')

    {{-- Team Scripts --}}
    @include('teams.scripts.team-script')

    {{-- Score Scripts --}}
    @include('scores.scripts.score-script')

    {{-- Subgroup Scripts --}}
    @include('subgroups.scripts.edit-subgroup-script')

    {{-- Student Scripts --}}
    @include('students.script')

    {{-- Card Scripts --}}
    @include('card.assign-script')
@endpush
