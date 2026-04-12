
<?php $__env->startSection('title', 'Tournament Bracket - SteamIQ'); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800;900&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root {
    --bb-bg:         #0a0e1a;
    --bb-surface:    #0d1220;
    --bb-surface2:   #111827;
    --bb-border:     #1e2d45;
    --bb-border-lit: #2a3f5f;
    --bb-cyan:       #22d3ee;
    --bb-gold:       #f59e0b;
    --bb-text:       #f1f5f9;
    --bb-muted:      #475569;
    --bb-dimmer:     #2d3f55;
    --bb-font:       'Barlow Condensed', sans-serif;
}

/* ── Full page wrapper ── */
.bb-page {
    background: #f1f5f9;
    min-height: 100vh;
    padding: 28px 0 64px;
    font-family: var(--bb-font);
}
.bb-wrap { max-width: 1400px; margin: 0 auto; padding: 0 24px; }

/* ── Top bar ── */
.bb-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 28px;
}
.bb-heading { display: flex; align-items: center; gap: 14px; }
.bb-icon {
    width: 52px; height: 52px;
    background: var(--bb-bg);
    border: 1.5px solid var(--bb-border-lit);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    color: var(--bb-cyan);
}
.bb-icon svg { width: 22px; height: 22px; }
.bb-title-main {
    font-size: 2rem; font-weight: 900; line-height: 1;
    color: #0f172a; text-transform: uppercase; letter-spacing: 3px;
}
.bb-title-sub {
    font-size: .7rem; font-weight: 700; letter-spacing: 2px;
    color: #64748b; text-transform: uppercase; margin-top: 3px;
}

