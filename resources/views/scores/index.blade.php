@extends('layouts.app')
@section('title', 'Scoring - SteamIQ')

@section('content')
<div class="container-fluid px-3">

    <section class="section">
        <div class="section-header d-flex align-items-center gap-2 flex-wrap mb-2">
            <h2 class="section-title mb-0">
                <span class="icon"><i data-lucide="award"></i></span>
                Scoring
            </h2>
            <div class="ms-auto d-flex gap-2 flex-wrap">
                <button class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#scoreModal">
                    <i data-lucide="plus" style="width:13px;height:13px;vertical-align:-1px;"></i> Add Score
                </button>
                <button class="btn btn-sm btn-warning fw-bold" id="bonusBtn">
                    <i data-lucide="zap" style="width:13px;height:13px;vertical-align:-1px;"></i> Bonus
                </button>
            </div>
        </div>

        <div id="lb-wrapper">
            {{-- Controls --}}
            <div id="lb-controls">
                <label for="selectEvent">Event</label>
                <select id="selectEvent">
                    <option value="" hidden>-- Select Event --</option>
                </select>

                <div class="lb-legend">
                    <span class="lb-legend-dot"><span style="background:var(--cat-science-bg)"></span>Science</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-tech-bg)"></span>Technology</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-eng-bg)"></span>Engineering</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-art-bg)"></span>Art</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-math-bg)"></span>Math</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-playground-bg)"></span>Playground</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-egaming-bg)"></span>EGaming</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-esports-bg)"></span>Esports</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-mission-bg)"></span>Mission</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-bonus-bg)"></span>Bonus</span>
                </div>

                <div class="lb-actions">
                    <button id="bulkEditBtn" class="lb-btn lb-btn-bulk">⊞ Bulk Edit</button>
                    <button id="openBulkModalBtn" class="lb-btn lb-btn-bulk-go" style="display:none;">✎ Edit Selected</button>
                    <button id="exportLeaderboard" class="lb-btn lb-btn-export">↓ Export</button>
                </div>
            </div>

            {{-- Bulk bar --}}
            <div id="bulk-bar">
                <span style="font-weight:900;color:#f5c518;font-size:12px;letter-spacing:.08em;">BULK EDIT MODE</span>
                <span style="color:#2a3040;">|</span>
                <span><span id="bulk-count" style="font-weight:900;color:#f5c518;font-size:16px;">0</span> selected</span>
                <span id="bulk-hint" style="color:#484f58;font-size:11px;">Click score cells to select/deselect</span>
                <button class="lb-btn lb-btn-bulk-go ms-auto" style="padding:5px 12px;font-size:12px;"
                        onclick="document.getElementById('openBulkModalBtn').click()">
                    Open Edit Panel →
                </button>
            </div>

            {{-- Table --}}
            <div id="lb-scroll">
                <table id="lb-table">
                    <thead id="lb-thead"></thead>
                    <tbody id="lb-tbody">
                        <tr class="lb-state-row"><td colspan="999">Select an event to load the leaderboard.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

@push('modals')
    @include('scores.modals.create-scores')
@endpush

@push('scripts')
    @include('scores.scripts.score-script')
@endpush
@endsection