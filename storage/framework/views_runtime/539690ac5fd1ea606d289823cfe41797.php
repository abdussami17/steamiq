<!-- ================= BRACKET MODAL ================= -->
<div class="modal fade" id="bracketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0" style="background:#0a0e1a;border-radius:20px;overflow:hidden;box-shadow:0 25px 80px rgba(0,0,0,.7);">

            <!-- Header -->
            <div class="modal-header border-0 px-3 py-3  pb-0" style="background:linear-gradient(135deg,#0d1220 0%,#111827 100%);">
                <div class="flex-grow-1">
                    <div id="bm-type-badge" class="mb-3"></div>
                    <h3 id="bm-title" style="color:#f1f5f9;font-weight:800;font-size:1.6rem;letter-spacing:-0.5px;margin:0 0 10px;"></h3>
                    <div id="bm-meta" class="d-flex flex-wrap gap-2 mt-1"></div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-4 mt-1 align-self-start" data-bs-dismiss="modal" style="filter:brightness(0.6);"></button>
            </div>

            <!-- Divider -->
            <div style="height:1px;background:linear-gradient(90deg,transparent,#1e2d45 20%,#1e2d45 80%,transparent);margin:20px 0 0;"></div>

            <div class="modal-body p-0">

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
@keyframes bm-spin    { to { transform:rotate(360deg); } }
@keyframes bm-fadein  { from { opacity:0;transform:translateY(8px); } to { opacity:1;transform:translateY(0); } }
@keyframes bm-pulse   { 0%,100%{box-shadow:0 0 0 0 rgba(99,102,241,.4)} 50%{box-shadow:0 0 0 6px rgba(99,102,241,0)} }

:root {
    --bm-bg:     #0a0e1a;
    --bm-card:   #111827;
    --bm-match:  #0f1828;
    --bm-border: #1e2d45;
    --bm-muted:  #475569;
    --bm-text:   #f1f5f9;
    --bm-conn:   #273549;
    --bm-gold:   #f59e0b;
}

