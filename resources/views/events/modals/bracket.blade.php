<!-- ================= BRACKET MODAL ================= -->
<div class="modal fade" id="bracketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0" style="background:#0a0e1a;border-radius:20px;overflow:hidden;box-shadow:0 25px 80px rgba(0,0,0,.7);">

            <!-- Header -->
            <div class="modal-header border-0 px-5 pt-5 pb-0" style="background:linear-gradient(135deg,#0d1220 0%,#111827 100%);">
                <div class="flex-grow-1">
                    <div id="bm-type-badge" class="mb-3"></div>
                    <h3 id="bm-title" style="color:#f1f5f9;font-weight:800;font-size:1.6rem;letter-spacing:-0.5px;margin:0 0 10px;"></h3>
                    <div id="bm-meta" class="d-flex flex-wrap gap-2 mt-1"></div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-4 mt-1 align-self-start" data-bs-dismiss="modal" style="filter:brightness(0.6);"></button>
            </div>

            <!-- Divider -->
            <div style="height:1px;background:linear-gradient(90deg,transparent,#1e2d45 20%,#1e2d45 80%,transparent);margin:20px 0 0;"></div>

            <div class="modal-body p-5">

                <!-- Loader -->
                <div id="bm-loader" class="text-center py-5">
                    <div style="position:relative;display:inline-block;width:48px;height:48px;">
                        <div style="position:absolute;inset:0;border-radius:50%;border:2px solid #1e2d45;"></div>
                        <div style="position:absolute;inset:0;border-radius:50%;border:2px solid transparent;border-top-color:#22d3ee;animation:bm-spin 0.8s linear infinite;"></div>
                    </div>
                    <p style="color:#475569;margin-top:14px;font-size:13px;letter-spacing:1px;text-transform:uppercase;">Loading bracket…</p>
                </div>

                <!-- Error -->
                <div id="bm-error" class="d-none" style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:12px;padding:16px 20px;color:#f87171;font-size:13px;display:flex;align-items:center;gap:10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span id="bm-error-text"></span>
                </div>

                <!-- XR Activities -->
                <div id="bm-activities" class="d-none mb-5"></div>

                <!-- Bracket Content -->
                <div id="bm-bracket" class="d-none"></div>

            </div>
        </div>
    </div>
</div>

<style>
@keyframes bm-spin { to { transform:rotate(360deg); } }
@keyframes bm-fadein { from { opacity:0;transform:translateY(8px); } to { opacity:1;transform:translateY(0); } }

:root {
    --bm-bg:      #0a0e1a;
    --bm-card:    #111827;
    --bm-match:   #131d2e;
    --bm-accent:  #6366f1;
    --bm-cyan:    #22d3ee;
    --bm-win:     #10b981;
    --bm-border:  #1e2d45;
    --bm-muted:   #475569;
    --bm-text:    #f1f5f9;
    --bm-conn:    #273549;
    --bm-gold:    #f59e0b;
}

