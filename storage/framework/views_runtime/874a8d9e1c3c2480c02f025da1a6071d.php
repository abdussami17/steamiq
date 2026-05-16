<script>
const bracketModal = new bootstrap.Modal(document.getElementById('bracketModal'));
let _bmEventId   = null;
let _bmEditable  = false;
let _bmEventType = 'esports';
let _bmEventName = '';

/* ══════════════════════════════════════════════════════════════════
   OPEN / LOAD
══════════════════════════════════════════════════════════════════ */
function openBracketModal(eventId) {
    _bmEventId = eventId;
    ['bm-error','bm-activities','bm-bracket'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.classList.add('d-none'); el.innerHTML = ''; }
    });
    document.getElementById('bm-loader').classList.remove('d-none');
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

/* ══════════════════════════════════════════════════════════════════
   RENDER TOP-LEVEL
══════════════════════════════════════════════════════════════════ */
function renderBracketModal(res) {
    const { event, setting, activities, type, editable, pods, grand_final } = res;
    _bmEditable  = !!editable;
    _bmEventType = event.type ?? 'esports';
    _bmEventName = event.name ?? '';

    document.getElementById('bm-loader').classList.add('d-none');

    // Badge
    const isEsports = event.type === 'esports';
    document.getElementById('bm-type-badge').innerHTML = `
        <span style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(90deg,rgba(99,102,241,.2),rgba(34,211,238,.15));color:#e2e8f0;font-size:10px;font-weight:800;letter-spacing:2px;text-transform:uppercase;padding:5px 14px;border-radius:999px;border:1px solid rgba(99,102,241,.25);">
            ${isEsports
                ? `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#22d3ee" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M12 12h.01M7 12v-2m0 0V8m0 2H5m2 0h2"/><circle cx="17" cy="12" r="1"/><circle cx="19" cy="10" r="1"/></svg> STEAM ESports`
                : `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#22d3ee" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg> STEAM XR Sports`
            }
        </span>`;

    document.getElementById('bm-title').textContent = event.name;

    // Meta pills
    const statusColor = { live:'#10b981', closed:'#ef4444', draft:'#f59e0b' }[event.status] ?? '#64748b';
    const metaItems = [
        { icon:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>`, label:event.location },
        { icon:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg>`, label:`${(type??'').replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase())}` },
        { icon:`<span style="width:7px;height:7px;border-radius:50%;background:${statusColor};display:inline-block;box-shadow:0 0 6px ${statusColor};"></span>`, label:event.status.charAt(0).toUpperCase()+event.status.slice(1) },
    ];
    if (editable) metaItems.push({ icon:`<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#22d3ee" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>`, label:'Edit Mode' });
    document.getElementById('bm-meta').innerHTML = metaItems
        .map(m => `<span class="bm-pill">${m.icon}<strong>${m.label}</strong></span>`).join('');

    // Reset bracket wrapper
    const bracketEl = document.getElementById('bm-bracket');
    bracketEl.innerHTML = '';

    // Re-init button (admin only)
    if (editable) {
        bracketEl.innerHTML += `
            <div class="d-flex justify-content-end mb-3">
                <button class="bm-reinit-btn" onclick="bmResetBracket(${event.id})">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.95"/></svg>
                    Reset Bracket
                </button>
            </div>`;
    }

    // Build pods
    if (pods && pods.length > 0) {
        bracketEl.innerHTML += buildPodBracketHtml(pods, grand_final, editable);
    } else {
        bracketEl.innerHTML += `<div class="bm-empty">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="8" y1="15" x2="16" y2="15"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
            <p>No teams registered yet</p>
        </div>`;
    }

    bracketEl.classList.remove('d-none');
}

