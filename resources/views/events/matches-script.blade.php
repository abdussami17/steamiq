<script>
    document.addEventListener('DOMContentLoaded', async () => {
        await loadTeams();
        fetchMatches();
    
        const matchForm = document.getElementById('matchForm');
        matchForm.addEventListener('submit', async e => {
            e.preventDefault();
    
            const formData = new FormData();
            formData.append('event_id', 1);
            formData.append('match_name', document.getElementById('matchName').value);
            formData.append('team_a', document.getElementById('matchTeamA').value);
            formData.append('team_b', document.getElementById('matchTeamB').value);
            formData.append('game_title', document.getElementById('matchGame').value);
            formData.append('format', document.getElementById('matchFormat').value);
            formData.append('date', document.getElementById('matchDate').value);
            formData.append('time', document.getElementById('matchTime').value);
    
            try {
                const res = await fetch('/matches', {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    toastr.success(data.message);
                    bootstrap.Modal.getInstance(document.getElementById('matchModal')).hide();
                    fetchMatches();
                } else if (data.errors) {
                    Object.values(data.errors).forEach(err => toastr.error(err));
                }
            } catch (err) {
                toastr.error('Something went wrong while creating match.');
                console.error(err);
            }
        });
    });
    
    // Load teams for dropdowns
    async function loadTeams() {
        try {
            const res = await fetch('/matches/teams', { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('Failed to fetch teams');
            const teams = await res.json();
    
            const teamASelect = document.getElementById('matchTeamA');
            const teamBSelect = document.getElementById('matchTeamB');
            teamASelect.innerHTML = '<option value="">-- Select Team --</option>';
            teamBSelect.innerHTML = '<option value="">-- Select Team --</option>';
    
            if (!teams.length) {
                teamASelect.innerHTML += '<option value="">No teams available</option>';
                teamBSelect.innerHTML += '<option value="">No teams available</option>';
                return;
            }
    
            teams.forEach(team => {
                const optionA = document.createElement('option');
                optionA.value = team.id;
                optionA.textContent = team.team_name;
                teamASelect.appendChild(optionA);
    
                const optionB = document.createElement('option');
                optionB.value = team.id;
                optionB.textContent = team.team_name;
                teamBSelect.appendChild(optionB);
            });
        } catch (err) {
            toastr.error('Failed to load teams.');
            console.error(err);
        }
    }
    
    // Fetch and render matches
    async function fetchMatches() {
        const container = document.querySelector('.schedule-grid');
        container.innerHTML = '<div style="padding:1rem;color:var(--text-dim)">Loading...</div>';
    
        try {
            const res = await fetch('/matches/fetch');
            if (!res.ok) throw new Error('Network response not ok');
            const matches = await res.json();
    
            if (!matches.length) {
                container.innerHTML = '<div style="padding:1rem;color:var(--text-dim)">No matches scheduled.</div>';
                return;
            }
    
            container.innerHTML = matches.map(m => {
                const matchDate = m.date ? new Date(m.date).toLocaleDateString('en-US', { month: 'short', day: '2-digit' }) : 'N/A';
                const matchTime = m.time || 'N/A';
                const teamAName = m.team_a?.team_name || 'TBD';
                const teamBName = m.team_b?.team_name || 'TBD';
                const teamAId = m.team_a?.id || '';
                const teamBId = m.team_b?.id || '';
                const matchName = m.match_name || 'Unnamed Match';
                const status = m.status || 'scheduled';
                const isLive = status === 'live';
                const isCompleted = status === 'completed';
    
                return `
                <div class="schedule-item">
                    <div class="schedule-time">
                        <div style="font-size:8px !important" class="badge mb-2 badge-${status}">${status.toUpperCase()}</div>
                        <div style="font-size:0.8rem;color:var(--text-dim)">${matchDate}</div>
                        <div>${matchTime}</div>
                    </div>
                    <div class="schedule-match">
                        <div class="schedule-match-title">${matchName}</div>
                        <div class="schedule-match-teams">${teamAName} vs ${teamBName}</div>
                    </div>
                    <div class="schedule-actions ">
                        <button class="btn btn-primary" onclick="generateMatchPIN(${m.id})">PIN</button>
                        ${isLive && !isCompleted ? `<button class="btn btn-secondary" onclick="openAddRoundModal(${m.id},'${teamAName}','${teamBName}',${teamAId},${teamBId})">Add Round</button>` : ''}
                    </div>
                </div>`;
            }).join('');
    
            if (typeof lucide !== 'undefined') lucide.createIcons();
        } catch (err) {
            container.innerHTML = '<div style="padding:1rem;color:red;">Failed to fetch matches.</div>';
            toastr.error('Failed to fetch matches.');
            console.error(err);
        }
    }
    
    // Generate PIN modal
    async function generateMatchPIN(matchId) {
        try {
            const res = await fetch(`/matches/${matchId}/generate-pin`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                const modalEl = document.getElementById('matchPinModal');
                modalEl.querySelector('.pin-value').textContent = data.pin;
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
                toastr.success(data.message || 'PIN generated successfully');
            } else if (data.errors) {
                Object.values(data.errors).forEach(err => toastr.error(err));
            }
        } catch (err) {
            toastr.error('Failed to generate PIN.');
            console.error(err);
        }
    }
    
    // Copy PIN
    function copyPin() {
        const pin = document.querySelector('#matchPinModal .pin-value').textContent;
        navigator.clipboard.writeText(pin).then(() => toastr.success('PIN copied to clipboard'));
    }
    
    // Add Round modal logic
    let currentMatchId;
    
    function openAddRoundModal(matchId, teamAName, teamBName, teamAId, teamBId) {
        currentMatchId = matchId;
        const select = document.getElementById('roundWinner');
        select.innerHTML = `
            <option value="">-- Select Winner --</option>
            <option value="${teamAId}">${teamAName}</option>
            <option value="${teamBId}">${teamBName}</option>
        `;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('addRoundModal')).show();
    }
    
    async function submitRound() {
    const winnerId = document.getElementById('roundWinner').value;
    if (!winnerId) return toastr.error('Select winner');

    console.log('Submitting round', { currentMatchId, winnerId });

    try {
        const res = await fetch(`/matches/${currentMatchId}/round`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ winner_team_id: winnerId })
        });

        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch {
            console.error('Server returned non-JSON response', text);
            throw new Error('Server returned non-JSON response');
        }

        if (res.ok && data.success) {
            toastr.success('Round added');
            if (data.winner) toastr.success('Match completed! Winner assigned.');
            fetchMatches();
            bootstrap.Modal.getInstance(document.getElementById('addRoundModal')).hide();
        } else if (data.errors) {
            console.error('Validation or business errors', data.errors);
            Object.values(data.errors).forEach(err => toastr.error(err));
        } else {
            console.error('Unknown error response', data);
            toastr.error('Failed to add round: Unknown error');
        }

    } catch (err) {
        console.error('Error submitting round', err);
        toastr.error('Failed to add round: ' + err.message);
    }
}


    </script>
    