@extends('layouts.app')
@section('title', 'Tournaments - SteamIQ')

@section('content')
    <div class="container">
        <section class="section">
            <div class="section-header d-flex justify-content-between align-items-center">
                <h2 class="section-title">
                    <span class="icon">
                        <i data-lucide="user"></i>
                    </span>
                    Tournaments
                </h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTournamentModal">Create
                    Tournament</button>
            </div>
            <div class="container py-4">
                <div class="row g-4">
                    @forelse($tournaments as $tournament)
                        <div class="col-md-6 col-lg-4">
                            <div class="card bg-dark text-white h-100 shadow-sm p-3">
                                <div class="card-body d-flex flex-column">
                                    <h4 class="card-title mb-1">{{ $tournament->name }} ({{ ucfirst($tournament->type) }})</h4>
                                    <small class="text-white mb-3">Event: {{ $tournament->event->name ?? 'N/A' }}</small>
            
                                    <div class="bracket-container mt-2">
                                        <div class="bracket d-flex flex-column gap-3">
                                            @foreach ($tournament->matches->groupBy('round_no') as $roundNo => $matches)
                                                <div class="bracket-round">
                                                    <div class="bracket-round-title fw-bold mb-2">
                                                        @php
                                                            $maxRound = $tournament->matches->max('round_no');
                                                        @endphp
            
                                                        @if ($roundNo == $maxRound)
                                                            Champion
                                                        @elseif($roundNo == $maxRound - 1)
                                                            Finals
                                                        @elseif($roundNo == $maxRound - 2)
                                                            Semifinals
                                                        @else
                                                            Round {{ $roundNo }}
                                                        @endif
                                                    </div>
            
                                                    @foreach ($matches as $match)
                                                        <div class="bracket-match d-flex align-items-center justify-content-between mb-2 p-3 rounded border"
                                                            style="@if ($match->winner_team_id) background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(0, 212, 255, 0.1)); border-color: #0d6efd; @endif">
            
                                                            <!-- Team A -->
                                                            <div class="bracket-team d-flex align-items-center gap-2 {{ $match->winner_team_id == $match->team_a_id ? 'fw-bold text-success' : '' }}">
                                                                <span class="bracket-team-name">{{ $match->teamA->team_name ?? 'TBD' }}</span>
                                                                @if (isset($match->score_a))
                                                                    <span class="badge bg-secondary">{{ $match->score_a }}</span>
                                                                @endif
                                                            </div>
            
                                                            <!-- VS / Buttons -->
                                                            <div class="d-flex gap-2">
                                                                <button class="btn btn-primary btn-sm text-white" onclick="generateMatchPIN({{ $match->id }})">PIN</button>
                                                                @if ($match->status != 'completed')
                                                                    <button class="btn btn-secondary btn-sm text-white" onclick="openAddRoundModal({{ $match->id }})">Add Winner</button>
                                                                @endif
                                                            </div>
            
                                                            <!-- Team B -->
                                                            <div class="bracket-team d-flex align-items-center gap-2 {{ $match->winner_team_id == $match->team_b_id ? 'fw-bold text-success' : '' }}">
                                                                <span class="bracket-team-name">{{ $match->teamB->team_name ?? 'TBD' }}</span>
                                                                @if (isset($match->score_b))
                                                                    <span class="badge bg-secondary">{{ $match->score_b }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-white">No tournaments found.</p>
                    @endforelse
                </div>
            </div>
            
        </section>
    </div>


    <!-- Add Winner Modal -->
    <div class="modal fade" id="addRoundModal" tabindex="-1" aria-labelledby="addRoundModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoundModalLabel">Select Winner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="roundWinner" class="form-label">Winner</label>
                        <select class="form-select" id="roundWinner">
                            <option value="">-- Select Winner --</option>
                            <!-- Options dynamically filled via JS -->
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary w-100" onclick="submitRound()">Submit</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Create Tournament Modal -->
    <div id="createTournamentModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content p-3">
                <h5 class="modal-title mb-3">Create Tournament</h5>
                <form method="POST" action="{{ route('tournaments.store') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-input" name="name" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="single">Single Elimination</option>
                            <option value="double">Double Elimination</option>
                            <option value="roundrobin">Round Robin</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Event</label>
                        <select name="event_id" class="form-select" required>
                            @foreach (\App\Models\Event::where('event_type', 'tournament')->get() as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Select Teams</label>
                        <select name="team_ids[]" class="form-select" multiple required>
                            @foreach (\App\Models\Team::all() as $team)
                                <option value="{{ $team->id }}">{{ $team->team_name }} (CAM: {{ $team->cam_points }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple teams.</small>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3 w-100">Create Tournament</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentMatchId;

        function openAddRoundModal(matchId) {
            currentMatchId = matchId;

            const matchRow = document.querySelector(`.bracket-match button[onclick="openAddRoundModal(${matchId})"]`)
                .closest('.bracket-match');
            const teamA = matchRow.querySelectorAll('.bracket-team .bracket-team-name')[0].innerText;
            const teamB = matchRow.querySelectorAll('.bracket-team .bracket-team-name')[1].innerText;

            const select = document.getElementById('roundWinner');
            select.innerHTML = `<option value="">-- Select Winner --</option>
                        <option value="teamA">${teamA}</option>
                        <option value="teamB">${teamB}</option>`;

            bootstrap.Modal.getOrCreateInstance(document.getElementById('addRoundModal')).show();
        }

        async function submitRound() {
            const winnerOption = document.getElementById('roundWinner').value;
            if (!winnerOption) return alert('Select winner');

            const matchRow = document.querySelector(
                `.bracket-match button[onclick="openAddRoundModal(${currentMatchId})"]`).closest('.bracket-match');
            const teamId = winnerOption === 'teamA' ?
                matchRow.querySelectorAll('.bracket-team')[0].dataset.id :
                matchRow.querySelectorAll('.bracket-team')[1].dataset.id;

            const res = await fetch(`/tournament-match/${currentMatchId}/winner`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    winner_team_id: teamId
                })
            });

            const data = await res.json();
            if (data.success) {
                alert('Winner updated!');
                location.reload();
            } else {
                alert('Failed to update winner');
            }
        }

        async function generateMatchPIN(matchId) {
            const res = await fetch(`/tournament-match/${matchId}/pin`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const data = await res.json();
            if (data.success) alert('Match PIN: ' + data.pin);
        }
    </script>
@endsection
