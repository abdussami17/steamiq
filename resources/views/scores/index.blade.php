@extends('layouts.app')
@section('title', 'Scoring - SteamIQ')


@section('content')
    <div class="container">


        <!-- Settings -->
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon">
                        <i data-lucide="award"></i>
                    </span>
                    Scoring
                </h2>
            </div>



       <!-- Scores Tab -->
 
        <div class="spreadsheet-container">
            <div class="spreadsheet-toolbar" id="scoreToolbar">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scoreModal">
                    <i data-lucide="plus"></i> Add Score
                </button>

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
                            <th style="width: 100px">Type</th>
                            <th style="width: 150px">Name</th>
                            <th style="width: 150px">Activity</th>
                            <th style="width: 100px">Total</th>
                            <th style="width: 80px">Rank</th>
                        </tr>
                    </thead>
                    <tbody id="scoreBody">
                        <tr>
                            <td colspan="5">Select event to load scores...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
  

            




        </section>


    </div>


@push('modals')
   {{-- Scores Modals --}}
   @include('scores.modals.create-scores')
   @include('scores.modals.edit-score')  
@endpush
@push('scripts')
        {{-- Score Scripts --}}
        @include('scores.scripts.score-script')
@endpush
       
@endsection
