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


        <div class="spreadsheet-container">
            <!-- Toolbar -->
            <div class="spreadsheet-toolbar mb-4">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#playerModal">
                    <i data-lucide="plus"></i> Add Player
                </button>
                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
                    Import Players
                </button>
                <button class="btn btn-secondary" onclick="exportGridToExcel()">
                    <i data-lucide="upload"></i> Export Excel
                </button>
                <button class="btn btn-secondary" onclick="refreshLeaderboard()">
                    <i data-lucide="refresh-cw"></i> Refresh
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
        
            <!-- AG Grid Container -->
            <div id="playersGrid" class="ag-theme-alpine" style="width:100%; height:400px;"></div>
        </div>
        
        
    </section>
</div>


@include('players.modals.create-players')
@include('players.script')
@include('players.modals.import-players')

@endsection
