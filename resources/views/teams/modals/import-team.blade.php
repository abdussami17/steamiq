{{-- ============================================================
     IMPORT TEAMS MODAL  (resources/views/modals/import-teams.blade.php)
     ============================================================ --}}

     <div class="modal fade" id="importTeamsModal" tabindex="-1" aria-labelledby="importTeamsModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
    
                {{-- Header --}}
                <div class="modal-header">
                    <h5 class="modal-title" id="importTeamsModalTitle">
                        <i data-lucide="file-spreadsheet" style="width:18px;height:18px;vertical-align:-3px;margin-right:6px;"></i>
                        Import Teams via Spreadsheet
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
    
                {{-- Body --}}
                <div class="modal-body">
    
                    {{-- ── Instructions card ── --}}
                    <div class="alert alert-info d-flex gap-3 align-items-start mb-4" role="alert">
                        <i data-lucide="info" style="width:20px;height:20px;flex-shrink:0;margin-top:2px;"></i>
                        <div>
                            <strong>Spreadsheet Format Instructions</strong>
                            <ul class="mb-1 mt-1 ps-3" style="font-size:.875rem;line-height:1.7;">
                                <li>Row <strong>1</strong> must be the header row (exact column names listed below).</li>
                                <li>One team per row. Team columns are <strong>required</strong>; student columns are <strong>optional</strong>.</li>
                                <li>To add <strong>multiple students</strong> to the same team, use <strong>comma-separated values</strong> in <code>student_name</code> and <code>student_email</code> (same order). You can also repeat the team row instead — both styles work.</li>
                                <li>Duplicate team names in the same file will be treated as the <strong>same team</strong>.</li>
                                <li>Accepted formats: <code>.xlsx</code> or <code>.csv</code> · Max file size: <strong>5 MB</strong>.</li>
                            </ul>
    
                            {{-- Column reference table --}}
                            <div class="table-responsive mt-2">
                                <table class="table table-sm table-bordered mb-0" style="font-size:.8rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Column</th>
                                            <th>Required?</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td><code>team_name</code></td><td><span class="badge bg-danger">Required</span></td><td>Unique team name</td></tr>
                                        <tr><td><code>organization</code></td><td><span class="badge bg-danger">Required</span></td><td>Exact organization name</td></tr>
                                        <tr><td><code>group</code></td><td><span class="badge bg-danger">Required</span></td><td>Exact group name under the organization</td></tr>
                                        <tr><td><code>subgroup</code></td><td><span class="badge bg-secondary">Optional</span></td><td>Leave blank if none</td></tr>
                                        <tr><td><code>division</code></td><td><span class="badge bg-danger">Required</span></td><td><code>Junior</code> or <code>Primary</code></td></tr>
                                        <tr><td><code>student_name</code></td><td><span class="badge bg-secondary">Optional</span></td><td>Single name <em>or</em> comma-separated: <code>Ali Hassan, Sara Khan</code></td></tr>
                                        <tr><td><code>student_email</code></td><td><span class="badge bg-secondary">Optional</span></td><td>Optional. Comma-separated in same order as names: <code>ali@ex.com, sara@ex.com</code></td></tr>
                                    </tbody>
                                </table>
                            </div>
    
                            <div class="mt-2">
                                <a href="{{ route('teams.import.template') }}" class="btn btn-sm btn-outline-secondary">
                                    <i data-lucide="download" style="width:14px;height:14px;vertical-align:-2px;margin-right:4px;"></i>
                                    Download Sample Template
                                </a>
                            </div>
                        </div>
                    </div>
    
                    {{-- ── Upload form ── --}}
                    <form id="importTeamsForm"
                          method="POST"
                          action="{{ route('teams.import') }}"
                          enctype="multipart/form-data">
                        @csrf
    
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="importTeamsFile">
                                Select Spreadsheet File <span class="text-danger">*</span>
                            </label>
    
                            {{-- Drop-zone --}}
                            <div id="importDropZone" class="import-drop-zone" onclick="document.getElementById('importTeamsFile').click()">
                                <div class="import-drop-zone-inner">
                                    <i data-lucide="upload-cloud" class="import-drop-icon"></i>
                                    <p class="import-drop-text">Drag &amp; drop your file here, or <span class="text-primary">browse</span></p>
                                    <p class="import-drop-hint">.xlsx or .csv · max 5 MB</p>
                                </div>
                            </div>
    
                            <input type="file"
                                   id="importTeamsFile"
                                   name="file"
                                   accept=".xlsx,.csv"
                                   class="d-none"
                                   required>
    
                            {{-- Selected file name display --}}
                            <div id="importFileInfo" class="import-file-info d-none">
                                <i data-lucide="file-check" style="width:16px;height:16px;color:#198754;"></i>
                                <span id="importFileName" class="ms-1 text-success fw-medium"></span>
                                <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" onclick="clearImportFile()">Remove</button>
                            </div>
                        </div>
    
                        {{-- Progress bar (shown while uploading) --}}
                        <div id="importProgress" class="d-none mb-3">
                            <div class="d-flex justify-content-between mb-1" style="font-size:.8rem;">
                                <span>Uploading &amp; processing…</span>
                                <span id="importProgressPct">0%</span>
                            </div>
                            <div class="progress" style="height:6px;">
                                <div id="importProgressBar"
                                     class="progress-bar progress-bar-striped progress-bar-animated"
                                     role="progressbar"
                                     style="width:0%"></div>
                            </div>
                        </div>
    
                    </form>
                </div>
    
                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="importTeamsSubmitBtn" class="btn btn-primary" onclick="submitImportTeams()">
                        <i data-lucide="file-up" style="width:15px;height:15px;vertical-align:-2px;margin-right:4px;"></i>
                        Import Teams
                    </button>
                </div>
    
            </div>
        </div>
    </div>
    
    
    {{-- ============================================================
         IMPORT RESULTS MODAL
         ============================================================ --}}
    
    <div class="modal fade" id="importTeamsResultModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
    
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i data-lucide="clipboard-list" style="width:18px;height:18px;vertical-align:-3px;margin-right:6px;"></i>
                        Import Results
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
    
                <div class="modal-body">
    
                    {{-- Summary badges --}}
                    <div class="d-flex gap-3 flex-wrap mb-4" id="importResultSummary"></div>
    
                    {{-- Tabs: success / failed --}}
                    <ul class="nav nav-tabs mb-3" id="importResultTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="resultSuccessTab"
                                    data-bs-toggle="tab" data-bs-target="#resultSuccessPane"
                                    type="button" role="tab">
                                <i data-lucide="check-circle" style="width:14px;height:14px;margin-right:4px;vertical-align:-1px;color:#198754;"></i>
                                Imported <span class="badge bg-success ms-1" id="successCount">0</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="resultFailedTab"
                                    data-bs-toggle="tab" data-bs-target="#resultFailedPane"
                                    type="button" role="tab">
                                <i data-lucide="x-circle" style="width:14px;height:14px;margin-right:4px;vertical-align:-1px;color:#dc3545;"></i>
                                Failed <span class="badge bg-danger ms-1" id="failedCount">0</span>
                            </button>
                        </li>
                    </ul>
    
                    <div class="tab-content">
    
                        {{-- Success pane --}}
                        <div class="tab-pane fade show active" id="resultSuccessPane" role="tabpanel">
                            <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>#</th>
                                            <th>Team Name</th>
                                            <th>Division</th>
                                            <th>Group</th>
                                            <th>Students Added</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="importSuccessRows">
                                        <tr><td colspan="6" class="text-center text-muted py-3">No data</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
    
                        {{-- Failed pane --}}
                        <div class="tab-pane fade" id="resultFailedPane" role="tabpanel">
                            <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>Row #</th>
                                            <th>Team Name</th>
                                            <th>Reason(s)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="importFailedRows">
                                        <tr><td colspan="3" class="text-center text-muted py-3">No failures</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
    
                    </div>
                </div>
    
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                            onclick="window.location.reload()">
                        Done &amp; Refresh
                    </button>
                </div>
    
            </div>
        </div>
    </div>
    
    
    {{-- ============================================================
         STYLES  (add to your main CSS file if preferred)
         ============================================================ --}}
    <style>
    .import-drop-zone {
        border: 2px dashed #adb5bd;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: border-color .2s, background .2s;
        background: #f8f9fa;
    }
    .import-drop-zone:hover,
    .import-drop-zone.dragover {
        border-color: #0d6efd;
        background: #e9f0ff;
    }
    .import-drop-icon {
        width: 40px;
        height: 40px;
        color: #6c757d;
        margin-bottom: .5rem;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    .import-drop-text {
        margin: 0;
        font-size: .9rem;
        color: #495057;
    }
    .import-drop-hint {
        margin: .25rem 0 0;
        font-size: .78rem;
        color: #6c757d;
    }
    .import-file-info {
        display: flex;
        align-items: center;
        margin-top: .5rem;
        font-size: .875rem;
    }
    </style>
    
    
    {{-- ============================================================
         JAVASCRIPT
         ============================================================ --}}
    <script>
    (function () {
    
        /* ── Drag-and-drop wiring ── */
        const dropZone = document.getElementById('importDropZone');
        const fileInput = document.getElementById('importTeamsFile');
    
        ['dragenter','dragover'].forEach(evt =>
            dropZone.addEventListener(evt, e => { e.preventDefault(); dropZone.classList.add('dragover'); })
        );
        ['dragleave','drop'].forEach(evt =>
            dropZone.addEventListener(evt, e => { e.preventDefault(); dropZone.classList.remove('dragover'); })
        );
        dropZone.addEventListener('drop', e => {
            const files = e.dataTransfer.files;
            if (files.length) setImportFile(files[0]);
        });
    
        fileInput.addEventListener('change', function () {
            if (this.files.length) setImportFile(this.files[0]);
        });
    
        function setImportFile(file) {
            // Validate client-side
            const allowed = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                             'text/csv', 'application/vnd.ms-excel'];
            const extOk   = /\.(xlsx|csv)$/i.test(file.name);
            const maxMB   = 5;
    
            if (!extOk) {
                showImportError('Only .xlsx or .csv files are accepted.');
                return;
            }
            if (file.size > maxMB * 1024 * 1024) {
                showImportError(`File is too large. Max ${maxMB} MB allowed.`);
                return;
            }
    
            // Assign to real input via DataTransfer
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
    
            document.getElementById('importFileName').textContent = file.name;
            document.getElementById('importFileInfo').classList.remove('d-none');
            dropZone.classList.add('d-none');
            if (window.lucide) lucide.createIcons();
        }
    
        window.clearImportFile = function () {
            fileInput.value = '';
            document.getElementById('importFileInfo').classList.add('d-none');
            dropZone.classList.remove('d-none');
        };
    
        function showImportError(msg) {
            alert('⚠️ ' + msg); // swap with toastr/SweetAlert if available
        }
    
        /* ── Submit via XHR so we can show progress & parse JSON result ── */
        window.submitImportTeams = function () {
            if (!fileInput.files.length) {
                showImportError('Please select a file first.');
                return;
            }
    
            const btn      = document.getElementById('importTeamsSubmitBtn');
            const progress = document.getElementById('importProgress');
            const bar      = document.getElementById('importProgressBar');
            const pct      = document.getElementById('importProgressPct');
    
            btn.disabled   = true;
            progress.classList.remove('d-none');
    
            const formData = new FormData(document.getElementById('importTeamsForm'));
    
            const xhr = new XMLHttpRequest();
            xhr.open('POST', document.getElementById('importTeamsForm').action);
    
            // CSRF header (Laravel)
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) xhr.setRequestHeader('X-CSRF-TOKEN', csrfMeta.content);
            xhr.setRequestHeader('Accept', 'application/json');
    
            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    const p = Math.round((e.loaded / e.total) * 80); // upload = 80%
                    bar.style.width = p + '%';
                    pct.textContent  = p + '%';
                }
            };
    
            xhr.onload = function () {
                bar.style.width = '100%';
                pct.textContent  = '100%';
    
                btn.disabled = false;
                progress.classList.add('d-none');
    
                let result;
                try {
                    result = JSON.parse(xhr.responseText);
                } catch (_) {
                    showImportError('Unexpected server response. Please try again.');
                    return;
                }
    
                if (xhr.status === 200 && result) {
                    // Close upload modal
                    bootstrap.Modal.getInstance(document.getElementById('importTeamsModal'))?.hide();
                    // Show result modal
                    showImportResults(result);
                } else {
                    const msg = result?.message || 'Import failed. Please check your file and try again.';
                    showImportError(msg);
                }
            };
    
            xhr.onerror = function () {
                btn.disabled = false;
                progress.classList.add('d-none');
                showImportError('Network error. Please try again.');
            };
    
            xhr.send(formData);
        };
    
        /* ── Populate & show the results modal ── */
        function showImportResults(data) {
            const { summary, imported, failed } = data;
    
            // Summary badges
            const summaryEl = document.getElementById('importResultSummary');
            summaryEl.innerHTML = `
                <span class="badge bg-primary fs-6 px-3 py-2">Total Rows: ${summary.total_rows}</span>
                <span class="badge bg-success fs-6 px-3 py-2">&#10003; Teams Created: ${summary.teams_created}</span>
                <span class="badge bg-info   fs-6 px-3 py-2">&#10003; Teams Existing: ${summary.teams_existing ?? 0}</span>
                <span class="badge bg-warning text-dark fs-6 px-3 py-2">Students Added: ${summary.students_added}</span>
                <span class="badge bg-danger  fs-6 px-3 py-2">&#10007; Failed Rows: ${summary.failed_rows}</span>
            `;
    
            // Success tab badge
            document.getElementById('successCount').textContent = imported.length;
            document.getElementById('failedCount').textContent  = failed.length;
    
            // Populate success table
            const successBody = document.getElementById('importSuccessRows');
            if (imported.length) {
                successBody.innerHTML = imported.map((row, i) => `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${esc(row.team_name)}</td>
                        <td><span class="badge bg-secondary">${esc(row.division)}</span></td>
                        <td>${esc(row.group)}</td>
                        <td>${row.students_added}</td>
                        <td>
                            ${ row.is_new
                                ? '<span class="badge bg-success">New</span>'
                                : '<span class="badge bg-info text-dark">Updated</span>'
                            }
                        </td>
                    </tr>
                `).join('');
            } else {
                successBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">No teams imported.</td></tr>';
            }
    
            // Populate failed table
            const failedBody = document.getElementById('importFailedRows');
            if (failed.length) {
                failedBody.innerHTML = failed.map(row => `
                    <tr>
                        <td>${row.row}</td>
                        <td>${esc(row.team_name ?? '—')}</td>
                        <td>
                            <ul class="mb-0 ps-3" style="font-size:.82rem;">
                                ${row.errors.map(e => `<li>${esc(e)}</li>`).join('')}
                            </ul>
                        </td>
                    </tr>
                `).join('');
            } else {
                failedBody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-3">No failures — clean import!</td></tr>';
            }
    
            // Switch to failed tab automatically if there are failures
            if (failed.length > 0 && imported.length === 0) {
                document.getElementById('resultFailedTab').click();
            }
    
            const resultModal = new bootstrap.Modal(document.getElementById('importTeamsResultModal'));
            resultModal.show();
            if (window.lucide) lucide.createIcons();
        }
    
        function esc(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }
    
    })();
    </script>