/* ══════════════════════════════════════════════════════════════════
   POD BRACKET HTML
══════════════════════════════════════════════════════════════════ */
function buildPodBracketHtml(pods, grandFinal, editable) {
    let html = '';
    const podColors = { Red:'#ef4444', Blue:'#3b82f6', Green:'#10b981', default:'#6366f1' };
    const hasManyPods = pods.length > 1;

    if (hasManyPods) {
        // Extract last-match winner from a pod's final phase
        const getPodChamp = pod => {
            if (!pod?.phases?.length) return null;
            const lp = pod.phases[pod.phases.length - 1];
            if (!lp?.rounds?.length) return null;
            const lr = lp.rounds[lp.rounds.length - 1];
            const lm = lr?.matches?.[lr.matches.length - 1];
            return lm?.winner_team_id ? { name: lm.winner_name ?? 'TBD', logo: lm.winner_logo ?? null } : null;
        };
        const redPod    = pods.find(p => p.name === 'Red');
        const bluePod   = pods.find(p => p.name === 'Blue');
        const redChamp  = redPod  ? getPodChamp(redPod)  : null;
        const blueChamp = bluePod ? getPodChamp(bluePod) : null;

        const tabs = pods.map((pod, i) => {
            const pc = podColors[pod.name] ?? podColors.default;
            return `<button class="bm-pod-tab ${i===0?'active':''}"
                onclick="bmSwitchPod(this,'bm-pod-${pod.name.toLowerCase()}')"
                style="--pod-color:${pc}">
                <span class="bm-pod-dot" style="background:${pc}"></span>
                ${pod.label}
            </button>`;
        }).join('');

        const _rcName = redChamp?.name  || 'TBD';
        const _bcName = blueChamp?.name || 'TBD';
        const _rcLogo = redChamp?.logo  || '';
        const _bcLogo = blueChamp?.logo || '';
        const _rcEsc  = _rcName.replace(/\\/g,'\\\\').replace(/'/g,"\\'");
        const _bcEsc  = _bcName.replace(/\\/g,'\\\\').replace(/'/g,"\\'");
        const _rlEsc  = _rcLogo.replace(/\\/g,'\\\\').replace(/'/g,"\\'");
        const _blEsc  = _bcLogo.replace(/\\/g,'\\\\').replace(/'/g,"\\'");
        const vsBtn = (redPod && bluePod)
            ? `<button class="bm-vs-btn" onclick="bmShowFinalShowdown('${_rcEsc}','${_bcEsc}','${_rlEsc}','${_blEsc}')">
                   <span class="bm-vs-dot" style="background:#ef4444;box-shadow:0 0 6px #ef4444bb;"></span>
                   ⚡ FINAL SHOWDOWN
                   <span class="bm-vs-dot" style="background:#3b82f6;box-shadow:0 0 6px #3b82f6bb;"></span>
               </button>`
            : '';

        html += `<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:0;">
            <div class="bm-pod-tabs">${tabs}</div>${vsBtn}
        </div><div class="bm-pod-panels">`;
    }

    pods.forEach((pod, pi) => {
        const podColor = podColors[pod.name] ?? podColors.default;
        const panelId  = `bm-pod-${pod.name.toLowerCase()}`;
        const hidden   = hasManyPods && pi > 0;

        html += `<div class="bm-pod-panel${hidden?' d-none':''}" id="${panelId}">`;
        html += `<div class="bm-pod-header">
            <span class="bm-pod-badge" style="background:${podColor}18;color:${podColor};border:1px solid ${podColor}35">
                ${pod.name} Pod
            </span>
        </div>`;

        const hasMultiPhase = pod.phases && pod.phases.length > 1;
        if (hasMultiPhase) {
            const phaseTabs = pod.phases.map((phase, fi) => {
                const icon = phase.key === 'pod_final'
                    ? `<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`
                    : (phase.division === 'Primary'
                        ? `<span class="bm-div-badge bm-div-primary" style="font-size:8px">P</span>`
                        : `<span class="bm-div-badge bm-div-junior" style="font-size:8px">J</span>`);
                return `<button class="bm-phase-tab${fi===0?' active':''}"
                    onclick="bmSwitchPhase(this,'${panelId}-${phase.key}')">
                    ${icon} ${phase.label}
                </button>`;
            }).join('');
            html += `<div class="bm-phase-tabs">${phaseTabs}</div>`;
        }

        html += `<div class="bm-phase-panels">`;
        pod.phases && pod.phases.forEach((phase, fi) => {
            const phaseId = `${panelId}-${phase.key}`;
            const isGrand = phase.key === 'pod_final';
            html += `<div class="bm-phase-panel${hasMultiPhase&&fi>0?' d-none':''}" id="${phaseId}">`;
            html += buildVisualBracket(phase.rounds, editable, podColor, isGrand);
            html += `</div>`;
        });
        html += `</div></div>`;
    });

    if (hasManyPods) html += `</div>`;

    // Grand Final
    if (grandFinal?.rounds?.length) {
        html += `<div class="bm-section-divider"></div>
            <div class="bm-section-header" style="color:#f59e0b;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                Grand Final
            </div>`;
        html += buildVisualBracket(grandFinal.rounds, editable, '#f59e0b', true);
    }

    return html;
}

/* ══════════════════════════════════════════════════════════════════
   VISUAL BRACKET
══════════════════════════════════════════════════════════════════ */
function buildVisualBracket(rounds, editable, accentColor, isChampSection) {
    if (!rounds || rounds.length === 0) {
        return `<div class="bm-empty"><p>Awaiting match data</p></div>`;
    }

    const lastRound = rounds[rounds.length - 1];
    const lastMatch = lastRound?.matches?.[lastRound.matches.length - 1];
    const champion  = lastMatch?.winner_team_id
        ? { id: lastMatch.winner_team_id, name: lastMatch.winner_name }
        : null;

    const roundCols = rounds.map((round, ri) => {
        const isGrandRound = isChampSection && ri === rounds.length - 1;
        const slots = round.matches.map((m, mi) =>
            `<div class="bm-match-slot">${buildMatchCard(m, mi+1, isGrandRound, editable, accentColor)}</div>`
        ).join('');
        const labelStyle = isGrandRound
            ? `color:#f59e0b;border-color:rgba(245,158,11,.3);background:rgba(245,158,11,.08);`
            : `color:${accentColor};border-color:${accentColor}30;background:${accentColor}12;`;
        return `<div class="bm-round ${isGrandRound?'grand':''}">
            <div class="bm-round-label" style="${labelStyle}">${round.round_name}</div>
            <div class="bm-matches-col">${slots}</div>
        </div>`;
    });

    const connCols = rounds.slice(0,-1).map(round => {
        const pc = Math.ceil(round.matches.length / 2);
        return `<div class="bm-conn">${Array.from({length:pc},()=>`<div class="bm-conn-pair"></div>`).join('')}</div>`;
    });

    const interleaved = roundCols.map((col,i) => i<connCols.length ? col+connCols[i] : col).join('');

    const champColor = isChampSection ? '#f59e0b' : accentColor;
    const champHtml  = champion
        ? `<div class="bm-champion-name" style="color:${champColor}">${champion.name}</div>`
        : `<div class="bm-champion-tbd">TBD</div>`;

    const champBox = `
        <div class="bm-champion-col">
            <div class="bm-champion-box" style="border-color:${champColor}35">
                <span class="bm-champion-icon">${isChampSection?'🏆':'🥇'}</span>
                <div class="bm-champion-label">${isChampSection?'Champion':'Phase Winner'}</div>
                ${champHtml}
            </div>
        </div>`;

    return `<div class="bm-scroll"><div class="bm-bracket-wrap">${interleaved}${champBox}</div></div>`;
}

/* ══════════════════════════════════════════════════════════════════
   MATCH CARD
══════════════════════════════════════════════════════════════════ */
function buildMatchCard(match, num, isGrand, editable, accentColor) {
    const { id, team_a, team_b, team_a_score, team_b_score, winner_team_id, status, is_bye_a, is_bye_b } = match;

    const isWinnerA = winner_team_id && team_a && winner_team_id === team_a.id;
    const isWinnerB = winner_team_id && team_b && winner_team_id === team_b.id;

    const renderTeam = (team, side, score, isWinner, isByeSelf, podColor) => {
        const isTbd  = !team;
        const name   = team?.name ?? (isByeSelf ? 'BYE' : 'TBD');
        const divKey = team?.division ? team.division.toLowerCase() : 'null';
        const divLbl = team?.division === 'Primary' ? 'P' : team?.division === 'Junior' ? 'J' : '';
        const init   = name.substring(0,2).toUpperCase();

        let scoreEl;
        if (isWinner) {
            scoreEl = `<div class="bm-score bm-score-win">✓</div>`;
        } else if (score !== null && score !== undefined) {
            scoreEl = `<div class="bm-score bm-score-num">${score}</div>`;
        } else {
            scoreEl = `<div class="bm-score">—</div>`;
        }

        const col = podColor ?? '#6366f1';
        const canClick = editable && !isTbd && !isByeSelf;
        let teamStyle = `border-left:3px solid ${col};`;
        if (isWinner) teamStyle += `background:${col}18;`;
        if (canClick) teamStyle += `cursor:pointer;`;

        const clickHandlers = canClick && !isWinner
            ? `onclick="bmSelectWinner(${id},${team?.id??'null'})" title="Set as winner"`
            : (canClick && isWinner
                ? `onclick="bmClearWinner(${id})" title="Undo winner"`
                : '');

        return `<div class="bm-team${isWinner?' winner':''}${isByeSelf?' bye':''}" style="${teamStyle}" ${clickHandlers}>
            <div class="bm-side-dot" style="background:${col};box-shadow:0 0 5px ${col}80;"></div>
            <div class="bm-team-avatar">${init}</div>
            ${divLbl ? `<div class="bm-div-badge bm-div-${divKey}">${divLbl}</div>` : ''}
            <div class="bm-tname${isTbd?' tbd':''}" title="${name}"${isWinner ? ` style="color:${col}cc;"` : ''}>${name}</div>
            ${scoreEl}
        </div>`;
    };

    const teamAHtml = renderTeam(team_a, 'a', team_a_score, isWinnerA, is_bye_a, accentColor);
    const teamBHtml = renderTeam(team_b, 'b', team_b_score, isWinnerB, is_bye_b, accentColor);

    const statusBadge = status === 'completed'
        ? `<div class="bm-match-status-done">✓</div>`
        : `<div class="bm-match-num">M${num}</div>`;

    const canScore = editable && team_a && team_b && !is_bye_a && !is_bye_b;
    const editBtn  = canScore
        ? `<button class="bm-score-btn" title="Edit score / winner"
                onclick="bmOpenScoreEntry(${id},${team_a.id},'${(team_a.name??'').replace(/'/g,"\\'")}',${team_b.id},'${(team_b.name??'').replace(/'/g,"\\'")}',${team_a_score??'null'},${team_b_score??'null'},${winner_team_id??'null'})">
                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
           </button>`
        : '';

    const matchAccent = isGrand ? 'rgba(245,158,11,.3)' : `${accentColor}30`;
    return `<div class="bm-match${isGrand?' grand':''}${status==='completed'?' bm-match-done':''}" id="bm-match-${id}"
        style="--match-accent:${matchAccent}">
        <div class="bm-match-header">${statusBadge}${editBtn}</div>
        ${teamAHtml}${teamBHtml}
    </div>`;
}

/* ══════════════════════════════════════════════════════════════════
   INTERACTIVE — quick winner click
══════════════════════════════════════════════════════════════════ */
function bmSelectWinner(matchId, teamId) {
    if (!_bmEditable) return;
    fetch(`/events/${_bmEventId}/bracket/matches/${matchId}`, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content??''},
        body:JSON.stringify({winner_team_id:teamId}),
    }).then(r=>r.json()).then(res=>{
        if (res.success) openBracketModal(_bmEventId);
        else alert(res.message||'Failed to set winner');
    }).catch(()=>openBracketModal(_bmEventId));
}

function bmClearWinner(matchId) {
    if (!_bmEditable) return;
    if (!confirm('Remove this winner and reset the match?')) return;
    fetch(`/events/${_bmEventId}/bracket/matches/${matchId}`, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content??''},
        body:JSON.stringify({winner_team_id:null}),
    }).then(r=>r.json()).then(res=>{
        if (res.success) openBracketModal(_bmEventId);
        else alert(res.message||'Failed.');
    }).catch(()=>openBracketModal(_bmEventId));
}

