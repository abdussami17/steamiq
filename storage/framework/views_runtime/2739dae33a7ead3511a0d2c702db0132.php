<script>
    // ── Status quick-edit ────────────────────────────────────────────────
    const _ecOpenDrop = {
        id: null
    };

    document.addEventListener('click', function(e) {
        if (_ecOpenDrop.id !== null && !e.target.closest('.ec-status-wrap')) {
            const d = document.getElementById('ec-drop-' + _ecOpenDrop.id);
            if (d) d.classList.add('d-none');
            _ecOpenDrop.id = null;
        }
    });

    function toggleStatusDrop(id, btn) {
        const drop = document.getElementById('ec-drop-' + id);
        if (!drop) return;
        const isOpen = !drop.classList.contains('d-none');
        // close any other open drop
        if (_ecOpenDrop.id !== null && _ecOpenDrop.id !== id) {
            const prev = document.getElementById('ec-drop-' + _ecOpenDrop.id);
            if (prev) prev.classList.add('d-none');
        }
        drop.classList.toggle('d-none', isOpen);
        _ecOpenDrop.id = isOpen ? null : id;
    }

    function setEventStatus(id, status, btn) {
        // If user chooses 'closed', first prompt to select a winner before closing
        if (status === 'closed') {
            // fetch teams to pick a winner
            fetch(`/events/${id}/winner-teams`)
                .then(r => r.json())
                .then(res => {
                    if (!res.success) throw new Error('Failed to fetch teams');
                    const hasFinalTeams = res.final_teams && res.final_teams.length > 0;
                    if (hasFinalTeams) {
                        openChooseWinnerModal(id, res.final_teams, res.current_winner, true);
                    } else {
                        // No teams in finals yet — warn and offer all teams on confirm
                        const confirmed = confirm(
                            'No teams have reached the bracket finals yet.\n\nDo you still want to close this event and manually select a winner from all tournament teams?'
                        );
                        if (confirmed) openChooseWinnerModal(id, res.teams, res.current_winner, false);
                    }
                })
                .catch(err => alert(err.message || 'Failed to load teams'));
            return;
        }

        fetch(`/events/${id}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    status
                })
            })
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                applyCardStatus(id, status);
                location.reload();
            });
    }

    function applyCardStatus(id, status) {
        // Update badge
        const badge = document.getElementById('ec-badge-' + id);
        if (badge) {
            const label = {
                closed: 'Closed',
                live: 'Live',
                draft: 'Draft'
            } [status] ?? (status.charAt(0).toUpperCase() + status.slice(1));
            badge.textContent = label;
            badge.className = `badge me-1 badge-${status}`;
        }
        // Show / hide summary (results) button
        const summaryBtn = document.getElementById('ec-summary-btn-' + id);
        if (summaryBtn) summaryBtn.style.display = status === 'closed' ? '' : 'none';
        // Mark active dropdown option and close it
        const drop = document.getElementById('ec-drop-' + id);
        if (drop) {
            drop.querySelectorAll('.ec-status-opt').forEach(o => {
                o.classList.toggle('active', o.dataset.status === status);
            });
            drop.classList.add('d-none');
        }
        _ecOpenDrop.id = null;
    }
</script>





<script>
    let _chooseEventId = null;
    const chooseWinnerModal = new bootstrap.Modal(document.getElementById('chooseWinnerModal'));

    function openChooseWinnerModal(eventId, teams, currentWinner = null, isFinals = false) {
        _chooseEventId = eventId;
        const list = document.getElementById('cw-team-list');
        list.innerHTML = '';

        // Context note
        const note = document.getElementById('cw-context-note');
        if (note) {
            note.textContent = isFinals ?
                '🏆 Showing teams that reached the bracket finals.' :
                '⚠️ Showing all tournament teams (no bracket finalists found).';
            note.style.color = isFinals ? '#10b981' : '#f59e0b';
        }

        teams.forEach(t => {
    const item = document.createElement('label');
    item.className = 'd-flex align-items-center gap-3';
    item.style.padding = '10px 12px';
    item.style.borderRadius = '8px';
    item.style.cursor = 'pointer';
    item.style.transition = '.15s';

    const isSelected = currentWinner == t.id;

    item.style.border = `1px solid ${isSelected ? '#ffffff' : '#2a3446'}`;
    item.style.background = isSelected ? '#1e293b' : '#0f172a';

    item.innerHTML = `
        <input 
            type="radio" 
            name="cw-winner" 
            value="${t.id}" 
            ${isSelected ? 'checked' : ''} 
            style="width:16px;height:16px;accent-color:#ffffff;cursor:pointer;" 
        />

       
        <div style="font-weight:600;color:#e2e8f0;">
            ${t.name}
        </div>

        ${
            t.org_name || t.subgroup_name 
            ? `<div style="margin-left:auto;color:#94a3b8;font-size:12px;">
                ${t.org_name ? ('Org: ' + t.org_name) : ''}
                ${t.subgroup_name ? (' • Sub: ' + t.subgroup_name) : ''}
              </div>` 
            : ''
        }
    `;

    list.appendChild(item);
});

        document.getElementById('cw-finalize-btn').onclick = finalizeWinnerSelection;
        chooseWinnerModal.show();
    }

    function finalizeWinnerSelection() {
        const selected = document.querySelector('input[name="cw-winner"]:checked');
        if (!selected) {
            alert('Please choose a winner team');
            return;
        }
        const winnerId = selected.value;
        const id = _chooseEventId;
        // send to server: set winner and close event, return stats
        fetch(`/events/${id}/set-winner`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    winner_team_id: winnerId,
                    close: true
                })
            })
            .then(r => r.json())
            .then(res => {
                if (!res.success) throw new Error(res.message || 'Failed to finalize');
                applyCardStatus(id, 'closed');
                chooseWinnerModal.hide();
                setTimeout(() => {
                    location.reload();
                }, 1000);

            })
            .catch(err => alert(err.message || 'Failed to finalize winner'));
    }

    function renderEventResultsInCard(eventId, stats, winner) {

        const container = document.getElementById('event-results-' + eventId);
        if (!container) return;

        const badge = document.getElementById('ec-badge-' + eventId);
        if (!badge) return;

        const status = badge.textContent.trim().toLowerCase();


        if (status !== 'closed') {
            container.innerHTML = '';
            return;
        }


    }

    function renderEventResultsInCard(eventId, stats, winner) {

const container = document.getElementById('event-results-' + eventId);
if (!container) return;

container.innerHTML = '';

const makeStat = (label, value) => `
    <div class="events_stat">
        <div class="events_stat-label">${label}</div>
        <div class="events_stat-value">${value ?? 'N/A'}</div>
    </div>
`;

const topRow = (arr = []) =>
    arr.slice(0, 3).map(i => `
        <div class="row-item">
            <div class="title">${i.student ?? i.team ?? '—'}</div>
            <div class="points">
                <span class="badge badge-live">${i.points ?? 0}</span>
            </div>
        </div>
    `).join('');

const toggleId = `event_toggle_${eventId}`;

container.innerHTML = `
    <div class="event-result-wrapper">

        <!-- TOP STATS -->
        <div class="events_stats-grid">
            ${makeStat('Winner','🏆 '+winner?.name ?? '—')}
            ${makeStat('Participants', stats.participants)}
            ${makeStat('Total Points', stats.total_points)}
            ${makeStat('Top Score', stats.top_score?.points ?? 0)}
        </div>

        <!-- TOGGLE HEADER -->
        <div class="event-toggle-header">
            

            <button class="toggle-btn" title="click to show details" onclick="toggleEventDetails('${toggleId}', this)">
                <i data-lucide="chevron-down" class="toggle-icon"></i>
            </button>
        </div>

        <!-- DETAILS -->
        <div id="${toggleId}" class="event-details collapse-box">

            <div class="section-title">🏆 Top Score</div>
            <div class="row-item">
                <div class="title">
                    ${stats.top_score?.student ?? stats.top_score?.team ?? '—'}
                </div>
                <div class="points">
                    <span class="badge badge-live">${stats.top_score?.points ?? 0}</span>
                </div>
            </div>

            <div class="section-title">Brain</div>
            ${stats.top10_brain?.length ? topRow(stats.top10_brain) : '<div class="text-muted">No data</div>'}

            <div class="section-title">Playground</div>
            ${stats.top10_playground?.length ? topRow(stats.top10_playground) : '<div class="text-muted">No data</div>'}

            <div class="section-title">E-Games</div>
            ${stats.top10_egaming?.length ? topRow(stats.top10_egaming) : '<div class="text-muted">No data</div>'}

            <div class="section-title">E-Sports</div>
            ${stats.top10_esports?.length ? topRow(stats.top10_esports) : '<div class="text-muted">No data</div>'}

        </div>
    </div>
`;

lucide.createIcons();
}
function toggleEventDetails(id, btn) {
    const el = document.getElementById(id);
    if (!el) return;

    const icon = btn.querySelector('.toggle-icon');

    if (el.style.display === 'none' || el.style.display === '') {
        el.style.display = 'block';
        setTimeout(() => el.classList.add('open'), 10);
        icon.style.transform = 'rotate(180deg)';
    } else {
        el.classList.remove('open');
        icon.style.transform = 'rotate(0deg)';
        setTimeout(() => el.style.display = 'none', 200);
    }
}
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.events_card').forEach(card => {

            const badge = card.querySelector('[id^="ec-badge-"]');
            if (!badge) return;

            const eventId = badge.id.replace('ec-badge-', '');
            const status = badge.textContent.trim().toLowerCase();


            if (status !== 'closed') return;

            fetch(`/events/${eventId}/results`)
                .then(r => r.json())
                .then(res => {
                    if (!res.success) return;

                    renderEventResultsInCard(eventId, res.stats, res.winner);
                })
                .catch(() => {});
        });

    });
</script>
<?php /**PATH C:\Users\PC\Downloads\steamiq (5)\resources\views/events/scripts/winner-script.blade.php ENDPATH**/ ?>