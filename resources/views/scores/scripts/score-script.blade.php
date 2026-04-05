<style>
    @import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800;900&family=Barlow:wght@400;500;600&display=swap');
    
    :root {
        --lb-bg:        #0f1318;
        --lb-surface:   #181e27;
        --lb-border:    #2a3040;
        --lb-header-bg: #1e2535;
        --lb-gold:      #f5c518;
        --lb-silver:    #b8c4d0;
        --lb-bronze:    #cd7f32;
    
        --cat-science-bg:     #c0392b; --cat-science-text:     #fff;
        --cat-tech-bg:        #e67e22; --cat-tech-text:        #fff;
        --cat-eng-bg:         #27ae60; --cat-eng-text:         #fff;
        --cat-art-bg:         #2980b9; --cat-art-text:         #fff;
        --cat-math-bg:        #8e44ad; --cat-math-text:        #fff;
        --cat-playground-bg:  #7f8c8d; --cat-playground-text:  #fff;
        --cat-egaming-bg:     #e91e8c; --cat-egaming-text:     #fff;
        --cat-esports-bg:     #00bcd4; --cat-esports-text:     #000;
        --cat-mission-bg:     #ff6f00; --cat-mission-text:     #fff;
        --cat-other-bg:       #34495e; --cat-other-text:       #c9d1d9;
        --cat-bonus-bg:       #f5c518; --cat-bonus-text:       #000;
    
        --total-team-bg:    #0d2d6e; --total-team-text:   #79c0ff;
        --total-player-bg:  #1a3a1a; --total-player-text: #56d364;
        --total-bonus-bg:   #2a1a00; --total-bonus-text:  #f5c518;
        --total-grand-bg:   #3d1a00; --total-grand-text:  #f5c518;
    
        --font-head: 'Barlow Condensed', sans-serif;
        --font-body: 'Barlow', sans-serif;
        --radius: 6px;
    }
    
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
        gap: 12px;
        padding: 12px 16px;
        background: var(--lb-surface);
        border-bottom: 1px solid var(--lb-border);
        flex-wrap: wrap;
    }
    #lb-controls label {
        font-family: var(--font-head);
        font-size: 11px;
        font-weight: 700;
        color: #8b949e;
        letter-spacing: .08em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    #selectEvent {
        background: var(--lb-header-bg);
        border: 1px solid var(--lb-border);
        color: #e6edf3;
        padding: 6px 12px;
        border-radius: var(--radius);
        font-family: var(--font-body);
        font-size: 13px;
        min-width: 220px;
        cursor: pointer;
    }
    #selectEvent:focus { outline: none; border-color: #58a6ff; }
    
    .lb-legend {
        display: flex; gap: 8px; flex-wrap: wrap; align-items: center;
    }
    .lb-legend-dot {
        display: inline-flex; align-items: center; gap: 4px;
        font-family: var(--font-head); font-size: 10px; font-weight: 700;
        letter-spacing: .04em; text-transform: uppercase; color: #c9d1d9;
    }
    .lb-legend-dot span { width: 10px; height: 10px; border-radius: 2px; display: inline-block; }
    
    .lb-actions { display: flex; gap: 8px; align-items: center; margin-left: auto; flex-wrap: wrap; }
    .lb-btn {
        border: none; padding: 7px 14px; border-radius: var(--radius);
        font-family: var(--font-head); font-weight: 700; font-size: 13px;
        letter-spacing: .04em; cursor: pointer; transition: all .18s;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .lb-btn-export  { background: #238636; color: #fff; }
    .lb-btn-export:hover { background: #2ea043; }
    .lb-btn-bulk    { background: #1a2a4a; color: #79c0ff; border: 1px solid #1e3a6e; }
    .lb-btn-bulk:hover { background: #1e3a6e; }
    .lb-btn-bulk.active { background: #58a6ff; color: #000; border-color: #58a6ff; }
    .lb-btn-bulk-go { background: #1e3a6e; color: #79c0ff; border: 1px solid #30507e; }
    .lb-btn-bulk-go:hover { background: #2a4a8e; }
    .lb-btn-bonus   { background: #2a1a00; color: #f5c518; border: 1px solid #7a5a00; }
    .lb-btn-bonus:hover { background: #3a2a00; }
    
    #bulk-bar {
        display: none;
        align-items: center;
        gap: 12px;
        padding: 7px 16px;
        background: #0d1e3a;
        border-bottom: 1px solid #1e3a6e;
        font-family: var(--font-head);
        font-size: 12px;
        color: #79c0ff;
    }
    #bulk-bar.show { display: flex; }
    #bulk-count { font-weight: 900; color: #f5c518; font-size: 15px; }
    #bulk-hint  { color: #484f58; font-size: 11px; }
    
    #lb-scroll { overflow-x: auto; overflow-y: auto; max-height: 72vh; }
    
    #lb-table {
        border-collapse: separate;
        border-spacing: 0;
        width: max-content;
        min-width: 100%;
        font-family: var(--font-body);
        font-size: 13px;
    }
    
    /* THEAD ROW 1 */
    #lb-table thead tr.row-banner th {
        background: var(--lb-surface);
        color: #8b949e;
        font-family: var(--font-head);
        font-size: 10px; font-weight: 700;
        letter-spacing: .1em; text-transform: uppercase;
        padding: 7px 10px;
        border-bottom: 1px solid var(--lb-border);
        border-right: 1px solid var(--lb-border);
        white-space: nowrap; text-align: center;
        position: sticky; top: 0; z-index: 20;
    }
    #lb-table thead tr.row-banner th.banner-science     { background: var(--cat-science-bg);   color: var(--cat-science-text); }
    #lb-table thead tr.row-banner th.banner-technology  { background: var(--cat-tech-bg);       color: var(--cat-tech-text); }
    #lb-table thead tr.row-banner th.banner-engineering { background: var(--cat-eng-bg);        color: var(--cat-eng-text); }
    #lb-table thead tr.row-banner th.banner-art         { background: var(--cat-art-bg);        color: var(--cat-art-text); }
    #lb-table thead tr.row-banner th.banner-math        { background: var(--cat-math-bg);       color: var(--cat-math-text); }
    #lb-table thead tr.row-banner th.banner-playground  { background: var(--cat-playground-bg); color: var(--cat-playground-text); }
    #lb-table thead tr.row-banner th.banner-egaming     { background: var(--cat-egaming-bg);    color: var(--cat-egaming-text); }
    #lb-table thead tr.row-banner th.banner-esports     { background: var(--cat-esports-bg);    color: var(--cat-esports-text); }
    #lb-table thead tr.row-banner th.banner-mission     { background: var(--cat-mission-bg);    color: var(--cat-mission-text); }
    #lb-table thead tr.row-banner th.banner-other       { background: var(--cat-other-bg);      color: var(--cat-other-text); }
    #lb-table thead tr.row-banner th.banner-bonus       { background: var(--cat-bonus-bg);      color: var(--cat-bonus-text); }
    #lb-table thead tr.row-banner th.banner-total-team   { background: var(--total-team-bg);   color: var(--total-team-text); }
    #lb-table thead tr.row-banner th.banner-total-player { background: var(--total-player-bg); color: var(--total-player-text); }
    #lb-table thead tr.row-banner th.banner-total-bonus  { background: var(--total-bonus-bg);  color: var(--total-bonus-text); }
    #lb-table thead tr.row-banner th.banner-total-grand  { background: var(--total-grand-bg);  color: var(--total-grand-text); }
    
    /* THEAD ROW 2 */
    #lb-table thead tr.row-cols th {
        background: var(--lb-header-bg);
        color: #c9d1d9;
        font-family: var(--font-head);
        font-size: 10px; font-weight: 800;
        letter-spacing: .07em; text-transform: uppercase;
        padding: 6px 10px;
        border-bottom: 2px solid var(--lb-border);
        border-right: 1px solid var(--lb-border);
        white-space: nowrap; text-align: center;
        position: sticky; top: 31px; z-index: 20;
        max-width: 140px; overflow: hidden; text-overflow: ellipsis;
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
    #lb-table thead tr.row-cols th.cat-bonus       { border-top: 3px solid var(--cat-bonus-bg);  color: var(--cat-bonus-bg); }
    #lb-table thead tr.row-cols th.col-total-team   { border-top: 3px solid var(--total-team-text);   color: var(--total-team-text); }
    #lb-table thead tr.row-cols th.col-total-player { border-top: 3px solid var(--total-player-text); color: var(--total-player-text); }
    #lb-table thead tr.row-cols th.col-total-bonus  { border-top: 3px solid var(--total-bonus-text);  color: var(--total-bonus-text); }
    #lb-table thead tr.row-cols th.col-total-grand  { border-top: 3px solid var(--total-grand-text);  color: var(--total-grand-text); }
    
    /* Sticky left columns */
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
    
    /* TBODY */
    #lb-table tbody td {
        padding: 5px 10px;
        border-bottom: 1px solid #1e2535;
        border-right: 1px solid #1e2535;
        white-space: nowrap; vertical-align: middle;
        color: #c9d1d9; text-align: center;
    }
    #lb-table tbody tr.tr-divider td {
        background: linear-gradient(90deg,#1a3a2a,#162a20);
        color: #56d364;
        font-family: var(--font-head); font-weight: 900; font-size: 12px;
        letter-spacing: .1em; text-transform: uppercase;
        padding: 7px 16px; border-bottom: 2px solid #238636; text-align: left;
    }
    #lb-table tbody tr.tr-team td {
        background: #1c2638; font-weight: 600; color: #e6edf3;
        border-bottom: 2px solid #0f1318;
    }
    #lb-table tbody tr.tr-student td {
        background: #151b27; font-size: 12px; color: #8b949e;
        border-bottom: 1px dashed #1e2535;
    }
    
    /* Sticky body cells */
    #lb-table tbody td.fix-0 { position: sticky; left: 0;     z-index: 5; }
    #lb-table tbody td.fix-1 { position: sticky; left: 46px;  z-index: 5; }
    #lb-table tbody td.fix-2 { position: sticky; left: 96px;  z-index: 5; }
    #lb-table tbody td.fix-3 { position: sticky; left: 230px; z-index: 5; }
    #lb-table tbody td.fix-4 { position: sticky; left: 360px; z-index: 5; }
    #lb-table tbody tr.tr-team    td.fix-0,
    #lb-table tbody tr.tr-team    td.fix-1,
    #lb-table tbody tr.tr-team    td.fix-2,
    #lb-table tbody tr.tr-team    td.fix-3,
    #lb-table tbody tr.tr-team    td.fix-4 { background: #1c2638; }
    #lb-table tbody tr.tr-student td.fix-0,
    #lb-table tbody tr.tr-student td.fix-1,
    #lb-table tbody tr.tr-student td.fix-2,
    #lb-table tbody tr.tr-student td.fix-3,
    #lb-table tbody tr.tr-student td.fix-4 { background: #151b27; }
    
    /* Score pills */
    .score-pill {
        display: inline-block; min-width: 40px;
        padding: 2px 6px; border-radius: 4px;
        font-family: var(--font-head); font-weight: 700; font-size: 13px; text-align: center;
    }
    .score-science     { background: var(--cat-science-bg);   color: var(--cat-science-text); }
    .score-technology  { background: var(--cat-tech-bg);       color: var(--cat-tech-text); }
    .score-engineering { background: var(--cat-eng-bg);        color: var(--cat-eng-text); }
    .score-art         { background: var(--cat-art-bg);        color: var(--cat-art-text); }
    .score-math        { background: var(--cat-math-bg);       color: var(--cat-math-text); }
    .score-playground  { background: var(--cat-playground-bg); color: var(--cat-playground-text); }
    .score-egaming     { background: var(--cat-egaming-bg);    color: var(--cat-egaming-text); }
    .score-esports     { background: var(--cat-esports-bg);    color: var(--cat-esports-text); }
    .score-mission     { background: var(--cat-mission-bg);    color: var(--cat-mission-text); }
    .score-other       { background: var(--cat-other-bg);      color: var(--cat-other-text); }
    .score-zero        { color: #3a4454; font-size: 12px; }
    .score-bonus-pill  { background: var(--cat-bonus-bg); color: var(--cat-bonus-text); font-family: var(--font-head); font-weight: 900; font-size: 13px; padding: 2px 8px; border-radius: 4px; display:inline-block; }
    
    /* Totals */
    td.td-total-team   { background: var(--total-team-bg)   !important; color: var(--total-team-text)   !important; font-family: var(--font-head) !important; font-weight: 800 !important; font-size: 14px !important; }
    td.td-total-player { background: var(--total-player-bg) !important; color: var(--total-player-text) !important; font-family: var(--font-head) !important; font-weight: 700 !important; font-size: 13px !important; }
    td.td-total-bonus  { background: var(--total-bonus-bg)  !important; color: var(--total-bonus-text)  !important; font-family: var(--font-head) !important; font-weight: 800 !important; font-size: 14px !important; }
    td.td-total-grand  { background: var(--total-grand-bg)  !important; color: var(--total-grand-text)  !important; font-family: var(--font-head) !important; font-weight: 900 !important; font-size: 15px !important; }
    td.td-org { color: #f5c518 !important; font-family: var(--font-head) !important; font-weight: 700 !important; font-size: 11px !important; letter-spacing:.04em; }
    
    /* Rank medals */
    .rank-medal {
        display: inline-flex; align-items: center; justify-content: center;
        width: 26px; height: 26px; border-radius: 50%;
        font-family: var(--font-head); font-weight: 900; font-size: 13px;
    }
    .rank-1 { background: var(--lb-gold);   color: #000; }
    .rank-2 { background: var(--lb-silver); color: #000; }
    .rank-3 { background: var(--lb-bronze); color: #fff; }
    .rank-n { background: #21262d; color: #8b949e; font-size: 11px; }
    
    /* Inline edit cell */
    .score-edit-cell { position: relative; cursor: pointer; transition: background .12s; }
    .score-edit-cell:hover { background: rgba(88,166,255,.07) !important; }
    .score-edit-cell:hover .score-pill  { opacity: .65; }
    .score-edit-cell:hover .score-zero  { opacity: .65; }
    .score-edit-cell:hover::after {
        content: '✎'; position: absolute; right: 3px; top: 50%;
        transform: translateY(-50%); font-size: 9px; color: #58a6ff; pointer-events: none;
    }
    /* Bulk mode */
    .bulk-active .score-edit-cell { cursor: crosshair; }
    .bulk-active .score-edit-cell:hover { background: rgba(88,166,255,.12) !important; }
    .score-edit-cell.bulk-sel {
        background: rgba(88,166,255,.22) !important;
        outline: 2px solid #58a6ff; outline-offset: -2px;
    }
    .score-edit-cell.bulk-sel::after { display: none; }
    
    .score-edit-input {
        width: 60px; background: #0d1117;
        border: 1.5px solid #58a6ff; color: #e6edf3;
        border-radius: 4px; padding: 2px 4px;
        font-family: var(--font-head); font-size: 14px; font-weight: 700;
        text-align: center; outline: none;
    }
    
    /* Bulk table rows */
    #bulkEditTbody tr { border-bottom: 1px solid #1e2535; }
    #bulkEditTbody td { padding: 8px 12px; background: #151b27; border: none; vertical-align: middle; }
    #bulkEditTbody tr:hover td { background: #1a2230; }
    .bulk-pts-inp {
        width: 72px; background: #0d1117;
        border: 1.5px solid #30507e; color: #e6edf3;
        border-radius: 4px; padding: 3px 5px;
        font-family: var(--font-head); font-size: 14px; font-weight: 700;
        text-align: center; outline: none;
        transition: border-color .15s;
    }
    .bulk-pts-inp:focus { border-color: #58a6ff; }
    .bulk-pts-inp.err   { border-color: #f85149 !important; }
    .bulk-err-msg { font-size: 10px; color: #f85149; display: none; margin-top: 2px; }
    
    /* Toast */
    #lb-toast {
        position: fixed; bottom: 20px; right: 20px;
        padding: 9px 16px; border-radius: 8px;
        font-family: var(--font-head); font-size: 13px; font-weight: 700;
        z-index: 9999; opacity: 0; transition: opacity .25s; pointer-events: none;
        max-width: 320px;
    }
    #lb-toast.show  { opacity: 1; }
    #lb-toast.ok    { background: #1a3a1a; color: #56d364; border: 1px solid #238636; }
    #lb-toast.err   { background: #3a1a1a; color: #f85149; border: 1px solid #f85149; }
    #lb-toast.warn  { background: #2a1a00; color: #f5c518; border: 1px solid #7a5a00; }
    
    /* State rows */
    .lb-state-row td {
        text-align: center; padding: 50px;
        color: #484f58; font-family: var(--font-head); font-size: 15px;
        letter-spacing: .06em; background: var(--lb-bg) !important;
    }
    </style>
    
    <div id="lb-toast"></div>
    <script>window.USER_ROLE = {{ auth()->check() ? auth()->user()->role : 0 }};</script>
    
    <script>
    (function () {
    'use strict';
    
    /* ═══════════════════════════════════════════════════════════════════
       CONSTANTS & STATE
    ═══════════════════════════════════════════════════════════════════ */
    const IS_ADMIN   = window.USER_ROLE === 1;
    const $  = id    => document.getElementById(id);
    const CSRF       = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    
    let _data        = null;   // { categories:[{name,type,id,max_score}], rows:[…] }
    let _bulkMode    = false;
    let _bulkList    = [];     // [{td, row, catIdx, catName, actId, pts, slug, max}]
    
    /* ═══════════════════════════════════════════════════════════════════
       TOAST
    ═══════════════════════════════════════════════════════════════════ */
    function toast(msg, type = 'ok') {
        const el = $('lb-toast');
        el.textContent = msg;
        el.className   = `show ${type}`;
        clearTimeout(el._t);
        el._t = setTimeout(() => el.className = '', 3000);
    }
    
    /* ═══════════════════════════════════════════════════════════════════
       HELPERS
    ═══════════════════════════════════════════════════════════════════ */
    function esc(s) {
        return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function pill(pts, slug) {
        return pts > 0
            ? `<span class="score-pill score-${slug}">${Number(pts).toLocaleString()}</span>`
            : `<span class="score-zero">—</span>`;
    }
    function bonusPill(pts) {
        return pts > 0
            ? `<span class="score-bonus-pill">+${Number(pts).toLocaleString()}</span>`
            : `<span class="score-zero">—</span>`;
    }
    function rankBadge(r) {
        if (!r) return '';
        const cls = r <= 3 ? `rank-${r}` : 'rank-n';
        return `<span class="rank-medal ${cls}">${r}</span>`;
    }
    function slugLabel(slug) {
        return { science:'Science', technology:'Technology', engineering:'Engineering',
                 art:'Art', math:'Math', playground:'Playground',
                 egaming:'E-Gaming', esports:'ESports', mission:'Missions', other:'Other' }[slug] ?? slug;
    }
    
    /* ═══════════════════════════════════════════════════════════════════
       FETCH & RENDER
    ═══════════════════════════════════════════════════════════════════ */
    async function loadLeaderboard(eventId) {
        if (!eventId) return;
        $('lb-thead').innerHTML = '';
        $('lb-tbody').innerHTML = `<tr class="lb-state-row"><td colspan="999">Loading…</td></tr>`;
    
        try {
            const res  = await fetch(`/leaderboard-data?event_id=${eventId}`, { headers:{ Accept:'application/json' } });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            _data = data;
    
            if (!data.rows?.length) {
                $('lb-tbody').innerHTML = `<tr class="lb-state-row"><td colspan="999">No data for this event.</td></tr>`;
                return;
            }
            buildTable(data);
        } catch(e) {
            console.error(e);
            $('lb-tbody').innerHTML = `<tr class="lb-state-row"><td colspan="999">Error loading leaderboard.</td></tr>`;
        }
    }
    
    /* ═══════════════════════════════════════════════════════════════════
       BUILD TABLE
    ═══════════════════════════════════════════════════════════════════ */
    function buildTable(data) {
        const cats     = data.categories ?? [];   // [{name,type,id,max_score}]
        const rows     = data.rows;
        const catNames = cats.map(c => c.name);
        const catSlugs = cats.map(c => c.type);
    
        /* Banner groups */
        const bannerGroups = [];
        catSlugs.forEach((slug, i) => {
            const last = bannerGroups[bannerGroups.length - 1];
            if (last && last.slug === slug) { last.span++; }
            else bannerGroups.push({ slug, span: 1 });
        });
    
        const thead = $('lb-thead');
        const tbody = $('lb-tbody');
        thead.innerHTML = '';
    
        /* Fixed column definitions */
        const fixedCols = [
            { banner:'#',        col:'RANK',      cls:'fix-0', w:46 },
            { banner:'NO',       col:'TEAM NO',   cls:'fix-1', w:50 },
            { banner:'TEAM',     col:'TEAM NAME', cls:'fix-2', w:134 },
            { banner:'MEMBERS',  col:'MEMBERS',   cls:'fix-3', w:130 },
            { banner:'DIVISION', col:'DIVISION',  cls:'fix-4', w:100 },
        ];
    
        /* ── ROW 1 banner ── */
        const r1 = document.createElement('tr');
        r1.className = 'row-banner';
        fixedCols.forEach(fc => {
            const th = document.createElement('th');
            th.className = fc.cls; th.textContent = fc.banner; th.style.minWidth = fc.w + 'px';
            r1.appendChild(th);
        });
        bannerGroups.forEach(g => {
            const th = document.createElement('th');
            th.colSpan   = g.span;
            th.textContent = slugLabel(g.slug).toUpperCase();
            th.className   = 'banner-' + g.slug;
            th.style.minWidth = (g.span * 120) + 'px';
            r1.appendChild(th);
        });
        // Bonus banner
        const thBonusBanner = document.createElement('th');
        thBonusBanner.textContent = 'BONUS';
        thBonusBanner.className   = 'banner-bonus';
        thBonusBanner.style.minWidth = '90px';
        r1.appendChild(thBonusBanner);
        // Total banners
        [
            { label:'TEAM PTS',     cls:'banner-total-team',   w:95 },
            { label:'PLAYER PTS',   cls:'banner-total-player', w:95 },
            { label:'GRAND TOTAL',  cls:'banner-total-grand',  w:100 },
            { label:'RANK',         cls:'',                    w:60 },
            { label:'ORG',          cls:'',                    w:120 },
        ].forEach(m => {
            const th = document.createElement('th');
            th.textContent = m.label; if (m.cls) th.className = m.cls;
            th.style.minWidth = m.w + 'px'; r1.appendChild(th);
        });
        thead.appendChild(r1);
    
        /* ── ROW 2 col labels ── */
        const r2 = document.createElement('tr');
        r2.className = 'row-cols';
        fixedCols.forEach(fc => {
            const th = document.createElement('th');
            th.className = fc.cls; th.textContent = fc.col; th.style.minWidth = fc.w + 'px';
            r2.appendChild(th);
        });
        catNames.forEach((name, i) => {
            const th = document.createElement('th');
            th.textContent = name;
            th.className   = 'cat-' + catSlugs[i];
            th.style.minWidth = '120px';
            th.title = name;
            r2.appendChild(th);
        });
        // Bonus col label
        const thBonusCol = document.createElement('th');
        thBonusCol.textContent = 'BONUS';
        thBonusCol.className   = 'cat-bonus';
        thBonusCol.style.minWidth = '90px';
        r2.appendChild(thBonusCol);
        [
            { label:'TEAM PTS',    cls:'col-total-team',   w:95 },
            { label:'PLAYER PTS',  cls:'col-total-player', w:95 },
            { label:'GRAND TOTAL', cls:'col-total-grand',  w:100 },
            { label:'RANK',        cls:'',                 w:60 },
            { label:'ORGANIZATION',cls:'',                 w:120 },
        ].forEach(m => {
            const th = document.createElement('th');
            th.textContent = m.label; if (m.cls) th.className = m.cls;
            th.style.minWidth = m.w + 'px'; r2.appendChild(th);
        });
        thead.appendChild(r2);
    
        /* ── TBODY ── */
        const frag = document.createDocumentFragment();
        let curGroup = null;
        const teamSeq = {};
    
        rows.forEach(row => {
            const isTeam   = row.type === 'team';
            const groupKey = row.group ?? 'Ungrouped';
    
            /* Group divider */
            if (isTeam && groupKey !== curGroup) {
                curGroup = groupKey;
                const dTr = document.createElement('tr');
                dTr.className = 'tr-divider';
                const dTd = document.createElement('td');
                dTd.colSpan = 5 + catNames.length + 6; // +1 for bonus col
                const sub = (row.subgroup && row.subgroup !== '-') ? ' › ' + row.subgroup.toUpperCase() : '';
                dTd.textContent = '▸  ' + groupKey.toUpperCase() + sub;
                dTr.appendChild(dTd);
                frag.appendChild(dTr);
            }
    
            const tr = document.createElement('tr');
            tr.className       = isTeam ? 'tr-team' : 'tr-student';
            tr.dataset.rowType = row.type;
            tr.dataset.rowId   = row.id;
    
            /* col 0: rank */
            const td0 = document.createElement('td');
            td0.className = 'fix-0'; td0.style.textAlign = 'center';
            if (isTeam && row.rank) td0.innerHTML = rankBadge(row.rank);
            tr.appendChild(td0);
    
            /* col 1: seq */
            const td1 = document.createElement('td');
            td1.className = 'fix-1'; td1.style.textAlign = 'center';
            if (isTeam) {
                teamSeq[groupKey] = (teamSeq[groupKey] ?? 0) + 1;
                td1.textContent = teamSeq[groupKey];
            }
            tr.appendChild(td1);
    
            /* col 2: team name */
            const td2 = document.createElement('td');
            td2.className = 'fix-2';
            td2.style.cssText = `text-align:left;padding-left:${isTeam?'10':'22'}px;${isTeam?'font-weight:700;':''}`;
            td2.textContent = isTeam ? (row.team_name || '—') : '';
            tr.appendChild(td2);
    
            /* col 3: member */
            const td3 = document.createElement('td');
            td3.className = 'fix-3'; td3.style.textAlign = 'left';
            td3.textContent = isTeam ? '' : (row.student_name || '—');
            tr.appendChild(td3);
    
            /* col 4: division */
            const td4 = document.createElement('td');
            td4.className = 'fix-4'; td4.textContent = row.division || '—';
            tr.appendChild(td4);
    
            /* Activity score columns */
            catNames.forEach((cat, i) => {
                const td   = document.createElement('td');
                td.className     = 'score-edit-cell';
                const pts        = row.scores?.[cat] ?? 0;
                const slug       = catSlugs[i];
                const actId      = cats[i]?.id;
                const maxScore   = cats[i]?.max_score ?? 9999;
    
                td.innerHTML     = pill(pts, slug);
                td.dataset.pts   = pts;
                td.dataset.cat   = cat;
                td.dataset.slug  = slug;
                td.dataset.actId = actId;
                td.dataset.max   = maxScore;
    
                if (IS_ADMIN) {
                    td.addEventListener('click', () => {
                        if (_bulkMode) toggleBulkCell(td, row, i);
                        else           openEditor(td, row, cat, pts, slug, actId, maxScore);
                    });
                } else {
                    td.style.cursor = 'default';
                }
                tr.appendChild(td);
            });
    
            /* Bonus column (read-only display) */
            const tdBonus = document.createElement('td');
            tdBonus.className = 'td-bonus-cell';
            const totalBonus = isTeam
                ? (row.total_bonus ?? 0)
                : (row.total_bonus ?? 0);
            tdBonus.innerHTML = bonusPill(totalBonus);
            tr.appendChild(tdBonus);
    
            /* Team points */
            const tdTP = document.createElement('td');
            tdTP.className = 'td-total-team';
            if (isTeam) tdTP.textContent = Number(row.team_points ?? 0).toLocaleString();
            tr.appendChild(tdTP);
    
            /* Player points */
            const tdPP = document.createElement('td');
            tdPP.className = 'td-total-player';
            tdPP.textContent = Number(isTeam ? (row.player_points ?? 0) : (row.total_points ?? 0)).toLocaleString();
            tr.appendChild(tdPP);
    
            /* Grand total */
            const tdGT = document.createElement('td');
            tdGT.className = 'td-total-grand';
            if (isTeam) tdGT.textContent = Number(row.grand_total ?? 0).toLocaleString();
            tr.appendChild(tdGT);
    
            /* Rank end */
            const tdRE = document.createElement('td');
            tdRE.style.cssText = 'font-family:var(--font-head);font-weight:900;';
            if (isTeam && row.rank) tdRE.innerHTML = rankBadge(row.rank);
            tr.appendChild(tdRE);
    
            /* Org */
            const tdOrg = document.createElement('td');
            tdOrg.className = 'td-org';
            tdOrg.textContent = isTeam ? (row.organization || '—') : '';
            tr.appendChild(tdOrg);
    
            frag.appendChild(tr);
        });
    
        tbody.innerHTML = '';
        tbody.appendChild(frag);
    }
    
    /* ═══════════════════════════════════════════════════════════════════
       INLINE EDITOR — no full re-render, pure DOM patch
    ═══════════════════════════════════════════════════════════════════ */
    function openEditor(td, row, cat, currentPts, slug, actId, maxScore) {
        if (td.querySelector('.score-edit-input')) return;
    
        const input     = document.createElement('input');
        input.type      = 'number';
        input.min       = '0';
        input.max       = String(maxScore);
        input.className = 'score-edit-input';
        input.value     = currentPts || 0;
        td.innerHTML    = '';
        td.appendChild(input);
        input.focus();
        input.select();
    
        let done = false;
    
        function restore() {
            if (done) return;
            done = true;
            td.innerHTML   = pill(currentPts, slug);
            td.dataset.pts = currentPts;
        }
    
        function commit() {
            if (done) return;
            done = true;
            const v = parseInt(input.value, 10);
            if (isNaN(v) || v < 0)  { restore(); return; }
            if (v > maxScore) {
                toast(`Max allowed is ${maxScore} for this activity`, 'warn');
                done = false;
                restore();
                return;
            }
            // Optimistic update
            td.innerHTML   = pill(v, slug);
            td.dataset.pts = v;
            patchTotalsInDOM(row, cat, v, currentPts);
            persistScore(actId, row, v, maxScore).then(ok => {
                if (!ok) {
                    // rollback
                    td.innerHTML   = pill(currentPts, slug);
                    td.dataset.pts = currentPts;
                    patchTotalsInDOM(row, cat, currentPts, v);
                }
            });
        }
    
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter')  { e.preventDefault(); commit(); }
            if (e.key === 'Escape') { done = true; restore(); }
        });
        // blur with small delay so keydown fires first
        input.addEventListener('blur', () => setTimeout(commit, 120));
    }
    
    /* ═══════════════════════════════════════════════════════════════════
       PERSIST SINGLE SCORE — uses activity ID (no string matching)
    ═══════════════════════════════════════════════════════════════════ */
    async function persistScore(actId, row, pts, maxScore) {
        const eventId = $('selectEvent').value;
        const payload = {
            event_id:               parseInt(eventId),
            challenge_activity_id:  parseInt(actId),
            points:                 pts,
        };
        if (row.type === 'student') payload.student_id = row.id;
        else                        payload.team_id    = row.id;
    
        try {
            const res  = await fetch('/scores/update-by-id', {
                method:  'POST',
                headers: { 'Content-Type':'application/json', Accept:'application/json', 'X-CSRF-TOKEN': CSRF() },
                body:    JSON.stringify(payload),
            });
            const json = await res.json();
            if (json.success) { toast('Score saved ✓'); return true; }
            toast(json.message || 'Save failed', 'err');
            return false;
        } catch(e) {
            toast('Network error', 'err');
            return false;
        }
    }
    
    /* ═══════════════════════════════════════════════════════════════════
       PATCH TOTALS IN DOM  (no full re-render)
    ═══════════════════════════════════════════════════════════════════ */
    function patchTotalsInDOM(row, cat, newPts, oldPts) {
        if (!_data) return;
        const diff = newPts - oldPts;
    
        // Update in-memory
        const dr = _data.rows.find(r => r.type === row.type && r.id === row.id);
        if (dr?.scores) dr.scores[cat] = newPts;
    
        const tbody = $('lb-tbody');
    
        if (row.type === 'team') {
            if (dr) {
                dr.team_points  = (dr.team_points  ?? 0) + diff;
                dr.grand_total  = (dr.grand_total  ?? 0) + diff;
            }
            const tr = tbody.querySelector(`tr[data-row-type="team"][data-row-id="${row.id}"]`);
            if (tr) {
                const tpEl = tr.querySelector('.td-total-team');
                const gtEl = tr.querySelector('.td-total-grand');
                if (tpEl && dr) tpEl.textContent = Number(dr.team_points).toLocaleString();
                if (gtEl && dr) gtEl.textContent = Number(dr.grand_total).toLocaleString();
            }
        } else {
            if (dr) dr.total_points = (dr.total_points ?? 0) + diff;
            const tr = tbody.querySelector(`tr[data-row-type="student"][data-row-id="${row.id}"]`);
            if (tr) {
                const ppEl = tr.querySelector('.td-total-player');
                if (ppEl && dr) ppEl.textContent = Number(dr.total_points).toLocaleString();
            }
            // Also patch parent team
            const teamDr = _data.rows.find(r => r.type === 'team' && r.team_name === row.team_name);
            if (teamDr) {
                teamDr.player_points = (teamDr.player_points ?? 0) + diff;
                teamDr.grand_total   = (teamDr.grand_total   ?? 0) + diff;
                const teamTr = tbody.querySelector(`tr[data-row-type="team"][data-row-id="${teamDr.id}"]`);
                if (teamTr) {
                    const ppEl = teamTr.querySelector('.td-total-player');
                    const gtEl = teamTr.querySelector('.td-total-grand');
                    if (ppEl) ppEl.textContent = Number(teamDr.player_points).toLocaleString();
                    if (gtEl) gtEl.textContent = Number(teamDr.grand_total).toLocaleString();
                }
            }
        }
    
        recomputeRanks();
    }
    
    /* ═══════════════════════════════════════════════════════════════════
       RECOMPUTE RANKS (only for teams with grand_total > 0)
    ═══════════════════════════════════════════════════════════════════ */
    function recomputeRanks() {
        if (!_data) return;
        const tbody    = $('lb-tbody');
        const teamRows = _data.rows.filter(r => r.type === 'team');
        const sorted   = [...teamRows].sort((a, b) => (b.grand_total ?? 0) - (a.grand_total ?? 0));
    
        let rank = 1, prevGT = null, prevRank = 1;
    
        sorted.forEach(row => {
            const gt = row.grand_total ?? 0;
            let assigned = null;
    
            if (gt > 0) {
                if (prevGT !== null && gt === prevGT) {
                    assigned = prevRank;
                } else {
                    assigned = rank;
                    prevRank = rank;
                }
                prevGT = gt;
                rank++;
            }
            row.rank = assigned;
    
            const tr = tbody.querySelector(`tr[data-row-type="team"][data-row-id="${row.id}"]`);
            if (!tr) return;
            const badge = assigned ? rankBadge(assigned) : '';
            const td0   = tr.querySelector('.fix-0');
            const tdRE  = tr.cells[tr.cells.length - 2]; // rank-end is 2nd to last
            if (td0) td0.innerHTML = badge;
            if (tdRE && tdRE.style.fontFamily.includes('Barlow')) tdRE.innerHTML = badge;
        });
    }
    
    /* ═══════════════════════════════════════════════════════════════════
       BULK EDIT
    ═══════════════════════════════════════════════════════════════════ */
    function toggleBulkMode() {
        _bulkMode = !_bulkMode;
        _bulkList = [];
    
        const btn  = $('bulkEditBtn');
        const bar  = $('bulk-bar');
        const tbl  = $('lb-table');
    
        if (_bulkMode) {
            btn.classList.add('active');
            btn.textContent = '✕ Exit Bulk';
            bar.classList.add('show');
            tbl.classList.add('bulk-active');
        } else {
            btn.classList.remove('active');
            btn.textContent = '⊞ Bulk Edit';
            bar.classList.remove('show');
            tbl.classList.remove('bulk-active');
            document.querySelectorAll('.score-edit-cell.bulk-sel').forEach(td => td.classList.remove('bulk-sel'));
        }
        updateBulkBar();
    }
    
    function toggleBulkCell(td, row, catIdx) {
        const idx = _bulkList.findIndex(s => s.td === td);
        if (idx > -1) {
            _bulkList.splice(idx, 1);
            td.classList.remove('bulk-sel');
        } else {
            const cat      = td.dataset.cat;
            const actId    = td.dataset.actId;
            const pts      = parseInt(td.dataset.pts ?? 0);
            const slug     = td.dataset.slug;
            const max      = parseInt(td.dataset.max ?? 9999);
            _bulkList.push({ td, row, cat, actId, pts, slug, max });
            td.classList.add('bulk-sel');
        }
        updateBulkBar();
    }
    
    function updateBulkBar() {
        const el = $('bulk-count');
        if (el) el.textContent = _bulkList.length;
    }
    
    function openBulkModal() {
        if (!_bulkList.length) { toast('Select score cells first by clicking them', 'warn'); return; }
    
        const tbody = $('bulkEditTbody');
        tbody.innerHTML = '';
    
        _bulkList.forEach((sel, i) => {
            const entityName = sel.row.type === 'team'
                ? (sel.row.team_name    || 'Team')
                : (sel.row.student_name || 'Player');
            const typeColor  = sel.row.type === 'team' ? '#79c0ff' : '#56d364';
            const typeBg     = sel.row.type === 'team' ? '#0d2d6e' : '#1a3a1a';
    
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="width:36px;">
                    <input type="checkbox" class="bk-chk" data-i="${i}" checked style="accent-color:#58a6ff;">
                </td>
                <td style="text-align:left;">
                    <span style="background:${typeBg};color:${typeColor};font-size:9px;font-family:var(--font-head);font-weight:800;padding:1px 5px;border-radius:3px;letter-spacing:.06em;">${sel.row.type.toUpperCase()}</span>
                    <span style="margin-left:6px;color:#c9d1d9;">${esc(entityName)}</span>
                </td>
                <td style="text-align:left;font-size:11px;color:#8b949e;max-width:160px;overflow:hidden;text-overflow:ellipsis;" title="${esc(sel.cat)}">${esc(sel.cat)}</td>
                <td style="text-align:center;">${pill(sel.pts, sel.slug)}</td>
                <td style="text-align:center;">
                    <input type="number" min="0" max="${sel.max}" value="${sel.pts}"
                           class="bulk-pts-inp" data-i="${i}" data-max="${sel.max}">
                    <div class="bulk-err-msg" id="bkerr-${i}">Max is ${sel.max}</div>
                </td>
                <td style="text-align:center;color:#8b949e;font-size:11px;">${sel.max}</td>
            `;
            tbody.appendChild(tr);
        });
    
        $('bulkEditCount').textContent = _bulkList.length + ' cells';
        $('bulkValidationMsg').textContent = '';
    
        // Validate on input
        tbody.querySelectorAll('.bulk-pts-inp').forEach(inp => {
            inp.addEventListener('input', function() {
                const max = parseInt(this.dataset.max);
                const val = parseInt(this.value);
                const err = $(`bkerr-${this.dataset.i}`);
                if (isNaN(val) || val < 0 || val > max) {
                    this.classList.add('err');
                    if (err) err.style.display = 'block';
                } else {
                    this.classList.remove('err');
                    if (err) err.style.display = 'none';
                }
            });
        });
    
        // Select all
        $('bulkCheckAll').checked = true;
        $('bulkCheckAll').onchange = function() {
            tbody.querySelectorAll('.bk-chk').forEach(cb => cb.checked = this.checked);
        };
    
        const modal = new bootstrap.Modal($('bulkEditModal'));
        modal.show();
        if (window.lucide) lucide.createIcons();
    }
    
    /* ── Save bulk ── */
    $('bulkSaveConfirmBtn').addEventListener('click', async function() {
        const inputs = document.querySelectorAll('.bulk-pts-inp');
        const checks = document.querySelectorAll('.bk-chk');
        const msgEl  = $('bulkValidationMsg');
        msgEl.textContent = '';
    
        // Validate all
        let hasErr = false;
        inputs.forEach((inp, i) => {
            if (!checks[i]?.checked) return;
            const v   = parseInt(inp.value);
            const max = parseInt(inp.dataset.max);
            if (isNaN(v) || v < 0 || v > max) {
                hasErr = true;
                inp.classList.add('err');
                const err = $(`bkerr-${inp.dataset.i}`);
                if (err) err.style.display = 'block';
            }
        });
        if (hasErr) { msgEl.textContent = 'Fix highlighted errors before saving.'; return; }
    
        this.disabled    = true;
        this.textContent = 'Saving…';
    
        const tasks = [];
        inputs.forEach((inp, i) => {
            if (!checks[i]?.checked) return;
            const idx  = parseInt(inp.dataset.i);
            const sel  = _bulkList[idx];
            const newPts = parseInt(inp.value);
            if (newPts === sel.pts) return; // unchanged — skip
            tasks.push({ sel, newPts });
        });
    
        if (!tasks.length) {
            toast('No changes to save', 'warn');
            this.disabled    = false;
            this.textContent = 'Save All';
            return;
        }
    
        let ok = 0, fail = 0;
        await Promise.all(tasks.map(async ({ sel, newPts }) => {
            const saved = await persistScore(sel.actId, sel.row, newPts, sel.max);
            if (saved) {
                sel.td.innerHTML   = pill(newPts, sel.slug);
                sel.td.dataset.pts = newPts;
                sel.td.classList.remove('bulk-sel');
                patchTotalsInDOM(sel.row, sel.cat, newPts, sel.pts);
                ok++;
            } else {
                fail++;
            }
        }));
    
        bootstrap.Modal.getInstance($('bulkEditModal'))?.hide();
        _bulkList = [];
        updateBulkBar();
        toast(fail === 0 ? `✓ ${ok} score(s) saved` : `${ok} saved, ${fail} failed`, fail ? 'err' : 'ok');
    
        this.disabled    = false;
        this.textContent = 'Save All';
    });
    
    /* ═══════════════════════════════════════════════════════════════════
       EVENT DROPDOWN INIT
    ═══════════════════════════════════════════════════════════════════ */
    fetch('/leaderboard-events', { headers:{ Accept:'application/json' } })
        .then(r => r.json())
        .then(events => {
            const sel = $('selectEvent');
            events.forEach(ev => {
                const o = document.createElement('option');
                o.value = ev.id; o.textContent = ev.name;
                sel.appendChild(o);
            });
            if (events.length) { sel.value = events[0].id; loadLeaderboard(events[0].id); }
        });
    
    $('selectEvent').addEventListener('change', e => {
        // Exit bulk mode on event change
        if (_bulkMode) toggleBulkMode();
        loadLeaderboard(e.target.value);
    });
    
    $('exportLeaderboard').addEventListener('click', () => {
        const id = $('selectEvent').value;
        if (!id) { alert('Select an event first'); return; }
        window.location.href = `/leaderboard-export?event_id=${id}`;
    });
    
    $('bulkEditBtn').addEventListener('click', toggleBulkMode);
    $('openBulkModalBtn').addEventListener('click', openBulkModal);
    $('bonusBtn').addEventListener('click', () => {
        new bootstrap.Modal($('bonusModal')).show();
        if (window.lucide) lucide.createIcons();
    });
    
    /* ═══════════════════════════════════════════════════════════════════
       ADD SCORE MODAL LOGIC
    ═══════════════════════════════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', () => {
    
        /* helpers */
        const showEl = id => $(id)?.classList.remove('d-none');
        const hideEl = id => $(id)?.classList.add('d-none');
        function setAlert(id, msg) {
            const el = $(id);
            if (!el) return;
            if (msg) { el.textContent = msg; el.classList.remove('d-none'); }
            else     { el.textContent = '';  el.classList.add('d-none'); }
        }
    
        let currentEvent = '';
    
        /* Reset chain from a given step */
        function scReset(from) {
            const chain = ['org','group','subgroup','type','team','student','activity','points'];
            const map = {
                org:      { div:'sc_organizationDiv', sel:'sc_organizationSelect' },
                group:    { div:'sc_groupDiv',         sel:'sc_groupSelect' },
                subgroup: { div:'sc_subgroupDiv',      sel:'sc_subgroupSelect' },
                type:     { div:'sc_typeDiv',          sel:'sc_entityType' },
                team:     { div:'sc_teamDiv',          sel:'sc_teamSelect' },
                student:  { div:'sc_studentDiv',       sel:'sc_studentSelect' },
                activity: { div:'sc_activityDiv',      sel:'sc_activitySelect' },
                points:   { div:'sc_pointsDiv',        sel:null },
            };
            chain.slice(chain.indexOf(from)).forEach(k => {
                hideEl(map[k].div);
                const sel = $(map[k].sel);
                if (sel) { if (sel.tagName === 'SELECT') sel.innerHTML = ''; else sel.value = ''; }
            });
            $('sc_submitBtn').disabled = true;
            $('sc_maxHint').textContent = '';
            setAlert('sc_alert', '');
        }
    
        /* EVENT */
        $('sc_eventSelect').addEventListener('change', function () {
            currentEvent = this.value;
            scReset('org');
            if (!this.value) return;
            showEl('sc_organizationDiv');
            loadOpts(`/events/${currentEvent}/organizations`, 'sc_organizationSelect', 'Select Organization', 'id', 'name');
        });
    
        /* ORGANIZATION */
        $('sc_organizationSelect').addEventListener('change', function () {
            scReset('group');
            if (!this.value) return;
            showEl('sc_groupDiv');
            loadOpts(`/organizations/${this.value}/groups`, 'sc_groupSelect', 'Select Group', 'id', 'group_name');
        });
    
        /* GROUP */
        $('sc_groupSelect').addEventListener('change', function () {
            scReset('subgroup');
            if (!this.value) return;
            fetch(`/groups/${this.value}/subgroups`).then(r=>r.json()).then(data => {
                if (data.length) {
                    showEl('sc_subgroupDiv');
                    loadOpts(null, 'sc_subgroupSelect', 'Select Sub Group', 'id', 'name', data);
                } else {
                    showEl('sc_typeDiv');
                }
            });
        });
    
        /* SUBGROUP */
        $('sc_subgroupSelect').addEventListener('change', function () {
            scReset('type');
            if (this.value) showEl('sc_typeDiv');
        });
    
        /* TYPE */
        $('sc_entityType').addEventListener('change', function () {
            scReset('team');
            if (!this.value) return;
            showEl('sc_teamDiv');
    
            const params = new URLSearchParams({
                group_id:     $('sc_groupSelect').value,
                sub_group_id: $('sc_subgroupSelect').value ?? '',
            });
            loadOpts(`/teams?${params}`, 'sc_teamSelect', 'Select Team', 'id', 'name');
        });
    
        /* TEAM */
        $('sc_teamSelect').addEventListener('change', function () {
            scReset('student');
            if (!this.value) return;
    
            if ($('sc_entityType').value === 'student') {
                showEl('sc_studentDiv');
                loadOpts(`/api/teams/${this.value}/students`, 'sc_studentSelect', 'Select Player', 'id', 'name');
            } else {
                loadActivities();
            }
        });
    
        /* STUDENT */
        $('sc_studentSelect').addEventListener('change', function () {
            if (!this.value) return;
            loadActivities();
        });
    
        function loadActivities() {
            showEl('sc_activityDiv');
            hideEl('sc_pointsDiv');
            $('sc_submitBtn').disabled = true;
    
            fetch(`/api/events/${currentEvent}/activities`).then(r=>r.json()).then(acts => {
                const sel = $('sc_activitySelect');
                sel.innerHTML = '<option value="">-- Select Activity --</option>';
                acts.forEach(a => {
                    let name = a.badge_name || a.brain_type || a.esports_type || a.egaming_type || a.name || 'Playground';
                    name = name.replace(/_/g,' ').replace(/\b\w/g, l=>l.toUpperCase());
                    let desc = '';
                    const t  = (a.activity_type||'').toLowerCase();
                    if (t==='brain'      && a.brain_description)       desc = a.brain_description;
                    else if (t==='playground' && a.playground_description) desc = a.playground_description;
                    else if (t==='esports'    && a.esports_description)    desc = a.esports_description;
                    else if (t==='egaming'    && a.egaming_description)    desc = a.egaming_description;
                    const full = desc ? `${name} – ${desc}` : name;
                    sel.innerHTML += `<option value="${a.id}" data-max="${a.max_score}">${full}</option>`;
                });
            });
        }
    
        /* ACTIVITY */
        $('sc_activitySelect').addEventListener('change', function () {
            hideEl('sc_pointsDiv');
            $('sc_submitBtn').disabled = true;
            if (!this.value) return;
    
            const max = this.options[this.selectedIndex]?.dataset.max ?? '';
            $('sc_maxHint').textContent = max ? `(Max: ${max})` : '';
            showEl('sc_pointsDiv');
            $('sc_pointsInput').value = '';
    
            // Load existing
            const params = new URLSearchParams({
                event_id:              currentEvent,
                challenge_activity_id: this.value,
                student_id: $('sc_entityType').value === 'student' ? $('sc_studentSelect').value : '',
                team_id:    $('sc_entityType').value === 'team'    ? $('sc_teamSelect').value    : '',
            });
            fetch(`/scores/existing?${params}`).then(r=>r.json()).then(d => {
                if (d.points !== null) {
                    $('sc_pointsInput').value  = d.points;
                    $('sc_submitBtn').disabled = false;
                }
            });
        });
    
        /* POINTS input */
        $('sc_pointsInput').addEventListener('input', function () {
            $('sc_submitBtn').disabled = !this.value || parseInt(this.value) < 0;
        });
    
        /* FORM SUBMIT */
        $('sc_scoreForm').addEventListener('submit', function (e) {
            e.preventDefault();
            setAlert('sc_alert', '');
            const fd = new FormData();
            fd.append('event_id',              currentEvent);
            fd.append('challenge_activity_id', $('sc_activitySelect').value);
            if ($('sc_entityType').value === 'student') fd.append('student_id', $('sc_studentSelect').value);
            else fd.append('team_id', $('sc_teamSelect').value);
            fd.append('points', $('sc_pointsInput').value);
    
            fetch("{{ route('scores.store') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF(), Accept: 'application/json' },
                body: fd,
            })
            .then(async r => { const d = await r.json(); if (!r.ok) throw new Error(d.message||'Failed'); return d; })
            .then(d => {
                if (d.success) {
                    if (typeof toastr !== 'undefined') toastr.success(d.message);
                    else toast(d.message);
                    bootstrap.Modal.getInstance($('scoreModal'))?.hide();
                    const ev = $('selectEvent').value;
                    if (ev) loadLeaderboard(ev);
                } else {
                    setAlert('sc_alert', d.message || 'Failed');
                }
            })
            .catch(err => setAlert('sc_alert', err.message));
        });
    
        /* ─── Utility: load select options ─── */
        function loadOpts(url, selId, placeholder, valKey, labelKey, preloaded = null) {
            const sel = $(selId);
            sel.innerHTML = `<option value="">-- ${placeholder} --</option>`;
            const process = data => data.forEach(item => {
                sel.innerHTML += `<option value="${item[valKey]}">${item[labelKey]}</option>`;
            });
            if (preloaded) { process(preloaded); return; }
            fetch(url).then(r=>r.json()).then(process).catch(() => {
                sel.innerHTML = `<option value="">Error loading</option>`;
            });
        }
    
        /* ═══════════════════════════════════════
           BONUS MODAL LOGIC
        ═══════════════════════════════════════ */
        let bonusEventId = '';
    
        function bonusReset(from) {
            const chain = ['org','group','subgroup','team','player','points','summary'];
            const map = {
                org:      'bonus_orgDiv',
                group:    'bonus_groupDiv',
                subgroup: 'bonus_subgroupDiv',
                team:     'bonus_teamDiv',
                player:   'bonus_playerDiv',
                points:   'bonus_pointsDiv',
                summary:  'bonus_summaryDiv',
            };
            chain.slice(chain.indexOf(from)).forEach(k => hideEl(map[k]));
            ['bonus_orgSelect','bonus_groupSelect','bonus_subgroupSelect','bonus_teamSelect','bonus_playerSelect'].forEach(id => {
                const el = $(id);
                if (el) el.innerHTML = '';
            });
            $('bonus_pointsInput').value = '';
            $('bonus_submitBtn').disabled = true;
            setAlert('bonus_alert', '');
        }
    
        $('bonus_eventSelect').addEventListener('change', function () {
            bonusEventId = this.value;
            $('bonus_targetType').value = '';
            bonusReset('org');
        });
    
        $('bonus_targetType').addEventListener('change', function () {
            bonusReset('org');
            if (!this.value || !bonusEventId) return;
    
            showEl('bonus_orgDiv');
            loadOpts(`/events/${bonusEventId}/organizations`, 'bonus_orgSelect', 'Select Organization', 'id', 'name');
    
            const type = this.value;
            const orgSel = $('bonus_orgSelect');
            // Reset previous handler
            orgSel.onchange = null;
    
            orgSel.onchange = function() {
                bonusReset('group');
                if (!this.value) return;
                if (type === 'organization') { showEl('bonus_pointsDiv'); updateBonusSummary(); return; }
    
                showEl('bonus_groupDiv');
                loadOpts(`/organizations/${this.value}/groups`, 'bonus_groupSelect', 'Select Group', 'id', 'group_name');
    
                $('bonus_groupSelect').onchange = function() {
                    bonusReset('subgroup');
                    if (!this.value) return;
                    if (type === 'group') { showEl('bonus_pointsDiv'); updateBonusSummary(); return; }
    
                    fetch(`/groups/${this.value}/subgroups`).then(r=>r.json()).then(subs => {
                        if (subs.length) {
                            showEl('bonus_subgroupDiv');
                            loadOpts(null, 'bonus_subgroupSelect', 'Select Sub Group', 'id', 'name', subs);
                            $('bonus_subgroupSelect').onchange = function() {
                                bonusReset('team');
                                if (!this.value) return;
                                if (type === 'subgroup') { showEl('bonus_pointsDiv'); updateBonusSummary(); return; }
                                loadBonusTeams();
                            };
                        } else {
                            loadBonusTeams();
                        }
                    });
                };
            };
    
            function loadBonusTeams() {
                const params = new URLSearchParams({
                    group_id:     $('bonus_groupSelect').value,
                    sub_group_id: $('bonus_subgroupSelect').value ?? '',
                });
                showEl('bonus_teamDiv');
                loadOpts(`/teams?${params}`, 'bonus_teamSelect', 'Select Team', 'id', 'name');
                $('bonus_teamSelect').onchange = function() {
                    bonusReset('player');
                    if (!this.value) return;
                    if (type === 'team') { showEl('bonus_pointsDiv'); updateBonusSummary(); return; }
                    // player
                    showEl('bonus_playerDiv');
                    loadOpts(`/api/teams/${this.value}/students`, 'bonus_playerSelect', 'Select Player', 'id', 'name');
                    $('bonus_playerSelect').onchange = function() {
                        if (!this.value) return;
                        showEl('bonus_pointsDiv');
                        updateBonusSummary();
                    };
                };
            }
        });
    
        $('bonus_pointsInput').addEventListener('input', function () {
            const v = parseInt(this.value);
            $('bonus_submitBtn').disabled = !v || v <= 0;
            if (v > 0) updateBonusSummary();
            else hideEl('bonus_summaryDiv');
        });
    
        function updateBonusSummary() {
            const type    = $('bonus_targetType').value;
            const pts     = parseInt($('bonus_pointsInput').value);
            const orgName = $('bonus_orgSelect').options[$('bonus_orgSelect').selectedIndex]?.text ?? '';
            const grpName = $('bonus_groupSelect').options[$('bonus_groupSelect').selectedIndex]?.text ?? '';
            const subName = $('bonus_subgroupSelect').options[$('bonus_subgroupSelect').selectedIndex]?.text ?? '';
            const tmName  = $('bonus_teamSelect').options[$('bonus_teamSelect').selectedIndex]?.text ?? '';
            const plName  = $('bonus_playerSelect').options[$('bonus_playerSelect').selectedIndex]?.text ?? '';
    
            const scopeMap = {
                organization: `Organization: ${orgName}`,
                group:        `Group: ${grpName}`,
                subgroup:     `Sub Group: ${subName}`,
                team:         `Team: ${tmName}`,
                player:       `Player: ${plName}`,
            };
            const scope = scopeMap[type] ?? '';
            const ptsStr = pts > 0 ? pts : '?';
    
            showEl('bonus_summaryDiv');
            $('bonus_summaryText').textContent = `⚡ +${ptsStr} bonus points → ${scope}`;
        }
    
        $('bonusForm').addEventListener('submit', function(e) {
            e.preventDefault();
            setAlert('bonus_alert', '');
            const type = $('bonus_targetType').value;
            const pts  = parseInt($('bonus_pointsInput').value);
    
            if (!bonusEventId)  { setAlert('bonus_alert', 'Please select an event.'); return; }
            if (!type)          { setAlert('bonus_alert', 'Please select a scope.'); return; }
            if (!pts || pts<=0) { setAlert('bonus_alert', 'Bonus points must be at least 1.'); return; }
    
            const payload = { event_id: bonusEventId, target_type: type, bonus_points: pts };
            if (['organization','group','subgroup','team','player'].includes(type))
                payload.organization_id = $('bonus_orgSelect').value;
            if (['group','subgroup','team','player'].includes(type))
                payload.group_id = $('bonus_groupSelect').value;
            if (['subgroup','team','player'].includes(type))
                payload.sub_group_id = $('bonus_subgroupSelect').value;
            if (['team','player'].includes(type))
                payload.team_id = $('bonus_teamSelect').value;
            if (type === 'player')
                payload.student_id = $('bonus_playerSelect').value;
    
            const btn = $('bonus_submitBtn');
            btn.disabled    = true;
            btn.textContent = 'Saving…';
    
            fetch("{{ route('scores.bonus') }}", {
                method:  'POST',
                headers: { 'Content-Type':'application/json', Accept:'application/json', 'X-CSRF-TOKEN': CSRF() },
                body:    JSON.stringify(payload),
            })
            .then(async r => { const d = await r.json(); if (!r.ok) throw new Error(d.message||'Failed'); return d; })
            .then(d => {
                if (d.success) {
                    if (typeof toastr!=='undefined') toastr.success(d.message);
                    else toast(d.message);
                    bootstrap.Modal.getInstance($('bonusModal'))?.hide();
                    const ev = $('selectEvent').value;
                    if (ev) loadLeaderboard(ev);
                } else {
                    setAlert('bonus_alert', d.message || 'Failed to assign bonus.');
                }
            })
            .catch(err => setAlert('bonus_alert', err.message))
            .finally(() => { btn.disabled = false; btn.textContent = 'Assign Bonus'; });
        });
    
    }); // end DOMContentLoaded
    
    })();
    </script>