/* ══════════════════════════════════════════════════════════════════
   SCORE ENTRY (pencil icon)
══════════════════════════════════════════════════════════════════ */
function bmOpenScoreEntry(matchId, teamAId, teamAName, teamBId, teamBName, scoreA, scoreB, currentWinner) {
    document.getElementById('bm-score-modal')?.remove();
    const label = _bmEventType === 'esports' ? 'Points' : 'Score';

    (document.getElementById('bracketModal') ?? document.body).insertAdjacentHTML('beforeend', `
    <div id="bm-score-modal" style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.65);animation:bm-fadein .15s ease;">
        <div style="background:#111827;border:1px solid #1e2d45;border-radius:20px;padding:28px 32px;width:360px;max-width:95vw;box-shadow:0 25px 80px rgba(0,0,0,.7);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <h6 style="color:#f1f5f9;font-weight:800;margin:0;font-size:14px;">⚔️ Match Result</h6>
                <button onclick="document.getElementById('bm-score-modal').remove()" style="background:none;border:none;color:#475569;font-size:18px;line-height:1;cursor:pointer;">✕</button>
            </div>
            <div style="display:grid;gap:12px;">
                <div style="background:#0f1828;border:1px solid #1e2d45;border-left:3px solid #ef4444;border-radius:10px;padding:14px;">
                    <div style="color:#fca5a5;font-weight:700;font-size:12px;margin-bottom:8px;">${teamAName}</div>
                    <input id="bm-score-a" type="number" value="${scoreA??''}" placeholder="${label}…"
                        style="width:100%;background:#070d17;border:1px solid #1e2d45;border-radius:8px;color:#f1f5f9;padding:8px 12px;font-size:14px;outline:none;">
                </div>
                <div style="background:#0f1828;border:1px solid #1e2d45;border-left:3px solid #3b82f6;border-radius:10px;padding:14px;">
                    <div style="color:#93c5fd;font-weight:700;font-size:12px;margin-bottom:8px;">${teamBName}</div>
                    <input id="bm-score-b" type="number" value="${scoreB??''}" placeholder="${label}…"
                        style="width:100%;background:#070d17;border:1px solid #1e2d45;border-radius:8px;color:#f1f5f9;padding:8px 12px;font-size:14px;outline:none;">
                </div>
                <div>
                    <div style="color:#64748b;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin-bottom:8px;">Winner</div>
                    <div style="display:flex;gap:8px;">
                        <button id="bm-wpick-a" onclick="bmToggleWinnerPick('a')"
                            style="flex:1;padding:9px;border-radius:8px;border:1px solid ${currentWinner===teamAId?'#ef4444':'#1e2d45'};background:${currentWinner===teamAId?'rgba(239,68,68,.12)':'#0f1828'};color:${currentWinner===teamAId?'#fca5a5':'#64748b'};font-weight:700;font-size:12px;cursor:pointer;transition:.15s;">
                            🔴 ${teamAName}
                        </button>
                        <button id="bm-wpick-b" onclick="bmToggleWinnerPick('b')"
                            style="flex:1;padding:9px;border-radius:8px;border:1px solid ${currentWinner===teamBId?'#3b82f6':'#1e2d45'};background:${currentWinner===teamBId?'rgba(59,130,246,.12)':'#0f1828'};color:${currentWinner===teamBId?'#93c5fd':'#64748b'};font-weight:700;font-size:12px;cursor:pointer;transition:.15s;">
                            🔵 ${teamBName}
                        </button>
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-top:20px;">
                <button onclick="bmSaveScore(${matchId},${teamAId},${teamBId})"
                    style="flex:1;padding:11px;border-radius:10px;background:linear-gradient(135deg,#6366f1,#22d3ee);border:none;color:#fff;font-weight:800;font-size:13px;cursor:pointer;">
                    Save
                </button>
                <button onclick="document.getElementById('bm-score-modal').remove()"
                    style="padding:11px 18px;border-radius:10px;background:#0f1828;border:1px solid #1e2d45;color:#64748b;font-weight:700;font-size:13px;cursor:pointer;">
                    Cancel
                </button>
            </div>
        </div>
    </div>`);

    window._bmWinnerPick = currentWinner === teamAId ? 'a' : currentWinner === teamBId ? 'b' : null;
    window._bmTeamAId    = teamAId;
    window._bmTeamBId    = teamBId;
}

