{{-- Bulk Edit Modal --}}
<div class="modal fade" id="bulkEditModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i data-lucide="edit-3" style="width:16px;height:16px;vertical-align:-2px;margin-right:6px;"></i>
                    Bulk Edit Scores
                    <span id="bulkEditCount" class="badge ms-2"
                        style="background:#58a6ff;color:#000;font-size:12px;"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:0;">
                <div class="table-responsive" style="max-height:400px;overflow-y:auto;-webkit-overflow-scrolling:touch;">
                    <table class="table table-sm mb-0" id="bulkEditTable">
                        <thead>
                            <tr>
                                <th style="width:30px;"><input type="checkbox" id="bulkSelectAll" title="Select all">
                                </th>
                                <th>Entity</th>
                                <th>Activity</th>
                                <th>Current</th>
                                <th>New Points</th>
                                <th>Max</th>
                            </tr>
                        </thead>
                        <tbody id="bulkEditTbody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="bulkSaveConfirmBtn" class="btn btn-primary">
                    <i data-lucide="save" ></i>
                    Save All Changes
                </button>
            </div>
        </div>
    </div>
</div>

<div id="lb-toast"></div>

<script>
    window.USER_ROLE = {{ auth()->check() ? auth()->user()->role : 0 }};
</script>

<script>
    (function() {
        'use strict';

        const $id = id => document.getElementById(id);

        /* ═══════════════════════════════════════════════════════════
           STATE
        ═══════════════════════════════════════════════════════════ */
        let _currentData = null;
        let _bulkMode = false;
        let _bulkSelected = [];

        /* ═══════════════════════════════════════════════════════════
           MOBILE DETECTION
        ═══════════════════════════════════════════════════════════ */
        function isTouchDevice() {
            return ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
        }

        /* ═══════════════════════════════════════════════════════════
           TOAST
        ═══════════════════════════════════════════════════════════ */
        function toast(msg, type = 'ok') {
            const el = $id('lb-toast');
            el.textContent = msg;
            el.className = 'show ' + type;
            clearTimeout(el._t);
            el._t = setTimeout(() => { el.className = ''; }, 2800);
        }

        /* ═══════════════════════════════════════════════════════════
           HELPERS
        ═══════════════════════════════════════════════════════════ */
        function scorePill(pts, slug) {
            const v = parseInt(pts) || 0;
            if (!v) return '<span class="score-zero">—</span>';
            return `<span class="score-pill score-${slug}">${Number(v).toLocaleString()}</span>`;
        }

        function bonusPill(pts) {
            const v = parseInt(pts) || 0;
            if (!v) return '<span class="score-zero">—</span>';
            return `<span class="score-pill score-bonus">+${Number(v).toLocaleString()}</span>`;
        }

        function rankBadge(r) {
            if (!r && r !== 0) return '';
            if (r === 1) return `<span class="rank-medal rank-1">1</span>`;
            if (r === 2) return `<span class="rank-medal rank-2">2</span>`;
            if (r === 3) return `<span class="rank-medal rank-3">3</span>`;
            return `<span class="rank-medal rank-n">${r}</span>`;
        }

        function renderCardBadges(cards) {
    if (!cards || !cards.length) return '<span class="score-zero">—</span>';

    const map = {
        red: 'card-red',
        yellow: 'card-yellow',
        orange: 'card-orange',
        unknown: 'card-unknown'
    };

    
    return `<span class="card-badges">${cards.map(c =>
        `<span class="card-chip ${map[c.type] || 'card-unknown'}"
            title="${esc((c.type || 'unknown').toUpperCase())}">
            
            <span class="card-chip-type">1</span>

            <button type="button"
                class="card-unassign-btn"
                data-assignment-id="${c.assignment_id}"
                data-card-type="${esc(c.type || 'unknown')}"><i height="11" width="11" data-lucide="x"></i></button>
        </span>`
    ).join('')}</span>`;
}

        async function unassignCard(assignmentId, cardType) {
            if (!assignmentId) return;

            const ok = confirm(`Unassign this ${String(cardType || 'card').toUpperCase()} card?`);
            if (!ok) return;

            try {
                const res = await fetch(`/card-assignments/${assignmentId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                    },
                });

                const json = await res.json();

                if (!res.ok || !json.success) {
                    throw new Error(json.message || 'Failed to unassign card');
                }

                toast('Card unassigned Successfully', 'ok');
                const eventId = $id('selectEvent').value;
                if (eventId) {
                    await fetchLeaderboard(eventId);
                }
            } catch (err) {
                console.error(err);
                toast('Error: ' + err.message, 'err');
            }
        }

        function csrfToken() {
            const m = document.querySelector('meta[name="csrf-token"]');
            return m ? m.getAttribute('content') : '';
        }

        function slugToLabel(slug) {
            const map = {
                science: 'Science', technology: 'Technology', engineering: 'Engineering',
                art: 'Art', math: 'Math', playground: 'Playground', egaming: 'E-Gaming',
                esports: 'ESports', mission: 'Missions', bonus: 'Bonus', other: 'Other',
            };
            return map[slug] || slug;
        }

        function esc(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        /* ═══════════════════════════════════════════════════════════
           POINT STRUCTURE VALIDATION
           Returns true if the row type is allowed to edit this cell.
           - point_structure 'per_team'   → only team rows can edit
           - point_structure 'per_player' → only student rows can edit
           - null / anything else         → both allowed (no restriction)
        ═══════════════════════════════════════════════════════════ */
        function isEditAllowed(rowType, pointStructure) {
            if (!pointStructure) return true;
            if (pointStructure === 'per_team')   return rowType === 'team';
            if (pointStructure === 'per_player') return rowType === 'student';
            return true;
        }

        /* ═══════════════════════════════════════════════════════════
           SCROLL HINT — hide after first scroll
        ═══════════════════════════════════════════════════════════ */
        (function() {
            const scroll = $id('lb-scroll');
            if (!scroll) return;
            function hideHint() {
                const hint = $id('lb-scroll-hint');
                if (hint) hint.style.display = 'none';
                scroll.removeEventListener('scroll', hideHint);
                scroll.removeEventListener('touchmove', hideHint);
            }
            scroll.addEventListener('scroll', hideHint, { passive: true });
            scroll.addEventListener('touchmove', hideHint, { passive: true });
        })();

        /* ═══════════════════════════════════════════════════════════
           FETCH LEADERBOARD DATA
        ═══════════════════════════════════════════════════════════ */
        async function fetchLeaderboard(eventId) {
            if (!eventId) return;

            const thead = $id('lb-thead');
            const tbody = $id('lb-tbody');
            tbody.innerHTML = `<tr class="lb-state-row"><td colspan="999">Loading…</td></tr>`;
            thead.innerHTML = '';

            // Re-show scroll hint on new event load
            const hint = $id('lb-scroll-hint');
            if (hint) hint.style.display = '';

            try {
                const res = await fetch(`/leaderboard-data?event_id=${eventId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                _currentData = data;
                window.__lbCurrentData = data;

                if (!data.rows || !data.rows.length) {
                    tbody.innerHTML = `<tr class="lb-state-row"><td colspan="999">No data available for this event.</td></tr>`;
                    return;
                }
                buildTable(data, thead, tbody);
            } catch (err) {
                console.error(err);
                tbody.innerHTML = `<tr class="lb-state-row"><td colspan="999">Error loading leaderboard.</td></tr>`;
            }
        }

        /* ═══════════════════════════════════════════════════════════
           UPDATE SINGLE TEAM/STUDENT TOTALS IN DOM
        ═══════════════════════════════════════════════════════════ */
        function updateRowTotalsInDOM(row, cat, newPts) {
            if (!_currentData) return;

            const dataRow = _currentData.rows.find(r => r.type === row.type && r.id === row.id);
            if (!dataRow) return;

            const oldPts = dataRow.scores?.[cat] ?? 0;
            const diff = newPts - oldPts;
            if (dataRow.scores) dataRow.scores[cat] = newPts;

            if (row.type === 'student') {
                dataRow.total_points = (dataRow.total_points ?? 0) + diff;
            } else {
                dataRow.team_points = (dataRow.team_points ?? 0) + diff;
                dataRow.total_points = (dataRow.total_points ?? 0) + diff;
                dataRow.grand_total = (dataRow.grand_total ?? 0) + diff;
            }

            const tbody = $id('lb-tbody');
            const tr = tbody.querySelector(`tr[data-row-type="${row.type}"][data-row-id="${row.id}"]`);
            if (!tr) return;

            if (row.type === 'team') {
                const tdTP = tr.querySelector('.td-total-team');
                const tdGT = tr.querySelector('.td-total-grand');
                if (tdTP) tdTP.textContent = Number(dataRow.team_points).toLocaleString();
                if (tdGT) tdGT.textContent = Number(dataRow.grand_total).toLocaleString();
            } else {
                const tdPP = tr.querySelector('.td-total-player');
                if (tdPP) tdPP.textContent = Number(dataRow.total_points).toLocaleString();

                const teamDataRow = _currentData.rows.find(r => r.type === 'team' && r.team_name === dataRow.team_name);
                if (teamDataRow) {
                    teamDataRow.player_points = (teamDataRow.player_points ?? 0) + diff;
                    teamDataRow.grand_total = (teamDataRow.grand_total ?? 0) + diff;
                    const teamTr = tbody.querySelector(`tr[data-row-type="team"][data-row-id="${teamDataRow.id}"]`);
                    if (teamTr) {
                        const tdPP2 = teamTr.querySelector('.td-total-player');
                        const tdGT2 = teamTr.querySelector('.td-total-grand');
                        if (tdPP2) tdPP2.textContent = Number(teamDataRow.player_points).toLocaleString();
                        if (tdGT2) tdGT2.textContent = Number(teamDataRow.grand_total).toLocaleString();
                    }
                }
            }

            recomputeRanks();
        }

        function recomputeRanks() {
            if (!_currentData) return;
            const tbody = $id('lb-tbody');

            const teamRows = _currentData.rows.filter(r => r.type === 'team');
            const sorted = [...teamRows].sort((a, b) => (b.grand_total ?? 0) - (a.grand_total ?? 0));

            let rank = 1, prevGT = null, prevRank = 1;
            sorted.forEach(row => {
                const gt = row.grand_total ?? 0;
                let assignedRank = null;

                if (gt > 0) {
                    if (prevGT !== null && gt === prevGT) {
                        assignedRank = prevRank;
                    } else {
                        assignedRank = rank;
                        prevRank = rank;
                    }
                    prevGT = gt;
                    rank++;
                }
                row.rank = assignedRank;

                const tr = tbody.querySelector(`tr[data-row-type="team"][data-row-id="${row.id}"]`);
                if (!tr) return;
                const td0 = tr.querySelector('.fix-0');
                const tdRankE = tr.querySelector('.td-rank-end');
                const badge = assignedRank ? rankBadge(assignedRank) : '';
                if (td0) td0.innerHTML = badge;
                if (tdRankE) tdRankE.innerHTML = badge;
            });
        }

        /* ═══════════════════════════════════════════════════════════
           BUILD TABLE
        ═══════════════════════════════════════════════════════════ */
        function buildTable(data, thead, tbody) {
            const cats = data.categories || [];
            const rows = data.rows;

            const catNames        = cats.map(c => c.name);
            const catSlugs        = cats.map(c => c.type);
            const catIds          = cats.map(c => c.id);
            const catMaxScores    = cats.map(c => c.max_score || 9999);
            // NEW: store point_structure per category column
            const catPointStructs = cats.map(c => c.point_structure || null);

            const hasBonusData = rows.some(r => (r.bonus_assignment ?? 0) > 0);
            const hasFlags     = rows.some(r => (r.cards && r.cards.length > 0));

            /* Banner groups — merge consecutive same-slug columns */
            const bannerGroups = [];
            catSlugs.forEach((slug, i) => {
                const last = bannerGroups[bannerGroups.length - 1];
                if (last && last.slug === slug) { last.span++; }
                else { bannerGroups.push({ slug, label: slugToLabel(slug), span: 1 }); }
            });

            const totalCols = 6 + catNames.length + (hasBonusData ? 1 : 0) + (hasFlags ? 1 : 0) + 4;

            /* ── ROW 1: Banner ── */
            const r1 = document.createElement('tr');
            r1.className = 'row-banner';

            [
                { label: '#',             cls: 'fix-0', w: 46 },
                { label: 'NO',            cls: 'fix-1', w: 50 },
                { label: 'TEAM',          cls: 'fix-2', w: 134 },
                { label: 'MEMBERS',       cls: 'fix-3', w: 130 },
                { label: 'PLAYER POINTS', cls: 'fix-4', w: 100 },
                { label: 'DIVISION',      cls: 'fix-5', w: 100 },
            ].forEach(m => {
                const th = document.createElement('th');
                th.className = m.cls;
                th.textContent = m.label;
                th.style.minWidth = m.w + 'px';
                r1.appendChild(th);
            });

            const thYP1 = document.createElement('th');
            thYP1.textContent = 'YOUR POINTS';
            thYP1.className = 'banner-your-points';
            thYP1.style.minWidth = '120px';
            r1.appendChild(thYP1);

            bannerGroups.forEach(g => {
                const th = document.createElement('th');
                th.colSpan = g.span;
                th.textContent = g.label.toUpperCase();
                th.className = 'banner-' + g.slug;
                th.style.minWidth = (g.span * 110) + 'px';
                r1.appendChild(th);
            });

            if (hasBonusData) {
                const thBonus = document.createElement('th');
                thBonus.textContent = 'BONUS';
                thBonus.className = 'banner-bonus';
                thBonus.style.minWidth = '90px';
                r1.appendChild(thBonus);
            }

            if (hasFlags) {
                const thFlags = document.createElement('th');
                thFlags.textContent = 'FLAGS';
                thFlags.className = 'banner-flags';
                thFlags.style.minWidth = '80px';
                r1.appendChild(thFlags);
            }

            [
                { label: 'TEAM POINTS', cls: 'banner-total-team', w: 100 },
                { label: 'RANK',        cls: '',                  w: 70 },
                { label: 'ORG',         cls: '',                  w: 120 },
            ].forEach(m => {
                const th = document.createElement('th');
                th.textContent = m.label;
                if (m.cls) th.className = m.cls;
                th.style.minWidth = m.w + 'px';
                r1.appendChild(th);
            });
            thead.appendChild(r1);

            /* ── ROW 2: Column labels ── */
            const r2 = document.createElement('tr');
            r2.className = 'row-cols';

            [
                { label: 'RANK',       cls: 'fix-0', w: 46 },
                { label: 'TEAM NO',    cls: 'fix-1', w: 50 },
                { label: 'TEAM NAME',  cls: 'fix-2', w: 134 },
                { label: 'MEMBERS',    cls: 'fix-3', w: 130 },
                { label: 'PLAYER PTS', cls: 'fix-4', w: 100 },
                { label: 'DIVISION',   cls: 'fix-5', w: 100 },
            ].forEach(m => {
                const th = document.createElement('th');
                th.className = m.cls;
                th.textContent = m.label;
                th.style.minWidth = m.w + 'px';
                r2.appendChild(th);
            });

            const thYP2 = document.createElement('th');
            thYP2.textContent = 'YOUR POINTS';
            thYP2.className = 'col-your-points';
            thYP2.style.minWidth = '120px';
            r2.appendChild(thYP2);

            catNames.forEach((name, i) => {
                const th = document.createElement('th');
                th.textContent = name;
                th.className = 'cat-' + catSlugs[i];
                th.style.minWidth = '120px';
                th.title = name;
                r2.appendChild(th);
            });

            if (hasBonusData) {
                const thBonus = document.createElement('th');
                thBonus.textContent = 'BONUS';
                thBonus.className = 'cat-bonus';
                thBonus.style.minWidth = '90px';
                r2.appendChild(thBonus);
            }

            if (hasFlags) {
                const thFlags = document.createElement('th');
                thFlags.textContent = 'FLAGS';
                thFlags.className = 'cat-flags';
                thFlags.style.minWidth = '80px';
                r2.appendChild(thFlags);
            }

            [
                { label: 'TEAM PTS',     cls: 'col-total-team', w: 100 },
                { label: 'RANK',         cls: '',               w: 70 },
                { label: 'ORGANIZATION', cls: '',               w: 120 },
            ].forEach(m => {
                const th = document.createElement('th');
                th.textContent = m.label;
                if (m.cls) th.className = m.cls;
                th.style.minWidth = m.w + 'px';
                r2.appendChild(th);
            });
            thead.appendChild(r2);

            /* ── TBODY ── */
            const frag = document.createDocumentFragment();
            let currentGroup = null;
            const teamSeq = {};

            rows.forEach(row => {
                const isTeam = row.type === 'team';
                const groupKey = row.group || 'Ungrouped';

                if (isTeam && groupKey !== currentGroup) {
                    currentGroup = groupKey;
                    const divTr = document.createElement('tr');
                    divTr.className = 'tr-divider';
                    const divTd = document.createElement('td');
                    divTd.colSpan = totalCols;
                    const sub = (row.subgroup && row.subgroup !== '-') ? '  ›  ' + row.subgroup.toUpperCase() : '';
                    divTd.textContent = '▸  ' + groupKey.toUpperCase() + sub;
                    divTr.appendChild(divTd);
                    frag.appendChild(divTr);
                }

                const tr = document.createElement('tr');
                tr.className = isTeam ? 'tr-team' : 'tr-student';
                tr.dataset.rowType = row.type;
                tr.dataset.rowId = row.id;

                /* col 0: rank medal */
                const td0 = document.createElement('td');
                td0.className = 'fix-0';
                td0.style.textAlign = 'center';
                if (isTeam && row.rank) td0.innerHTML = rankBadge(row.rank);
                tr.appendChild(td0);

                /* col 1: sequence number */
                const td1 = document.createElement('td');
                td1.className = 'fix-1';
                td1.style.textAlign = 'center';
                if (isTeam) {
                    if (!teamSeq[groupKey]) teamSeq[groupKey] = 0;
                    teamSeq[groupKey]++;
                    td1.textContent = teamSeq[groupKey];
                }
                tr.appendChild(td1);

                /* col 2: team name */
                const td2 = document.createElement('td');
                td2.className = 'fix-2';
                td2.style.textAlign = 'left';
                td2.style.paddingLeft = isTeam ? '10px' : '22px';
                td2.textContent = isTeam ? (row.team_name || '—') : '';
                if (isTeam) td2.style.fontWeight = '700';
                tr.appendChild(td2);

                /* col 3: member name */
                const td3 = document.createElement('td');
                td3.className = 'fix-3';
                td3.style.textAlign = 'left';
                td3.textContent = isTeam ? '' : (row.student_name || '—');
                tr.appendChild(td3);

                /* player points */
                const tdPPearly = document.createElement('td');
                tdPPearly.className = 'fix-4 td-total-player';
                tdPPearly.textContent = isTeam ?
                    Number(row.player_points ?? 0).toLocaleString() :
                    Number(row.total_points ?? 0).toLocaleString();
                tr.appendChild(tdPPearly);

                /* col 4: division */
                const td4 = document.createElement('td');
                td4.className = 'fix-5';
                td4.textContent = row.division || '—';
                tr.appendChild(td4);

                /* YOUR POINTS */
                const tdGT = document.createElement('td');
                tdGT.className = 'td-total-grand td-your-points';
                tdGT.textContent = isTeam
                    ? Number(row.grand_total ?? 0).toLocaleString()
                    : Number(row.total_points ?? 0).toLocaleString();
                tr.appendChild(tdGT);

                /* ── Activity score columns ── */
                catNames.forEach((cat, i) => {
                    const td = document.createElement('td');
                    const pts = row.scores?.[cat] ?? 0;
                    const slug = catSlugs[i];
                    const actId = catIds[i];
                    const maxSc = catMaxScores[i];
                    const pointStruct = catPointStructs[i]; // NEW

                    td.innerHTML = scorePill(pts, slug);
                    td.dataset.cat = cat;
                    td.dataset.pts = pts;
                    td.dataset.slug = slug;
                    td.dataset.activityId = actId;
                    td.dataset.maxScore = maxSc;
                    td.dataset.pointStructure = pointStruct || ''; // NEW

                    // NEW: check if this row type is allowed to edit this cell
                    const allowed = isEditAllowed(row.type, pointStruct);

                    if (allowed) {
                        td.className = 'score-edit-cell';

                        // Use touchend on mobile to avoid 300ms tap delay
                        if (isTouchDevice()) {
                            let touchMoved = false;
                            td.addEventListener('touchstart', () => { touchMoved = false; }, { passive: true });
                            td.addEventListener('touchmove', () => { touchMoved = true; }, { passive: true });
                            td.addEventListener('touchend', function(e) {
                                if (touchMoved) return; // was a scroll gesture, not a tap
                                e.preventDefault();
                                if (_bulkMode) { toggleBulkSelect(td, row); }
                                else { openScoreEditor(td, row); }
                            });
                        } else {
                            td.addEventListener('click', function() {
                                if (_bulkMode) { toggleBulkSelect(td, row); }
                                else { openScoreEditor(td, row); }
                            });
                        }
                    } else {
                        // NOT allowed: visually indicate cell is locked for this row type
                        td.className = 'score-edit-cell score-cell-locked';
                        td.title = pointStruct === 'per_team'
                            ? 'This activity only accepts team scores'
                            : 'This activity only accepts player scores';
                        td.style.cursor = 'not-allowed';
                        td.style.opacity = '0.4';
                    }

                    tr.appendChild(td);
                });

                /* ── Bonus column ── */
                if (hasBonusData) {
                    const tdBon = document.createElement('td');
                    const bonusVal = parseInt(row.bonus_assignment) || 0;
                    if (bonusVal > 0) {
                        tdBon.className = 'td-bonus';
                        tdBon.innerHTML = bonusPill(bonusVal);
                    } else {
                        tdBon.className = 'td-bonus-zero';
                        tdBon.innerHTML = '<span class="score-zero">—</span>';
                    }
                    tr.appendChild(tdBon);
                }

                /* Flags column */
                if (hasFlags) {
                    const tdFlags = document.createElement('td');
                    tdFlags.innerHTML = renderCardBadges(row.cards);
                    if (window.lucide) lucide.createIcons();
                    tdFlags.querySelectorAll('.card-unassign-btn').forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            unassignCard(this.dataset.assignmentId, this.dataset.cardType);
                        });
                    });
                    tr.appendChild(tdFlags);
                }

                /* team points */
                const tdTP = document.createElement('td');
                tdTP.className = 'td-total-team';
                if (isTeam) tdTP.textContent = Number(row.team_points ?? 0).toLocaleString();
                tr.appendChild(tdTP);

                /* rank end */
                const tdRankEnd = document.createElement('td');
                tdRankEnd.className = 'td-rank-end';
                if (isTeam && row.rank) tdRankEnd.innerHTML = rankBadge(row.rank);
                tr.appendChild(tdRankEnd);

                /* organization */
                const tdOrg = document.createElement('td');
                tdOrg.className = 'td-org';
                tdOrg.textContent = isTeam ? (row.organization || '—') : '';
                tr.appendChild(tdOrg);

                frag.appendChild(tr);
            });

            tbody.innerHTML = '';
            tbody.appendChild(frag);
            if (window.lucide) lucide.createIcons();
        }

        /* ═══════════════════════════════════════════════════════════
           INLINE SCORE EDITOR
        ═══════════════════════════════════════════════════════════ */
        function openScoreEditor(td, row) {
            if (td.querySelector('.score-edit-input')) return;

            const cat = td.dataset.cat;
            const slug = td.dataset.slug;
            const actId = parseInt(td.dataset.activityId);
            const maxScore = parseInt(td.dataset.maxScore) || 9999;
            const oldPts = parseInt(td.dataset.pts) || 0;

            const input = document.createElement('input');
            input.type = 'number';
            input.inputMode = 'numeric'; // triggers numeric keyboard on mobile
            input.pattern = '[0-9]*';    // iOS numeric keyboard
            input.max = maxScore;
            input.min = 0;
            input.className = 'score-edit-input';
            input.value = oldPts;
            input.title = `Max: ${maxScore}`;

            // Zero-out button
            const zeroBtn = document.createElement('button');
            zeroBtn.type = 'button';
            zeroBtn.className = 'score-zero-btn';
            zeroBtn.title = 'Set to 0';
            zeroBtn.textContent = '×0';

            const wrap = document.createElement('div');
            wrap.className = 'score-edit-wrap';
            wrap.appendChild(input);
            wrap.appendChild(zeroBtn);

            td.innerHTML = '';
            td.appendChild(wrap);
            input.focus();
            // On iOS, select() may not work perfectly — use setSelectionRange
            try { input.select(); } catch(e) {}
            try { input.setSelectionRange(0, input.value.length); } catch(e) {}

            let committed = false;

            function commit() {
                if (committed) return;

                // NEW: treat empty/blank input as 0 (remove points)
                const rawVal = input.value.trim();
                const newVal = rawVal === '' ? 0 : parseInt(rawVal, 10);

                if (isNaN(newVal)) { cancel(); return; }
                if (newVal > maxScore) { toast(`Max score is ${maxScore}`, 'warn'); cancel(); return; }

                committed = true;
                td.innerHTML = scorePill(newVal, slug);
                td.dataset.pts = newVal;
                updateRowTotalsInDOM(row, cat, newVal);
                saveScore(td, row, cat, newVal, slug, oldPts, actId, maxScore);
            }

            function cancel() {
                if (committed) return;
                td.innerHTML = scorePill(oldPts, slug);
                td.dataset.pts = oldPts;
            }

            // Prevent blur on input when clicking the zero button
            zeroBtn.addEventListener('mousedown', e => e.preventDefault());
            zeroBtn.addEventListener('click', () => {
                input.value = 0;
                commit();
            });

            input.addEventListener('keydown', e => {
                if (e.key === 'Enter') { e.preventDefault(); commit(); }
                if (e.key === 'Escape') cancel();
            });
            input.addEventListener('blur', () => { if (!committed) commit(); });
        }

        /* ── POST score to server ── */
        async function saveScore(td, row, cat, newPts, slug, oldPts, activityId, maxScore) {
            const eventId = $id('selectEvent').value;

            const payload = {
                event_id: eventId,
                challenge_activity_id: activityId,
                points: newPts,
            };
            if (row.type === 'student') payload.student_id = row.id;
            else payload.team_id = row.id;

            try {
                const res = await fetch('/scores/update-by-id', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                    },
                    body: JSON.stringify(payload),
                });

                const json = await res.json();

                if (json.success) {
                    toast('Score saved ✓', 'ok');
                    td.dataset.pts = newPts;
                } else {
                    throw new Error(json.message || 'Save failed');
                }
            } catch (err) {
                console.error(err);
                toast('Error: ' + err.message, 'err');
                td.innerHTML = scorePill(oldPts, slug);
                td.dataset.pts = oldPts;
                updateRowTotalsInDOM(row, cat, oldPts);
            }
        }

        /* ═══════════════════════════════════════════════════════════
           BULK EDIT MODE
        ═══════════════════════════════════════════════════════════ */
        function toggleBulkMode() {
            _bulkMode = !_bulkMode;
            _bulkSelected = [];

            const btn = $id('bulkEditBtn');
            const bulkBar = $id('bulk-bar');
            const table = $id('lb-table');

            if (_bulkMode) {
                btn.classList.add('active');
                btn.textContent = '✕ Exit Bulk';
                bulkBar.classList.add('visible');
                table.classList.add('bulk-mode');
            } else {
                btn.classList.remove('active');
                btn.innerHTML = '<i data-lucide="plus-square"></i> Bulk Edit';
                bulkBar.classList.remove('visible');
                table.classList.remove('bulk-mode');
                document.querySelectorAll('.score-edit-cell.bulk-selected')
                    .forEach(td => td.classList.remove('bulk-selected'));
                if (window.lucide) lucide.createIcons();
            }
            updateBulkBar();
        }

        function toggleBulkSelect(td, row) {
            // NEW: locked cells cannot be bulk-selected
            if (td.classList.contains('score-cell-locked')) return;

            const idx = _bulkSelected.findIndex(s => s.td === td);
            if (idx > -1) {
                _bulkSelected.splice(idx, 1);
                td.classList.remove('bulk-selected');
            } else {
                _bulkSelected.push({
                    td, row,
                    cat: td.dataset.cat,
                    pts: parseInt(td.dataset.pts) || 0,
                    slug: td.dataset.slug,
                    maxScore: parseInt(td.dataset.maxScore) || 9999,
                    activityId: parseInt(td.dataset.activityId) || 0,
                });
                td.classList.add('bulk-selected');
            }
            updateBulkBar();
        }

        function updateBulkBar() {
            const countEl = $id('bulk-count');
            if (countEl) countEl.textContent = _bulkSelected.length;
        }

        /* ── Open Bulk Edit Modal ── */
        function openBulkEditModal() {
            if (!_bulkSelected.length) {
                toast('Select at least one score cell first', 'warn');
                return;
            }

            const tbodyEl = $id('bulkEditTbody');
            tbodyEl.innerHTML = '';

            _bulkSelected.forEach((sel, i) => {
                const entityName = sel.row.type === 'team' ?
                    (sel.row.team_name || 'Team') :
                    (sel.row.student_name || 'Player');

                const teamColor = sel.row.type === 'team' ? '#1a3a6e' : '#1a3a1a';
                const textColor = sel.row.type === 'team' ? '#79c0ff' : '#56d364';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input type="checkbox" class="bulk-row-check" data-idx="${i}" checked></td>
                    <td style="text-align:left;">
                        <span class="badge" style="background:${teamColor};color:${textColor};font-size:10px;">
                            ${sel.row.type.toUpperCase()}
                        </span>
                        <span style="margin-left:6px;font-weight:600">${esc(entityName)}</span>
                    </td>
                    <td style="text-align:left;font-size:11px;">${esc(sel.cat)}</td>
                    <td><span class="score-pill score-${sel.slug}">${sel.pts}</span></td>
                    <td>
                        <input type="number" inputmode="numeric" pattern="[0-9]*"
                               max="${sel.maxScore}" min="0" value="${sel.pts}"
                               class="bulk-pts-input" data-idx="${i}" data-max="${sel.maxScore}"
                               placeholder="0">
                        <div class="err-hint" id="bulk-err-${i}">Exceeds max (${sel.maxScore})</div>
                    </td>
                    <td style="font-size:11px;">${sel.maxScore}</td>
                `;
                tbodyEl.appendChild(tr);
            });

            $id('bulkEditCount').textContent = _bulkSelected.length + ' cells';

            const selectAll = $id('bulkSelectAll');
            selectAll.checked = true;
            selectAll.onchange = function() {
                document.querySelectorAll('.bulk-row-check').forEach(cb => { cb.checked = this.checked; });
            };

            tbodyEl.querySelectorAll('.bulk-pts-input').forEach(input => {
                input.addEventListener('input', function() {
                    const max = parseInt(this.dataset.max);
                    // NEW: empty is valid (treated as 0), only flag if a number exceeds max
                    const rawVal = this.value.trim();
                    const val = rawVal === '' ? 0 : parseInt(rawVal);
                    const errEl = $id(`bulk-err-${this.dataset.idx}`);
                    if (!isNaN(val) && val > max) {
                        this.style.borderColor = '#f85149';
                        if (errEl) errEl.style.display = 'block';
                    } else {
                        this.style.borderColor = '#58a6ff';
                        if (errEl) errEl.style.display = 'none';
                    }
                });
            });

            const modal = new bootstrap.Modal($id('bulkEditModal'));
            modal.show();
            if (window.lucide) lucide.createIcons();
        }

        /* ── Save All Bulk ── */
        $id('bulkSaveConfirmBtn').addEventListener('click', async function() {
            const btn = this;
            btn.disabled = true;
            btn.textContent = 'Saving…';

            const eventId = $id('selectEvent').value;
            const inputs = document.querySelectorAll('#bulkEditTbody .bulk-pts-input');
            const checks = document.querySelectorAll('#bulkEditTbody .bulk-row-check');

            let hasError = false;
            const tasks = [];

            inputs.forEach((input, i) => {
                if (!checks[i]?.checked) return;

                const idx = parseInt(input.dataset.idx);
                const max = parseInt(input.dataset.max);

                // NEW: treat empty as 0
                const rawVal = input.value.trim();
                const newPts = rawVal === '' ? 0 : parseInt(rawVal);

                if (isNaN(newPts) || newPts > max) {
                    hasError = true;
                    input.style.borderColor = '#f85149';
                    return;
                }
                tasks.push({ sel: _bulkSelected[idx], newPts });
            });

            if (hasError) {
                toast('Fix validation errors before saving', 'err');
                btn.disabled = false;
                btn.textContent = 'Save All Changes';
                return;
            }

            let successCount = 0;
            let failCount = 0;

            await Promise.all(tasks.map(async ({ sel, newPts }) => {
                const payload = {
                    event_id: eventId,
                    challenge_activity_id: sel.activityId,
                    points: newPts,
                };
                if (sel.row.type === 'student') payload.student_id = sel.row.id;
                else payload.team_id = sel.row.id;

                try {
                    const res = await fetch('/scores/update-by-id', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken(),
                        },
                        body: JSON.stringify(payload),
                    });
                    const json = await res.json();

                    if (json.success) {
                        sel.td.innerHTML = scorePill(newPts, sel.slug);
                        sel.td.dataset.pts = newPts;
                        sel.td.classList.remove('bulk-selected');
                        updateRowTotalsInDOM(sel.row, sel.cat, newPts);
                        successCount++;
                    } else {
                        throw new Error(json.message);
                    }
                } catch (e) {
                    console.error(e);
                    failCount++;
                }
            }));

            bootstrap.Modal.getInstance($id('bulkEditModal'))?.hide();
            _bulkSelected = [];
            updateBulkBar();

            if (failCount === 0) { toast(`✓ ${successCount} score(s) saved`, 'ok'); }
            else { toast(`${successCount} saved, ${failCount} failed`, 'err'); }

            btn.disabled = false;
            btn.textContent = 'Save All Changes';
        });

        /* ═══════════════════════════════════════════════════════════
           EVENT DROPDOWN INIT
        ═══════════════════════════════════════════════════════════ */
        fetch('/leaderboard-events', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(events => {
                const sel = $id('selectEvent');
                events.forEach(ev => {
                    const o = document.createElement('option');
                    o.value = ev.id;
                    o.textContent = ev.name;
                    sel.appendChild(o);
                });
                if (events.length) {
                    sel.value = events[0].id;
                    fetchLeaderboard(events[0].id);
                }
            })
            .catch(console.error);

        $id('selectEvent').addEventListener('change', function() {
            if (_bulkMode) toggleBulkMode();
            _bulkSelected = [];
            updateBulkBar();
            fetchLeaderboard(this.value);
        });

        $id('bulkEditBtn').addEventListener('click', toggleBulkMode);
        $id('openBulkModalBtn').addEventListener('click', openBulkEditModal);

    })();
</script>

<script>
    (function() {
        'use strict';

        const COLORS = {
            science:     { bg: 'FFC0392B', fg: 'FFFFFFFF' },
            technology:  { bg: 'FFE67E22', fg: 'FFFFFFFF' },
            engineering: { bg: 'FF27AE60', fg: 'FFFFFFFF' },
            art:         { bg: 'FF2980B9', fg: 'FFFFFFFF' },
            math:        { bg: 'FF8E44AD', fg: 'FFFFFFFF' },
            playground:  { bg: 'FF7F8C8D', fg: 'FFFFFFFF' },
            egaming:     { bg: 'FFE91E8C', fg: 'FFFFFFFF' },
            esports:     { bg: 'FF00BCD4', fg: 'FF000000' },
            mission:     { bg: 'FFFF6F00', fg: 'FFFFFFFF' },
            other:       { bg: 'FF34495E', fg: 'FFC9D1D9' },
            bonus:       { bg: 'FFB8860B', fg: 'FFFFFFFF' },
            totalTeam:   { bg: 'FF0D2D6E', fg: 'FF79C0FF' },
            totalPlayer: { bg: 'FF1A3A1A', fg: 'FF56D364' },
            totalGrand:  { bg: 'FF3D1A00', fg: 'FFF5C518' },
            teamRow:     { bg: 'FF1C2638', fg: 'FFE6EDF3' },
            studentRow:  { bg: 'FF151B27', fg: 'FF8B949E' },
            divider:     { bg: 'FF1A3A2A', fg: 'FF56D364' },
            headerBg:    { bg: 'FF1E2535', fg: 'FFC9D1D9' },
            rankCol:     { bg: 'FF0F1318', fg: 'FFFFFFFF' },
            gold:        { bg: 'FFF5C518', fg: 'FF000000' },
            silver:      { bg: 'FFB8C4D0', fg: 'FF000000' },
            bronze:      { bg: 'FFCD7F32', fg: 'FFFFFFFF' },
            orgCell:     { bg: 'FF1C2638', fg: 'FFF5C518' },
        };

        function slugColor(slug) { return COLORS[slug] || COLORS.other; }

        function rankLabel(r) {
            if (!r && r !== 0) return '';
            if (r === 1) return '#1';
            if (r === 2) return '#2';
            if (r === 3) return '#3';
            return '#' + r;
        }

        async function exportLeaderboardExcelJS() {
            const data = window.__lbCurrentData;
            if (!data || !data.rows || !data.rows.length) {
                alert('No leaderboard data to export. Please select an event first.');
                return;
            }

            const eventName = document.getElementById('selectEvent')?.selectedOptions?.[0]?.text || 'Event';
            const cats = data.categories || [];
            const rows = data.rows;
            const catNames = cats.map(c => c.name);
            const catSlugs = cats.map(c => c.type);
            const hasBonusData = rows.some(r => (r.bonus_assignment ?? 0) > 0);

            const META_COLS = ['Rank', 'No', 'Team Name', 'Member', 'Division', 'Your Points'];

            const workbook = new ExcelJS.Workbook();
            const sheet = workbook.addWorksheet('Leaderboard', { views: [{ state: 'frozen', ySplit: 3 }] });

            const cellStyle = (bg, fg, opts = {}) => ({
                font: { bold: opts.bold || false, color: { argb: fg }, size: opts.sz || 11, italic: opts.italic || false },
                alignment: { horizontal: opts.halign || 'center', vertical: 'middle' },
                fill: { type: 'pattern', pattern: 'solid', fgColor: { argb: bg } },
                border: {
                    top:    { style: 'thin', color: { argb: 'FF2A3040' } },
                    bottom: { style: 'thin', color: { argb: 'FF2A3040' } },
                    left:   { style: 'thin', color: { argb: 'FF2A3040' } },
                    right:  { style: 'thin', color: { argb: 'FF2A3040' } }
                }
            });

            const titleRow = sheet.addRow([eventName + '  —  Leaderboard']);
            titleRow.font = { bold: true, color: { argb: 'FFF5C518' }, size: 14 };
            sheet.mergeCells(1, 1, 1, META_COLS.length + catNames.length + (hasBonusData ? 1 : 0) + 5);

            const bannerRowValues = ['', '', '', '', '', 'YOUR POINTS'];
            catNames.forEach((name, i) => bannerRowValues.push(catSlugs[i].toUpperCase()));
            if (hasBonusData) bannerRowValues.push('BONUS');
            bannerRowValues.push('TEAM POINTS', 'PLAYER POINTS', 'RANK', 'ORGANIZATION');
            const bannerRow = sheet.addRow(bannerRowValues);
            bannerRow.eachCell((cell, idx) => {
                if (idx < META_COLS.length) {
                    cell.style = cellStyle(COLORS.headerBg.bg, COLORS.headerBg.fg, { bold: true, sz: 10 });
                } else if (idx === META_COLS.length) {
                    cell.style = cellStyle(COLORS.totalGrand.bg, COLORS.totalGrand.fg, { bold: true, sz: 10 });
                } else {
                    const catIdx = idx - META_COLS.length - 1;
                    if (catIdx < catSlugs.length) {
                        const colColor = slugColor(catSlugs[catIdx]);
                        cell.style = cellStyle(colColor.bg, colColor.fg, { bold: true, sz: 10 });
                    } else if (cell.value === 'BONUS') {
                        cell.style = cellStyle(COLORS.bonus.bg, COLORS.bonus.fg, { bold: true, sz: 10 });
                    } else {
                        cell.style = cellStyle(COLORS.totalTeam.bg, COLORS.totalTeam.fg, { bold: true, sz: 10 });
                    }
                }
            });

            const colLabelRowValues = ['RANK', 'NO', 'TEAM NAME', 'MEMBER', 'DIVISION', 'YOUR POINTS', ...catNames];
            if (hasBonusData) colLabelRowValues.push('BONUS');
            colLabelRowValues.push('TEAM PTS', 'PLAYER PTS', 'RANK', 'ORGANIZATION');
            const colLabelRow = sheet.addRow(colLabelRowValues);
            colLabelRow.eachCell(cell => cell.style = cellStyle(COLORS.headerBg.bg, COLORS.headerBg.fg, { bold: true, sz: 10 }));

            let currentGroup = null;
            const teamSeq = {};
            rows.forEach(row => {
                const isTeam = row.type === 'team';
                const groupKey = row.group || 'Ungrouped';

                if (isTeam && groupKey !== currentGroup) {
                    currentGroup = groupKey;
                    const sub = (row.subgroup && row.subgroup !== '-') ? '  \u203a  ' + row.subgroup.toUpperCase() : '';
                    const divRow = sheet.addRow(['\u25b8  ' + groupKey.toUpperCase() + sub]);
                    divRow.height = 18;
                    sheet.mergeCells(divRow.number, 1, divRow.number, colLabelRowValues.length);
                    divRow.eachCell(c => c.style = cellStyle(COLORS.divider.bg, COLORS.divider.fg, { bold: true, sz: 11, halign: 'left' }));
                }

                const dataRowValues = [];
                const rank = isTeam ? row.rank : null;
                dataRowValues.push(rank ? rankLabel(rank) : '');
                if (isTeam) { if (!teamSeq[groupKey]) teamSeq[groupKey] = 0; teamSeq[groupKey]++; }
                dataRowValues.push(isTeam ? teamSeq[groupKey] : '');
                dataRowValues.push(isTeam ? (row.team_name || '—') : '');
                dataRowValues.push(isTeam ? '' : (row.student_name || '—'));
                dataRowValues.push(row.division || '');
                dataRowValues.push(isTeam ? row.grand_total ?? 0 : row.total_points ?? 0);
                catNames.forEach(cat => dataRowValues.push(row.scores?.[cat] ?? 0));
                if (hasBonusData) dataRowValues.push(row.bonus_assignment ?? 0);
                dataRowValues.push(isTeam ? row.team_points ?? 0 : '');
                dataRowValues.push(isTeam ? row.player_points ?? 0 : '');
                dataRowValues.push(isTeam ? rankLabel(rank) : '');
                dataRowValues.push(isTeam ? row.organization ?? '' : '');
                sheet.addRow(dataRowValues).height = 18;
            });

            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            const safeName = eventName.replace(/[\/\\?*\[\]]/g, '_').substring(0, 30);
            a.href = url;
            a.download = `Leaderboard_${safeName}_${new Date().toISOString().slice(0,10)}.xlsx`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        window.__exportLeaderboardExcelJS = exportLeaderboardExcelJS;
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('exportXlsxBtn');
            if (btn) btn.addEventListener('click', exportLeaderboardExcelJS);
        });
    })();
</script>