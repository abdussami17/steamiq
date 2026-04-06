<script>
    const bracketModal = new bootstrap.Modal(document.getElementById('bracketModal'));
    
    function openBracketModal(eventId) {
        // reset
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
                document.getElementById('bm-error').classList.remove('d-none');
                document.getElementById('bm-error-text').textContent = err.message;
            });
    }
    
    function renderBracketModal(res) {
        const { event, setting, activities, type, rounds, teams } = res;
    
        document.getElementById('bm-loader').classList.add('d-none');
    
        // ── type badge ──
        const isEsports = event.type === 'esports';
        document.getElementById('bm-type-badge').innerHTML = `
            <span style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(90deg,rgba(99,102,241,.2),rgba(34,211,238,.15));color:#e2e8f0;font-size:10px;font-weight:800;letter-spacing:2px;text-transform:uppercase;padding:5px 14px;border-radius:999px;border:1px solid rgba(99,102,241,.25);">
                ${isEsports
                    ? `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#22d3ee" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M12 12h.01M7 12v-2m0 0V8m0 2H5m2 0h2"/><circle cx="17" cy="12" r="1"/><circle cx="19" cy="10" r="1"/></svg> STEAM ESports`
                    : `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#22d3ee" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg> STEAM XR Sports`
                }
            </span>`;
    
        // ── title ──
        document.getElementById('bm-title').textContent = event.name;
    
        // ── meta pills ──
        const statusColor = { live:'#10b981', closed:'#ef4444', draft:'#f59e0b' }[event.status] ?? '#64748b';
        const metaItems = [
            { icon: `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>`, label: event.location },
            { icon: `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg>`, label: `${type.replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase())}` },
            { icon: `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`, label: `${teams?.length ?? setting.number_of_teams ?? 0} Teams` },
            ...(setting.game ? [{ icon: `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M12 12h.01M7 12v-2m0 0V8m0 2H5m2 0h2"/><circle cx="17" cy="12" r="1"/><circle cx="19" cy="10" r="1"/></svg>`, label: setting.game }] : []),
            { icon: `<span style="width:7px;height:7px;border-radius:50%;background:${statusColor};display:inline-block;box-shadow:0 0 6px ${statusColor};"></span>`, label: event.status.charAt(0).toUpperCase()+event.status.slice(1) },
        ];
    
        document.getElementById('bm-meta').innerHTML = metaItems
            .map(m => `<span class="bm-pill" style="color:#64748b;">${m.icon}<strong style="color:#e2e8f0;">${m.label}</strong></span>`).join('');
    
        // ── XR activities ──
        if (event.type === 'xr' && activities?.length) {
            const tagMap = { brain:'🧠 Brain', esports:'🕹 ESports', egaming:'🎮 Gaming', playground:'🏃 Outdoor' };
            const cards = activities.map(a => {
                const isMission = a.activity_or_mission === 'mission';
                const tag = isMission
                    ? `<span class="bm-tag bm-tag-mission">🎖 Mission</span>`
                    : `<span class="bm-tag bm-tag-${a.activity_type ?? 'brain'}">${tagMap[a.activity_type] ?? a.activity_type}</span>`;
                const sub  = a.brain_type ?? a.esports_type ?? a.egaming_type ?? '';
                const desc = a.brain_description ?? a.esports_description ?? a.egaming_description ?? a.playground_description ?? '';
                return `<div class="bm-act-card">
                    ${tag}
                    ${isMission ? `<div style="font-size:13px;font-weight:700;color:#f1f5f9;margin-bottom:4px;">${a.badge_name ?? 'Mission'}</div>` : ''}
                    ${sub  ? `<div style="font-size:11px;color:#475569;margin-bottom:3px;">${sub.replace(/_/g,' ')}</div>` : ''}
                    ${desc ? `<div style="font-size:11px;color:#334155;margin-bottom:8px;line-height:1.5;">${desc}</div>` : ''}
                    <div class="bm-act-score">${a.max_score ?? 0}<span>pts</span></div>
                    ${a.point_structure ? `<div style="font-size:9px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:1.5px;margin-top:4px;">${a.point_structure.replace('_',' ')}</div>` : ''}
                </div>`;
            }).join('');
    
            const actEl = document.getElementById('bm-activities');
            actEl.innerHTML = `
                <div class="bm-section-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    C.A.M. Activities &amp; Missions
                </div>
                <div class="bm-act-grid">${cards}</div>
                <div class="bm-section-divider"></div>`;
            actEl.classList.remove('d-none');
        }
    
        // ── bracket ──
        const bracketEl = document.getElementById('bm-bracket');
        bracketEl.innerHTML = type === 'round_robin'
            ? buildRRHtml(rounds, teams)
            : buildEliminationHtml(rounds, type === 'double_elimination');
        bracketEl.classList.remove('d-none');
    }
    
    /* ────────────────────────────────
       Round Robin
    ──────────────────────────────── */
    function buildRRHtml(rounds, teams) {
        const matches = rounds[0]?.matches ?? [];
        if (!matches.length) return buildEmpty('No matches scheduled yet');
    
        const rows = matches.map((m, i) => `
            <tr>
                <td style="color:#334155;font-size:10px;font-weight:800;width:36px;">${String(i+1).padStart(2,'0')}</td>
                <td><div class="bm-vs">
                    <span style="font-weight:700;color:${m[0].name!=='TBD'?'#e2e8f0':'#334155'};">${m[0].name}</span>
                    <span class="bm-vs-div">VS</span>
                    <span style="font-weight:700;color:${m[1].name!=='TBD'?'#e2e8f0':'#334155'};">${m[1].name}</span>
                </div></td>
                <td><span class="bm-pending">Pending</span></td>
                <td style="color:#273549;font-weight:700;font-size:13px;letter-spacing:2px;">— : —</td>
            </tr>`).join('');
    
        const teamRows = teams?.length
            ? `<div class="bm-section-divider" style="margin:20px 0;"></div>
               <div class="bm-section-header">
                   <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                   Registered Teams
               </div>
               <div style="display:flex;flex-wrap:wrap;gap:10px;">
                   ${teams.map((t,i) => `
                       <div style="display:inline-flex;align-items:center;gap:8px;background:#131d2e;border:1px solid #1e2d45;border-radius:10px;padding:8px 14px;font-size:12px;font-weight:600;color:#e2e8f0;">
                           <div style="width:20px;height:20px;border-radius:6px;background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.2);display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:900;color:#6366f1;">${i+1}</div>
                           ${t.name}
                       </div>`).join('')}
               </div>`
            : '';
    
        return `
            <div class="bm-section-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                Match Schedule
            </div>
            <div class="bm-rr"><table>
                <thead><tr><th>#</th><th>Match</th><th>Status</th><th>Score</th></tr></thead>
                <tbody>${rows}</tbody>
            </table></div>
            ${teamRows}`;
    }
    
    /* ────────────────────────────────
       Elimination
    ──────────────────────────────── */
    function buildEliminationHtml(rounds, isDouble) {
        if (!rounds?.length) return buildEmpty('No bracket data available');
    
        if (!isDouble) {
            return `
                <div class="bm-section-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg>
                    Tournament Bracket
                </div>
                <div class="bm-scroll"><div class="bm-bracket-wrap">${buildRoundsHtml(rounds)}</div></div>`;
        }
    
        const groups = { Winners:[], Losers:[], 'Grand Final':[] };
        rounds.forEach(r => { if (groups[r.bracket] !== undefined) groups[r.bracket].push(r); });
    
        return Object.entries(groups).filter(([,arr])=>arr.length).map(([label, arr]) => {
            const labelMap = { Winners:'bm-winners', Losers:'bm-losers', 'Grand Final':'bm-gf' };
            const iconMap  = {
                Winners: `<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`,
                Losers:  `<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`,
                'Grand Final': `<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`,
            };
            return `<div class="bm-section-wrap">
                <span class="bm-section-label ${labelMap[label]}">${iconMap[label] ?? ''} ${label} Bracket</span>
                <div class="bm-scroll"><div class="bm-bracket-wrap">${buildRoundsHtml(arr)}</div></div>
            </div>`;
        }).join('');
    }
    
    function buildRoundsHtml(rounds) {
        return rounds.map((round, ri) => {
            const isGrand  = round.name === 'Grand Final';
            const isLosers = round.bracket === 'Losers';
            const matches  = round.matches.map((m, mi) => buildMatchHtml(m, mi+1, isGrand)).join('');
            const conn     = ri < rounds.length - 1
                ? `<div class="bm-conn">${round.matches.map(()=>'<div class="bm-conn-line"></div>').join('')}</div>`
                : '';
            return `<div class="bm-round ${isGrand?'grand':''} ${isLosers?'losers':''}">
                <div class="bm-round-label">${round.name}</div>
                <div class="bm-matches-col">${matches}</div>
            </div>${conn}`;
        }).join('');
    }
    
    function buildMatchHtml(match, num, isGrand) {
        const teams = match.map(t => {
            const isBye = t.name === 'BYE';
            const isTbd = t.name === 'TBD' || t.seed === null;
            const initials = (!isBye && !isTbd) ? t.name.substring(0,2).toUpperCase() : '?';
            return `<div class="bm-team ${isBye?'bye':''}">
                <div class="bm-team-avatar" style="${!isTbd?'background:linear-gradient(135deg,#1e2d45,#152035);color:#6366f1;font-weight:900;':''}">${initials}</div>
                <div class="bm-tname ${isTbd?'tbd':''}" title="${t.name}">${t.name}</div>
                <div class="bm-score">—</div>
            </div>`;
        }).join('');
    
        return `<div class="bm-match ${isGrand?'grand':''}">
            <div class="bm-match-num">Match ${num}</div>
            ${teams}
        </div>`;
    }
    
    function buildEmpty(msg) {
        return `<div class="bm-empty">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="8" y1="15" x2="16" y2="15"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
            <p>${msg}</p>
        </div>`;
    }
    </script>