/* ── META PILLS ── */
.bm-pill { display:inline-flex;align-items:center;gap:6px;background:#131d2e;border:1px solid #1e2d45;border-radius:999px;padding:5px 13px;font-size:1.1rem;color:#64748b;font-weight:500; }
.bm-pill strong { color:#e2e8f0;font-weight:600; }

/* ── ACTIVITY CARDS ── */
.bm-act-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px; }
.bm-act-card { background:#111827;border:1px solid #1e2d45;border-radius:14px;padding:18px;transition:border-color .2s,transform .2s,box-shadow .2s;animation:bm-fadein .4s ease both; }
.bm-act-card:hover { border-color:#6366f1;transform:translateY(-3px);box-shadow:0 10px 30px rgba(99,102,241,.15); }
.bm-tag { display:inline-flex;align-items:center;gap:5px;font-size:1rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;padding:3px 10px;border-radius:999px;margin-bottom:10px; }
.bm-tag-brain      { background:rgba(139,92,246,.12);color:#a78bfa;border:1px solid rgba(139,92,246,.25); }
.bm-tag-esports    { background:rgba(99,102,241,.12); color:#818cf8;border:1px solid rgba(99,102,241,.25); }
.bm-tag-egaming    { background:rgba(34,211,238,.08); color:#22d3ee;border:1px solid rgba(34,211,238,.18); }
.bm-tag-playground { background:rgba(16,185,129,.08); color:#34d399;border:1px solid rgba(16,185,129,.18); }
.bm-tag-mission    { background:rgba(245,158,11,.08); color:#fbbf24;border:1px solid rgba(245,158,11,.18); }
.bm-act-score { font-size:2.4rem;font-weight:600;color:#22d3ee;line-height:1;margin:8px 0 4px;letter-spacing:-1px; }
.bm-act-score span { font-size:12px;font-weight:500;color:#475569;margin-left:4px; }

/* ═══════════════════════════════════════
   POD TABS
═══════════════════════════════════════ */
.bm-pod-tabs {
    display:flex;gap:6px;margin-bottom:0;padding:5px;
    background:#060c18;border:1px solid #1a2740;border-radius:14px 14px 0 0;
    border-bottom:none;width:fit-content;
}
.bm-pod-tab {
    display:inline-flex;align-items:center;gap:8px;
    padding:9px 18px;border-radius:9px;border:none;
    background:transparent;color:#475569;
    font-size:1.4rem;font-weight:700;letter-spacing:.5px;cursor:pointer;
    transition:background .2s,color .2s,box-shadow .2s;
}
.bm-pod-tab.active {
    background:#0f1828;color:#e2e8f0;
    box-shadow:0 2px 10px rgba(0,0,0,.6),0 0 0 1px var(--pod-color,#6366f1)30;
}
.bm-pod-tab:not(.active):hover { color:#94a3b8; }
.bm-pod-dot { width:8px;height:8px;border-radius:50%;flex-shrink:0; }
.bm-pod-panels { /* wrapper */ }
.bm-pod-panel { animation:bm-fadein .2s ease; }
.bm-pod-header {
    padding:10px 14px 12px;
    border:1px solid #1a2740;border-top:none;
    background:#090f1e;
    margin-bottom:12px;
    border-radius:0 0 10px 10px;
}
.bm-pod-badge {
    font-size:1.3rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;
    padding:4px 14px;border-radius:999px;
}

/* ═══════════════════════════════════════
   PHASE TABS
═══════════════════════════════════════ */
.bm-phase-tabs {
    display:flex;gap:4px;margin-bottom:14px;
    background:#070d1a;border:1px solid #1a2740;border-radius:10px;
    padding:4px;width:fit-content;
}
.bm-phase-tab {
    display:inline-flex;align-items:center;gap:6px;
    padding:7px 15px;border-radius:7px;border:none;
    background:transparent;color:#475569;
    font-size:1.1rem;font-weight:700;cursor:pointer;
    transition:background .2s,color .2s;
}
.bm-phase-tab svg{
    height: 16px;
    width: 16px;

}
.bm-phase-tab.active { background:#111827;color:#e2e8f0;box-shadow:0 2px 8px rgba(0,0,0,.5); }
.bm-phase-tab:not(.active):hover { color:#94a3b8; }
.bm-phase-panels { /* wrapper */ }
.bm-phase-panel { animation:bm-fadein .2s ease; }

/* ═══════════════════════════════════════
   VISUAL BRACKET
═══════════════════════════════════════ */
.bm-scroll { overflow-x:auto;overflow-y:visible;padding:16px 4px 20px; }
.bm-scroll::-webkit-scrollbar { height:5px; }
.bm-scroll::-webkit-scrollbar-track { background:#0a0e1a; }
.bm-scroll::-webkit-scrollbar-thumb { background:#1e2d45;border-radius:3px; }

.bm-bracket-wrap { display:flex;align-items:center;min-width:max-content;padding:8px 4px;position:relative; }

.bm-round { display:flex;flex-direction:column;align-items:center;min-width:210px;position:relative; }
.bm-round-label {
    font-size:1.3rem;font-weight:900;letter-spacing:2.5px;text-transform:uppercase;
    margin-bottom:14px;padding:4px 14px;border:1px solid;border-radius:999px;white-space:nowrap;
}

.bm-matches-col { display:flex;flex-direction:column;gap:0;width:100%; }

.bm-match-slot {
    display:flex;align-items:center;position:relative;
    flex:1;min-height:100px;padding:8px 0;
}
.bm-match-slot::after {
    content:'';position:absolute;right:0;top:50%;width:24px;height:1px;background:#273549;
}
.bm-round:last-child .bm-match-slot::after { display:none; }

/* connectors */
.bm-conn { width:24px;align-self:stretch;display:flex;flex-direction:column;position:relative;flex-shrink:0; }
.bm-conn-pair { display:flex;flex-direction:column;flex:1;position:relative; }
.bm-conn-pair::before { content:'';position:absolute;right:0;top:25%;bottom:25%;width:1px;background:#273549; }
.bm-conn-pair::after  { content:'';position:absolute;right:0;top:50%;width:100%;height:1px;background:#273549; }

/* ─── MATCH CARD ─── */
.bm-match {
    background:#0f1828;border:1px solid #1e2d45;border-radius:10px;
    overflow:hidden;min-width:200px;max-width:100%;flex-shrink:0;
    transition:border-color .2s,box-shadow .2s;
    animation:bm-fadein .35s ease both;position:relative;
}
.bm-match::before {
    content:'';position:absolute;inset:0;pointer-events:none;
    background:linear-gradient(135deg,rgba(99,102,241,.03) 0%,transparent 60%);
}
.bm-match:hover { border-color:var(--match-accent,rgba(99,102,241,.5));box-shadow:0 0 16px var(--match-accent,rgba(99,102,241,.12)); }
.bm-match.grand { border-color:rgba(245,158,11,.4);box-shadow:0 0 28px rgba(245,158,11,.18);min-width:215px; width:100%}
.bm-match.bm-match-done { border-color:rgba(16,185,129,.3); }
.bm-match.bm-match-done::after {
    content:'';position:absolute;inset:0;pointer-events:none;
    background:linear-gradient(135deg,rgba(16,185,129,.04) 0%,transparent 70%);
}

/* ─── MATCH HEADER ─── */
.bm-match-header {
    display:flex;align-items:center;justify-content:space-between;
    padding:5px 8px 0;min-height:22px;
}
.bm-match-num { font-size:1.3rem;font-weight:900;letter-spacing:2px;text-transform:uppercase;color:#fff; }
.bm-match-status-done { font-size:20px;color:#10b981;font-weight:900; }

/* pencil edit button */
.bm-score-btn {
    background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.2);border-radius:5px;
    color:#6366f1;width:30px;height:30px;display:flex;align-items:center;justify-content:center;
    cursor:pointer;transition:background .15s,border-color .15s;flex-shrink:0;
    padding:0;
}
.bm-score-btn  svg{
    height: 16px;
    width:16px;
}
.bm-score-btn:hover { background:rgba(99,102,241,.2);border-color:rgba(99,102,241,.4); }

/* ─── TEAM ROW ─── */
.bm-team {
    display:flex;align-items:center;gap:7px;
    padding:9px 10px;border-top:1px solid #141f33;
    transition:background .12s;position:relative;min-height:40px;
}
.bm-team:first-of-type { border-top:none; }
.bm-team.bye { opacity:.2;pointer-events:none; }
.bm-team.winner .bm-tname { font-weight:700; }
.bm-team-red  { border-left:3px solid #ef4444 !important; }
.bm-team-blue { border-left:3px solid #3b82f6 !important; }
.bm-team-red.winner  { background:rgba(239,68,68,.08); }
.bm-team-red.winner .bm-tname { color:#fca5a5; }
.bm-team-blue.winner { background:rgba(59,130,246,.08); }
.bm-team-blue.winner .bm-tname { color:#93c5fd; }

/* hover highlight for clickable teams */
.bm-team[onclick]:hover { background:rgba(255,255,255,.04); }
.bm-team[onclick]:active { background:rgba(255,255,255,.07); }

.bm-side-dot { width:6px;height:6px;border-radius:50%;flex-shrink:0; }
.bm-side-red  { background:#ef4444;box-shadow:0 0 5px rgba(239,68,68,.7); }
.bm-side-blue { background:#3b82f6;box-shadow:0 0 5px rgba(59,130,246,.7); }

.bm-team-avatar {
    width:30px;height:30px;border-radius:6px;
    background:linear-gradient(135deg,#1a2d4a,#0d1928);border:1px solid #273549;
    display:flex;align-items:center;justify-content:center;
    font-size:15px;font-weight:900;color:#64748b;flex-shrink:0;text-transform:uppercase;
}

.bm-tname { font-size:1.2rem;font-weight:600;color:#e2e8f0;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
    /* max-width:100px;  */
}
.bm-tname.tbd { color:#2d3f55;font-weight:400;font-style:italic; }

.bm-score { min-width:40px;height:26px;background:rgba(255,255,255,.03);border:1px solid #1e2d45;border-radius:4px;font-size:14px;font-weight:700;color:#475569;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.bm-score-win  { background:rgba(16,185,129,.12);border-color:rgba(16,185,129,.3);color:#34d399; }
.bm-score-num  { background:rgba(99,102,241,.08);border-color:rgba(99,102,241,.25);color:#818cf8; }

/* ─── CHAMPION BOX ─── */
.bm-champion-col { display:flex;flex-direction:column;align-items:center;justify-content:center;min-width:160px;padding:0 12px; }
.bm-champion-box {
    background:linear-gradient(135deg,#1a2540,#0f1828);
    border:2px solid rgba(245,158,11,.4);border-radius:14px;
    padding:22px 18px;text-align:center;min-width:148px;
    box-shadow:0 0 40px rgba(245,158,11,.12),inset 0 1px 0 rgba(255,255,255,.04);
    animation:bm-fadein .5s ease both;position:relative;overflow:hidden;
}
.bm-champion-box::before {
    content:'';position:absolute;inset:0;
    background:radial-gradient(circle at 50% 0%,rgba(245,158,11,.08) 0%,transparent 65%);
    pointer-events:none;
}
.bm-champion-icon  { font-size:30px;margin-bottom:8px;display:block;filter:drop-shadow(0 0 8px rgba(245,158,11,.5)); }
.bm-champion-label { font-size:1.2rem;font-weight:900;letter-spacing:3px;text-transform:uppercase;color:#f59e0b;margin-bottom:8px; }
.bm-champion-name  { font-size:1.4rem;font-weight:900;text-transform:uppercase;letter-spacing:.5px;word-break:break-word;line-height:1.25;min-height:32px;display:flex;align-items:center;justify-content:center; }
.bm-champion-tbd   { color:#334155;font-size:1.2rem;font-weight:400;font-style:italic;letter-spacing:0;text-transform:none; }

/* ─── SECTION HEADER ─── */
.bm-section-header { display:flex;align-items:center;gap:10px;font-size:1.3rem;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;color:#22d3ee;margin-bottom:16px; }
.bm-section-divider { height:1px;background:#1a2740;margin:28px 0; }

/* ── DIVISION BADGES ── */
.bm-div-badge { min-width:30px;height:30px;border-radius:4px;display:inline-flex;align-items:center;justify-content:center;font-size:1.1rem !important;font-weight:900;flex-shrink:0; }
.bm-div-primary { background:rgba(245,158,11,.15);color:#fbbf24;border:1px solid rgba(245,158,11,.3); }
.bm-div-junior  { background:rgba(99,102,241,.15);color:#818cf8;border:1px solid rgba(99,102,241,.3); }
.bm-div-null    { display:none; }

/* ── EMPTY STATE ── */
.bm-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 24px;gap:12px; }
.bm-empty p { color:#334155;font-size:13px;font-weight:500;margin:0; }

/* ── RESET BUTTON ── */
.bm-reinit-btn {
    display:inline-flex;align-items:center;gap:6px;
    background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);
    border-radius:8px;padding:6px 14px;
    color:#f87171;font-size:1.2rem;font-weight:700;cursor:pointer;
    transition:background .15s,border-color .15s;
}
.bm-reinit-btn:hover { background:rgba(239,68,68,.15);border-color:rgba(239,68,68,.35); }

/* ════════════════════════════════════════════════
   FINAL SHOWDOWN  –  VS Button
════════════════════════════════════════════════ */
.bm-vs-btn {
    display:inline-flex;align-items:center;gap:9px;
    padding:9px 20px;border-radius:10px;cursor:pointer;
    background:linear-gradient(135deg,rgba(239,68,68,.12) 0%,rgba(0,0,0,.15) 50%,rgba(59,130,246,.12) 100%);
    border:1.5px solid rgba(255,255,255,.08);
    color:#e2e8f0;font-family:var(--bm-font,inherit);
    font-size:1.05rem;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;
    transition:border-color .25s,box-shadow .25s,transform .18s;
    position:relative;overflow:hidden;
}
.bm-vs-btn::before {
    content:'';position:absolute;inset:0;
    background:linear-gradient(135deg,rgba(239,68,68,.18),rgba(59,130,246,.18));
    opacity:0;transition:opacity .25s;
}
.bm-vs-btn:hover::before { opacity:1; }
.bm-vs-btn:hover {
    border-color:rgba(255,255,255,.18);
    box-shadow:0 0 18px rgba(239,68,68,.25),0 0 18px rgba(59,130,246,.25);
    transform:translateY(-1px);
}
.bm-vs-btn:active { transform:translateY(0); }
.bm-vs-dot { width:8px;height:8px;border-radius:50%;flex-shrink:0; }

/* ════════════════════════════════════════════════
   FINAL SHOWDOWN  –  Full-screen overlay
════════════════════════════════════════════════ */
@keyframes bm-sd-enter  { from{opacity:0;transform:scale(.96)} to{opacity:1;transform:scale(1)} }
@keyframes bm-sd-float  { 0%,100%{transform:translateY(0) scale(1);opacity:.5} 50%{transform:translateY(-28px) scale(1.4);opacity:.9} }
@keyframes bm-sd-glow-r { 0%,100%{box-shadow:0 0 35px rgba(239,68,68,.55),0 0 70px rgba(239,68,68,.2)} 50%{box-shadow:0 0 60px rgba(239,68,68,.9),0 0 120px rgba(239,68,68,.4)} }
@keyframes bm-sd-glow-b { 0%,100%{box-shadow:0 0 35px rgba(59,130,246,.55),0 0 70px rgba(59,130,246,.2)} 50%{box-shadow:0 0 60px rgba(59,130,246,.9),0 0 120px rgba(59,130,246,.4)} }
@keyframes bm-sd-pulse  { 0%,100%{transform:scale(1)} 50%{transform:scale(1.08)} }
@keyframes bm-sd-spin   { to{transform:rotate(360deg)} }
@keyframes bm-sd-bolt   { 0%,100%{opacity:.15;transform:scaleY(1)} 50%{opacity:.55;transform:scaleY(1.15)} }
@keyframes bm-sd-slide-r{ from{opacity:0;transform:translateX(-60px)} to{opacity:1;transform:translateX(0)} }
@keyframes bm-sd-slide-l{ from{opacity:0;transform:translateX(60px)}  to{opacity:1;transform:translateX(0)} }

.bm-sd-overlay {
    position:fixed;inset:0;z-index:99999;
    background:#00060f;
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    animation:bm-sd-enter .35s cubic-bezier(.22,.61,.36,1) both;
    overflow:hidden;cursor:default;
}

/* ambient halves */
.bm-sd-overlay::before,
.bm-sd-overlay::after {
    content:'';position:absolute;top:0;width:50%;height:100%;pointer-events:none;
}
.bm-sd-overlay::before {
    left:0;
    background:radial-gradient(ellipse 80% 80% at 0% 50%,rgba(239,68,68,.14) 0%,transparent 70%);
}
.bm-sd-overlay::after {
    right:0;
    background:radial-gradient(ellipse 80% 80% at 100% 50%,rgba(59,130,246,.14) 0%,transparent 70%);
}

/* center split line */
.bm-sd-divider {
    position:absolute;left:50%;top:10%;height:80%;width:1px;
    background:linear-gradient(to bottom,transparent,rgba(255,255,255,.06) 30%,rgba(255,255,255,.06) 70%,transparent);
    pointer-events:none;
}

/* decorative diagonal lines */
.bm-sd-line { position:absolute;pointer-events:none;opacity:.04; }
.bm-sd-line1 { top:-20%;left:0;width:60%;height:200%;border-right:1.5px solid #ef4444;transform:rotate(-15deg); }
.bm-sd-line2 { top:-20%;right:0;width:60%;height:200%;border-left:1.5px solid #3b82f6;transform:rotate(15deg); }

/* floating particles */
.bm-sd-particle {
    position:absolute;border-radius:50%;pointer-events:none;
    animation:bm-sd-float 4s ease-in-out infinite;filter:blur(1px);
}

/* close */
.bm-sd-close {
    position:absolute;top:22px;right:26px;z-index:10;
    width:40px;height:40px;border-radius:50%;
    background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);
    color:#475569;font-size:16px;cursor:pointer;
    display:flex;align-items:center;justify-content:center;
    transition:background .15s,color .15s;
}
.bm-sd-close:hover { background:rgba(255,255,255,.11);color:#e2e8f0; }

/* super title */
.bm-sd-title-wrap { position:relative;z-index:2;margin-bottom:28px;text-align:center; }
.bm-sd-event-name {
    font-size:2rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;
    /* color:#94a3b8;*/
    margin-bottom:6px; 
    color: #fff;
    text-shadow:0 0 20px rgba(148,163,184,.15);
}
.bm-sd-supertitle {
    font-size:1.8rem;font-weight:900;letter-spacing:6px;text-transform:uppercase;
    color:#fff;
    text-shadow:0 0 20px rgba(34,211,238,.15);
}

/* footer */
.bm-sd-footer {
    position:relative;z-index:2;margin-top:28px;
    font-size:2rem;font-weight:800;letter-spacing:4px;text-transform:uppercase;
    /* color:#475569; */ color: #fff;
    text-shadow:0 0 16px rgba(71,85,105,.3);
}

/* inner row */
.bm-sd-inner {
    display:flex;align-items:center;justify-content:center;
    gap:0;width:100%;max-width:1100px;padding:0 20px;
    position:relative;z-index:2;
}

/* each side */
.bm-sd-side {
    flex:1;display:flex;flex-direction:column;align-items:center;
    gap:16px;padding:32px 24px;position:relative;
}
.bm-sd-red  { animation:bm-sd-slide-r .55s .1s cubic-bezier(.22,.61,.36,1) both; }
.bm-sd-blue { animation:bm-sd-slide-l .55s .15s cubic-bezier(.22,.61,.36,1) both; }

/* pod tag pill */
.bm-sd-tag {
    font-size:1.1rem;font-weight:900;letter-spacing:3px;text-transform:uppercase;
    padding:5px 18px;border-radius:999px;border:1px solid;
}
.bm-sd-champ-lbl {
    font-size:1rem;font-weight:800;letter-spacing:4px;text-transform:uppercase;
    color:#1e3a5f;
}

/* avatar */
.bm-sd-avatar {
    width:140px;height:140px;border-radius:4px;
    display:flex;align-items:center;justify-content:center;
    font-size:3.2rem;font-weight:900;color:#fff;text-transform:uppercase;
    position:relative;z-index:1;
}
.bm-sd-avatar-red  {
    background:linear-gradient(135deg,#7f1d1d,#3b0a0a);border:3px solid #ef4444;
    animation:bm-sd-glow-r 2.2s ease-in-out infinite;
}
.bm-sd-avatar-blue {
    background:linear-gradient(135deg,#1e3a8a,#0c1f61);border:3px solid #3b82f6;
    animation:bm-sd-glow-b 2.2s ease-in-out infinite;
}

/* aura ring behind avatar */
.bm-sd-aura {
    position:absolute;width:180px;height:180px;border-radius:50%;
    opacity:.25;pointer-events:none;
    animation:bm-sd-pulse 2.2s ease-in-out infinite;
}
.bm-sd-aura-red  { background:radial-gradient(circle,#ef4444 0%,transparent 70%); }
.bm-sd-aura-blue { background:radial-gradient(circle,#3b82f6 0%,transparent 70%); }

/* team name */
.bm-sd-name {
    font-size:2.4rem;font-weight:900;text-transform:uppercase;letter-spacing:1px;
    text-align:center;line-height:1.15;word-break:break-word;max-width:300px;
}

/* lightning bolts decoration */
.bm-sd-bolts {
    display:flex;gap:6px;opacity:.18;
}
.bm-sd-bolt {
    width:18px;height:18px;
    animation:bm-sd-bolt 1.6s ease-in-out infinite;
}
.bm-sd-red  .bm-sd-bolt { color:#ef4444; }
.bm-sd-blue .bm-sd-bolt { color:#3b82f6; }
.bm-sd-bolt:nth-child(2) { animation-delay:.3s; }
.bm-sd-bolt:nth-child(3) { animation-delay:.6s; }

/* VS center */
.bm-sd-center {
    position:relative;display:flex;align-items:center;justify-content:center;
    width:130px;flex-shrink:0;
    animation:bm-sd-enter .5s .3s ease both;
}
.bm-sd-vs-ring {
    position:absolute;border-radius:50%;border:1.5px solid;
    animation:bm-sd-spin linear infinite;pointer-events:none;
}
.bm-sd-vs-ring.r1 { width:110px;height:110px;border-color:rgba(255,255,255,.06);animation-duration:8s; }
.bm-sd-vs-ring.r2 { width:86px; height:86px; border-color:rgba(255,255,255,.05);animation-duration:5s;animation-direction:reverse; }
.bm-sd-vs-ring.r3 { width:62px; height:62px; border-color:rgba(255,255,255,.04);animation-duration:3s; }
.bm-sd-vs {
    font-size:6rem;font-weight:900;letter-spacing:3px;
    background:linear-gradient(135deg,#ef4444 0%,#fff 45%,#3b82f6 100%);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
    filter:drop-shadow(0 0 14px rgba(255,255,255,.25));
    position:relative;z-index:2;
    animation:bm-sd-pulse 1.8s ease-in-out infinite;
}

/* responsive */
@media(max-width:640px) {
    .bm-sd-inner  { flex-direction:column;gap:12px; }
     { transform:rotate(90deg); }
    .bm-sd-name   { font-size:1.6rem; }
    .bm-sd-avatar { width:96px;height:96px;font-size:2.2rem; }
    .bm-sd-supertitle { font-size:.6rem;letter-spacing:3px; }
}
</style>
<?php /**PATH C:\Users\PC\Desktop\steam-two\resources\views/events/modals/bracket.blade.php ENDPATH**/ ?>