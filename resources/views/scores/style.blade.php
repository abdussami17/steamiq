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
        font-size: 1rem;
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
        font-size: 1.2rem;
        min-width: 240px;
        cursor: pointer;
    }

    #selectEvent:focus {
        outline: none;
        border-color: #58a6ff;
    }

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
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #c9d1d9;
    }

    .lb-legend-dot span {
        width: 20px;
        height: 20px;
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
        font-size: 1rem;
        letter-spacing: .04em;
        cursor: pointer;
        transition: background .2s, opacity .2s;
        white-space: nowrap;
    }

    .lb-btn svg {
        height: 14px;
        width: 14px;
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
        gap: 12px;
        padding: 9px 20px;
        background: #1a2a4a;
        border-bottom: 1px solid #30507e;
        font-family: var(--font-head);
        font-size: 13px;
        color: #79c0ff;
    }

    #bulk-bar.visible {
        display: flex;
    }

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
        font-size: 1.2rem;
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

    #lb-table thead tr.row-banner th.banner-science {
        background: var(--cat-science-bg);
        color: var(--cat-science-text);
    }

    #lb-table thead tr.row-banner th.banner-technology {
        background: var(--cat-tech-bg);
        color: var(--cat-tech-text);
    }

    #lb-table thead tr.row-banner th.banner-engineering {
        background: var(--cat-eng-bg);
        color: var(--cat-eng-text);
    }

    #lb-table thead tr.row-banner th.banner-art {
        background: var(--cat-art-bg);
        color: var(--cat-art-text);
    }

    #lb-table thead tr.row-banner th.banner-math {
        background: var(--cat-math-bg);
        color: var(--cat-math-text);
    }

    #lb-table thead tr.row-banner th.banner-playground {
        background: var(--cat-playground-bg);
        color: var(--cat-playground-text);
    }

    #lb-table thead tr.row-banner th.banner-egaming {
        background: var(--cat-egaming-bg);
        color: var(--cat-egaming-text);
    }

    #lb-table thead tr.row-banner th.banner-esports {
        background: var(--cat-esports-bg);
        color: var(--cat-esports-text);
    }

    #lb-table thead tr.row-banner th.banner-mission {
        background: var(--cat-mission-bg);
        color: var(--cat-mission-text);
    }

    #lb-table thead tr.row-banner th.banner-other {
        background: var(--cat-other-bg);
        color: var(--cat-other-text);
    }

    #lb-table thead tr.row-banner th.banner-bonus {
        background: var(--cat-bonus-bg);
        color: var(--cat-bonus-text);
    }

    #lb-table thead tr.row-banner th.banner-total-team {
        background: var(--total-team-bg);
        color: var(--total-team-text);
    }

    #lb-table thead tr.row-banner th.banner-total-player {
        background: var(--total-player-bg);
        color: var(--total-player-text);
    }

    #lb-table thead tr.row-banner th.banner-total-grand {
        background: var(--total-grand-bg);
        color: var(--total-grand-text);
    }

    #lb-table thead tr.row-banner th.banner-your-points {
        background: var(--total-grand-bg);
        color: var(--total-grand-text);
        font-size: 13px;
        letter-spacing: .08em;
    }

    /* ── THEAD ROW 2: COL LABELS ── */
    #lb-table thead tr.row-cols th {
        background: var(--lb-header-bg);
        color: #c9d1d9;
        font-family: var(--font-head);
        font-size: 1.3rem;
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

    #lb-table thead tr.row-cols th.cat-science {
        border-top: 3px solid var(--cat-science-bg);
    }

    #lb-table thead tr.row-cols th.cat-technology {
        border-top: 3px solid var(--cat-tech-bg);
    }

    #lb-table thead tr.row-cols th.cat-engineering {
        border-top: 3px solid var(--cat-eng-bg);
    }

    #lb-table thead tr.row-cols th.cat-art {
        border-top: 3px solid var(--cat-art-bg);
    }

    #lb-table thead tr.row-cols th.cat-math {
        border-top: 3px solid var(--cat-math-bg);
    }

    #lb-table thead tr.row-cols th.cat-playground {
        border-top: 3px solid var(--cat-playground-bg);
    }

    #lb-table thead tr.row-cols th.cat-egaming {
        border-top: 3px solid var(--cat-egaming-bg);
    }

    #lb-table thead tr.row-cols th.cat-esports {
        border-top: 3px solid var(--cat-esports-bg);
    }

    #lb-table thead tr.row-cols th.cat-mission {
        border-top: 3px solid var(--cat-mission-bg);
    }

    #lb-table thead tr.row-cols th.cat-other {
        border-top: 3px solid var(--cat-other-bg);
    }

    #lb-table thead tr.row-cols th.cat-bonus {
        border-top: 3px solid var(--cat-bonus-bg);
        color: #f5c518;
    }

    #lb-table thead tr.row-cols th.col-total-team {
        border-top: 3px solid var(--total-team-text);
        color: var(--total-team-text);
    }

    #lb-table thead tr.row-cols th.col-total-player {
        border-top: 3px solid var(--total-player-text);
        color: var(--total-player-text);
    }

    #lb-table thead tr.row-cols th.col-total-grand {
        border-top: 3px solid var(--total-grand-text);
        color: var(--total-grand-text);
    }

    #lb-table thead tr.row-cols th.col-your-points {
        border-top: 3px solid var(--total-grand-text);
        color: var(--total-grand-text);
        background: #231200;
    }

    /* Sticky fixed-left columns */
    #lb-table thead tr.row-banner th.fix-0,
    #lb-table thead tr.row-cols th.fix-0 {
        position: sticky;
        left: 0;
        z-index: 25;
        background: var(--lb-surface);
    }

    #lb-table thead tr.row-banner th.fix-1,
    #lb-table thead tr.row-cols th.fix-1 {
        position: sticky;
        left: 46px;
        z-index: 25;
        background: var(--lb-header-bg);
    }

    #lb-table thead tr.row-banner th.fix-2,
    #lb-table thead tr.row-cols th.fix-2 {
        position: sticky;
        left: 96px;
        z-index: 25;
        background: var(--lb-header-bg);
    }

    #lb-table thead tr.row-banner th.fix-3,
    #lb-table thead tr.row-cols th.fix-3 {
        position: sticky;
        left: 230px;
        z-index: 25;
        background: var(--lb-header-bg);
    }

    #lb-table thead tr.row-banner th.fix-4,
    #lb-table thead tr.row-cols th.fix-4 {
        position: sticky;
        left: 360px;
        z-index: 25;
        background: var(--lb-header-bg);
    }

    #lb-table thead tr.row-banner th.fix-5,
    #lb-table thead tr.row-cols th.fix-5 {
        position: sticky;
        left: 460px;
        z-index: 25;
        background: var(--lb-header-bg);
    }

    /* ── TBODY GENERAL ── */
    #lb-table tbody td {
        padding: 5px 10px;
        border-bottom: 1px solid #1e2535;
        border-right: 1px solid #1e2535;
        white-space: nowrap;
        vertical-align: middle;
        color: #c9d1d9;
        font-size: 1.3rem;
        text-align: center;
    }

    /* GROUP DIVIDER */
    #lb-table tbody tr.tr-divider td {
        background: linear-gradient(90deg, #1a3a2a 0%, #162a20 100%);
        color: #56d364;
        font-family: var(--font-head);
        font-weight: 900;
        font-size: 1.3rem;
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
        font-size: 1.2rem;
        /* color: #8b949e; */
        color: #fff;
        border-bottom: 1px dashed #1e2535;
    }

    /* Sticky body left columns */
    #lb-table tbody td.fix-0 {
        position: sticky;
        left: 0;
        z-index: 5;
        background: inherit;
    }

    #lb-table tbody td.fix-1 {
        position: sticky;
        left: 46px;
        z-index: 5;
        background: inherit;
    }

    #lb-table tbody td.fix-2 {
        position: sticky;
        left: 96px;
        z-index: 5;
        background: inherit;
    }

    #lb-table tbody td.fix-3 {
        position: sticky;
        left: 230px;
        z-index: 5;
        background: inherit;
    }

    #lb-table tbody td.fix-4 {
        position: sticky;
        left: 360px;
        z-index: 5;
        background: inherit;
    }

    #lb-table tbody td.fix-5 {
        position: sticky;
        left: 460px;
        z-index: 5;
        background: inherit;
    }

    /* ── SCORE PILLS ── */
    .score-pill {
        display: inline-block;
        min-width: 44px;
        padding: 2px 7px;
        border-radius: 4px;
        font-family: var(--font-head);
        font-weight: 700;
        font-size: 1.250rem;
        text-align: center;
    }

    .score-science {
        background: var(--cat-science-bg);
        color: var(--cat-science-text);
    }

    .score-technology {
        background: var(--cat-tech-bg);
        color: var(--cat-tech-text);
    }

    .score-engineering {
        background: var(--cat-eng-bg);
        color: var(--cat-eng-text);
    }

    .score-art {
        background: var(--cat-art-bg);
        color: var(--cat-art-text);
    }

    .score-math {
        background: var(--cat-math-bg);
        color: var(--cat-math-text);
    }

    .score-playground {
        background: var(--cat-playground-bg);
        color: var(--cat-playground-text);
    }

    .score-egaming {
        background: var(--cat-egaming-bg);
        color: var(--cat-egaming-text);
    }

    .score-esports {
        background: var(--cat-esports-bg);
        color: var(--cat-esports-text);
    }

    .score-mission {
        background: var(--cat-mission-bg);
        color: var(--cat-mission-text);
    }

    .score-other {
        background: var(--cat-other-bg);
        color: var(--cat-other-text);
    }

    .score-bonus {
        background: var(--cat-bonus-bg);
        color: var(--cat-bonus-text);
    }

    .score-zero {
        color: #3a4454;
        font-size: 13px;
    }

    /* ── TOTAL CELLS ── */
    td.td-total-team {
        background: var(--total-team-bg) !important;
        color: var(--total-team-text) !important;
        font-family: var(--font-head) !important;
        font-weight: 800 !important;
        font-size: 1.4rem  !important;
    }

    td.td-total-player {
        background: var(--total-player-bg) !important;
        color: var(--total-player-text) !important;
        font-family: var(--font-head) !important;
        font-weight: 700 !important;
        font-size: 1.4rem !important;
    }

    td.td-total-grand {
        background: var(--total-grand-bg) !important;
        color: var(--total-grand-text) !important;
        font-family: var(--font-head) !important;
        font-weight: 900 !important;
        font-size: 1.5rem !important;
    }

    /* ── BONUS CELL ── */
    td.td-bonus {
        background: #2a1e00 !important;
        color: #f5c518 !important;
        font-family: var(--font-head) !important;
        font-weight: 800 !important;
        font-size: 14px !important;
    }

    td.td-bonus-zero {
        color: #3a4454 !important;
    }

    /* ── RANK MEDALS ── */
    .rank-medal {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-family: var(--font-head);
        font-weight: 900;
        font-size: 1.5rem;
    }

    .rank-1 {
        background: var(--lb-gold);
        color: #000;
    }

    .rank-2 {
        background: var(--lb-silver);
        color: #000;
    }

    .rank-3 {
        background: var(--lb-bronze);
        color: #fff;
    }

    .rank-n {
        background: #21262d;
        color: #8b949e;
        font-size: 12px;
    }

    /* ── CARD BADGES ── */
    .card-badges {
        display: inline-flex;
        gap: 6px;
        align-items: center;
        justify-content: center;
    }

    .card-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 28px;
        padding: 0 6px;
        border-radius: 4px;
        font-family: var(--font-head);
        font-weight: 900;
        font-size: 1.1rem;
        color: #fff;
    }

    .card-red { background: #ef4444; }
    .card-yellow { background: #facc15; color: #000; }
    .card-orange { background: #f97316; }
    .card-unknown { background: #6b7280; }

    td.td-rank-end {
        font-family: var(--font-head);
        font-weight: 900;
    }

    td.td-org {
        color: #f5c518 !important;
        font-family: var(--font-head) !important;
        font-weight: 700 !important;
        font-size: 1.2rem !important;
        letter-spacing: .04em;
    }

    /* ── INLINE EDIT ── */
    .score-edit-cell {
        position: relative;
        cursor: pointer;
        transition: background .15s;
    }

    .score-edit-cell:hover .score-pill,
    .score-edit-cell:hover .score-zero {
        opacity: 0.6;
    }

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

    .score-edit-cell.no-edit {
        cursor: not-allowed;
    }

    .score-edit-cell.no-edit:hover::after {
        display: none;
    }

    /* ── BULK EDIT MODE ── */
    .bulk-mode .score-edit-cell {
        cursor: crosshair;
    }

    .bulk-mode .score-edit-cell:hover {
        background: rgba(88, 166, 255, .08) !important;
    }

    .score-edit-cell.bulk-selected {
        background: rgba(88, 166, 255, .18) !important;
        outline: 2px solid #58a6ff;
        outline-offset: -2px;
    }

    .score-edit-cell.bulk-selected::after {
        display: none;
    }

    .score-edit-input {
        width: 64px;
        background: #0d1117;
        border: 1px solid #58a6ff;
        color: #e6edf3;
        border-radius: 4px;
        padding: 2px 5px;
        font-family: var(--font-head);
        font-size: 1.2rem;
        font-weight: 700;
        text-align: center;
        outline: none;
    }

    /* ── BULK EDIT MODAL ── */
    /* #bulkEditModal .modal-content {
        background: #0f1318;
        border: 1px solid #2a3040;
    }

    #bulkEditModal .modal-header {
        background: #1a2a4a;
        border-bottom: 1px solid #30507e;
    } */

    #bulkEditModal .modal-title {
        /* color: #79c0ff; */
        font-family: var(--font-head);
        font-weight: 900;
    }

    #bulkEditModal .table {
        color: #c9d1d9;
    }

    #bulkEditModal .table th {
        /* background: #1e2535;
        color: #8b949e; */
        font-size: 11px;
        font-family: "Space Mono";
        letter-spacing: .06em;
         background: #000;
         color: #fff;
    }

    #bulkEditModal .table td {
        /* background: #151b27;
        border-color: #1e2535; */
        vertical-align: middle;
        color: #000;
    }

    #bulkEditModal .bulk-pts-input {
        width: 80px;
        /* background: #0d1117; */
        border: 1px solid #ccc;
        color: #000;
        border-radius: 4px;
        padding: 3px 6px;
        font-family: var(--font-head);
        font-size: 14px;
        font-weight: 700;
        text-align: center;
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
        right: 24px;
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

    #lb-toast.show {
        opacity: 1;
    }

    #lb-toast.ok {
        background: #1a3a1a;
        color: #56d364;
        border: 1px solid #238636;
    }

    #lb-toast.err {
        background: #3a1a1a;
        color: #f85149;
        border: 1px solid #f85149;
    }

    #lb-toast.warn {
        background: #3d2a00;
        color: #f5c518;
        border: 1px solid #7a5a00;
    }

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
        #lb-controls {
            flex-direction: column;
            align-items: flex-start;
        }

        .lb-actions {
            margin-left: 0;
        }
    }
</style>