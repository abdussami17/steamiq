@extends('layouts.app')
@section('title', 'Players - SteamIQ')

@section('content')
<div class="container">
    <section class="section">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h2 class="section-title">
                <span class="icon"><i data-lucide="users"></i></span>
                Players Management
            </h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#playerModal">
                <i data-lucide="plus"></i> Add New Player
            </button>
        </div>

        <!-- Event Filter -->
        <div class="mb-4">
            <label for="eventFilter" class="form-label">Select Event <span class="text-danger">*</span></label>
            <select id="eventFilter" class="form-select">
                <option hidden>-- Select Event --</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="spreadsheet-container">
            <div class="spreadsheet-toolbar mb-4">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#playerModal">
                    <i data-lucide="plus"></i> Add Player
                </button>
                <button class="btn btn-secondary" onclick="importPlayers()">
                    <i data-lucide="download"></i> Import CSV
                </button>
                <button class="btn btn-secondary">
                    <i data-lucide="upload"></i> Export
                </button>
                <button class="btn btn-secondary" onclick="refreshLeaderboard()">
                    <i data-lucide="refresh-cw"></i> Refresh
                </button>
            </div>

            <table class="data-table table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Player ID</th>
                        <th>Name</th>
                        <th>Team</th>
                        <th>Brain Points</th>
                        <th>Playground Points</th>
                        <th>E-Gaming Points</th>
                        <th>Total</th>
                        <th>Rank</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="playersTableBody">
                    <!-- Dynamically populated via JS -->
                </tbody>
            </table>
        </div>
    </section>
</div>


@include('players.modals.create-players')
@include('players.script')
@endsection