function bmToggleWinnerPick(side) {
    window._bmWinnerPick = window._bmWinnerPick === side ? null : side;
    const selA = window._bmWinnerPick === 'a';
    const selB = window._bmWinnerPick === 'b';
    const aBtn = document.getElementById('bm-wpick-a');
    const bBtn = document.getElementById('bm-wpick-b');
    if (aBtn) { aBtn.style.borderColor=selA?'#ef4444':'#1e2d45'; aBtn.style.background=selA?'rgba(239,68,68,.12)':'#0f1828'; aBtn.style.color=selA?'#fca5a5':'#64748b'; }
    if (bBtn) { bBtn.style.borderColor=selB?'#3b82f6':'#1e2d45'; bBtn.style.background=selB?'rgba(59,130,246,.12)':'#0f1828'; bBtn.style.color=selB?'#93c5fd':'#64748b'; }
}

function bmSaveScore(matchId, teamAId, teamBId) {
    const scoreA   = document.getElementById('bm-score-a')?.value;
    const scoreB   = document.getElementById('bm-score-b')?.value;
    const winnerId = window._bmWinnerPick === 'a' ? teamAId : window._bmWinnerPick === 'b' ? teamBId : null;

    fetch(`/events/${_bmEventId}/bracket/matches/${matchId}`, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content??''},
        body:JSON.stringify({
            winner_team_id:winnerId,
            team_a_score:  scoreA!==''&&scoreA!==undefined ? parseInt(scoreA) : null,
            team_b_score:  scoreB!==''&&scoreB!==undefined ? parseInt(scoreB) : null,
        }),
    }).then(r=>r.json()).then(res=>{
        document.getElementById('bm-score-modal')?.remove();
        if (res.success) openBracketModal(_bmEventId);
        else alert(res.message||'Failed to save.');
    }).catch(()=>{
        document.getElementById('bm-score-modal')?.remove();
        openBracketModal(_bmEventId);
    });
}

