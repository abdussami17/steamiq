@extends('layouts.app')
@section('title', 'Tournaments - SteamIQ')
@include('tournament.style')
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
            <div>
                <div class="tournament-all-matches-section">
                    @forelse($tournaments as $tournament)
                        <div class="tournament-match-card-container">
                
                            <div class="tournament-match-header-section">
                                <h2 class="tournament-match-name-primary">
                                    {{ $tournament->name }}
                                </h2>
                
                                <span class="tournament-match-type-label">
                                    {{ ucfirst($tournament->type) }}
                                    • {{ $tournament->event->name ?? 'N/A' }}
                                </span>
                            </div>
                
                            <div class="d-flex flex-column gap-4">
                
                                @foreach ($tournament->matches->groupBy('round_no') as $roundNo => $matches)
                
                                    @php
                                        $maxRound = $tournament->matches->max('round_no');
                                    @endphp
                
                                    <div>
                                        <div class="fw-bold mb-3 text-center text-secondary">
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
                
                                            <div class="tournament-match-card-container {{ $match->winner_team_id ? 'tournament-completed-match-state' : '' }} mb-3" id="match-{{ $match->id }}">
                
                                                <div class="tournament-teams-versus-section">
                
                                                    <div
                                                        class="tournament-team-display-block
                                                        {{ $match->winner_team_id == $match->team_a_id ? 'tournament-winning-team-state' : '' }}
                                                        {{ $match->winner_team_id && $match->winner_team_id != $match->team_a_id ? 'tournament-losing-team-state' : '' }}">
                
                                                        <div class="tournament-team-name-text" data-team-id="{{ $match->team_a_id }}">
                                                            {{ $match->teamA->team_name ?? 'TBD' }}
                                                            @if(isset($match->score_a))
                                                                ({{ $match->score_a }})
                                                            @endif
                                                        </div>
                
                                                        <div class="tournament-winner-badge-indicator">★</div>
                                                    </div>
                
                                                    <div class="tournament-versus-image-container">
                                                        <div class="tournament-versus-badge-graphic">
                                                            <img src="{{ asset('assets/vers.png') }}" alt="image">
                                                        </div>
                                                    </div>
                
                                                    <div
                                                        class="tournament-team-display-block
                                                        {{ $match->winner_team_id == $match->team_b_id ? 'tournament-winning-team-state' : '' }}
                                                        {{ $match->winner_team_id && $match->winner_team_id != $match->team_b_id ? 'tournament-losing-team-state' : '' }}">
                
                                                        <div class="tournament-team-name-text" data-team-id="{{ $match->team_b_id }}">
                                                            {{ $match->teamB->team_name ?? 'TBD' }}
                                                            @if(isset($match->score_b))
                                                                ({{ $match->score_b }})
                                                            @endif
                                                        </div>
                
                                                        <div class="tournament-winner-badge-indicator">★</div>
                                                    </div>
                
                                                </div>
                
                                                <div class="tournament-match-actions-section">
                
                                                    <button
                                                        class="tournament-pin-match-button"
                                                        onclick="generateMatchPIN({{ $match->id }})">
                                                        Pin Match
                                                    </button>
                
                                                    @if ($match->status != 'completed')
                                                        <button
                                                            class="tournament-select-winner-button"
                                                            onclick="openSelectWinnerModal({{ $match->id }})">
                                                            Select Winner
                                                        </button>
                                                    @else
                                                        <button
                                                            class="tournament-select-winner-button"
                                                            disabled>
                                                            Winner Selected
                                                        </button>
                                                    @endif
                
                                                </div>
                
                                            </div>
                
                                        @endforeach
                                    </div>
                
                                @endforeach
                
                            </div>
                        </div>
                
                    @empty
                        <p class="text-center text-white">No tournaments found.</p>
                    @endforelse
                </div>
                
            </div>
            
        </section>
    </div>

    <div class="modal fade" id="selectWinnerModal" tabindex="-1" aria-labelledby="selectWinnerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="selectWinnerModalLabel">Select Winner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="winnerSelect" class="form-label">Choose winning team</label>
                        <select class="form-select" id="winnerSelect">
                            <option value="">-- Select Winner --</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitWinner()">Submit Winner</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pinModal" tabindex="-1" aria-labelledby="pinModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pinModalLabel">Match PIN</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="pinDisplay" style="font-size: 36px; font-weight: bold; letter-spacing: 5px;color:#fff"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

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
        let currentMatchIdForWinner = null;
        let currentMatchIdForPin = null;

        function openSelectWinnerModal(matchId) {
            currentMatchIdForWinner = matchId;
            const matchElement = document.getElementById(`match-${matchId}`);
            const teamElements = matchElement.querySelectorAll('.tournament-team-name-text');
            
            const teamAElement = teamElements[0];
            const teamBElement = teamElements[1];
            
            const teamAName = teamAElement.childNodes[0].nodeValue.trim();
            const teamBName = teamBElement.childNodes[0].nodeValue.trim();
            const teamAId = teamAElement.getAttribute('data-team-id');
            const teamBId = teamBElement.getAttribute('data-team-id');
            
            const select = document.getElementById('winnerSelect');
            select.innerHTML = `
                <option value="">-- Select Winner --</option>
                <option value="${teamAId}">${teamAName}</option>
                <option value="${teamBId}">${teamBName}</option>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('selectWinnerModal'));
            modal.show();
        }

        async function submitWinner() {
            const winnerId = document.getElementById('winnerSelect').value;
            if (!winnerId) {
                alert('Please select a winner');
                return;
            }

            try {
                const response = await fetch(`/tournament-match/${currentMatchIdForWinner}/winner`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ winner_team_id: winnerId })
                });

                const data = await response.json();
                
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('selectWinnerModal')).hide();
                    location.reload();
                } else {
                    alert('Failed to update winner: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                alert('Error updating winner: ' + error.message);
            }
        }

        async function generateMatchPIN(matchId) {
    try {
        const response = await fetch(`/tournament-match/${matchId}/pin`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Server error: ' + response.status);
        }

        const { success, pin } = await response.json();

        if (!success) {
            throw new Error('Failed to generate PIN');
        }

        document.getElementById('pinDisplay').textContent = pin;

        bootstrap.Modal
            .getOrCreateInstance(document.getElementById('pinModal'))
            .show();

    } catch (error) {
        alert(error.message);
    }
}


    </script>
@endsection