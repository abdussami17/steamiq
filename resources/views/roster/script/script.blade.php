<script>
    (function () {
        'use strict';
     
        // ── CSRF helper ───────────────────────────────────────────────────────
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
     
        // ── AJAX: load roster table ───────────────────────────────────────────
        function loadRosters(eventId = '') {
            const url = new URL('{{ route("rosters.list") }}', window.location.origin);
            if (eventId) url.searchParams.set('event_id', eventId);
     
            fetch(url)
                .then(r => r.json())
                .then(({ data }) => renderTable(data))
                .catch(() => {
                    document.getElementById('rosterTableBody').innerHTML =
                        '<tr><td colspan="8" class="text-center text-danger">Failed to load rosters.</td></tr>';
                });
        }
     
        function renderTable(rows) {
            const tbody = document.getElementById('rosterTableBody');
     
            if (!rows.length) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">No rosters found.</td></tr>';
                return;
            }
     
            const statusBadge = s => ({
                draft:       '<span class="badge bg-secondary">Draft</span>',
                ready:       '<span class="badge bg-success">Ready</span>',
                'checked-in':'<span class="badge bg-primary">Checked-In</span>',
            }[s] ?? `<span class="badge bg-light text-dark">${s}</span>`);
     
            tbody.innerHTML = rows.map((r, i) => `
                <tr>
                    <td>${r.id}</td>
                    <td>${r.event ?? '—'}</td>
                    <td>${r.organization ?? '—'}</td>
                    <td>${r.coach}</td>
                    <td class="text-center">${r.total_players}</td>
                    <td>${statusBadge(r.status)}</td>
                    <td>${r.uploaded_at ?? '—'}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button class="btn btn-icon btn-view viewRosterBtn"
        data-id="${r.id}"
        title="View Roster Details">
    <i data-lucide="eye"></i>
</button>
                            ${buildPhase2Buttons(r)}
                        </div>
                    </td>
                </tr>
            `).join('');
     
            lucide.createIcons();
        }
     
        // ── Filter ─────────────────────────────────────────────────────────────
        document.getElementById('filterEvent').addEventListener('change', function () {
            loadRosters(this.value);
        });
     
        // ── Import ─────────────────────────────────────────────────────────────
        const steps = ['Processing...', 'Validating...', 'Creating records...'];
        let stepInterval = null;
     
        function setImportLoading(active) {
            document.getElementById('importForm').classList.toggle('d-none', active);
            document.getElementById('importProgress').classList.toggle('d-none', !active);
            document.getElementById('importModalFooter').classList.toggle('d-none', active);
     
            if (active) {
                let i = 0;
                const el = document.getElementById('importProgressText');
                el.textContent = steps[0];
                stepInterval = setInterval(() => {
                    i = (i + 1) % steps.length;
                    el.textContent = steps[i];
                }, 1800);
            } else {
                clearInterval(stepInterval);
            }
        }
     
        function showReport(report) {
            document.getElementById('importProgress').classList.add('d-none');
            document.getElementById('importReport').classList.remove('d-none');
     
            document.getElementById('reportTotal').textContent     = report.total_rows;
            document.getElementById('reportInserted').textContent  = report.inserted;
            document.getElementById('reportDuplicates').textContent= report.duplicates;
            document.getElementById('reportFailed').textContent    = report.failed.length;
     
            if (report.failed.length) {
                document.getElementById('reportFailedList').classList.remove('d-none');
                document.getElementById('reportFailedBody').innerHTML =
                    report.failed.map(f =>
                        `<tr><td>${f.row}</td><td>${f.reason}</td></tr>`
                    ).join('');
            }
     
            // Show a "Close & Refresh" button
            document.getElementById('importModalFooter').innerHTML = `
                <button type="button" class="btn btn-success" id="importDoneBtn">
                   Done
                </button>`;
     
            document.getElementById('importDoneBtn').addEventListener('click', () => {
                bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
                setTimeout(() => {
    loadRosters(document.getElementById('filterEvent').value);
}, 800);
                resetImportModal();
            });
        }
     
        function resetImportModal() {
            document.getElementById('importForm').classList.remove('d-none');
            document.getElementById('importReport').classList.add('d-none');
            document.getElementById('importProgress').classList.add('d-none');
            document.getElementById('reportFailedList').classList.add('d-none');
            document.getElementById('reportFailedBody').innerHTML = '';
            document.getElementById('importFile').value = '';
            document.getElementById('importEventId').value = '';
            document.getElementById('importModalFooter').innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="importSubmitBtn">
                    <i class="bi bi-upload me-1"></i> Import
                </button>`;
            bindSubmitBtn();
        }
     
        function bindSubmitBtn() {
            document.getElementById('importSubmitBtn')?.addEventListener('click', handleImport);
        }
     
        function handleImport() {
            const eventId = document.getElementById('importEventId').value;
            const file    = document.getElementById('importFile').files[0];
     
            if (!eventId) { alert('Please select an event.'); return; }
            if (!file)    { alert('Please choose a file to upload.'); return; }
     
            const formData = new FormData();
            formData.append('event_id', eventId);
            formData.append('file', file);
     
            setImportLoading(true);
     
            fetch('{{ route("rosters.import") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF },
                body: formData,
            })
            .then(async r => {
                const json = await r.json();
                if (!r.ok) throw new Error(json.message ?? 'Import failed.');
                return json;
            })
            .then(({ report }) => {
                setImportLoading(false);
                showReport(report);
            })
            .catch(err => {
                setImportLoading(false);
                alert('Error: ' + err.message);
            });
        }
     
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.viewRosterBtn');
            if (!btn) return;
     
            const id = btn.dataset.id;
     
            const modalEl = document.getElementById('viewRosterModal');
            const modal = new bootstrap.Modal(modalEl);
     
            document.getElementById('rosterPlayersBody').innerHTML =
                '<tr><td colspan="5" class="text-center">Loading...</td></tr>';
     
            fetch(`/rosters/${id}`)
                .then(r => r.json())
                .then(data => {
     
                    // Meta info
                    document.getElementById('rosterMeta').innerHTML = `
                        <strong>Event:</strong> ${data.event} |
                        <strong>Organization:</strong> ${data.organization} |
                        <strong>Coach:</strong> ${data.coach} |
                        <strong>Status:</strong> ${data.status}
                    `;
     
                    // Players
                    if (!data.players.length) {
                        document.getElementById('rosterPlayersBody').innerHTML =
                            '<tr><td colspan="5" class="text-center text-muted">No players</td></tr>';
                    } else {
                        document.getElementById('rosterPlayersBody').innerHTML =
                            data.players.map(p => `
                                <tr>
                                    <td>${p.name}</td>
                                    <td>${p.age ?? '-'}</td>
                                    <td>${p.grade ?? '-'}</td>
                                    <td>${p.team}</td>
                                    <td>${p.group}</td>
                                </tr>
                            `).join('');
                    }
     
                    modal.show();
                })
                .catch(() => {
                    alert('Failed to load roster details');
                });
        });
     
        // ── Init ───────────────────────────────────────────────────────────────
        loadRosters();
        bindSubmitBtn();
     
        // Reset modal state when closed
        document.getElementById('importModal').addEventListener('hidden.bs.modal', resetImportModal);
     
        // =========================================================================
        // PHASE 2 — added below, zero Phase 1 code touched above
        // =========================================================================
     
        // Bootstrap instances for Phase 2 modals
        const qrModal     = new bootstrap.Modal(document.getElementById('qrModal'));
        let toastEl = null;
let packetToast = null;

document.addEventListener('DOMContentLoaded', function () {
    toastEl = document.getElementById('packetToast');

    if (toastEl) {
        packetToast = new bootstrap.Toast(toastEl, { autohide: false });
    } else {
        console.error('packetToast element not found in DOM');
    }
});
     
        // Build Phase 2 action buttons per row (PDF, Generate, QR)
        function buildPhase2Buttons(r) {
            const canGenerate = r.status !== 'checked-in';
     
            const generateBtn = canGenerate
    ? `<button class="btn btn-icon btn-danger js-generate"
            data-id="${r.id}"
            title="Generate Field Packet PDF">
           <i data-lucide="file-down"></i>
       </button>`
    : `<button class="btn btn-icon btn-view"
            disabled
            title="Field Packet Already Checked-In">
           <i data-lucide="file-down"></i>
       </button>`;

const pdfBtn = r.has_pdf
    ? `<a href="${r.pdf_url}"
          target="_blank"
          class="btn btn-icon btn-delete"
          title="Download Generated PDF">
           <i data-lucide="file-text"></i>
       </a>`
    : `<button class="btn btn-icon btn-view"
            disabled
            title="PDF Not Generated Yet">
           <i data-lucide="file-text"></i>
       </button>`;

const qrBtn = r.has_qr
    ? `<button class="btn btn-icon btn-delete js-show-qr"
            data-id="${r.id}"
            title="View QR Code">
           <i data-lucide="qr-code"></i>
       </button>`
    : `<button class="btn btn-icon btn-view"
            disabled
            title="QR Code Not Generated Yet">
           <i data-lucide="qr-code"></i>
       </button>`;
     
            return generateBtn + pdfBtn + qrBtn;
        }
     
        // Generate packet — delegated click handler
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.js-generate');
            if (!btn) return;
            generatePacket(btn.dataset.id);
        });
     
        // Show QR — delegated click handler
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.js-show-qr');
            if (!btn) return;
            showQr(btn.dataset.id);
        });
     
        // Generate packet steps
        const generateSteps = ['Generating PDF...', 'Creating QR Code...', 'Finalising packet...'];
     
        function generatePacket(rosterId) {
            if (!toastEl) {
        console.error('Toast not initialized');
        return;
    }
            let stepIdx = 0;
            toastEl.classList.remove('bg-success', 'bg-danger', 'text-white');
            document.getElementById('packetToastText').textContent = generateSteps[0];
            document.getElementById('packetToastSpinner').classList.remove('d-none');
            packetToast.show();
     
            const stepInterval = setInterval(() => {
                stepIdx = (stepIdx + 1) % generateSteps.length;
                document.getElementById('packetToastText').textContent = generateSteps[stepIdx];
            }, 1600);
     
            fetch(`/rosters/${rosterId}/generate-packet`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
            })
            .then(async res => {
                const json = await res.json();
                if (!res.ok) throw new Error(json.message ?? 'Generation failed.');
                return json;
            })
            .then(() => {
                clearInterval(stepInterval);
                document.getElementById('packetToastSpinner').classList.add('d-none');
                toastEl.classList.add('bg-success', 'text-white');
                document.getElementById('packetToastText').textContent = '✓ Packet ready!';
                setTimeout(() => packetToast.hide(), 2500);
                loadRosters(document.getElementById('filterEvent').value);
            })
            .catch(err => {
                clearInterval(stepInterval);
                document.getElementById('packetToastSpinner').classList.add('d-none');
                toastEl.classList.add('bg-danger', 'text-white');
                document.getElementById('packetToastText').textContent = 'X ' + err.message;
                setTimeout(() => packetToast.hide(), 4000);
            });
        }
     
        function showQr(rosterId) {
    const qrBody = document.getElementById('qrModalBody');
    const qrMeta = document.getElementById('qrModalMeta');
    const dlBtn  = document.getElementById('qrDownloadBtn');

    qrBody.innerHTML = `
    <div class="d-flex justify-content-center align-items-center" style="min-height:200px">
        <div class="spinner-border text-primary"></div>
    </div>
`;
    qrMeta.textContent = '';
    dlBtn.classList.add('d-none');

    qrModal.show();

    fetch(`/rosters/${rosterId}/qr?ts=${Date.now()}`, {
        headers: { 'Accept': 'application/json' },
    })
    .then(async res => {
        const json = await res.json();
        if (!res.ok) throw new Error(json.message ?? 'Could not load QR.');
        return json;
    })
    .then(data => {
        const qrUrl = data.qr_url;

        qrBody.innerHTML = `
            <img src="${qrUrl}" 
                 alt="Roster QR Code"
                 class="img-fluid rounded shadow-sm"
                 style="max-width:260px;">
        `;

        qrMeta.textContent = `${data.organization} — ${data.event}`;

        dlBtn.href = qrUrl;
        dlBtn.download = `roster-${rosterId}-qr.svg`;

        dlBtn.classList.remove('d-none');
    })
    .catch(err => {
        qrBody.innerHTML =
            `<div class="alert alert-danger mb-0">${err.message}</div>`;
    });
}

// =========================================================================
// GAME CARD EXPORT — zero Phase 1/2 code touched above
// =========================================================================

document.getElementById('exportGameCardBtn').addEventListener('click', function () {
    const btn = this;
    const originalHtml = btn.innerHTML;

    // Show loading state
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Exporting...';

    // Build URL respecting current event filter
    const eventId  = document.getElementById('filterEvent').value;
    const url      = new URL('{{ route("rosters.export.game-cards") }}', window.location.origin);
    if (eventId) url.searchParams.set('event_id', eventId);

    fetch(url, {
        method: 'GET',
        headers: { 'X-CSRF-TOKEN': CSRF },
    })
    .then(response => {
        if (!response.ok) throw new Error('Export failed. Server returned ' + response.status);
        return response.blob();
    })
    .then(blob => {
        // Derive filename from Content-Disposition if available, else fallback
        const now       = new Date();
        const ts        = now.getFullYear().toString()
                        + String(now.getMonth()+1).padStart(2,'0')
                        + String(now.getDate()).padStart(2,'0');
        const suffix    = eventId ? `_event${eventId}` : '_all';
        const filename  = `game_cards${suffix}_${ts}.xlsx`;

        // Trigger browser download without page reload
        const blobUrl  = URL.createObjectURL(blob);
        const anchor   = document.createElement('a');
        anchor.href     = blobUrl;
        anchor.download = filename;
        document.body.appendChild(anchor);
        anchor.click();
        document.body.removeChild(anchor);
        URL.revokeObjectURL(blobUrl);
    })
    .catch(err => {
        alert('Export error: ' + err.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        lucide.createIcons();
    });
});
     
    })();
    </script>