/* ══════════════════════════════════════════════════════════════════
   RESET BRACKET
══════════════════════════════════════════════════════════════════ */
function bmResetBracket(eventId) {
    if (!confirm('Reset the entire bracket? All match results will be cleared.')) return;
    fetch(`/events/${eventId}/bracket/init`,{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content??''},
    }).then(r=>r.json()).then(res=>{
        if(res.success) openBracketModal(eventId);
        else alert(res.message||'Failed to reset bracket.');
    }).catch(()=>openBracketModal(eventId));
}

/* ══════════════════════════════════════════════════════════════════
   TAB SWITCHING
══════════════════════════════════════════════════════════════════ */
function bmSwitchPod(btn, targetId) {
    const tabs = btn.closest('.bm-pod-tabs');
    if (!tabs) return;
    tabs.querySelectorAll('.bm-pod-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    const panels = document.querySelectorAll('.bm-pod-panel');
    panels.forEach(p => p.classList.toggle('d-none', p.id !== targetId));
}

function bmSwitchPhase(btn, targetId) {
    const tabs = btn.closest('.bm-phase-tabs');
    if (!tabs) return;
    tabs.querySelectorAll('.bm-phase-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    const container = tabs.nextElementSibling;
    if (!container) return;
    container.querySelectorAll('.bm-phase-panel').forEach(p => p.classList.toggle('d-none', p.id !== targetId));
}

/* ══════════════════════════════════════════════════════════════════
   FINAL SHOWDOWN OVERLAY  (Red Pod Champ  ⚡  vs  ⚡  Blue Pod Champ)
══════════════════════════════════════════════════════════════════ */
function bmShowFinalShowdown(redName, blueName, redLogo, blueLogo) {
    document.getElementById('bm-showdown')?.remove();

    const ri = (redName  || 'TBD').substring(0, 2).toUpperCase();
    const bi = (blueName || 'TBD').substring(0, 2).toUpperCase();

    const makeAvatar = (name, logo, side) => {
        const init = (name || 'TBD').substring(0, 2).toUpperCase();
        const cls  = side === 'red' ? 'bm-sd-avatar-red' : 'bm-sd-avatar-blue';
        if (logo) {
            return `<div class="bm-sd-avatar ${cls}" style="padding:0;overflow:hidden;">
                        <img src="/storage/${logo}" style="width:100%;height:100%;object-fit:cover;" alt="${name}">
                    </div>`;
        }
        return `<div class="bm-sd-avatar ${cls}">${init}</div>`;
    };

    const _typeMap  = { esports: 'STEAM ESports', xr: 'STEAM XR Sports' };
    const typeLabel = _typeMap[_bmEventType] ?? (_bmEventType || 'esports').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    const eventTitle = _bmEventName || 'STEAM IQ Tournament';

    // Floating particles
    let particles = '';
    for (let i = 0; i < 24; i++) {
        const x = Math.random() * 100, y = Math.random() * 100;
        const s = 2 + Math.random() * 4, d = (Math.random() * 4).toFixed(2);
        const col = x < 50 ? '#ef4444' : '#3b82f6';
        particles += `<div class="bm-sd-particle" style="left:${x.toFixed(1)}%;top:${y.toFixed(1)}%;width:${s.toFixed(1)}px;height:${s.toFixed(1)}px;background:${col};animation-delay:${d}s;"></div>`;
    }

    // Lightning bolts (decorative)
    const bolt = `<svg class="bm-sd-bolt" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L4.09 12.26A1 1 0 0 0 5 14h6l-1 8 8.91-10.26A1 1 0 0 0 18 10h-6z"/></svg>`;

    document.body.insertAdjacentHTML('beforeend', `
<div id="bm-showdown" class="bm-sd-overlay" onclick="if(event.target===this)this.remove()">
    ${particles}

    <!-- ambient lines -->
    <div class="bm-sd-line bm-sd-line1"></div>
    <div class="bm-sd-line bm-sd-line2"></div>
    <div class="bm-sd-divider"></div>

    <button class="bm-sd-close" onclick="document.getElementById('bm-showdown').remove()">✕</button>

    <div class="bm-sd-title-wrap">
        <div class="bm-sd-event-name">${eventTitle}</div>
        <div class="bm-sd-supertitle">⚡ FINAL SHOWDOWN ⚡</div>
    </div>

    <div class="bm-sd-inner">

        <!-- RED SIDE -->
        <div class="bm-sd-side bm-sd-red">
            <div class="bm-sd-tag" style="color:#ef4444;background:rgba(239,68,68,.12);border-color:rgba(239,68,68,.3);">
                🔴 RED POD
            </div>
            <div class="bm-sd-champ-lbl">CHAMPION</div>
            <div class="bm-sd-aura bm-sd-aura-red"></div>
            ${makeAvatar(redName, redLogo, 'red')}
            <div class="bm-sd-name" style="color:#fca5a5;text-shadow:0 0 30px #ef444488;">${redName || 'TBD'}</div>
            <div class="bm-sd-bolts">${bolt}${bolt}${bolt}</div>
        </div>

        <!-- VS CENTER -->
        <div class="bm-sd-center">
            <div class="bm-sd-vs-ring r1"></div>
            <div class="bm-sd-vs-ring r2"></div>
            <div class="bm-sd-vs-ring r3"></div>
            <div class="bm-sd-vs">VS</div>
        </div>

        <!-- BLUE SIDE -->
        <div class="bm-sd-side bm-sd-blue">
            <div class="bm-sd-tag" style="color:#3b82f6;background:rgba(59,130,246,.12);border-color:rgba(59,130,246,.3);">
                🔵 BLUE POD
            </div>
            <div class="bm-sd-champ-lbl">CHAMPION</div>
            <div class="bm-sd-aura bm-sd-aura-blue"></div>
            ${makeAvatar(blueName, blueLogo, 'blue')}
            <div class="bm-sd-name" style="color:#93c5fd;text-shadow:0 0 30px #3b82f688;">${blueName || 'TBD'}</div>
            <div class="bm-sd-bolts">${bolt}${bolt}${bolt}</div>
        </div>

    </div>

    <div class="bm-sd-footer">${typeLabel}</div>
</div>`);
}
</script>

<?php /**PATH C:\Users\PC\Desktop\steam-two\resources\views/events/scripts/bracket-script.blade.php ENDPATH**/ ?>