/* ── Event selector ── */
.bb-selector { display: flex; align-items: center; gap: 10px; }
.bb-sel-lbl {
    font-size: .7rem; font-weight: 700; letter-spacing: 2px;
    color: #64748b; text-transform: uppercase; white-space: nowrap;
}
.bb-select {
    appearance: none;
    background: var(--bb-surface2)
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2322d3ee'/%3E%3C/svg%3E")
        no-repeat right 12px center;
    border: 1.5px solid var(--bb-border-lit);
    border-radius: 8px;
    color: var(--bb-text);
    font-family: var(--bb-font);
    font-size: 1rem; font-weight: 700; letter-spacing: .3px;
    padding: 10px 40px 10px 16px;
    cursor: pointer;
    min-width: 300px;
    transition: border-color .2s, box-shadow .2s;
}
.bb-select:focus {
    outline: none;
    border-color: var(--bb-cyan);
    box-shadow: 0 0 0 3px rgba(34,211,238,.12);
}
.bb-select option { background: #1e293b; color: #fff; }

/* ── Main panel ── */
.bb-panel {
    background: var(--bb-bg);
    border: 1.5px solid var(--bb-border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 40px rgba(0,0,0,.45);
    position: relative;
}
/* corner accents */
.bb-panel::before, .bb-panel::after {
    content: '';
    position: absolute;
    width: 22px; height: 22px;
    border-color: var(--bb-cyan);
    border-style: solid;
    z-index: 2; pointer-events: none;
}
.bb-panel::before { top: -1px; left: -1px; border-width: 2px 0 0 2px; border-radius: 16px 0 0 0; }
.bb-panel::after  { top: -1px; right: -1px; border-width: 2px 2px 0 0; border-radius: 0 16px 0 0; }

/* ── Placeholder ── */
.bb-placeholder {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 100px 24px; gap: 16px;
    color: var(--bb-dimmer);
}
.bb-placeholder svg { opacity: .18; }
.bb-placeholder p {
    font-size: 1rem; font-weight: 600; letter-spacing: 1px;
    text-transform: uppercase; margin: 0; color: var(--bb-dimmer);
}

/* ── Loader ── */
.bb-loader-wrap {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 80px 24px; gap: 16px;
}
.bb-spinner {
    width: 48px; height: 48px; position: relative;
}
.bb-spinner::before {
    content: ''; position: absolute; inset: 0;
    border-radius: 50%; border: 2px solid var(--bb-border);
}
.bb-spinner::after {
    content: ''; position: absolute; inset: 0;
    border-radius: 50%;
    border: 2px solid transparent;
    border-top-color: var(--bb-cyan);
    animation: bb-spin .8s linear infinite;
}
@keyframes bb-spin { to { transform: rotate(360deg); } }
@keyframes bb-fadein { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

.bb-loader-lbl {
    color: var(--bb-muted); font-size: .72rem; font-weight: 700;
    letter-spacing: 2px; text-transform: uppercase;
}

/* ── Event header inside panel ── */
.bb-event-hdr {
    padding: 22px 28px 18px;
    background: linear-gradient(135deg, var(--bb-surface) 0%, var(--bb-surface2) 100%);
    border-bottom: 1px solid var(--bb-border);
    animation: bb-fadein .3s ease;
}

/* ── Error ── */
.bb-error {
    margin: 24px;
    background: rgba(239,68,68,.08);
    border: 1px solid rgba(239,68,68,.25);
    border-radius: 12px;
    padding: 16px 20px;
    color: #f87171;
    font-size: 13px;
    display: flex; align-items: center; gap: 10px;
}

/* ── Bracket content area ── */
.bb-body { padding: 24px 20px 28px; animation: bb-fadein .25s ease; }

@media (max-width:700px) {
    .bb-title-main { font-size: 1.4rem; }
    .bb-topbar { flex-direction: column; align-items: flex-start; }
    .bb-select { min-width: 100%; }
    .bb-wrap { padding: 0 14px; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="bb-page">
<div class="bb-wrap">

    
    <div class="bb-topbar">
        <div class="bb-heading">
            <div class="bb-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/>
                    <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/>
                    <path d="M4 22h16"/>
                    <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/>
                    <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/>
                    <path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/>
                </svg>
            </div>
            <div>
                <div class="bb-title-main">Tournament Bracket</div>
                <div class="bb-title-sub">Live bracket view</div>
            </div>
        </div>
        <div class="bb-selector">
            <span class="bb-sel-lbl">Event</span>
            <select id="bb-select-event" class="bb-select" onchange="loadBracketBoard(this.value)">
                <option value="" hidden>— Select an Event —</option>
                <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($event->id); ?>">
                        <?php echo e($event->name); ?><?php if($event->start_date): ?> (<?php echo e(\Carbon\Carbon::parse($event->start_date)->format('M Y')); ?>)<?php endif; ?>
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>

    
    <div class="bb-panel">

        
        <div id="bb-placeholder" class="bb-placeholder">
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/>
                <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/>
                <path d="M4 22h16"/>
                <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/>
                <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/>
                <path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/>
            </svg>
            <p>Select an event above to view its bracket</p>
        </div>

        
        <div id="bb-content" class="d-none">

            
            <div id="bm-loader" class="d-none bb-loader-wrap">
                <div class="bb-spinner"></div>
                <span class="bb-loader-lbl">Loading bracket…</span>
            </div>

            
            <div id="bm-error" class="bb-error d-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span id="bm-error-text"></span>
            </div>

            
            <div id="bb-event-header" class="bb-event-hdr d-none">
                <div id="bm-type-badge" class="mb-2"></div>
                <h3 id="bm-title" style="color:#f1f5f9;font-weight:800;font-size:1.5rem;letter-spacing:-.3px;margin:0 0 10px;"></h3>
                <div id="bm-meta" class="d-flex flex-wrap gap-2"></div>
            </div>

            
            <div id="bm-activities" class="d-none" style="padding: 20px 24px 0;"></div>

            
            <div id="bm-bracket" class="d-none bb-body"></div>

        </div>
    </div>

</div>
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('modals'); ?>
    <?php echo $__env->make('events.modals.bracket', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <?php echo $__env->make('events.scripts.bracket-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
        /* ══════════════════════════════════════════════════════════
           BRACKET BOARD – inline bracket loader
        ══════════════════════════════════════════════════════════ */
        function loadBracketBoard(eventId) {
            if (!eventId) return;

            _bmEventId = eventId;

            document.getElementById('bb-placeholder').classList.add('d-none');
            document.getElementById('bb-content').classList.remove('d-none');
            document.getElementById('bb-event-header').classList.add('d-none');

            ['bm-error', 'bm-activities', 'bm-bracket'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) { el.classList.add('d-none'); el.innerHTML = ''; }
            });

            var loaderEl = document.getElementById('bm-loader');
            if (loaderEl) loaderEl.classList.remove('d-none');

            fetch('/events/' + eventId + '/bracket')
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (!res.success) throw new Error(res.message || 'Failed to load bracket.');
                    document.getElementById('bb-event-header').classList.remove('d-none');
                    renderBracketModal(res);
                })
                .catch(function(err) {
                    var loaderEl = document.getElementById('bm-loader');
                    if (loaderEl) loaderEl.classList.add('d-none');
                    var errEl = document.getElementById('bm-error');
                    if (errEl) errEl.classList.remove('d-none');
                    var errTextEl = document.getElementById('bm-error-text');
                    if (errTextEl) errTextEl.textContent = err.message;
                });
        }

        // After saving a winner/score, reload inline instead of opening the modal
        window.openBracketModal = loadBracketBoard;

        // bmOpenScoreEntry appends the score popup to #bracketModal, which is
        // display:none on this page → the popup is invisible. Patch it to use
        // document.body as the insertion target instead.
        (function () {
            var _orig = bmOpenScoreEntry;
            bmOpenScoreEntry = function () {
                var bm = document.getElementById('bracketModal');
                if (bm) bm.id = '_bracketModal_hidden';
                _orig.apply(this, arguments);
                if (bm) bm.id = 'bracketModal';
            };
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u236413684/domains/voags.com/public_html/steamiq/resources/views/bracket/index.blade.php ENDPATH**/ ?>