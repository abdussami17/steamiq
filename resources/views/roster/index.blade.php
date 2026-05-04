@extends('layouts.app')

@section('title', 'Roster Management')

@section('content')
<div class="container">

<section class="section">
        {{-- ── Header ─────────────────────────────────────────────────────── --}}

        <div class="section-header">
            <h2 class="section-title">
                <span class="icon">
                    <i data-lucide="user"></i>
                </span>
                Roster Management
            </h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i data-lucide="import"></i> Import Roster
            </button>
        </div>
    <div class="spreadsheet-container">
        <div class="spreadsheet-toolbar">
            <div class="form-group d-flex justify-content-normal align-items-center gap-3 w-100">
                <label class="form-label">Filter by Event:</label>
                <select id="filterEvent" class="form-select " style="max-width: 300px">
                    <option value="">All Events</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->name }}</option>
                    @endforeach
                </select>
            </div>
         
        </div>

        <div >
            <table class="data-table" id="rosterTable">
                <thead >
                    <tr>
                        <th>#</th>
                        <th>Event</th>
                        <th>Organization</th>
                        <th>Coach</th>
                        <th>Total Players</th>
                        <th>Status</th>
                        <th>Uploaded At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="rosterTableBody">
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <span class="spinner-border spinner-border-sm me-2"></span> Loading...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    
    
</section>

</div>

@push('modals')
    @include('roster.modal.import')
    @include('roster.modal.view')
@endpush
@endsection


@push('scripts')
@include('roster.script.script')
@endpush