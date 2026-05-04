{{-- ═══════════════════════════════════════════════════════════════════════════
     IMPORT MODAL
══════════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="bi bi-file-earmark-arrow-up me-2"></i>Import Roster
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                {{-- Import Form --}}
                <div id="importForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Event <span class="text-danger">*</span></label>
                        <select id="importEventId" class="form-select" required>
                            <option value="">Select Event</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Excel / CSV File <span class="text-danger">*</span></label>
                        <input type="file" id="importFile" class="form-control" accept=".xlsx,.csv" required>
                        <div class="form-text">Accepted: .xlsx, .csv - Max 10 MB</div>
                    </div>

                   {{-- ── Instructions card ── --}}
<div class="alert alert-info d-flex gap-3 align-items-start mb-4" role="alert">
    <i data-lucide="info" style="width:20px;height:20px;flex-shrink:0;margin-top:2px;"></i>
    <div>
        <strong>Roster Import Instructions</strong>

        <ul class="mb-2 mt-2 ps-3" style="font-size:.875rem;line-height:1.7;">
            <li>Row <strong>1</strong> must be the header row (exact column names required).</li>
            <li><strong>Each row = one student</strong> (not team).</li>
            <li>All columns are <strong>required</strong> — empty values will cause import failure.</li>
            <li>If <strong>Organization / Group / Team</strong> does not exist, it will be <strong>automatically created</strong>.</li>
            <li>Duplicate students (same name in same team) will be <strong>skipped</strong>.</li>
            <li>Accepted formats: <code>.xlsx</code> or <code>.csv</code> · Max size: <strong>5 MB</strong></li>
        </ul>

        {{-- Flow explanation --}}
        <div class="bg-light border rounded p-2 mb-2" style="font-size:.8rem;">
            <strong>How System Processes Your File:</strong><br>
            Organization → Group → Team → Student → Roster (auto-linked)
        </div>

        {{-- Coach login explanation --}}
        <div class="bg-warning-subtle border rounded p-2 mb-2" style="font-size:.8rem;">
            <strong>Coach Account:</strong><br>
            If a coach name is provided:
            <ul class="mb-1 ps-3">
                <li>Coach account will be <strong>automatically created</strong></li>
                <li>Email is auto-generated (example: <code>john.smith.coach@steamiq.local</code>)</li>
                <li>Default password: <code>password</code></li>
                <li>Coach can login and change password later</li>
            </ul>
        </div>

        {{-- Column reference --}}
        <div class="table-responsive mt-2">
            <table class="table table-sm table-bordered mb-0" style="font-size:.8rem;">
                <thead class="table-light">
                    <tr>
                        <th>Column</th>
                        <th>Required?</th>
                        <th>Example</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>Name</code></td>
                        <td><span class="badge bg-danger">Required</span></td>
                        <td>Jake Mill</td>
                        <td>Student full name</td>
                    </tr>
                    <tr>
                        <td><code>Age</code></td>
                        <td><span class="badge bg-danger">Required</span></td>
                        <td>12</td>
                        <td>Must be a number</td>
                    </tr>
                    <tr>
                        <td><code>Grade</code></td>
                        <td><span class="badge bg-danger">Required</span></td>
                        <td>7</td>
                        <td>Student grade/class</td>
                    </tr>
                    <tr>
                        <td><code>Shirt Size</code></td>
                        <td><span class="badge bg-danger">Required</span></td>
                        <td>M</td>
                        <td>S, M, L, XL etc</td>
                    </tr>
                    <tr>
                        <td><code>Team</code></td>
                        <td><span class="badge bg-danger">Required</span></td>
                        <td>Team A</td>
                        <td>Team name</td>
                    </tr>
                    <tr>
                        <td><code>Group</code></td>
                        <td><span class="badge bg-danger">Required</span></td>
                        <td>Group 1</td>
                        <td>Group under organization</td>
                    </tr>
                    <tr>
                        <td><code>Coach</code></td>
                        <td><span class="badge bg-danger">Required</span></td>
                        <td>John Smith</td>
                        <td>Coach name (auto account created)</td>
                    </tr>
                    <tr>
                        <td><code>Organization</code></td>
                        <td><span class="badge bg-danger">Required</span></td>
                        <td>ABC School</td>
                        <td>Organization name</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>
                </div>

                {{-- Progress State --}}
                <div id="importProgress" class="d-none text-center py-4">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p class="mb-0 fw-semibold" id="importProgressText">Processing...</p>
                </div>

                {{-- Report --}}
                <div id="importReport" class="d-none">
                    <h6 class="fw-bold mb-3">Import Report</h6>

                    <div class="row g-2 mb-3">
                        <div class="col-3">
                            <div class="card text-center border-secondary">
                                <div class="card-body py-2">
                                    <div class="fs-4 fw-bold" id="reportTotal">—</div>
                                    <div class="small text-muted">Total Rows</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card text-center border-success">
                                <div class="card-body py-2">
                                    <div class="fs-4 fw-bold text-success" id="reportInserted">—</div>
                                    <div class="small text-muted">Inserted</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card text-center border-warning">
                                <div class="card-body py-2">
                                    <div class="fs-4 fw-bold text-warning" id="reportDuplicates">—</div>
                                    <div class="small text-muted">Duplicates</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card text-center border-danger">
                                <div class="card-body py-2">
                                    <div class="fs-4 fw-bold text-danger" id="reportFailed">—</div>
                                    <div class="small text-muted">Failed</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Failed rows detail --}}
                    <div id="reportFailedList" class="d-none">
                        <h6 class="text-danger">Failed Rows</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-danger">
                                    <tr>
                                        <th>Row #</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody id="reportFailedBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer" id="importModalFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="importSubmitBtn">
                    <i data-lucide="import"></i> Import
                </button>
            </div>

        </div>
    </div>
</div>