/* ── META PILLS ── */
.bm-pill {
    display:inline-flex;align-items:center;gap:6px;
    background:#131d2e;border:1px solid #1e2d45;
    border-radius:999px;padding:5px 13px;
    font-size:12px;color:#64748b;font-weight:500;
    transition:border-color .2s;
}
.bm-pill:hover { border-color:#334155; }
.bm-pill strong { color:#e2e8f0;font-weight:600; }

/* ── ACTIVITY CARDS ── */
.bm-act-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px; }
.bm-act-card {
    background:#111827;border:1px solid #1e2d45;border-radius:14px;
    padding:18px;transition:border-color .2s,transform .2s,box-shadow .2s;
    animation:bm-fadein .4s ease both;
}
.bm-act-card:hover { border-color:#6366f1;transform:translateY(-3px);box-shadow:0 10px 30px rgba(99,102,241,.15); }
.bm-tag {
    display:inline-flex;align-items:center;gap:5px;
    font-size:9px;font-weight:800;letter-spacing:2px;
    text-transform:uppercase;padding:3px 10px;border-radius:999px;margin-bottom:10px;
}
.bm-tag-brain      { background:rgba(139,92,246,.12);color:#a78bfa;border:1px solid rgba(139,92,246,.25); }
.bm-tag-esports    { background:rgba(99,102,241,.12); color:#818cf8;border:1px solid rgba(99,102,241,.25); }
.bm-tag-egaming    { background:rgba(34,211,238,.08); color:#22d3ee;border:1px solid rgba(34,211,238,.18); }
.bm-tag-playground { background:rgba(16,185,129,.08); color:#34d399;border:1px solid rgba(16,185,129,.18); }
.bm-tag-mission    { background:rgba(245,158,11,.08); color:#fbbf24;border:1px solid rgba(245,158,11,.18); }
.bm-act-score { font-size:28px;font-weight:900;color:#22d3ee;line-height:1;margin:8px 0 4px;letter-spacing:-1px; }
.bm-act-score span { font-size:12px;font-weight:500;color:#475569;margin-left:4px; }

/* ── BRACKET SCROLL ── */
.bm-scroll { overflow-x:auto;padding-bottom:12px; }
.bm-scroll::-webkit-scrollbar { height:4px; }
.bm-scroll::-webkit-scrollbar-track { background:#0d1220; }
.bm-scroll::-webkit-scrollbar-thumb { background:#273549;border-radius:2px; }
.bm-bracket-wrap { display:flex;align-items:flex-start;gap:0;min-width:max-content;padding:8px 4px; }

/* ── ROUND ── */
.bm-round { display:flex;flex-direction:column;align-items:center;min-width:210px; }
.bm-round-label {
    font-size:10px;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;
    color:#22d3ee;margin-bottom:18px;padding:5px 16px;
    border:1px solid rgba(34,211,238,.18);border-radius:999px;
    background:rgba(34,211,238,.05);white-space:nowrap;
}
.bm-round.grand .bm-round-label { color:#f59e0b;border-color:rgba(245,158,11,.25);background:rgba(245,158,11,.06); }
.bm-round.losers .bm-round-label { color:#f87171;border-color:rgba(248,113,113,.25);background:rgba(248,113,113,.06); }
.bm-matches-col { display:flex;flex-direction:column;gap:16px;width:100%; }

/* ── CONNECTOR ── */
.bm-conn { width:32px;align-self:stretch;display:flex;flex-direction:column;padding:30px 0;gap:16px; }
.bm-conn-line { flex:1;position:relative; }
.bm-conn-line::before { content:'';position:absolute;right:0;top:25%;bottom:25%;width:1px;background:var(--bm-conn); }
.bm-conn-line::after  { content:'';position:absolute;right:0;top:50%;width:100%;height:1px;background:var(--bm-conn); }

/* ── MATCH CARD ── */
.bm-match {
    background:#131d2e;border:1px solid #1e2d45;border-radius:12px;
    overflow:hidden;width:195px;
    transition:border-color .2s,box-shadow .2s;
    animation:bm-fadein .4s ease both;
}
.bm-match:hover { border-color:#6366f1;box-shadow:0 0 20px rgba(99,102,241,.18); }
.bm-match.grand {
    border-color:rgba(245,158,11,.3);
    box-shadow:0 0 24px rgba(245,158,11,.12);
    width:210px;
}
.bm-match-num {
    font-size:9px;font-weight:800;letter-spacing:2px;text-transform:uppercase;
    color:#334155;padding:7px 12px 0;
}
.bm-team {
    display:flex;align-items:center;gap:8px;padding:10px 12px;
    border-top:1px solid #1a2740;
    transition:background .15s;position:relative;
}
.bm-team:first-of-type { border-top:none; }
.bm-team:hover { background:rgba(99,102,241,.06); }
.bm-team.bye { opacity:.25;pointer-events:none; }
.bm-team.winner { background:rgba(16,185,129,.06); }
.bm-team.winner .bm-tname { color:#34d399; }
.bm-seed {
    min-width:22px;height:22px;border-radius:6px;
    background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.25);
    display:flex;align-items:center;justify-content:center;
    font-size:9px;font-weight:900;color:#6366f1;flex-shrink:0;
    letter-spacing:0;
}
.bm-seed.tbd { background:rgba(71,85,105,.12);border-color:rgba(71,85,105,.2);color:#475569; }
.bm-team-avatar {
    width:22px;height:22px;border-radius:6px;
    background:linear-gradient(135deg,#1e3a5f,#0d1f35);
    border:1px solid #273549;
    display:flex;align-items:center;justify-content:center;
    font-size:8px;font-weight:800;color:#64748b;flex-shrink:0;
    text-transform:uppercase;
}
.bm-tname { font-size:12px;font-weight:600;color:#e2e8f0;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:110px; }
.bm-tname.tbd { color:#334155;font-weight:400;font-style:italic; }
.bm-score { min-width:26px;height:22px;background:rgba(255,255,255,.03);border:1px solid #1e2d45;border-radius:5px;font-size:11px;font-weight:700;color:#475569;display:flex;align-items:center;justify-content:center;flex-shrink:0; }

/* ── ROUND ROBIN ── */
.bm-rr { background:#111827;border-radius:14px;overflow:hidden;border:1px solid #1e2d45; }
.bm-rr table { width:100%;border-collapse:collapse; }
.bm-rr th { background:#131d2e;color:#22d3ee;font-size:10px;font-weight:800;letter-spacing:2px;text-transform:uppercase;padding:14px 18px;border-bottom:1px solid #1e2d45;text-align:left; }
.bm-rr td { padding:13px 18px;border-bottom:1px solid #0d1525;font-size:12px;color:#e2e8f0;vertical-align:middle; }
.bm-rr tr:last-child td { border-bottom:none; }
.bm-rr tr:hover td { background:rgba(99,102,241,.04); }
.bm-vs { display:inline-flex;align-items:center;gap:10px;font-weight:600;font-size:12px; }
.bm-vs-div { font-size:8px;font-weight:900;color:#6366f1;padding:3px 8px;background:rgba(99,102,241,.12);border-radius:4px;letter-spacing:2px;border:1px solid rgba(99,102,241,.2); }
.bm-pending { font-size:9px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:#475569;padding:3px 10px;background:rgba(71,85,105,.08);border:1px solid rgba(71,85,105,.18);border-radius:999px; }

/* ── SECTION LABELS ── */
.bm-section-wrap { margin-bottom:24px; }
.bm-section-label {
    display:inline-flex;align-items:center;gap:7px;
    font-size:10px;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;
    padding:6px 16px;border-radius:999px;margin-bottom:16px;
}
.bm-winners { color:#10b981;background:rgba(16,185,129,.07);border:1px solid rgba(16,185,129,.18); }
.bm-losers  { color:#f87171;background:rgba(248,113,113,.07);border:1px solid rgba(248,113,113,.18); }
.bm-gf      { color:#f59e0b;background:rgba(245,158,11,.07);border:1px solid rgba(245,158,11,.18); }

/* ── EMPTY STATE ── */
.bm-empty {
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    padding:48px 24px;gap:12px;
}
.bm-empty svg { color:#273549; }
.bm-empty p { color:#334155;font-size:13px;font-weight:500;margin:0; }

/* ── HEADER SECTION ── */
.bm-section-header {
    display:flex;align-items:center;gap:10px;
    font-size:10px;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;
    color:#22d3ee;margin-bottom:16px;
}
.bm-section-header svg { color:#22d3ee;opacity:.7; }
.bm-section-divider { height:1px;background:#1a2740;margin:28px 0; }
</style>