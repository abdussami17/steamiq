
<style>
    :root {
        --bm-bg:      #0a0e1a;
        --bm-card:    #111827;
        --bm-match:   #1a2236;
        --bm-accent:  #6366f1;
        --bm-cyan:    #22d3ee;
        --bm-win:     #10b981;
        --bm-border:  #1e2d45;
        --bm-muted:   #64748b;
        --bm-text:    #f1f5f9;
        --bm-conn:    #334155;
    }
    
    /* ── META PILLS ── */
    .bm-pill {
        display:inline-flex;align-items:center;gap:5px;
        background:#1a2236;border:1px solid #1e2d45;
        border-radius:20px;padding:3px 12px;
        font-size:11px;color:#64748b;
    }
    .bm-pill strong { color:#f1f5f9; }
    
    /* ── ACTIVITY CARDS ── */
    .bm-act-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px; }
    .bm-act-card {
        background:#111827;border:1px solid #1e2d45;border-radius:12px;
        padding:16px;transition:border-color .2s,transform .2s;
    }
    .bm-act-card:hover { border-color:#6366f1;transform:translateY(-2px); }
    .bm-tag {
        display:inline-block;font-size:9px;font-weight:700;letter-spacing:2px;
        text-transform:uppercase;padding:2px 10px;border-radius:20px;margin-bottom:8px;
    }
    .bm-tag-brain      { background:rgba(139,92,246,.15);color:#a78bfa;border:1px solid rgba(139,92,246,.3); }
    .bm-tag-esports    { background:rgba(99,102,241,.15); color:#818cf8;border:1px solid rgba(99,102,241,.3); }
    .bm-tag-egaming    { background:rgba(34,211,238,.1);  color:#22d3ee;border:1px solid rgba(34,211,238,.2); }
    .bm-tag-playground { background:rgba(16,185,129,.1);  color:#34d399;border:1px solid rgba(16,185,129,.2); }
    .bm-tag-mission    { background:rgba(251,191,36,.1);  color:#fbbf24;border:1px solid rgba(251,191,36,.2); }
    .bm-act-score { font-size:26px;font-weight:800;color:#22d3ee;line-height:1;margin:6px 0 3px; }
    .bm-act-score span { font-size:11px;font-weight:400;color:#64748b;margin-left:3px; }
    
    /* ── BRACKET SCROLL ── */
    .bm-scroll { overflow-x:auto;padding-bottom:8px; }
    .bm-bracket-wrap { display:flex;align-items:flex-start;gap:0;min-width:max-content; }
    
    /* ── ROUND ── */
    .bm-round { display:flex;flex-direction:column;align-items:center;min-width:200px; }
    .bm-round-label {
        font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;
        color:#22d3ee;margin-bottom:16px;padding:4px 14px;
        border:1px solid rgba(34,211,238,.2);border-radius:20px;
        background:rgba(34,211,238,.05);white-space:nowrap;
    }
    .bm-round.grand .bm-round-label { color:#fbbf24;border-color:rgba(251,191,36,.3);background:rgba(251,191,36,.06); }
    .bm-matches-col { display:flex;flex-direction:column;gap:14px;width:100%; }
    
    /* ── CONNECTOR ── */
    .bm-conn { width:36px;align-self:stretch;display:flex;flex-direction:column;padding:28px 0;gap:14px; }
    .bm-conn-line { flex:1;position:relative; }
    .bm-conn-line::before { content:'';position:absolute;right:0;top:25%;bottom:25%;width:1px;background:#334155; }
    .bm-conn-line::after  { content:'';position:absolute;right:0;top:50%;width:100%;height:1px;background:#334155; }
    
    /* ── MATCH CARD ── */
    .bm-match {
        background:#1a2236;border:1px solid #1e2d45;border-radius:10px;
        overflow:hidden;width:185px;transition:border-color .2s,box-shadow .2s;
    }
    .bm-match:hover { border-color:#6366f1;box-shadow:0 0 16px rgba(99,102,241,.2); }
    .bm-match.grand { border-color:rgba(251,191,36,.35);box-shadow:0 0 20px rgba(251,191,36,.12);width:200px; }
    .bm-match-num { font-size:8px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#64748b;padding:5px 10px 0; }
    .bm-team {
        display:flex;align-items:center;gap:8px;padding:9px 10px;
        border-bottom:1px solid #1e2d45;transition:background .15s;
    }
    .bm-team:last-child { border-bottom:none; }
    .bm-team:hover { background:rgba(99,102,241,.07); }
    .bm-team.bye { opacity:.3; }
    .bm-seed {
        min-width:20px;height:20px;border-radius:5px;
        background:rgba(99,102,241,.2);border:1px solid rgba(99,102,241,.3);
        display:flex;align-items:center;justify-content:center;
        font-size:9px;font-weight:800;color:#6366f1;flex-shrink:0;
    }
    .bm-seed.tbd { background:rgba(100,116,139,.1);border-color:rgba(100,116,139,.2);color:#64748b; }
    .bm-tname { font-size:12px;font-weight:600;color:#f1f5f9;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .bm-tname.tbd { color:#64748b;font-weight:400;font-style:italic; }
    .bm-score { width:26px;height:20px;background:rgba(255,255,255,.04);border:1px solid #1e2d45;border-radius:4px;font-size:11px;font-weight:700;color:#64748b;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
    
    /* ── ROUND ROBIN ── */
    .bm-rr { background:#111827;border-radius:12px;overflow:hidden;border:1px solid #1e2d45; }
    .bm-rr table { width:100%;border-collapse:collapse; }
    .bm-rr th { background:#1a2236;color:#22d3ee;font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:12px 16px;border-bottom:1px solid #1e2d45;text-align:left; }
    .bm-rr td { padding:11px 16px;border-bottom:1px solid #1e2d45;font-size:12px;color:#f1f5f9; }
    .bm-rr tr:last-child td { border-bottom:none; }
    .bm-rr tr:hover td { background:rgba(99,102,241,.04); }
    .bm-vs { display:inline-flex;align-items:center;gap:8px;font-weight:600; }
    .bm-vs-div { font-size:9px;font-weight:800;color:#6366f1;padding:2px 7px;background:rgba(99,102,241,.15);border-radius:4px;letter-spacing:1px; }
    .bm-pending { font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#64748b;padding:2px 9px;background:rgba(100,116,139,.1);border:1px solid rgba(100,116,139,.2);border-radius:20px; }
    
    /* ── SECTION LABELS ── */
    .bm-section-label {
        font-size:9px;font-weight:800;letter-spacing:3px;text-transform:uppercase;
        padding:6px 16px;border-radius:8px;margin-bottom:14px;display:inline-block;
    }
    .bm-winners { color:#10b981;background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2); }
    .bm-losers  { color:#f87171;background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.2); }
    .bm-gf      { color:#fbbf24;background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.2); }
    </style>
    
    <script>
    const bracketModal = new bootstrap.Modal(document.getElementById('bracketModal'));
    
    function openBracketModal(eventId) {
        // reset state
        document.getElementById('bm-loader').classList.remove('d-none');
        document.getElementById('bm-error').classList.add('d-none');
        document.getElementById('bm-activities').classList.add('d-none');
        document.getElementById('bm-bracket').classList.add('d-none');
        document.getElementById('bm-title').textContent    = '';
        document.getElementById('bm-meta').innerHTML       = '';
        document.getElementById('bm-type-badge').innerHTML = '';
    
        bracketModal.show();
    
        fetch(`/events/${eventId}/bracket`)
            .then(r => r.json())
            .then(res => {
                if (!res.success) throw new Error(res.message || 'Failed to load bracket.');
                renderBracketModal(res);
            })
            .catch(err => {
                document.getElementById('bm-loader').classList.add('d-none');
                const el = document.getElementById('bm-error');
                el.textContent = err.message;
                el.classList.remove('d-none');
            });
    }
    
    function renderBracketModal(res) {
        const { event, setting, activities, type, rounds } = res;
    
        document.getElementById('bm-loader').classList.add('d-none');
    
        // type badge
        document.getElementById('bm-type-badge').innerHTML =
            `<span style="background:linear-gradient(90deg,#6366f1,#22d3ee);color:#fff;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:3px 12px;border-radius:20px;">
                ${event.type === 'esports' ? 'STEAM ESports' : 'STEAM XR Sports'}
            </span>`;
    
        // title
        document.getElementById('bm-title').textContent = event.name;
    
        // meta pills
        const statusColor = event.status === 'live' ? '#10b981' : event.status === 'closed' ? '#ef4444' : '#f59e0b';
        const metaItems = [
            `📍 <strong>${event.location}</strong>`,
            `🏆 <strong>${type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}</strong>`,
            `👥 <strong>${setting.number_of_teams ?? 0}</strong> Teams`,
            setting.game ? `🎮 <strong>${setting.game}</strong>` : null,
            `<span style="width:7px;height:7px;border-radius:50%;background:${statusColor};display:inline-block;"></span> <strong>${event.status.charAt(0).toUpperCase() + event.status.slice(1)}</strong>`,
        ].filter(Boolean);
    
        document.getElementById('bm-meta').innerHTML = metaItems
            .map(m => `<span class="bm-pill">${m}</span>`).join('');
    
        // XR activities
        if (event.type === 'xr' && activities.length) {
            const tagMap = { brain:'🧠 Brain Game', esports:'🕹 E-Sports', egaming:'🎮 E-Gaming', playground:'🏃 Playground' };
            const cards  = activities.map(a => {
                const isMission = a.activity_or_mission === 'mission';
                const tag       = isMission
                    ? `<span class="bm-tag bm-tag-mission">🎖 Mission</span>`
                    : `<span class="bm-tag bm-tag-${a.activity_type}">${tagMap[a.activity_type] ?? a.activity_type}</span>`;
                const sub  = a.brain_type ?? a.esports_type ?? a.egaming_type ?? '';
                const desc = a.brain_description ?? a.esports_description ?? a.egaming_description ?? a.playground_description ?? '';
                const name = isMission ? `<div style="font-size:13px;font-weight:700;color:#f1f5f9;">${a.badge_name ?? 'Mission'}</div>` : '';
                return `<div class="bm-act-card">
                    ${tag}${name}
                    ${sub  ? `<div style="font-size:11px;color:#64748b;margin-bottom:3px;">${sub.replace(/_/g,' ')}</div>` : ''}
                    ${desc ? `<div style="font-size:11px;color:#64748b;margin-bottom:6px;">${desc}</div>` : ''}
                    <div class="bm-act-score">${a.max_score ?? 0}<span>pts</span></div>
                    ${a.point_structure ? `<div style="font-size:10px;color:#64748b;text-transform:uppercase;letter-spacing:1px;">${a.point_structure.replace('_',' ')}</div>` : ''}
                </div>`;
            }).join('');
    
            const actEl = document.getElementById('bm-activities');
            actEl.innerHTML = `
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#22d3ee;margin-bottom:12px;">
                    C.A.M. Activities &amp; Missions
                </div>
                <div class="bm-act-grid">${cards}</div>
                <hr style="border-color:#1e2d45;margin:20px 0 0;">`;
            actEl.classList.remove('d-none');
        }
    
        // bracket
        const bracketEl = document.getElementById('bm-bracket');
        bracketEl.innerHTML = type === 'round_robin'
            ? buildRRHtml(rounds)
            : buildEliminationHtml(rounds, type === 'double_elimination');
        bracketEl.classList.remove('d-none');
    }
    
    /* ── Round Robin HTML ── */
    function buildRRHtml(rounds) {
        const rows = (rounds[0]?.matches ?? []).map((m, i) => `
            <tr>
                <td style="color:#64748b;font-size:10px;font-weight:700;">${i + 1}</td>
                <td><div class="bm-vs">
                    <span>${m[0].name}</span>
                    <span class="bm-vs-div">VS</span>
                    <span>${m[1].name}</span>
                </div></td>
                <td><span class="bm-pending">Pending</span></td>
                <td style="color:#64748b;">— : —</td>
            </tr>`).join('');
    
        return `<div class="bm-rr"><table>
            <thead><tr><th>#</th><th>Match</th><th>Status</th><th>Score</th></tr></thead>
            <tbody>${rows}</tbody>
        </table></div>`;
    }
    
    /* ── Elimination HTML ── */
    function buildEliminationHtml(rounds, isDouble) {
        if (!isDouble) {
            return `<div class="bm-scroll"><div class="bm-bracket-wrap">${buildRoundsHtml(rounds)}</div></div>`;
        }
    
        const groups = { Winners: [], Losers: [], 'Grand Final': [] };
        rounds.forEach(r => { if (groups[r.bracket] !== undefined) groups[r.bracket].push(r); });
    
        return Object.entries(groups).filter(([, arr]) => arr.length).map(([label, arr]) => {
            const cls  = label === 'Grand Final' ? 'bm-gf' : label === 'Losers' ? 'bm-losers' : 'bm-winners';
            return `<div class="mb-4">
                <span class="bm-section-label ${cls}">${label} Bracket</span>
                <div class="bm-scroll"><div class="bm-bracket-wrap">${buildRoundsHtml(arr)}</div></div>
            </div>`;
        }).join('');
    }
    
    function buildRoundsHtml(rounds) {
        return rounds.map((round, ri) => {
            const isGrand  = round.name === 'Grand Final';
            const matches  = round.matches.map((m, mi) => buildMatchHtml(m, mi + 1, isGrand)).join('');
            const conn     = ri < rounds.length - 1
                ? `<div class="bm-conn">${round.matches.map(() => '<div class="bm-conn-line"></div>').join('')}</div>`
                : '';
    
            return `<div class="bm-round ${isGrand ? 'grand' : ''}">
                <div class="bm-round-label">${round.name}</div>
                <div class="bm-matches-col">${matches}</div>
            </div>${conn}`;
        }).join('');
    }
    
    function buildMatchHtml(match, num, isGrand) {
        const teams = match.map(t => {
            const isBye = t.name === 'BYE';
            const isTbd = t.name === 'TBD' || t.seed === null;
            return `<div class="bm-team ${isBye ? 'bye' : ''}">
                <div class="bm-seed ${isTbd ? 'tbd' : ''}">${t.seed ?? '?'}</div>
                <div class="bm-tname ${isTbd ? 'tbd' : ''}">${t.name}</div>
                <div class="bm-score">—</div>
            </div>`;
        }).join('');
    
        return `<div class="bm-match ${isGrand ? 'grand' : ''}">
            <div class="bm-match-num">Match ${num}</div>
            ${teams}
        </div>`;
    }
    </script>