<style>
    @import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800;900&family=Barlow:wght@400;500;600&display=swap');

    :root {
        --lb-bg: #0f1318;
        --lb-surface: #181e27;
        --lb-border: #2a3040;
        --lb-header-bg: #1e2535;

        --lb-gold: #f5c518;
        --lb-silver: #b8c4d0;
        --lb-bronze: #cd7f32;

        --cat-science-bg: #c0392b;
        --cat-science-text: #ffffff;
        --cat-tech-bg: #e67e22;
        --cat-tech-text: #ffffff;
        --cat-eng-bg: #27ae60;
        --cat-eng-text: #ffffff;
        --cat-art-bg: #2980b9;
        --cat-art-text: #ffffff;
        --cat-math-bg: #8e44ad;
        --cat-math-text: #ffffff;
        --cat-playground-bg: #7f8c8d;
        --cat-playground-text: #ffffff;
        --cat-egaming-bg: #e91e8c;
        --cat-egaming-text: #ffffff;
        --cat-esports-bg: #00bcd4;
        --cat-esports-text: #000000;
        --cat-mission-bg: #ff6f00;
        --cat-mission-text: #ffffff;
        --cat-other-bg: #34495e;
        --cat-other-text: #c9d1d9;
        --cat-bonus-bg: #b8860b;
        --cat-bonus-text: #ffffff;

        --total-team-bg: #0d2d6e;
        --total-team-text: #79c0ff;
        --total-player-bg: #1a3a1a;
        --total-player-text: #56d364;
        --total-grand-bg: #3d1a00;
        --total-grand-text: #f5c518;

        --font-head: 'Barlow Condensed', sans-serif;
        --font-body: 'Barlow', sans-serif;
        --radius: 6px;

        /* Sticky column left offsets — desktop */
        --fix0-left: 0px;
        --fix1-left: 46px;
        --fix2-left: 96px;
        --fix3-left: 230px;
        --fix4-left: 360px;
        --fix5-left: 460px;
    }

    /* ── WRAPPER & CONTROLS ── */
    #lb-wrapper {
        font-family: var(--font-body);
        background: var(--lb-bg);
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--lb-border);
        margin-top: 12px;
        /* Safari: contain stacking context */
        -webkit-transform: translateZ(0);
        transform: translateZ(0);
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
        font-size: 1rem;
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
        padding: 7px 14px;
        border-radius: var(--radius);
        font-family: var(--font-body);
        font-size: 1rem;
        min-width: 180px;
        max-width: 100%;
        cursor: pointer;
        /* Safari select fix */
        -webkit-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%238b949e' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        padding-right: 30px;
    }

    #selectEvent:focus {
        outline: none;
        border-color: #58a6ff;
    }

    .lb-legend {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .lb-legend-dot {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-family: var(--font-head);
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #c9d1d9;
        white-space: nowrap;
    }

    .lb-legend-dot span {
        width: 14px;
        height: 14px;
        border-radius: 3px;
        display: inline-block;
        flex-shrink: 0;
    }

    /* ── ACTION BUTTONS ── */
    .lb-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-left: auto;
        flex-wrap: wrap;
    }

    .lb-btn {
        border: none;
        padding: 7px 12px;
        border-radius: var(--radius);
        font-family: var(--font-head);
        font-weight: 700;
        font-size: 0.95rem;
        letter-spacing: .04em;
        cursor: pointer;
        transition: background .2s, opacity .2s;
        white-space: nowrap;
        /* Prevent iOS button styling */
        -webkit-appearance: none;
        touch-action: manipulation;
    }

    .lb-btn svg {
        height: 14px;
        width: 14px;
        vertical-align: middle;
    }

    .lb-btn-bulk {
        background: #1a3a6e;
        color: #79c0ff;
        border: 1px solid #30507e;
    }

    .lb-btn-bulk:hover {
        background: #1e4a8a;
    }

    .lb-btn-bulk.active {
        background: #58a6ff;
        color: #000;
        border-color: #58a6ff;
    }

    .lb-btn-bulk-go {
        background: #1a4a3a;
        color: #56d364;
        border: 1px solid #238636;
    }

    .lb-btn-bulk-go:hover {
        background: #1e5a46;
    }

    /* ── BULK BAR ── */
    #bulk-bar {
        display: none;
        align-items: center;
        gap: 10px;
        padding: 8px 16px;
        background: #1a2a4a;
        border-bottom: 1px solid #30507e;
        font-family: var(--font-head);
        font-size: 12px;
        color: #79c0ff;
        flex-wrap: wrap;
    }

    #bulk-bar.visible {
        display: flex;
    }

    /* ── TABLE SCROLL ── */
    #lb-scroll {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 74vh;
        /* Smooth momentum scroll on iOS */
        -webkit-overflow-scrolling: touch;
        /* Safari: promote to own compositing layer */
        -webkit-transform: translateZ(0);
        transform: translateZ(0);
        /* Show scrollbar always on desktop so users know it scrolls */
        scrollbar-width: thin;
        scrollbar-color: #2a3040 transparent;
    }

    #lb-scroll::-webkit-scrollbar {
        height: 6px;
        width: 6px;
    }

    #lb-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    #lb-scroll::-webkit-scrollbar-thumb {
        background: #2a3040;
        border-radius: 3px;
    }

    /* Scroll hint indicator on small screens */
    #lb-scroll-hint {
        display: none;
        font-family: var(--font-head);
        font-size: 11px;
        color: #58a6ff;
        padding: 5px 16px;
        background: var(--lb-surface);
        border-bottom: 1px solid var(--lb-border);
        text-align: center;
        letter-spacing: .05em;
    }

    /* ── TABLE BASE ── */
    #lb-table {
        border-collapse: separate;
        border-spacing: 0;
        width: max-content;
        min-width: 100%;
        font-family: var(--font-body);
        font-size: 13px;
        /* Safari table fix */
        -webkit-border-horizontal-spacing: 0;
        -webkit-border-vertical-spacing: 0;
    }

    /* ── THEAD ROW 1: BANNER ── */
    #lb-table thead tr.row-banner th {
        background: var(--lb-surface);
        color: #8b949e;
        font-family: var(--font-head);
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        padding: 7px 8px;
        border-bottom: 1px solid var(--lb-border);
        border-right: 1px solid var(--lb-border);
        white-space: nowrap;
        text-align: center;
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        z-index: 20;
        /* Safari sticky fix */
        -webkit-transform: translateZ(0);
    }

    #lb-table thead tr.row-banner th.banner-science   { background: var(--cat-science-bg);    color: var(--cat-science-text); }
    #lb-table thead tr.row-banner th.banner-technology { background: var(--cat-tech-bg);       color: var(--cat-tech-text); }
    #lb-table thead tr.row-banner th.banner-engineering{ background: var(--cat-eng-bg);        color: var(--cat-eng-text); }
    #lb-table thead tr.row-banner th.banner-art        { background: var(--cat-art-bg);        color: var(--cat-art-text); }
    #lb-table thead tr.row-banner th.banner-math       { background: var(--cat-math-bg);       color: var(--cat-math-text); }
    #lb-table thead tr.row-banner th.banner-playground { background: var(--cat-playground-bg); color: var(--cat-playground-text); }
    #lb-table thead tr.row-banner th.banner-egaming    { background: var(--cat-egaming-bg);    color: var(--cat-egaming-text); }
    #lb-table thead tr.row-banner th.banner-esports    { background: var(--cat-esports-bg);    color: var(--cat-esports-text); }
    #lb-table thead tr.row-banner th.banner-mission    { background: var(--cat-mission-bg);    color: var(--cat-mission-text); }
    #lb-table thead tr.row-banner th.banner-other      { background: var(--cat-other-bg);      color: var(--cat-other-text); }
    #lb-table thead tr.row-banner th.banner-bonus      { background: var(--cat-bonus-bg);      color: var(--cat-bonus-text); }
    #lb-table thead tr.row-banner th.banner-total-team { background: var(--total-team-bg);     color: var(--total-team-text); }
    #lb-table thead tr.row-banner th.banner-total-player{ background: var(--total-player-bg);  color: var(--total-player-text); }
    #lb-table thead tr.row-banner th.banner-total-grand{ background: var(--total-grand-bg);    color: var(--total-grand-text); }
    #lb-table thead tr.row-banner th.banner-your-points{ background: var(--total-grand-bg);    color: var(--total-grand-text); font-size: 12px; letter-spacing: .08em; }

    /* ── THEAD ROW 2: COL LABELS ── */
    #lb-table thead tr.row-cols th {
        background: var(--lb-header-bg);
        color: #c9d1d9;
        font-family: var(--font-head);
        font-size: 1.1rem;
        font-weight: 800;
        letter-spacing: .07em;
        text-transform: uppercase;
        padding: 6px 8px;
        border-bottom: 2px solid var(--lb-border);
        border-right: 1px solid var(--lb-border);
        white-space: nowrap;
        text-align: center;
        position: -webkit-sticky;
        position: sticky;
        top: 34px;
        z-index: 20;
        -webkit-transform: translateZ(0);
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
    #lb-table thead tr.row-cols th.cat-bonus       { border-top: 3px solid var(--cat-bonus-bg); color: #f5c518; }
    #lb-table thead tr.row-cols th.col-total-team  { border-top: 3px solid var(--total-team-text);   color: var(--total-team-text); }
    #lb-table thead tr.row-cols th.col-total-player{ border-top: 3px solid var(--total-player-text); color: var(--total-player-text); }
    #lb-table thead tr.row-cols th.col-total-grand { border-top: 3px solid var(--total-grand-text);  color: var(--total-grand-text); }
    #lb-table thead tr.row-cols th.col-your-points { border-top: 3px solid var(--total-grand-text);  color: var(--total-grand-text); background: #231200; }

    /* ── STICKY FIXED-LEFT COLUMNS — thead ── */
    #lb-table thead tr.row-banner th.fix-0,
    #lb-table thead tr.row-cols th.fix-0 {
        position: -webkit-sticky;
        position: sticky;
        left: var(--fix0-left);
        z-index: 25;
        background: var(--lb-surface);
        -webkit-transform: translateZ(0);
    }

    #lb-table thead tr.row-banner th.fix-1,
    #lb-table thead tr.row-cols th.fix-1 {
        position: -webkit-sticky;
        position: sticky;
        left: var(--fix1-left);
        z-index: 25;
        background: var(--lb-header-bg);
        -webkit-transform: translateZ(0);
    }

    #lb-table thead tr.row-banner th.fix-2,
    #lb-table thead tr.row-cols th.fix-2 {
        position: -webkit-sticky;
        position: sticky;
        left: var(--fix2-left);
        z-index: 25;
        background: var(--lb-header-bg);
        -webkit-transform: translateZ(0);
    }

    #lb-table thead tr.row-banner th.fix-3,
    #lb-table thead tr.row-cols th.fix-3 {
        position: -webkit-sticky;
        position: sticky;
        left: var(--fix3-left);
        z-index: 25;
        background: var(--lb-header-bg);
        -webkit-transform: translateZ(0);
    }

    #lb-table thead tr.row-banner th.fix-4,
    #lb-table thead tr.row-cols th.fix-4 {
        position: -webkit-sticky;
        position: sticky;
        left: var(--fix4-left);
        z-index: 25;
        background: var(--lb-header-bg);
        -webkit-transform: translateZ(0);
    }

    #lb-table thead tr.row-banner th.fix-5,
    #lb-table thead tr.row-cols th.fix-5 {
        position: -webkit-sticky;
        position: sticky;
        left: var(--fix5-left);
        z-index: 25;
        background: var(--lb-header-bg);
        -webkit-transform: translateZ(0);
    }

    /* ── TBODY GENERAL ── */
    #lb-table tbody td {
        padding: 5px 8px;
        border-bottom: 1px solid #1e2535;
        border-right: 1px solid #1e2535;
        white-space: nowrap;
        vertical-align: middle;
        color: #c9d1d9;
        font-size: 1.2rem;
        text-align: center;
    }

    /* GROUP DIVIDER */
    #lb-table tbody tr.tr-divider td {
        background: linear-gradient(90deg, #1a3a2a 0%, #162a20 100%);
        color: #56d364;
        font-family: var(--font-head);
        font-weight: 900;
        font-size: 1.2rem;
        letter-spacing: .1em;
        text-transform: uppercase;
        padding: 7px 14px;
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
        font-size: 1.1rem;
        color: #fff;
        border-bottom: 1px dashed #1e2535;
    }

    /* ── STICKY BODY LEFT COLUMNS ── */
    #lb-table tbody td.fix-0 {
        position: -webkit-sticky;
        position: sticky;
        left: var(--fix0-left);
        z-index: 5;
        background: inherit;
        -webkit-transform: translateZ(0);
    }

    #lb-table tbody td.fix-1 {
        position: -webkit-sticky;
        position: sticky;
        left: var(--fix1-left);
        z-index: 5;
        background: inherit;
        -webkit-transform: translateZ(0);
    }

    #lb-table tbody td.fix-2 {
        position: -webkit-sticky;
        position: sticky;
        left: var(--fix2-left);
        z-index: 5;
        background: inherit;
        -webkit-transform: translateZ(0);
    }

    #lb-table tbody td.fix-3 {
        position: -webkit-sticky;
        position: sticky;
        left: var(--fix3-left);
        z-index: 5;
        background: inherit;
        -webkit-transform: translateZ(0);
    }

    #lb-table tbody td.fix-4 {
        position: -webkit-sticky;
        position: sticky;
        left: var(--fix4-left);
        z-index: 5;
        background: inherit;
        -webkit-transform: translateZ(0);
    }

    #lb-table tbody td.fix-5 {
        position: -webkit-sticky;
        position: sticky;
        left: var(--fix5-left);
        z-index: 5;
        background: inherit;
        -webkit-transform: translateZ(0);
    }

    /* ── SCORE PILLS ── */
    .score-pill {
        display: inline-block;
        min-width: 40px;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: var(--font-head);
        font-weight: 700;
        font-size: 1.15rem;
        text-align: center;
    }

    .score-science     { background: var(--cat-science-bg);    color: var(--cat-science-text); }
    .score-technology  { background: var(--cat-tech-bg);       color: var(--cat-tech-text); }
    .score-engineering { background: var(--cat-eng-bg);        color: var(--cat-eng-text); }
    .score-art         { background: var(--cat-art-bg);        color: var(--cat-art-text); }
    .score-math        { background: var(--cat-math-bg);       color: var(--cat-math-text); }
    .score-playground  { background: var(--cat-playground-bg); color: var(--cat-playground-text); }
    .score-egaming     { background: var(--cat-egaming-bg);    color: var(--cat-egaming-text); }
    .score-esports     { background: var(--cat-esports-bg);    color: var(--cat-esports-text); }
    .score-mission     { background: var(--cat-mission-bg);    color: var(--cat-mission-text); }
    .score-other       { background: var(--cat-other-bg);      color: var(--cat-other-text); }
    .score-bonus       { background: var(--cat-bonus-bg);      color: var(--cat-bonus-text); }
    .score-zero        { color: #3a4454; font-size: 13px; }

    /* ── TOTAL CELLS ── */
    td.td-total-team {
        background: var(--total-team-bg) !important;
        color: var(--total-team-text) !important;
        font-family: var(--font-head) !important;
        font-weight: 800 !important;
        font-size: 1.3rem !important;
    }

    td.td-total-player {
        background: var(--total-player-bg) !important;
        color: var(--total-player-text) !important;
        font-family: var(--font-head) !important;
        font-weight: 700 !important;
        font-size: 1.3rem !important;
    }

    td.td-total-grand {
        background: var(--total-grand-bg) !important;
        color: var(--total-grand-text) !important;
        font-family: var(--font-head) !important;
        font-weight: 900 !important;
        font-size: 1.4rem !important;
    }

    /* ── BONUS CELL ── */
    td.td-bonus {
        background: #2a1e00 !important;
        color: #f5c518 !important;
        font-family: var(--font-head) !important;
        font-weight: 800 !important;
        font-size: 14px !important;
    }

    td.td-bonus-zero { color: #3a4454 !important; }

    /* ── RANK MEDALS ── */
    .rank-medal {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        font-family: var(--font-head);
        font-weight: 900;
        font-size: 1.3rem;
    }

    .rank-1 { background: var(--lb-gold);   color: #000; }
    .rank-2 { background: var(--lb-silver); color: #000; }
    .rank-3 { background: var(--lb-bronze); color: #fff; }
    .rank-n { background: #21262d; color: #8b949e; font-size: 11px; }

    /* ── CARD BADGES ── */
    .card-badges {
        display: inline-flex;
        gap: 5px;
        align-items: center;
        justify-content: center;
    }

    .card-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
        height: 24px;
        padding: 0 5px;
        border-radius: 4px;
        font-family: var(--font-head);
        font-weight: 900;
        font-size: 1rem;
        color: #fff;
    }

    .card-red     { background: #ef4444; }
    .card-yellow  { background: #facc15; color: #000; }
    .card-orange  { background: #f97316; }
    .card-unknown { background: #6b7280; }

    .card-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 4px;
        padding: 2px 4px;
        color: #fff;
    }

    .card-chip-type {
        font-family: var(--font-head);
        font-weight: 900;
        font-size: .95rem;
        line-height: 1;
    }

    .card-unassign-btn {
        border: 0;
        background: rgba(0, 0, 0, .35);
        color: inherit;
        width: 16px;
        height: 16px;
        line-height: 16px;
        text-align: center;
        border-radius: 50%;
        font-family: var(--font-head);
        font-size: 11px;
        font-weight: 900;
        padding: 0;
        cursor: pointer;
    }

    .card-unassign-btn:hover {
        background: rgba(0, 0, 0, .55);
    }

    td.td-rank-end {
        font-family: var(--font-head);
        font-weight: 900;
    }

    td.td-org {
        color: #f5c518 !important;
        font-family: var(--font-head) !important;
        font-weight: 700 !important;
        font-size: 1.1rem !important;
        letter-spacing: .04em;
    }

    /* ── INLINE EDIT ── */
    .score-edit-cell {
        position: relative;
        cursor: pointer;
        transition: background .15s;
    }

    .score-edit-cell:hover .score-pill,
    .score-edit-cell:hover .score-zero { opacity: 0.6; }

    .score-edit-cell:hover::after {
        content: '✎';
        position: absolute;
        right: 3px;
        top: 50%;
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
        width: 60px;
        background: #0d1117;
        border: 1px solid #58a6ff;
        color: #e6edf3;
        border-radius: 4px;
        padding: 2px 4px;
        font-family: var(--font-head);
        font-size: 1.1rem;
        font-weight: 700;
        text-align: center;
        outline: none;
        /* iOS zoom prevention — min 16px triggers no zoom */
        font-size: 16px;
    }

    .score-edit-wrap {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .score-zero-btn {
        padding: 2px 7px;
        background: rgba(239, 68, 68, .12);
        border: 1px solid rgba(239, 68, 68, .35);
        border-radius: 4px;
        color: #f87171;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .5px;
        cursor: pointer;
        white-space: nowrap;
        line-height: 1.6;
        transition: background .15s, border-color .15s;
    }
    .score-zero-btn:hover {
        background: rgba(239, 68, 68, .25);
        border-color: rgba(239, 68, 68, .6);
        color: #fca5a5;
    }

    /* ── BULK EDIT MODAL ── */
    #bulkEditModal .modal-title {
        font-family: var(--font-head);
        font-weight: 900;
    }

    #bulkEditModal .table { color: #c9d1d9; }

    #bulkEditModal .table th {
        font-size: 11px;
        font-family: "Space Mono", monospace;
        letter-spacing: .06em;
        background: #000;
        color: #fff;
    }

    #bulkEditModal .table td {
        vertical-align: middle;
        color: #000;
    }

    #bulkEditModal .bulk-pts-input {
        width: 80px;
        border: 1px solid #ccc;
        color: #000;
        border-radius: 4px;
        padding: 3px 6px;
        font-family: var(--font-head);
        font-size: 14px;
        font-weight: 700;
        text-align: center;
        /* iOS zoom prevention */
        font-size: 16px;
    }

    #bulkEditModal .bulk-pts-input:focus {
        outline: none;
        border-color: #79c0ff;
    }

    #bulkEditModal .err-hint {
        font-size: 10px;
        color: #f85149;
        margin-top: 2px;
        display: none;
    }

    /* ── TOAST ── */
    #lb-toast {
        position: fixed;
        bottom: 24px;
        right: 16px;
        left: 16px;
        max-width: 320px;
        margin: 0 auto;
        padding: 10px 16px;
        border-radius: 8px;
        font-family: var(--font-head);
        font-size: 1.3rem;
        font-weight: 700;
        z-index: 9999;
        opacity: 0;
        transition: opacity .3s;
        pointer-events: none;
        text-align: center;
    }

    #lb-toast.show { opacity: 1; }
    #lb-toast.ok   { background: #1a3a1a; color: #56d364; border: 1px solid #238636; }
    #lb-toast.err  { background: #3a1a1a; color: #f85149; border: 1px solid #f85149; }
    #lb-toast.warn { background: #3d2a00; color: #f5c518; border: 1px solid #7a5a00; }

    /* ── STATE ROWS ── */
    .lb-state-row td {
        text-align: center;
        padding: 40px 20px;
        color: #484f58;
        font-family: var(--font-head);
        font-size: 15px;
        letter-spacing: .06em;
        background: var(--lb-bg) !important;
    }

    /* ═══════════════════════════════════════════
       RESPONSIVE BREAKPOINTS
    ═══════════════════════════════════════════ */

    /* ── TABLET (≤ 1024px) ── */
    @media (max-width: 1024px) {
        #lb-controls {
            gap: 10px;
            padding: 10px 12px;
        }

        .lb-legend {
            order: 3;
            width: 100%;
            margin-top: 4px;
        }

        .lb-actions {
            margin-left: 0;
        }

        #lb-scroll {
            max-height: 68vh;
        }

        #lb-scroll-hint {
            display: block;
        }
    }

    /* ── MOBILE (≤ 768px) ── */
    @media (max-width: 768px) {
        :root {
            /* Narrower sticky columns on mobile */
            --fix0-left: 0px;
            --fix1-left: 34px;
            --fix2-left: 68px;
            --fix3-left: 165px;
            --fix4-left: 260px;
            --fix5-left: 330px;
        }

        #lb-controls {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
            padding: 10px 12px;
        }

        #lb-controls label { font-size: 0.85rem; }

        #selectEvent {
            min-width: 0;
            width: 100%;
            font-size: 0.95rem;
            padding: 8px 30px 8px 10px;
        }

        .lb-legend {
            order: 3;
            width: 100%;
            gap: 6px;
        }

        .lb-legend-dot {
            font-size: 0.75rem;
        }

        .lb-legend-dot span {
            width: 12px;
            height: 12px;
        }

        .lb-actions {
            margin-left: 0;
            width: 100%;
            gap: 6px;
        }

        .lb-btn {
            padding: 8px 10px;
            font-size: 0.85rem;
            flex: 1;
            text-align: center;
            justify-content: center;
            min-height: 38px; /* Better touch targets */
        }

        #bulk-bar {
            padding: 8px 12px;
            font-size: 11px;
        }

        #lb-scroll {
            max-height: 62vh;
        }

        #lb-scroll-hint {
            display: block;
            font-size: 10px;
        }

        /* Compact table on mobile */
        #lb-table tbody td {
            padding: 4px 6px;
            font-size: 1rem;
        }

        #lb-table tbody tr.tr-student td {
            font-size: 0.95rem;
        }

        #lb-table tbody tr.tr-divider td {
            font-size: 1rem;
            padding: 6px 10px;
        }

        #lb-table thead tr.row-banner th {
            font-size: 0.85rem;
            padding: 5px 6px;
        }

        #lb-table thead tr.row-cols th {
            font-size: 0.85rem;
            padding: 5px 6px;
        }

        /* Smaller fixed columns on mobile */
        #lb-table thead tr.row-banner th.fix-0,
        #lb-table thead tr.row-cols th.fix-0,
        #lb-table tbody td.fix-0 {
            min-width: 34px !important;
        }

        #lb-table thead tr.row-banner th.fix-1,
        #lb-table thead tr.row-cols th.fix-1,
        #lb-table tbody td.fix-1 {
            min-width: 34px !important;
        }

        #lb-table thead tr.row-banner th.fix-2,
        #lb-table thead tr.row-cols th.fix-2,
        #lb-table tbody td.fix-2 {
            min-width: 97px !important;
        }

        #lb-table thead tr.row-banner th.fix-3,
        #lb-table thead tr.row-cols th.fix-3,
        #lb-table tbody td.fix-3 {
            min-width: 95px !important;
        }

        #lb-table thead tr.row-banner th.fix-4,
        #lb-table thead tr.row-cols th.fix-4,
        #lb-table tbody td.fix-4 {
            min-width: 70px !important;
        }

        #lb-table thead tr.row-banner th.fix-5,
        #lb-table thead tr.row-cols th.fix-5,
        #lb-table tbody td.fix-5 {
            min-width: 70px !important;
        }

        .rank-medal {
            width: 24px;
            height: 24px;
            font-size: 1.1rem;
        }

        .score-pill {
            min-width: 34px;
            font-size: 1rem;
            padding: 1px 4px;
        }

        td.td-total-grand { font-size: 1.2rem !important; }
        td.td-total-team  { font-size: 1.1rem !important; }
        td.td-total-player{ font-size: 1.1rem !important; }

        /* Toast full bottom on mobile */
        #lb-toast {
            left: 12px;
            right: 12px;
            bottom: 16px;
            max-width: none;
        }

        /* Bulk edit modal full-screen on mobile */
        #bulkEditModal .modal-dialog {
            margin: 8px;
            max-width: calc(100vw - 16px);
        }

        #bulkEditModal .table-responsive {
            max-height: 55vh;
        }
    }

    /* ── SMALL MOBILE (≤ 480px) ── */
    @media (max-width: 480px) {
        :root {
            --fix0-left: 0px;
            --fix1-left: 28px;
            --fix2-left: 56px;
            --fix3-left: 136px;
            --fix4-left: 216px;
            --fix5-left: 280px;
        }

        #lb-table thead tr.row-banner th.fix-0,
        #lb-table thead tr.row-cols th.fix-0,
        #lb-table tbody td.fix-0 { min-width: 28px !important; }

        #lb-table thead tr.row-banner th.fix-1,
        #lb-table thead tr.row-cols th.fix-1,
        #lb-table tbody td.fix-1 { min-width: 28px !important; }

        #lb-table thead tr.row-banner th.fix-2,
        #lb-table thead tr.row-cols th.fix-2,
        #lb-table tbody td.fix-2 { min-width: 80px !important; }

        #lb-table thead tr.row-banner th.fix-3,
        #lb-table thead tr.row-cols th.fix-3,
        #lb-table tbody td.fix-3 { min-width: 80px !important; }

        #lb-table thead tr.row-banner th.fix-4,
        #lb-table thead tr.row-cols th.fix-4,
        #lb-table tbody td.fix-4 { min-width: 64px !important; }

        #lb-table thead tr.row-banner th.fix-5,
        #lb-table thead tr.row-cols th.fix-5,
        #lb-table tbody td.fix-5 { min-width: 64px !important; }

        #lb-scroll { max-height: 58vh; }

        .lb-btn { font-size: 0.8rem; padding: 7px 8px; }

        /* Section header stacking */
        .section-header { flex-direction: column; align-items: flex-start !important; gap: 8px !important; }
        .section-header .ms-auto { margin-left: 0 !important; }
    }

    /* ── PRINT ── */
    @media print {
        #lb-scroll {
            max-height: none;
            overflow: visible;
        }

        #lb-controls .lb-actions,
        #bulk-bar,
        #lb-scroll-hint { display: none !important; }

        #lb-wrapper { border: none; }
    }
</style><?php /**PATH /home/u236413684/domains/voags.com/public_html/steamiq/resources/views/scores/style.blade.php ENDPATH**/ ?>