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

    /* ── ACTION BUTTONS (lb-btn) ── */
    .lb-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-left: auto;
    }
    .lb-btn {
        border: none;
        padding: 7px 16px;
        border-radius: var(--radius);
        font-family: var(--font-head);
        font-weight: 700;
        font-size: 13px;
        letter-spacing: .04em;
        cursor: pointer;
        transition: background .2s, opacity .2s;
        white-space: nowrap;
    }
    .lb-btn-bulk {
        background: #1a3a6e;
        color: #79c0ff;
        border: 1px solid #30507e;
    }
    .lb-btn-bulk:hover  { background: #1e4a8a; }
    .lb-btn-bulk.active { background: #58a6ff; color: #000; border-color: #58a6ff; }

    .lb-btn-bulk-go {
        background: #1a4a3a;
        color: #56d364;
        border: 1px solid #238636;
    }
    .lb-btn-bulk-go:hover { background: #1e5a46; }

    .lb-btn-export {
        background: #238636;
        color: #fff;
    }
    .lb-btn-export:hover { background: #2ea043; }

    /* ── BULK BAR ── */
    #bulk-bar {
        display: none;
        align-items: center;
        gap: 12px;
        padding: 9px 20px;
        background: #1a2a4a;
        border-bottom: 1px solid #30507e;
        font-family: var(--font-head);
        font-size: 13px;
        color: #79c0ff;
    }
    #bulk-bar.visible { display: flex; }

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
    #lb-table thead tr.row-cols   th.fix-0 { position: sticky; left: 0;     z-index: 25; background: var(--lb-surface); }
    #lb-table thead tr.row-banner th.fix-1,
    #lb-table thead tr.row-cols   th.fix-1 { position: sticky; left: 46px;  z-index: 25; background: var(--lb-header-bg); }
    #lb-table thead tr.row-banner th.fix-2,
    #lb-table thead tr.row-cols   th.fix-2 { position: sticky; left: 96px;  z-index: 25; background: var(--lb-header-bg); }
    #lb-table thead tr.row-banner th.fix-3,
    #lb-table thead tr.row-cols   th.fix-3 { position: sticky; left: 230px; z-index: 25; background: var(--lb-header-bg); }
    #lb-table thead tr.row-banner th.fix-4,
    #lb-table thead tr.row-cols   th.fix-4 { position: sticky; left: 360px; z-index: 25; background: var(--lb-header-bg); }

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
    .score-edit-cell { position: relative; cursor: pointer; transition: background .15s; }
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
    .score-edit-cell.no-edit { cursor: not-allowed; }
    .score-edit-cell.no-edit:hover::after { display: none; }

    /* ── BULK EDIT MODE ── */
    .bulk-mode .score-edit-cell { cursor: crosshair; }
    .bulk-mode .score-edit-cell:hover { background: rgba(88,166,255,.08) !important; }
    .score-edit-cell.bulk-selected {
        background: rgba(88,166,255,.18) !important;
        outline: 2px solid #58a6ff;
        outline-offset: -2px;
    }
    .score-edit-cell.bulk-selected::after { display: none; }

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

    /* ── BULK EDIT MODAL ── */
    #bulkEditModal .modal-content { background: #0f1318; border: 1px solid #2a3040; }
    #bulkEditModal .modal-header  { background: #1a2a4a; border-bottom: 1px solid #30507e; }
    #bulkEditModal .modal-title   { color: #79c0ff; font-family: var(--font-head); font-weight: 900; }
    #bulkEditModal .table         { color: #c9d1d9; }
    #bulkEditModal .table th      { background: #1e2535; color: #8b949e; font-size: 11px; letter-spacing: .06em; }
    #bulkEditModal .table td      { background: #151b27; border-color: #1e2535; vertical-align: middle; }
    #bulkEditModal .bulk-pts-input {
        width: 80px;
        background: #0d1117;
        border: 1px solid #58a6ff;
        color: #e6edf3;
        border-radius: 4px;
        padding: 3px 6px;
        font-family: var(--font-head);
        font-size: 14px;
        font-weight: 700;
        text-align: center;
    }
    #bulkEditModal .bulk-pts-input:focus { outline: none; border-color: #79c0ff; }
    #bulkEditModal .err-hint { font-size: 10px; color: #f85149; margin-top: 2px; display: none; }

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
    #lb-toast.warn { background: #3d2a00; color: #f5c518; border: 1px solid #7a5a00; }

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
        .lb-actions  { margin-left: 0; }
    }
</style>

{{-- Bulk Edit Modal --}}
<div class="modal fade" id="bulkEditModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i data-lucide="edit-3" style="width:16px;height:16px;vertical-align:-2px;margin-right:6px;"></i>
                    Bulk Edit Scores
                    <span id="bulkEditCount" class="badge ms-2" style="background:#58a6ff;color:#000;font-size:12px;"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:0;">
                <div class="table-responsive" style="max-height:400px;overflow-y:auto;">
                    <table class="table table-sm mb-0" id="bulkEditTable">
                        <thead>
                            <tr>
                                <th style="width:30px;"><input type="checkbox" id="bulkSelectAll" title="Select all"></th>
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
            <div class="modal-footer" style="background:#0f1318;border-top:1px solid #2a3040;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="bulkSaveConfirmBtn" class="btn btn-primary fw-bold">
                    <i data-lucide="save" style="width:14px;height:14px;vertical-align:-2px;margin-right:4px;"></i>
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
(function () {
    'use strict';

    const IS_ADMIN = window.USER_ROLE === 1;
    const $id = id => document.getElementById(id);

    /* ═══════════════════════════════════════════════════════════
       STATE
    ═══════════════════════════════════════════════════════════ */
    let _currentData  = null;   // last fetched {categories, rows}
    let _bulkMode     = false;
    let _bulkSelected = [];     // [{td, row, cat, pts, slug, maxScore, activityId}, …]

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

    function rankBadge(r) {
        if (!r && r !== 0) return '';
        if (r === 1) return `<span class="rank-medal rank-1">1</span>`;
        if (r === 2) return `<span class="rank-medal rank-2">2</span>`;
        if (r === 3) return `<span class="rank-medal rank-3">3</span>`;
        return `<span class="rank-medal rank-n">${r}</span>`;
    }

    function csrfToken() {
        const m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    function slugToLabel(slug) {
        const map = {
            science: 'Science', technology: 'Technology', engineering: 'Engineering',
            art: 'Art', math: 'Math', playground: 'Playground',
            egaming: 'E-Gaming', esports: 'ESports', mission: 'Missions', other: 'Other',
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
       FETCH LEADERBOARD DATA
    ═══════════════════════════════════════════════════════════ */
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
            _currentData = data;

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
        const diff   = newPts - oldPts;
        if (dataRow.scores) dataRow.scores[cat] = newPts;

        if (row.type === 'student') {
            dataRow.total_points = (dataRow.total_points ?? 0) + diff;
        } else {
            dataRow.team_points  = (dataRow.team_points  ?? 0) + diff;
            dataRow.total_points = (dataRow.total_points ?? 0) + diff;
            dataRow.grand_total  = (dataRow.grand_total  ?? 0) + diff;
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

            // Update parent team's player_points & grand_total
            const teamDataRow = _currentData.rows.find(r => r.type === 'team' && r.team_name === dataRow.team_name);
            if (teamDataRow) {
                teamDataRow.player_points = (teamDataRow.player_points ?? 0) + diff;
                teamDataRow.grand_total   = (teamDataRow.grand_total   ?? 0) + diff;
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
        const sorted   = [...teamRows].sort((a, b) => (b.grand_total ?? 0) - (a.grand_total ?? 0));

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
            const td0     = tr.querySelector('.fix-0');
            const tdRankE = tr.querySelector('.td-rank-end');
            const badge   = assignedRank ? rankBadge(assignedRank) : '';
            if (td0)     td0.innerHTML     = badge;
            if (tdRankE) tdRankE.innerHTML = badge;
        });
    }

    /* ═══════════════════════════════════════════════════════════
       BUILD TABLE
       Uses category id/max_score from the server response directly
       so no async race condition with activity meta.
    ═══════════════════════════════════════════════════════════ */
    function buildTable(data, thead, tbody) {
        const cats      = data.categories || [];
        const rows      = data.rows;

        // Pull arrays from server response (id & max_score are included)
        const catNames     = cats.map(c => c.name);
        const catSlugs     = cats.map(c => c.type);
        const catIds       = cats.map(c => c.id);
        const catMaxScores = cats.map(c => c.max_score || 9999);

        /* Banner groups — merge consecutive same-slug columns */
        const bannerGroups = [];
        catSlugs.forEach((slug, i) => {
            const last = bannerGroups[bannerGroups.length - 1];
            if (last && last.slug === slug) {
                last.span++;
            } else {
                bannerGroups.push({ slug, label: slugToLabel(slug), span: 1 });
            }
        });

        /* ── ROW 1: Banner ── */
        const r1 = document.createElement('tr');
        r1.className = 'row-banner';
        [
            { label: '#',        cls: 'fix-0', w: 46  },
            { label: 'NO',       cls: 'fix-1', w: 50  },
            { label: 'TEAM',     cls: 'fix-2', w: 134 },
            { label: 'MEMBERS',  cls: 'fix-3', w: 130 },
            { label: 'DIVISION', cls: 'fix-4', w: 100 },
        ].forEach(m => {
            const th = document.createElement('th');
            th.className = m.cls; th.textContent = m.label; th.style.minWidth = m.w + 'px';
            r1.appendChild(th);
        });
        bannerGroups.forEach(g => {
            const th = document.createElement('th');
            th.colSpan     = g.span;
            th.textContent = g.label.toUpperCase();
            th.className   = 'banner-' + g.slug;
            th.style.minWidth = (g.span * 120) + 'px';
            r1.appendChild(th);
        });
        [
            { label: 'TEAM POINTS',   cls: 'banner-total-team',   w: 100 },
            { label: 'PLAYER POINTS', cls: 'banner-total-player', w: 100 },
            { label: 'GRAND TOTAL',   cls: 'banner-total-grand',  w: 100 },
            { label: 'RANK',          cls: '',                     w: 70  },
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
            { label: 'RANK',      cls: 'fix-0', w: 46  },
            { label: 'TEAM NO',   cls: 'fix-1', w: 50  },
            { label: 'TEAM NAME', cls: 'fix-2', w: 134 },
            { label: 'MEMBERS',   cls: 'fix-3', w: 130 },
            { label: 'DIVISION',  cls: 'fix-4', w: 100 },
        ].forEach(m => {
            const th = document.createElement('th');
            th.className = m.cls; th.textContent = m.label; th.style.minWidth = m.w + 'px';
            r2.appendChild(th);
        });
        catNames.forEach((name, i) => {
            const th = document.createElement('th');
            th.textContent  = name;
            th.className    = 'cat-' + catSlugs[i];
            th.style.minWidth = '140px';
            th.title        = name;
            r2.appendChild(th);
        });
        [
            { label: 'TEAM PTS',    cls: 'col-total-team',   w: 100 },
            { label: 'PLAYER PTS',  cls: 'col-total-player', w: 100 },
            { label: 'GRAND TOTAL', cls: 'col-total-grand',  w: 100 },
            { label: 'RANK',        cls: '',                  w: 70  },
            { label: 'ORGANIZATION',cls: '',                  w: 120 },
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
        const teamSeq    = {};

        rows.forEach(row => {
            const isTeam   = row.type === 'team';
            const groupKey = row.group || 'Ungrouped';

            /* Group divider row */
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
            tr.className       = isTeam ? 'tr-team' : 'tr-student';
            tr.dataset.rowType = row.type;
            tr.dataset.rowId   = row.id;

            /* col 0: rank medal */
            const td0 = document.createElement('td');
            td0.className   = 'fix-0';
            td0.style.textAlign = 'center';
            if (isTeam && row.rank) td0.innerHTML = rankBadge(row.rank);
            tr.appendChild(td0);

            /* col 1: sequence number */
            const td1 = document.createElement('td');
            td1.className   = 'fix-1';
            td1.style.textAlign = 'center';
            if (isTeam) {
                if (!teamSeq[groupKey]) teamSeq[groupKey] = 0;
                teamSeq[groupKey]++;
                td1.textContent = teamSeq[groupKey];
            }
            tr.appendChild(td1);

            /* col 2: team name */
            const td2 = document.createElement('td');
            td2.className       = 'fix-2';
            td2.style.textAlign = 'left';
            td2.style.paddingLeft = isTeam ? '10px' : '22px';
            td2.textContent     = isTeam ? (row.team_name || '—') : '';
            if (isTeam) td2.style.fontWeight = '700';
            tr.appendChild(td2);

            /* col 3: member name */
            const td3 = document.createElement('td');
            td3.className   = 'fix-3';
            td3.style.textAlign = 'left';
            td3.textContent = isTeam ? '' : (row.student_name || '—');
            tr.appendChild(td3);

            /* col 4: division */
            const td4 = document.createElement('td');
            td4.className   = 'fix-4';
            td4.textContent = row.division || '—';
            tr.appendChild(td4);

            /* ── Activity score columns ── */
            catNames.forEach((cat, i) => {
                const td   = document.createElement('td');
                const pts  = row.scores?.[cat] ?? 0;
                const slug = catSlugs[i];
                const actId   = catIds[i];
                const maxSc   = catMaxScores[i];

                td.innerHTML = scorePill(pts, slug);

                // Store everything needed on the element so callbacks don't
                // need closure variables that can go stale.
                td.dataset.cat        = cat;
                td.dataset.pts        = pts;
                td.dataset.slug       = slug;
                td.dataset.activityId = actId;
                td.dataset.maxScore   = maxSc;

                if (IS_ADMIN) {
                    td.className = 'score-edit-cell';
                    td.addEventListener('click', function () {
                        if (_bulkMode) {
                            toggleBulkSelect(td, row);
                        } else {
                            openScoreEditor(td, row);
                        }
                    });
                } else {
                    td.className = 'score-edit-cell no-edit';
                    td.title = 'Only admin can edit scores';
                }

                tr.appendChild(td);
            });

            /* team points */
            const tdTP = document.createElement('td');
            tdTP.className  = 'td-total-team';
            if (isTeam) tdTP.textContent = Number(row.team_points ?? 0).toLocaleString();
            tr.appendChild(tdTP);

            /* player points */
            const tdPP = document.createElement('td');
            tdPP.className  = 'td-total-player';
            tdPP.textContent = isTeam
                ? Number(row.player_points ?? 0).toLocaleString()
                : Number(row.total_points  ?? 0).toLocaleString();
            tr.appendChild(tdPP);

            /* grand total */
            const tdGT = document.createElement('td');
            tdGT.className  = 'td-total-grand';
            if (isTeam) tdGT.textContent = Number(row.grand_total ?? 0).toLocaleString();
            tr.appendChild(tdGT);

            /* rank end */
            const tdRankEnd = document.createElement('td');
            tdRankEnd.className = 'td-rank-end';
            if (isTeam && row.rank) tdRankEnd.innerHTML = rankBadge(row.rank);
            tr.appendChild(tdRankEnd);

            /* organization */
            const tdOrg = document.createElement('td');
            tdOrg.className  = 'td-org';
            tdOrg.textContent = isTeam ? (row.organization || '—') : '';
            tr.appendChild(tdOrg);

            frag.appendChild(tr);
        });

        tbody.innerHTML = '';
        tbody.appendChild(frag);
    }

    /* ═══════════════════════════════════════════════════════════
       INLINE SCORE EDITOR
    ═══════════════════════════════════════════════════════════ */
    function openScoreEditor(td, row) {
        // Prevent opening a second input while one is already open
        if (td.querySelector('.score-edit-input')) return;

        const cat      = td.dataset.cat;
        const slug     = td.dataset.slug;
        const actId    = parseInt(td.dataset.activityId);
        const maxScore = parseInt(td.dataset.maxScore) || 9999;
        const oldPts   = parseInt(td.dataset.pts) || 0;

        const input = document.createElement('input');
        input.type      = 'number';
        input.min       = '0';
        input.max       = maxScore;
        input.className = 'score-edit-input';
        input.value     = oldPts;
        input.title     = `Max: ${maxScore}`;

        td.innerHTML = '';
        td.appendChild(input);
        input.focus();
        input.select();

        let committed = false;

        function commit() {
            if (committed) return;
            const newVal = parseInt(input.value, 10);

            if (isNaN(newVal) || newVal < 0) {
                cancel();
                return;
            }
            if (newVal > maxScore) {
                toast(`Max score is ${maxScore}`, 'warn');
                cancel();
                return;
            }

            committed = true;
            // Optimistic update
            td.innerHTML   = scorePill(newVal, slug);
            td.dataset.pts = newVal;
            updateRowTotalsInDOM(row, cat, newVal);

            saveScore(td, row, cat, newVal, slug, oldPts, actId, maxScore);
        }

        function cancel() {
            if (committed) return;
            td.innerHTML   = scorePill(oldPts, slug);
            td.dataset.pts = oldPts;
        }

        input.addEventListener('keydown', e => {
            if (e.key === 'Enter')  { e.preventDefault(); commit(); }
            if (e.key === 'Escape') cancel();
        });
        input.addEventListener('blur', () => { if (!committed) commit(); });
    }

    /* ── POST score to server ── */
    async function saveScore(td, row, cat, newPts, slug, oldPts, activityId, maxScore) {
        const eventId = $id('selectEvent').value;

        const payload = {
            event_id:              eventId,
            challenge_activity_id: activityId,
            points:                newPts,
        };
        if (row.type === 'student') payload.student_id = row.id;
        else                        payload.team_id    = row.id;

        try {
            const res = await fetch('/scores/update-by-id', {
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
                td.dataset.pts = newPts;
            } else {
                throw new Error(json.message || 'Save failed');
            }
        } catch (err) {
            console.error(err);
            toast('Error: ' + err.message, 'err');
            // Rollback optimistic update
            td.innerHTML   = scorePill(oldPts, slug);
            td.dataset.pts = oldPts;
            updateRowTotalsInDOM(row, cat, oldPts);
        }
    }

    /* ═══════════════════════════════════════════════════════════
       BULK EDIT MODE
    ═══════════════════════════════════════════════════════════ */
    function toggleBulkMode() {
        _bulkMode     = !_bulkMode;
        _bulkSelected = [];

        const btn    = $id('bulkEditBtn');
        const bulkBar = $id('bulk-bar');
        const table  = $id('lb-table');

        if (_bulkMode) {
            btn.classList.add('active');
            btn.textContent = '✕ Exit Bulk';
            bulkBar.classList.add('visible');
            table.classList.add('bulk-mode');
        } else {
            btn.classList.remove('active');
            btn.textContent = '⊞ Bulk Edit';
            bulkBar.classList.remove('visible');
            table.classList.remove('bulk-mode');
            document.querySelectorAll('.score-edit-cell.bulk-selected')
                .forEach(td => td.classList.remove('bulk-selected'));
        }
        updateBulkBar();
    }

    function toggleBulkSelect(td, row) {
        const idx = _bulkSelected.findIndex(s => s.td === td);
        if (idx > -1) {
            _bulkSelected.splice(idx, 1);
            td.classList.remove('bulk-selected');
        } else {
            _bulkSelected.push({
                td,
                row,
                cat:        td.dataset.cat,
                pts:        parseInt(td.dataset.pts) || 0,
                slug:       td.dataset.slug,
                maxScore:   parseInt(td.dataset.maxScore)   || 9999,
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
            const entityName = sel.row.type === 'team'
                ? (sel.row.team_name    || 'Team')
                : (sel.row.student_name || 'Player');

            const teamColor   = sel.row.type === 'team' ? '#1a3a6e' : '#1a3a1a';
            const textColor   = sel.row.type === 'team' ? '#79c0ff' : '#56d364';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="checkbox" class="bulk-row-check" data-idx="${i}" checked></td>
                <td style="text-align:left;">
                    <span class="badge" style="background:${teamColor};color:${textColor};font-size:10px;">
                        ${sel.row.type.toUpperCase()}
                    </span>
                    <span style="margin-left:6px;">${esc(entityName)}</span>
                </td>
                <td style="text-align:left;font-size:11px;color:#8b949e;">${esc(sel.cat)}</td>
                <td><span class="score-pill score-${sel.slug}">${sel.pts}</span></td>
                <td>
                    <input type="number" min="0" max="${sel.maxScore}" value="${sel.pts}"
                           class="bulk-pts-input" data-idx="${i}" data-max="${sel.maxScore}">
                    <div class="err-hint" id="bulk-err-${i}">Exceeds max (${sel.maxScore})</div>
                </td>
                <td style="color:#8b949e;font-size:11px;">${sel.maxScore}</td>
            `;
            tbodyEl.appendChild(tr);
        });

        $id('bulkEditCount').textContent = _bulkSelected.length + ' cells';

        /* Select-all checkbox */
        const selectAll = $id('bulkSelectAll');
        selectAll.checked  = true;
        selectAll.onchange = function () {
            document.querySelectorAll('.bulk-row-check').forEach(cb => {
                cb.checked = this.checked;
            });
        };

        /* Validate on input */
        tbodyEl.querySelectorAll('.bulk-pts-input').forEach(input => {
            input.addEventListener('input', function () {
                const max   = parseInt(this.dataset.max);
                const val   = parseInt(this.value);
                const errEl = $id(`bulk-err-${this.dataset.idx}`);
                if (val > max) {
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
    $id('bulkSaveConfirmBtn').addEventListener('click', async function () {
        const btn = this;
        btn.disabled    = true;
        btn.textContent = 'Saving…';

        const eventId = $id('selectEvent').value;
        const inputs  = document.querySelectorAll('#bulkEditTbody .bulk-pts-input');
        const checks  = document.querySelectorAll('#bulkEditTbody .bulk-row-check');

        let hasError = false;
        const tasks  = [];

        inputs.forEach((input, i) => {
            if (!checks[i]?.checked) return;

            const idx    = parseInt(input.dataset.idx);
            const max    = parseInt(input.dataset.max);
            const newPts = parseInt(input.value);

            if (isNaN(newPts) || newPts < 0 || newPts > max) {
                hasError = true;
                input.style.borderColor = '#f85149';
                return;
            }
            tasks.push({ sel: _bulkSelected[idx], newPts });
        });

        if (hasError) {
            toast('Fix validation errors before saving', 'err');
            btn.disabled    = false;
            btn.textContent = 'Save All Changes';
            return;
        }

        let successCount = 0;
        let failCount    = 0;

        await Promise.all(tasks.map(async ({ sel, newPts }) => {
            const payload = {
                event_id:              eventId,
                challenge_activity_id: sel.activityId,
                points:                newPts,
            };
            if (sel.row.type === 'student') payload.student_id = sel.row.id;
            else                            payload.team_id    = sel.row.id;

            try {
                const res = await fetch('/scores/update-by-id', {
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
                    sel.td.innerHTML   = scorePill(newPts, sel.slug);
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

        if (failCount === 0) {
            toast(`✓ ${successCount} score(s) saved`, 'ok');
        } else {
            toast(`${successCount} saved, ${failCount} failed`, 'err');
        }

        btn.disabled    = false;
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

    $id('selectEvent').addEventListener('change', function () {
        // Reset bulk mode on event change
        if (_bulkMode) toggleBulkMode();
        _bulkSelected = [];
        updateBulkBar();
        fetchLeaderboard(this.value);
    });

    $id('exportLeaderboard').addEventListener('click', () => {
        const id = $id('selectEvent').value;
        if (!id) { alert('Please select an event first!'); return; }
        window.location.href = `/leaderboard-export?event_id=${id}`;
    });

    $id('bulkEditBtn').addEventListener('click', toggleBulkMode);
    $id('openBulkModalBtn').addEventListener('click', openBulkEditModal);



})();
</script>