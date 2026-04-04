<style>
    @import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800;900&family=Barlow:wght@400;500;600&display=swap');

    :root {
        --lb-bg:        #0f1318;
        --lb-surface:   #181e27;
        --lb-border:    #2a3040;
        --lb-header-bg: #1e2535;

        --lb-gold:    #f5c518;
        --lb-silver:  #b8c4d0;
        --lb-bronze:  #cd7f32;

        /* ── CATEGORY COLOURS ── */
        --cat-science-bg:     #c0392b;   --cat-science-text:     #ffffff;
        --cat-tech-bg:        #e67e22;   --cat-tech-text:        #ffffff;
        --cat-eng-bg:         #27ae60;   --cat-eng-text:         #ffffff;
        --cat-art-bg:         #2980b9;   --cat-art-text:         #ffffff;
        --cat-math-bg:        #8e44ad;   --cat-math-text:        #ffffff;
        --cat-playground-bg:  #7f8c8d;   --cat-playground-text:  #ffffff;
        --cat-egaming-bg:     #e91e8c;   --cat-egaming-text:     #ffffff;
        --cat-esports-bg:     #00bcd4;   --cat-esports-text:     #000000;
        --cat-mission-bg:     #ff6f00;   --cat-mission-text:     #ffffff;
        --cat-other-bg:       #34495e;   --cat-other-text:       #c9d1d9;

        --total-team-bg:      #0d2d6e;   --total-team-text:    #79c0ff;
        --total-player-bg:    #1a3a1a;   --total-player-text:  #56d364;
        --total-grand-bg:     #3d1a00;   --total-grand-text:   #f5c518;

        --font-head: 'Barlow Condensed', sans-serif;
        --font-body: 'Barlow', sans-serif;
        --radius: 6px;
    }

    /* ── WRAPPER & CONTROLS ── */
    #lb-wrapper {
        font-family: var(--font-body);
        background: var(--lb-bg);
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--lb-border);
        margin-top: 12px;
    }

    #lb-controls {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 14px 20px;
        background: var(--lb-surface);
        border-bottom: 1px solid var(--lb-border);
        flex-wrap: wrap;
    }
    #lb-controls label {
        font-family: var(--font-head);
        font-size: 12px;
        font-weight: 700;
        color: #8b949e;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
    #selectEvent {
        background: var(--lb-header-bg);
        border: 1px solid var(--lb-border);
        color: #e6edf3;
        padding: 7px 14px;
        border-radius: var(--radius);
        font-family: var(--font-body);
        font-size: 14px;
        min-width: 240px;
        cursor: pointer;
    }
    #selectEvent:focus { outline: none; border-color: #58a6ff; }

    .lb-legend {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        margin-left: 8px;
    }
    .lb-legend-dot {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-family: var(--font-head);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #c9d1d9;
    }
    .lb-legend-dot span {
        width: 12px; height: 12px;
        border-radius: 3px;
        display: inline-block;
    }

    #exportLeaderboard {
        margin-left: auto;
        background: #238636;
        color: #fff;
        border: none;
        padding: 8px 18px;
        border-radius: var(--radius);
        font-family: var(--font-head);
        font-weight: 700;
        font-size: 14px;
        letter-spacing: .04em;
        cursor: pointer;
        transition: background .2s;
    }
    #exportLeaderboard:hover { background: #2ea043; }

    /* ── TABLE SCROLL ── */
    #lb-scroll {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 74vh;
    }

    /* ── TABLE BASE ── */
    #lb-table {
        border-collapse: separate;
        border-spacing: 0;
        width: max-content;
        min-width: 100%;
        font-family: var(--font-body);
        font-size: 13px;
    }

    /* ── THEAD ROW 1: BANNER ── */
    #lb-table thead tr.row-banner th {
        background: var(--lb-surface);
        color: #8b949e;
        font-family: var(--font-head);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        padding: 8px 10px;
        border-bottom: 1px solid var(--lb-border);
        border-right: 1px solid var(--lb-border);
        white-space: nowrap;
        text-align: center;
        position: sticky;
        top: 0;
        z-index: 20;
    }
    #lb-table thead tr.row-banner th.banner-science     { background: var(--cat-science-bg);    color: var(--cat-science-text); }
    #lb-table thead tr.row-banner th.banner-technology  { background: var(--cat-tech-bg);        color: var(--cat-tech-text); }
    #lb-table thead tr.row-banner th.banner-engineering { background: var(--cat-eng-bg);         color: var(--cat-eng-text); }
    #lb-table thead tr.row-banner th.banner-art         { background: var(--cat-art-bg);         color: var(--cat-art-text); }
    #lb-table thead tr.row-banner th.banner-math        { background: var(--cat-math-bg);        color: var(--cat-math-text); }
    #lb-table thead tr.row-banner th.banner-playground  { background: var(--cat-playground-bg);  color: var(--cat-playground-text); }
    #lb-table thead tr.row-banner th.banner-egaming     { background: var(--cat-egaming-bg);     color: var(--cat-egaming-text); }
    #lb-table thead tr.row-banner th.banner-esports     { background: var(--cat-esports-bg);     color: var(--cat-esports-text); }
    #lb-table thead tr.row-banner th.banner-mission     { background: var(--cat-mission-bg);     color: var(--cat-mission-text); }
    #lb-table thead tr.row-banner th.banner-other       { background: var(--cat-other-bg);       color: var(--cat-other-text); }
    #lb-table thead tr.row-banner th.banner-total-team   { background: var(--total-team-bg);   color: var(--total-team-text); }
    #lb-table thead tr.row-banner th.banner-total-player { background: var(--total-player-bg); color: var(--total-player-text); }
    #lb-table thead tr.row-banner th.banner-total-grand  { background: var(--total-grand-bg);  color: var(--total-grand-text); }

    /* ── THEAD ROW 2: COL LABELS ── */
    #lb-table thead tr.row-cols th {
        background: var(--lb-header-bg);
        color: #c9d1d9;
        font-family: var(--font-head);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .07em;
        text-transform: uppercase;
        padding: 7px 10px;
        border-bottom: 2px solid var(--lb-border);
        border-right: 1px solid var(--lb-border);
        white-space: nowrap;
        text-align: center;
        position: sticky;
        top: 34px;
        z-index: 20;
    }
    #lb-table thead tr.row-cols th.cat-science     { border-top: 3px solid var(--cat-science-bg); }
    #lb-table thead tr.row-cols th.cat-technology  { border-top: 3px solid var(--cat-tech-bg); }
    #lb-table thead tr.row-cols th.cat-engineering { border-top: 3px solid var(--cat-eng-bg); }
    #lb-table thead tr.row-cols th.cat-art         { border-top: 3px solid var(--cat-art-bg); }
    #lb-table thead tr.row-cols th.cat-math        { border-top: 3px solid var(--cat-math-bg); }
    #lb-table thead tr.row-cols th.cat-playground  { border-top: 3px solid var(--cat-playground-bg); }
    #lb-table thead tr.row-cols th.cat-egaming     { border-top: 3px solid var(--cat-egaming-bg); }
    #lb-table thead tr.row-cols th.cat-esports     { border-top: 3px solid var(--cat-esports-bg); }
    #lb-table thead tr.row-cols th.cat-mission     { border-top: 3px solid var(--cat-mission-bg); }
    #lb-table thead tr.row-cols th.cat-other       { border-top: 3px solid var(--cat-other-bg); }
    #lb-table thead tr.row-cols th.col-total-team   { border-top: 3px solid var(--total-team-text);   color: var(--total-team-text); }
    #lb-table thead tr.row-cols th.col-total-player { border-top: 3px solid var(--total-player-text); color: var(--total-player-text); }
    #lb-table thead tr.row-cols th.col-total-grand  { border-top: 3px solid var(--total-grand-text);  color: var(--total-grand-text); }

    /* Sticky fixed-left columns */
    #lb-table thead tr.row-banner th.fix-0,
    #lb-table thead tr.row-cols   th.fix-0 { position: sticky; left: 0;     z-index: 25; }
    #lb-table thead tr.row-banner th.fix-1,
    #lb-table thead tr.row-cols   th.fix-1 { position: sticky; left: 46px;  z-index: 25; }
    #lb-table thead tr.row-banner th.fix-2,
    #lb-table thead tr.row-cols   th.fix-2 { position: sticky; left: 96px;  z-index: 25; }
    #lb-table thead tr.row-banner th.fix-3,
    #lb-table thead tr.row-cols   th.fix-3 { position: sticky; left: 230px; z-index: 25; }
    #lb-table thead tr.row-banner th.fix-4,
    #lb-table thead tr.row-cols   th.fix-4 { position: sticky; left: 360px; z-index: 25; }

    /* ── TBODY GENERAL ── */
    #lb-table tbody td {
        padding: 5px 10px;
        border-bottom: 1px solid #1e2535;
        border-right: 1px solid #1e2535;
        white-space: nowrap;
        vertical-align: middle;
        color: #c9d1d9;
        text-align: center;
    }

    /* GROUP DIVIDER */
    #lb-table tbody tr.tr-divider td {
        background: linear-gradient(90deg, #1a3a2a 0%, #162a20 100%);
        color: #56d364;
        font-family: var(--font-head);
        font-weight: 900;
        font-size: 13px;
        letter-spacing: .1em;
        text-transform: uppercase;
        padding: 8px 16px;
        border-bottom: 2px solid #238636;
        text-align: left;
    }

    /* TEAM ROW */
    #lb-table tbody tr.tr-team td {
        background: #1c2638;
        font-weight: 600;
        color: #e6edf3;
        border-bottom: 2px solid #0f1318;
    }

    /* STUDENT ROW */
    #lb-table tbody tr.tr-student td {
        background: #151b27;
        font-size: 12px;
        color: #8b949e;
        border-bottom: 1px dashed #1e2535;
    }

    /* Sticky body left columns */
    #lb-table tbody td.fix-0 { position: sticky; left: 0;     z-index: 5; background: inherit; }
    #lb-table tbody td.fix-1 { position: sticky; left: 46px;  z-index: 5; background: inherit; }
    #lb-table tbody td.fix-2 { position: sticky; left: 96px;  z-index: 5; background: inherit; }
    #lb-table tbody td.fix-3 { position: sticky; left: 230px; z-index: 5; background: inherit; }
    #lb-table tbody td.fix-4 { position: sticky; left: 360px; z-index: 5; background: inherit; }

    /* ── SCORE PILLS ── */
    .score-pill {
        display: inline-block;
        min-width: 44px;
        padding: 2px 7px;
        border-radius: 4px;
        font-family: var(--font-head);
        font-weight: 700;
        font-size: 13px;
        text-align: center;
    }
    .score-science     { background: var(--cat-science-bg);    color: var(--cat-science-text); }
    .score-technology  { background: var(--cat-tech-bg);        color: var(--cat-tech-text); }
    .score-engineering { background: var(--cat-eng-bg);         color: var(--cat-eng-text); }
    .score-art         { background: var(--cat-art-bg);         color: var(--cat-art-text); }
    .score-math        { background: var(--cat-math-bg);        color: var(--cat-math-text); }
    .score-playground  { background: var(--cat-playground-bg);  color: var(--cat-playground-text); }
    .score-egaming     { background: var(--cat-egaming-bg);     color: var(--cat-egaming-text); }
    .score-esports     { background: var(--cat-esports-bg);     color: var(--cat-esports-text); }
    .score-mission     { background: var(--cat-mission-bg);     color: var(--cat-mission-text); }
    .score-other       { background: var(--cat-other-bg);       color: var(--cat-other-text); }
    .score-zero { color: #3a4454; font-size: 13px; }

    /* ── TOTAL CELLS ── */
    td.td-total-team   { background: var(--total-team-bg)   !important; color: var(--total-team-text)   !important; font-family: var(--font-head) !important; font-weight: 800 !important; font-size: 15px !important; }
    td.td-total-player { background: var(--total-player-bg) !important; color: var(--total-player-text) !important; font-family: var(--font-head) !important; font-weight: 700 !important; font-size: 13px !important; }
    td.td-total-grand  { background: var(--total-grand-bg)  !important; color: var(--total-grand-text)  !important; font-family: var(--font-head) !important; font-weight: 900 !important; font-size: 16px !important; }

    /* ── RANK MEDALS ── */
    .rank-medal {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px; height: 26px;
        border-radius: 50%;
        font-family: var(--font-head);
        font-weight: 900;
        font-size: 14px;
    }
    .rank-1 { background: var(--lb-gold);   color: #000; }
    .rank-2 { background: var(--lb-silver); color: #000; }
    .rank-3 { background: var(--lb-bronze); color: #fff; }
    .rank-n { background: #21262d; color: #8b949e; font-size: 12px; }

    td.td-rank-end { font-family: var(--font-head); font-weight: 900; }
    td.td-org { color: #f5c518 !important; font-family: var(--font-head) !important; font-weight: 700 !important; font-size: 12px !important; letter-spacing: .04em; }

    /* ── INLINE EDIT ── */
    .score-edit-cell { position: relative; cursor: pointer; }
    .score-edit-cell:hover .score-pill,
    .score-edit-cell:hover .score-zero { opacity: 0.6; }
    .score-edit-cell:hover::after {
        content: '✎';
        position: absolute;
        right: 3px; top: 50%;
        transform: translateY(-50%);
        font-size: 10px;
        color: #58a6ff;
        pointer-events: none;
    }
    .score-edit-input {
        width: 64px;
        background: #0d1117;
        border: 1px solid #58a6ff;
        color: #e6edf3;
        border-radius: 4px;
        padding: 2px 5px;
        font-family: var(--font-head);
        font-size: 14px;
        font-weight: 700;
        text-align: center;
        outline: none;
    }
    .save-indicator {
        display: inline-block;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: var(--font-head);
        font-weight: 700;
    }
    .save-ok  { background: #1a3a1a; color: #56d364; }
    .save-err { background: #3a1a1a; color: #f85149; }
    .save-saving { background: #1a2a3a; color: #58a6ff; }

    /* ── TOAST ── */
    #lb-toast {
        position: fixed;
        bottom: 24px; right: 24px;
        padding: 10px 18px;
        border-radius: 8px;
        font-family: var(--font-head);
        font-size: 14px;
        font-weight: 700;
        z-index: 9999;
        opacity: 0;
        transition: opacity .3s;
        pointer-events: none;
    }
    #lb-toast.show { opacity: 1; }
    #lb-toast.ok   { background: #1a3a1a; color: #56d364; border: 1px solid #238636; }
    #lb-toast.err  { background: #3a1a1a; color: #f85149; border: 1px solid #f85149; }

    /* ── STATE ROWS ── */
    .lb-state-row td {
        text-align: center;
        padding: 50px;
        color: #484f58;
        font-family: var(--font-head);
        font-size: 16px;
        letter-spacing: .06em;
        background: var(--lb-bg) !important;
    }

    @media (max-width: 768px) {
        #lb-controls { flex-direction: column; align-items: flex-start; }
        #exportLeaderboard { margin-left: 0; }
    }
</style>

<div id="lb-toast"></div>
<script>
    window.USER_ROLE = {{ auth()->check() ? auth()->user()->role : 0 }};
</script>
<script>
(function () {
    'use strict';
    const IS_ADMIN = window.USER_ROLE === 1;
    const $id = id => document.getElementById(id);

    /* ── Toast helper ── */
    function toast(msg, type = 'ok') {
        const el = $id('lb-toast');
        el.textContent = msg;
        el.className = 'show ' + type;
        clearTimeout(el._t);
        el._t = setTimeout(() => { el.className = ''; }, 2800);
    }

    /*
     * ── scorePill ──────────────────────────────────────────────────────────
     * slug is now always correct because it comes directly from the server
     * (computed from activity_type / activity_or_mission) — NOT guessed from
     * the display name string.
     */
    function scorePill(pts, slug) {
        const v = pts ?? 0;
        if (!v) return '<span class="score-zero">—</span>';
        return `<span class="score-pill score-${slug}">${Number(v).toLocaleString()}</span>`;
    }

    function rankBadge(r) {
        if (!r && r !== 0) return '';
        if (r === 1) return `<span class="rank-medal rank-1">1</span>`;
        if (r === 2) return `<span class="rank-medal rank-2">2</span>`;
        if (r === 3) return `<span class="rank-medal rank-3">3</span>`;
        return `<span class="rank-medal rank-n">${r}</span>`;
    }

    /* ── CSRF token (Laravel) ── */
    function csrfToken() {
        const m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    /* ── Main fetch ── */
    async function fetchLeaderboard(eventId) {
        if (!eventId) return;
        const thead = $id('lb-thead');
        const tbody = $id('lb-tbody');

        tbody.innerHTML = `<tr class="lb-state-row"><td colspan="999">Loading…</td></tr>`;
        thead.innerHTML = '';

        try {
            const res = await fetch(`/leaderboard-data?event_id=${eventId}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();

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

    /* ── Re-render after an edit ── */
    function reRender() {
        const eventId = $id('selectEvent').value;
        if (eventId) fetchLeaderboard(eventId);
    }

    /* ════════════════════════════════════════════════════════════════════════
       BUILD TABLE
       ─────────────────────────────────────────────────────────────────────
       data.categories is now an array of { name, type } objects where:
         name  = display name string (e.g. "Aquaball Clash", "Playground")
         type  = CSS slug (e.g. "egaming", "esports", "mission", "science"…)

       data.rows comes pre-sorted by grand_total DESC from the server so
       teams are already in ranking order (rank 1 at the top).
    ════════════════════════════════════════════════════════════════════════ */
    function buildTable(data, thead, tbody) {
        const cats  = data.categories || [];   // [{ name, type }, …]
        const rows  = data.rows;

        /* Parallel arrays for convenience */
        const catNames = cats.map(c => c.name);
        const catSlugs = cats.map(c => c.type); // ← correct slug from server

        /* Banner groups — merge consecutive same-slug columns */
        const bannerGroups = [];
        catSlugs.forEach((slug, i) => {
            const last = bannerGroups[bannerGroups.length - 1];
            if (last && last.slug === slug) {
                last.span++;
                /* Accumulate display labels for merged columns */
                last.labels.push(catNames[i]);
            } else {
                /* Use a human-readable group label for the banner */
                const label = slugToLabel(slug);
                bannerGroups.push({ slug, label, span: 1, labels: [catNames[i]] });
            }
        });

        /* ── ROW 1: Banner ── */
        const r1 = document.createElement('tr');
        r1.className = 'row-banner';

        [
            { label: '#',        cls: 'fix-0', w: 46 },
            { label: 'NO',       cls: 'fix-1', w: 50 },
            { label: 'TEAM',     cls: 'fix-2', w: 134 },
            { label: 'MEMBERS',  cls: 'fix-3', w: 130 },
            { label: 'DIVISION', cls: 'fix-4', w: 100 },
        ].forEach(m => {
            const th = document.createElement('th');
            th.className = m.cls;
            th.textContent = m.label;
            th.style.minWidth = m.w + 'px';
            r1.appendChild(th);
        });

        bannerGroups.forEach(g => {
            const th = document.createElement('th');
            th.colSpan = g.span;
            th.textContent = g.label.toUpperCase();
            th.className = 'banner-' + g.slug;
            th.style.minWidth = (g.span * 110) + 'px';
            r1.appendChild(th);
        });

        [
            { label: 'TEAM POINTS',   cls: 'banner-total-team',   w: 100 },
            { label: 'PLAYER POINTS', cls: 'banner-total-player', w: 100 },
            { label: 'GRAND TOTAL',   cls: 'banner-total-grand',  w: 100 },
            { label: 'RANK',          cls: '',                     w: 70 },
            { label: 'ORG',           cls: '',                     w: 120 },
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
            { label: 'RANK',      cls: 'fix-0', w: 46 },
            { label: 'TEAM NO',   cls: 'fix-1', w: 50 },
            { label: 'TEAM NAME', cls: 'fix-2', w: 134 },
            { label: 'MEMBERS',   cls: 'fix-3', w: 130 },
            { label: 'DIVISION',  cls: 'fix-4', w: 100 },
        ].forEach(m => {
            const th = document.createElement('th');
            th.className = m.cls;
            th.textContent = m.label;
            th.style.minWidth = m.w + 'px';
            r2.appendChild(th);
        });

        catNames.forEach((name, i) => {
            const th = document.createElement('th');
            th.textContent = name;
            th.className = 'cat-' + catSlugs[i]; // ← correct class
            th.style.minWidth = '110px';
            r2.appendChild(th);
        });

        [
            { label: 'TEAM PTS',     cls: 'col-total-team',   w: 100 },
            { label: 'PLAYER PTS',   cls: 'col-total-player', w: 100 },
            { label: 'GRAND TOTAL',  cls: 'col-total-grand',  w: 100 },
            { label: 'RANK',         cls: '',                  w: 70 },
            { label: 'ORGANIZATION', cls: '',                  w: 120 },
        ].forEach(m => {
            const th = document.createElement('th');
            th.textContent = m.label;
            if (m.cls) th.className = m.cls;
            th.style.minWidth = m.w + 'px';
            r2.appendChild(th);
        });
        thead.appendChild(r2);

        /* ── TBODY ── */

        /* Aggregate team totals from flat row list */
        const teamAgg = {};
        let lastTeamId = null;
        rows.forEach(row => {
            if (row.type === 'team') {
                lastTeamId = row.id;
                teamAgg[row.id] = {
                    team_points:   row.team_points   ?? row.total_points ?? 0,
                    player_points: row.player_points ?? 0,
                };
            } else if (row.type === 'student' && lastTeamId !== null) {
                if (row.team_points === undefined) {
                    teamAgg[lastTeamId].player_points += row.total_points ?? 0;
                }
            }
        });
        Object.values(teamAgg).forEach(a => { a.grand_total = a.team_points + a.player_points; });

        const frag = document.createDocumentFragment();
        let currentGroup = null;
        const teamSeq = {};

        rows.forEach(row => {
            const isTeam   = row.type === 'team';
            const groupKey = row.group || 'Ungrouped';

            /* Group divider (only for team rows, and only on group change) */
            if (isTeam && groupKey !== currentGroup) {
                currentGroup = groupKey;
                const divTr = document.createElement('tr');
                divTr.className = 'tr-divider';
                const divTd = document.createElement('td');
                divTd.colSpan = 5 + catNames.length + 5;
                const sub = (row.subgroup && row.subgroup !== '-') ? '  ›  ' + row.subgroup.toUpperCase() : '';
                divTd.textContent = '▸  ' + groupKey.toUpperCase() + sub;
                divTr.appendChild(divTd);
                frag.appendChild(divTr);
            }

            const tr = document.createElement('tr');
            tr.className = isTeam ? 'tr-team' : 'tr-student';

            /* Fixed col 0: rank badge */
            const td0 = document.createElement('td');
            td0.className = 'fix-0';
            td0.style.textAlign = 'center';
            if (isTeam) td0.innerHTML = rankBadge(row.rank);
            tr.appendChild(td0);

            /* Fixed col 1: team sequential number */
            const td1 = document.createElement('td');
            td1.className = 'fix-1';
            td1.style.textAlign = 'center';
            if (isTeam) {
                if (!teamSeq[groupKey]) teamSeq[groupKey] = 0;
                teamSeq[groupKey]++;
                td1.textContent = teamSeq[groupKey];
            }
            tr.appendChild(td1);

            /* Fixed col 2: team name */
            const td2 = document.createElement('td');
            td2.className = 'fix-2';
            td2.style.textAlign = 'left';
            td2.style.paddingLeft = isTeam ? '10px' : '22px';
            td2.textContent = isTeam ? (row.team_name || '—') : '';
            if (isTeam) td2.style.fontWeight = '700';
            tr.appendChild(td2);

            /* Fixed col 3: member name */
            const td3 = document.createElement('td');
            td3.className = 'fix-3';
            td3.style.textAlign = 'left';
            td3.textContent = isTeam ? '' : (row.student_name || '—');
            tr.appendChild(td3);

            /* Fixed col 4: division */
            const td4 = document.createElement('td');
            td4.className = 'fix-4';
            td4.textContent = row.division || '—';
            tr.appendChild(td4);

            /* Activity score columns — editable */
            catNames.forEach((cat, i) => {
                const td   = document.createElement('td');
                td.className = 'score-edit-cell';
                const pts  = row.scores?.[cat] ?? 0;
                const slug = catSlugs[i]; // ← always correct from server

                td.innerHTML = scorePill(pts, slug);

                if (IS_ADMIN) {
                    td.addEventListener('click', function () {
                        openScoreEditor(td, row, cat, pts, slug);
                    });
                } else {
                    td.style.cursor = 'not-allowed';
                    td.title = 'Only admin can edit';
                }

                tr.appendChild(td);
            });

            /* Total: team points */
            const tdTP = document.createElement('td');
            tdTP.className = 'td-total-team';
            if (isTeam) {
                const agg = teamAgg[row.id] || {};
                tdTP.textContent = Number(agg.team_points ?? 0).toLocaleString();
            }
            tr.appendChild(tdTP);

            /* Total: player points */
            const tdPP = document.createElement('td');
            tdPP.className = 'td-total-player';
            if (isTeam) {
                const agg = teamAgg[row.id] || {};
                tdPP.textContent = Number(agg.player_points ?? 0).toLocaleString();
            } else {
                tdPP.textContent = Number(row.total_points ?? 0).toLocaleString();
            }
            tr.appendChild(tdPP);

            /* Total: grand total */
            const tdGT = document.createElement('td');
            tdGT.className = 'td-total-grand';
            if (isTeam) {
                const agg = teamAgg[row.id] || {};
                tdGT.textContent = Number(agg.grand_total ?? 0).toLocaleString();
            }
            tr.appendChild(tdGT);

            /* Rank end column */
            const tdRankEnd = document.createElement('td');
            tdRankEnd.className = 'td-rank-end';
            if (isTeam) tdRankEnd.innerHTML = rankBadge(row.rank);
            tr.appendChild(tdRankEnd);

            /* Organization */
            const tdOrg = document.createElement('td');
            tdOrg.className = 'td-org';
            tdOrg.textContent = isTeam ? (row.organization || '—') : '';
            tr.appendChild(tdOrg);

            frag.appendChild(tr);
        });

        tbody.innerHTML = '';
        tbody.appendChild(frag);
    }

    /* ════════════════════════════════════════════════════════════════════════
       SLUG → HUMAN-READABLE BANNER LABEL
       Maps the CSS slug back to a display label for the banner row.
    ════════════════════════════════════════════════════════════════════════ */
    function slugToLabel(slug) {
        const map = {
            science:     'Science',
            technology:  'Technology',
            engineering: 'Engineering',
            art:         'Art',
            math:        'Math',
            playground:  'Playground',
            egaming:     'E-Gaming',
            esports:     'ESports',
            mission:     'Missions',
            other:       'Other',
        };
        return map[slug] || slug;
    }

    /* ════════════════════════════════════════════════════════════════════════
       INLINE SCORE EDITOR
    ════════════════════════════════════════════════════════════════════════ */
    function openScoreEditor(td, row, cat, currentPts, slug) {
        if (td.querySelector('.score-edit-input')) return; // already open

        const input = document.createElement('input');
        input.type  = 'number';
        input.min   = '0';
        input.className = 'score-edit-input';
        input.value = currentPts || 0;

        td.innerHTML = '';
        td.appendChild(input);
        input.focus();
        input.select();

        function commit() {
            const newVal = parseInt(input.value, 10);
            if (isNaN(newVal) || newVal < 0) { cancel(); return; }
            saveScore(td, row, cat, newVal, slug);
        }

        function cancel() {
            td.innerHTML = scorePill(currentPts, slug);
            td.addEventListener('click', function () {
                openScoreEditor(td, row, cat, currentPts, slug);
            }, { once: true });
        }

        input.addEventListener('keydown', e => {
            if (e.key === 'Enter')  commit();
            if (e.key === 'Escape') cancel();
        });
        input.addEventListener('blur', commit);
    }

    /* ── Save score to DB then re-render (re-fetch keeps ranking fresh) ── */
    async function saveScore(td, row, cat, newPts, slug) {
        const indicator = document.createElement('span');
        indicator.className = 'save-indicator save-saving';
        indicator.textContent = '…';
        td.innerHTML = '';
        td.appendChild(indicator);

        const eventId = $id('selectEvent').value;

        const payload = {
            event_id:              eventId,
            activity_display_name: cat,
            points:                newPts,
        };

        if (row.type === 'student') {
            payload.student_id = row.id;
        } else {
            payload.team_id = row.id;
        }

        try {
            const res = await fetch('/scores/update-by-name', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify(payload),
            });

            const json = await res.json();

            if (json.success) {
                toast('Score saved ✓', 'ok');
                /* Re-fetch the whole leaderboard so ranks / totals update */
                reRender();
            } else {
                throw new Error(json.message || 'Save failed');
            }
        } catch (err) {
            console.error(err);
            toast('Error: ' + err.message, 'err');
            td.innerHTML = scorePill(newPts, slug);
            td.addEventListener('click', function () {
                openScoreEditor(td, row, cat, newPts, slug);
            }, { once: true });
        }
    }

    /* ── Event dropdown initialization ── */
    fetch('/leaderboard-events')
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

    $id('selectEvent').addEventListener('change', e => fetchLeaderboard(e.target.value));

    $id('exportLeaderboard').addEventListener('click', () => {
        const id = $id('selectEvent').value;
        if (!id) { alert('Please select an event first!'); return; }
        window.location.href = `/leaderboard-export?event_id=${id}`;
    });

})();
</script>