<script>
    (function () {
        'use strict';
    
        // ── CSRF helper ───────────────────────────────────────────────────────
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    
        // ── AJAX: load roster table ───────────────────────────────────────────
        function loadRosters(eventId = '') {
            const url = new URL('<?php echo e(route("rosters.list")); ?>', window.location.origin);
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
                        <button class="btn btn-icon btn-view viewRosterBtn" data-id="${r.id}">
    <i data-lucide="eye"></i> 
</button>
                        <button class="btn btn-sm btn-outline-secondary disabled" title="Coming soon">
                            <i class="bi bi-file-pdf"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary disabled" title="Coming soon">
                            <i class="bi bi-qr-code"></i>
                        </button>
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
                loadRosters(document.getElementById('filterEvent').value);
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
    
            fetch('<?php echo e(route("rosters.import")); ?>', {
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
    })();
    </script><?php /**PATH C:\Users\PC\Downloads\steamiq (8)\resources\views/roster/script/script.blade.php ENDPATH**/ ?>