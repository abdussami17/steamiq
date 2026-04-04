@extends('layouts.app')
@section('title', 'Leaderboard - SteamIQ')


@section('content')
    <div class="container">


                           {{-- ══════════════════════════════════════════════════════
         LEADERBOARD SECTION
    ══════════════════════════════════════════════════════ --}}
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="icon"><i data-lucide="award"></i></span>
                 Leaderboard
            </h2>
        </div>

        <div id="lb-wrapper">

            {{-- Controls --}}
            <div id="lb-controls">
                <label for="selectEvent">Event</label>
                <select id="selectEvent">
                    <option value="" hidden>-- Select Event --</option>
                </select>

                {{-- Legend --}}
                <div class="lb-legend">
                    <span class="lb-legend-dot"><span style="background:var(--cat-science-bg)"></span>Science</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-tech-bg)"></span>Technology</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-eng-bg)"></span>Engineering</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-art-bg)"></span>Art</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-math-bg)"></span>Math</span>
                    <span class="lb-legend-dot"><span
                            style="background:var(--cat-playground-bg)"></span>Playground</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-egaming-bg)"></span>E-Gaming</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-esports-bg)"></span>ESports</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-mission-bg)"></span>Missions</span>


                </div>

            </div>

            {{-- Table --}}
            <div id="lb-scroll">
                <table id="lb-table">
                    <thead id="lb-thead"></thead>
                    <tbody id="lb-tbody"></tbody>
                </table>
            </div>

        </div>
    </section>

    @push('scripts')
    @include('leaderboard.leaderboard-script')
        
    @endpush


    </div>
@endsection
