<!DOCTYPE html>
<html lang="en">
<head>
    <title>Roster Check-In</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            padding: 20px 12px 40px;
            color: #1a1a2e;
        }

        /* ── Loading / Error screens ─────────────────────────────────── */
        #screen-loading,
        #screen-error {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
            gap: 14px;
        }

        #screen-checkin { display: none; }
        #screen-done    { display: none; }

        .spinner {
            width: 42px; height: 42px;
            border: 4px solid #e0e0e0;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .error-icon { font-size: 40px; }
        .error-msg  { color: #dc2626; font-weight: 600; font-size: 16px; }
        .sub-msg    { color: #6b7280; font-size: 14px; }

        /* ── Header card ─────────────────────────────────────────────── */
        .header-card {
            background: #fff;
            border-radius: 14px;
            padding: 18px 20px 14px;
            margin-bottom: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
        }

        .org-name  { font-size: 20px; font-weight: 700; color: #0f172a; }
        .event-tag { font-size: 13px; color: #64748b; margin-top: 3px; }

        .already-badge {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            margin-top: 8px;
        }

        /* ── Toolbar ─────────────────────────────────────────────────── */
        .toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .count-pill {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 13px;
            color: #374151;
            flex: 1;
            min-width: 140px;
        }

        .count-pill .cnt { font-weight: 700; color: #059669; }

        .btn-tool {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 7px 14px;
            font-size: 13px;
            cursor: pointer;
            color: #334155;
            white-space: nowrap;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }
        .btn-tool:active          { background: #e2e8f0; }
        .btn-tool.deselect        { color: #dc2626; border-color: #fca5a5; }
        .btn-tool.deselect:active { background: #fef2f2; }

        /* ── Student list ────────────────────────────────────────────── */
        .student-list {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
            overflow: hidden;
            margin-bottom: 80px;
        }

        /*
         * Each row is a <label> wrapping a hidden native <input type="checkbox">.
         * All visual state (green bg, filled box, checkmark) is driven by CSS
         * :has(input:checked) — no JS class-toggling in the hot path.
         * This eliminates the mobile double-fire and the class-drift glitches.
         */
        .student-item {
            display: flex;
            align-items: center;
            padding: 13px 16px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            gap: 14px;
            background: #fff;
            transition: background 0.15s;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }
        .student-item:last-child { border-bottom: none; }

        /* Hide the native checkbox — visual state comes purely from :has() */
        .student-item input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }

        /* Present state is driven by checkbox, not a JS-toggled class */
        .student-item:has(input:checked) {
            background: #f0fdf4;
        }

        /* Custom checkbox visual */
        .chk-box {
            width: 24px; height: 24px;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: #fff;
            transition: background 0.15s, border-color 0.15s;
        }
        .student-item:has(input:checked) .chk-box {
            background: #16a34a;
            border-color: #16a34a;
        }

        /* Checkmark — animated in when checked */
        .chk-box svg {
            opacity: 0;
            transform: scale(0.5);
            transition: opacity 0.12s, transform 0.12s;
        }
        .student-item:has(input:checked) .chk-box svg {
            opacity: 1;
            transform: scale(1);
        }

        .student-info { flex: 1; min-width: 0; }
        .student-name { font-size: 15px; font-weight: 600; color: #0f172a; }
        .student-meta { font-size: 12px; color: #94a3b8; margin-top: 2px; }

        .team-badge {
            font-size: 11px;
            background: #f1f5f9;
            color: #475569;
            border-radius: 6px;
            padding: 2px 8px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ── Sticky submit footer ───────────────────────────────────── */
        .submit-footer {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 12px 16px;
            box-shadow: 0 -4px 16px rgba(0,0,0,.08);
        }

        .btn-submit {
            width: 100%;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            -webkit-tap-highlight-color: transparent;
        }
        .btn-submit:active   { background: #1d4ed8; transform: scale(0.98); }
        .btn-submit:disabled { background: #93c5fd; cursor: not-allowed; transform: none; }

        /* ── Confirmation overlay ────────────────────────────────────── */
        .overlay-bg {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            display: none;
            align-items: flex-end;
            justify-content: center;
            z-index: 100;
        }
        .overlay-bg.show { display: flex; }

        .confirm-sheet {
            background: #fff;
            border-radius: 20px 20px 0 0;
            padding: 24px 20px 32px;
            width: 100%;
            max-width: 520px;
        }

        .confirm-title   { font-size: 17px; font-weight: 700; margin-bottom: 10px; }
        .confirm-body    { font-size: 14px; color: #374151; line-height: 1.6; margin-bottom: 6px; }
        .confirm-missing { font-size: 13px; color: #dc2626; margin-bottom: 18px; min-height: 18px; }

        .confirm-actions { display: flex; gap: 10px; }

        .btn-cancel-confirm {
            flex: 1;
            background: #f1f5f9;
            color: #374151;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }
        .btn-ok {
            flex: 2;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }
        .btn-ok:disabled { background: #93c5fd; cursor: not-allowed; }

        /* ── Done screen ─────────────────────────────────────────────── */
        #screen-done {
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
            text-align: center;
            gap: 12px;
        }
        .done-icon  { font-size: 56px; }
        .done-title { font-size: 22px; font-weight: 700; color: #15803d; }
        .done-sub   { font-size: 15px; color: #374151; }
        .done-meta  { font-size: 13px; color: #94a3b8; margin-top: 4px; }
    </style>
</head>
<body>

    {{-- Loading --}}
    <div id="screen-loading">
        <div class="spinner"></div>
        <p style="color:#6b7280;font-size:14px">Loading roster…</p>
    </div>

    {{-- Error --}}
    <div id="screen-error" style="display:none">
        <div class="error-icon">⚠️</div>
        <div class="error-msg" id="error-text">Something went wrong.</div>
        <div class="sub-msg">Please try scanning the QR code again.</div>
    </div>

    {{-- Check-in form --}}
    <div id="screen-checkin">

        <div class="header-card">
            <div class="org-name"  id="ci-org">—</div>
            <div class="event-tag" id="ci-event">—</div>
            <div id="ci-already-badge" style="display:none">
                <span class="already-badge">Already checked-in - you can update attendance</span>
            </div>
        </div>

        <div class="toolbar">
            <div class="count-pill">
                <span class="cnt" id="ci-present-count">0</span> of
                <span id="ci-total-count">0</span> present
            </div>
            <button class="btn-tool" id="btn-select-all">Select all</button>
            <button class="btn-tool deselect" id="btn-deselect-all">Deselect all</button>
        </div>

        <div class="student-list" id="student-list"></div>

        <div class="submit-footer">
            <button class="btn-submit" id="btn-submit">Submit check-in</button>
        </div>

    </div>

    {{-- Confirmation sheet --}}
    <div class="overlay-bg" id="overlay-confirm">
        <div class="confirm-sheet">
            <div class="confirm-title">Confirm check-in</div>
            <div class="confirm-body"    id="confirm-body-text"></div>
            <div class="confirm-missing" id="confirm-missing-text"></div>
            <div class="confirm-actions">
                <button class="btn-cancel-confirm" id="btn-confirm-no">No, go back</button>
                <button class="btn-ok"             id="btn-confirm-ok">OK, submit</button>
            </div>
        </div>
    </div>

    {{-- Done --}}
    <div id="screen-done">
        <div class="done-icon">✅</div>
        <div class="done-title">Check-in complete!</div>
        <div class="done-sub"  id="done-sub-text"></div>
        <div class="done-meta" id="done-meta-text"></div>
    </div>

<script>
(function () {
    'use strict';

    const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
    const rosterId = "{{ $roster_id }}";
    const checksum = "{{ $checksum }}";

    let students = [];

    // ── Screen switcher ────────────────────────────────────────────────────
    function show(id) {
        ['screen-loading','screen-error','screen-checkin','screen-done'].forEach(function (s) {
            document.getElementById(s).style.display = 'none';
        });
        var target = document.getElementById(id);
        target.style.display = (id === 'screen-checkin') ? 'block' : 'flex';
    }

    // ── Read present IDs straight from the DOM checkboxes ─────────────────
    // Single source of truth — no separate JS Set that can drift out of sync.
    function getPresentIds() {
        return Array.from(
            document.querySelectorAll('#student-list input[type="checkbox"]:checked')
        ).map(function (cb) { return parseInt(cb.value, 10); });
    }

    function updateCounts() {
        var checked = document.querySelectorAll(
            '#student-list input[type="checkbox"]:checked'
        ).length;
        document.getElementById('ci-present-count').textContent = checked;
        document.getElementById('ci-total-count').textContent   = students.length;
    }

    // ── XSS guard ──────────────────────────────────────────────────────────
    function esc(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // ── Render student rows ────────────────────────────────────────────────
    // Uses <label> + hidden native checkbox so the browser owns toggle state.
    // CSS :has(input:checked) drives every visual change — background, box
    // fill, checkmark — so there is nothing to keep in sync with JS.
    // The `checked` attribute is set from server-returned attendance_status,
    // which fixes the "all unchecked on rescan" bug.
    function renderStudentList(data) {
        var list = document.getElementById('student-list');

        list.innerHTML = data.map(function (s) {
            var isPresent = s.attendance_status === 'present';
            return [
                '<label class="student-item">',
                    '<input type="checkbox" value="', esc(String(s.id)), '"',
                        isPresent ? ' checked' : '',
                    '>',
                    '<div class="chk-box">',
                        '<svg width="14" height="11" viewBox="0 0 14 11" fill="none">',
                            '<path d="M1 5.5L5 9.5L13 1.5"',
                                  ' stroke="#fff" stroke-width="2.2"',
                                  ' stroke-linecap="round" stroke-linejoin="round"/>',
                        '</svg>',
                    '</div>',
                    '<div class="student-info">',
                        '<div class="student-name">', esc(s.name), '</div>',
                        '<div class="student-meta">Age ', esc(String(s.age)),
                            ' &middot; Grade ', esc(String(s.grade)), '</div>',
                    '</div>',
                    '<div class="team-badge">', esc(s.team), '</div>',
                '</label>',
            ].join('');
        }).join('');

        // Single delegated listener on the container — fires once per real
        // checkbox change, never doubled on mobile.
        list.addEventListener('change', function (e) {
            if (e.target.type === 'checkbox') updateCounts();
        });

        updateCounts();
    }

    // ── Step 1: fetch roster + persisted attendance ────────────────────────
    fetch('/checkin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
        },
        body: JSON.stringify({ roster_id: rosterId, checksum: checksum }),
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (!data.success) {
            document.getElementById('error-text').textContent =
                data.message || 'Invalid QR code.';
            show('screen-error');
            return;
        }

        students = data.students || [];

        document.getElementById('ci-org').textContent   = data.organization || '—';
        document.getElementById('ci-event').textContent = (data.event || '—');

        if (data.already_checked_in) {
            document.getElementById('ci-already-badge').style.display = 'block';
        }

        renderStudentList(students);
        show('screen-checkin');
    })
    .catch(function () {
        document.getElementById('error-text').textContent = 'Network error. Please try again.';
        show('screen-error');
    });

    // ── Select all ────────────────────────────────────────────────────────
    document.getElementById('btn-select-all').addEventListener('click', function () {
        document.querySelectorAll('#student-list input[type="checkbox"]')
            .forEach(function (cb) { cb.checked = true; });
        updateCounts();
    });

    // ── Deselect all ──────────────────────────────────────────────────────
    document.getElementById('btn-deselect-all').addEventListener('click', function () {
        document.querySelectorAll('#student-list input[type="checkbox"]')
            .forEach(function (cb) { cb.checked = false; });
        updateCounts();
    });

    // ── Submit → open confirmation sheet ──────────────────────────────────
    document.getElementById('btn-submit').addEventListener('click', function () {
        var presentIds   = getPresentIds();
        var missingNames = students
            .filter(function (s) { return presentIds.indexOf(s.id) === -1; })
            .map(function (s) { return s.name; });

        var orgName = document.getElementById('ci-org').textContent;

        document.getElementById('confirm-body-text').textContent =
            'Are you sure you want to submit ' + presentIds.length +
            ' participant(s) for ' + orgName + '?';

        document.getElementById('confirm-missing-text').textContent =
            missingNames.length ? '\u26A0 Not attending: ' + missingNames.join(', ') : '';

        document.getElementById('overlay-confirm').classList.add('show');
    });

    // ── Confirmation: go back ──────────────────────────────────────────────
    document.getElementById('btn-confirm-no').addEventListener('click', function () {
        document.getElementById('overlay-confirm').classList.remove('show');
    });

    // ── Confirmation: OK → POST to server ─────────────────────────────────
    document.getElementById('btn-confirm-ok').addEventListener('click', function () {
        var btn        = this;
        var presentIds = getPresentIds(); // read fresh at the moment of submit

        btn.disabled    = true;
        btn.textContent = 'Submitting\u2026';

        fetch('/checkin/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({
                roster_id:   rosterId,
                checksum:    checksum,
                present_ids: presentIds,
            }),
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            document.getElementById('overlay-confirm').classList.remove('show');

            if (!data.success) {
                alert(data.message || 'Submission failed.');
                btn.disabled    = false;
                btn.textContent = 'OK, submit';
                return;
            }

            var total   = students.length;
            var present = data.present_count;
            var missing = total - present;

            document.getElementById('done-sub-text').textContent =
                present + ' of ' + total + ' students marked present' +
                (missing > 0 ? ' \u00B7 ' + missing + ' absent' : '');

            document.getElementById('done-meta-text').textContent =
                data.organization + ' \u2014 ' + data.event;

            show('screen-done');
        })
        .catch(function () {
            alert('Network error. Please try again.');
            btn.disabled    = false;
            btn.textContent = 'OK, submit';
        });
    });

})();
</script>

</body>
</html>