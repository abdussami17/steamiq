{{-- ══════════════════════════════════════════
     ASSIGN SCORE MODAL
══════════════════════════════════════════ --}}
<div class="modal fade" id="scoreModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark text-light border-secondary">
            <form id="sc_scoreForm" novalidate>
                @csrf
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold">
                        <i data-lucide="plus-circle" style="width:16px;height:16px;vertical-align:-2px;margin-right:6px;color:#58a6ff;"></i>
                        Assign Points
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    {{-- Alert area --}}
                    <div id="sc_alert" class="alert alert-danger d-none py-2" role="alert"></div>

                    <div class="row g-3">

                        {{-- Event --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Event <span class="text-danger">*</span></label>
                            <select id="sc_eventSelect" class="form-select form-select-sm bg-dark text-light border-secondary" required>
                                <option value="">-- Select Event --</option>
                                @foreach ($events as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Organization --}}
                        <div class="col-md-4 d-none" id="sc_organizationDiv">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Organization <span class="text-danger">*</span></label>
                            <select id="sc_organizationSelect" class="form-select form-select-sm bg-dark text-light border-secondary"></select>
                        </div>

                        {{-- Group --}}
                        <div class="col-md-4 d-none" id="sc_groupDiv">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Group <span class="text-danger">*</span></label>
                            <select id="sc_groupSelect" class="form-select form-select-sm bg-dark text-light border-secondary"></select>
                        </div>

                        {{-- SubGroup --}}
                        <div class="col-md-4 d-none" id="sc_subgroupDiv">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Sub Group</label>
                            <select id="sc_subgroupSelect" class="form-select form-select-sm bg-dark text-light border-secondary"></select>
                        </div>

                        {{-- Assign To --}}
                        <div class="col-md-4 d-none" id="sc_typeDiv">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Assign To <span class="text-danger">*</span></label>
                            <select id="sc_entityType" class="form-select form-select-sm bg-dark text-light border-secondary">
                                <option value="">-- Select --</option>
                                <option value="student">Player</option>
                                <option value="team">Team</option>
                            </select>
                        </div>

                        {{-- Team --}}
                        <div class="col-md-4 d-none" id="sc_teamDiv">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Team <span class="text-danger">*</span></label>
                            <select id="sc_teamSelect" class="form-select form-select-sm bg-dark text-light border-secondary"></select>
                        </div>

                        {{-- Player (only after team selected, in player mode) --}}
                        <div class="col-md-4 d-none" id="sc_studentDiv">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Player <span class="text-danger">*</span></label>
                            <select id="sc_studentSelect" class="form-select form-select-sm bg-dark text-light border-secondary"></select>
                        </div>

                        {{-- Activity --}}
                        <div class="col-md-4 d-none" id="sc_activityDiv">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Activity <span class="text-danger">*</span></label>
                            <select id="sc_activitySelect" class="form-select form-select-sm bg-dark text-light border-secondary"></select>
                        </div>

                        {{-- Points --}}
                        <div class="col-md-4 d-none" id="sc_pointsDiv">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">
                                Points <span class="text-danger">*</span>
                                <span id="sc_maxHint" class="ms-1 text-warning" style="font-size:11px;letter-spacing:0;text-transform:none;font-weight:400;"></span>
                            </label>
                            <input type="number" min="0" id="sc_pointsInput"
                                   class="form-control form-control-sm bg-dark text-light border-secondary"
                                   placeholder="Enter points">
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary fw-bold" id="sc_submitBtn" disabled>
                        <i data-lucide="save" style="width:13px;height:13px;vertical-align:-1px;margin-right:4px;"></i>
                        Save Points
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════
     BONUS POINTS MODAL
══════════════════════════════════════════ --}}
<div class="modal fade" id="bonusModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0" style="background:#0f1318;">
            <form id="bonusForm" novalidate>
                @csrf
                <div class="modal-header" style="background:#1a1000;border-bottom:1px solid #7a5a00;">
                    <h5 class="modal-title fw-bold" style="color:#f5c518;font-family:'Barlow Condensed',sans-serif;letter-spacing:.05em;">
                        <i data-lucide="zap" style="width:18px;height:18px;vertical-align:-3px;margin-right:6px;"></i>
                        Assign Bonus Points
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="background:#0f1318;">
                    <div id="bonus_alert" class="alert alert-danger d-none py-2"></div>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Event <span class="text-danger">*</span></label>
                            <select id="bonus_eventSelect" class="form-select form-select-sm border-secondary" style="background:#1e2535;color:#e6edf3;" required>
                                <option value="">-- Select Event --</option>
                                @foreach ($events as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Assign Bonus To <span class="text-danger">*</span></label>
                            <select id="bonus_targetType" class="form-select form-select-sm border-secondary" style="background:#1e2535;color:#e6edf3;" required>
                                <option value="">-- Select Scope --</option>
                                <option value="organization">Entire Organization</option>
                                <option value="group">Group</option>
                                <option value="subgroup">Sub Group</option>
                                <option value="team">Team</option>
                                <option value="player"> Player</option>
                            </select>
                        </div>

                        <div class="col-md-6 d-none" id="bonus_orgDiv">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Organization</label>
                            <select id="bonus_orgSelect" class="form-select form-select-sm border-secondary" style="background:#1e2535;color:#e6edf3;"></select>
                        </div>

                        <div class="col-md-6 d-none" id="bonus_groupDiv">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Group</label>
                            <select id="bonus_groupSelect" class="form-select form-select-sm border-secondary" style="background:#1e2535;color:#e6edf3;"></select>
                        </div>

                        <div class="col-md-6 d-none" id="bonus_subgroupDiv">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Sub Group</label>
                            <select id="bonus_subgroupSelect" class="form-select form-select-sm border-secondary" style="background:#1e2535;color:#e6edf3;"></select>
                        </div>

                        <div class="col-md-6 d-none" id="bonus_teamDiv">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Team</label>
                            <select id="bonus_teamSelect" class="form-select form-select-sm border-secondary" style="background:#1e2535;color:#e6edf3;"></select>
                        </div>

                        <div class="col-md-6 d-none" id="bonus_playerDiv">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Player</label>
                            <select id="bonus_playerSelect" class="form-select form-select-sm border-secondary" style="background:#1e2535;color:#e6edf3;"></select>
                        </div>

                        <div class="col-md-6 d-none" id="bonus_pointsDiv">
                            <label class="form-label fw-semibold text-secondary" style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;">Bonus Points <span class="text-danger">*</span></label>
                            <input type="number" min="1" id="bonus_pointsInput"
                                   class="form-control form-control-sm border-secondary"
                                   style="background:#1e2535;color:#f5c518;font-weight:700;font-size:16px;"
                                   placeholder="e.g. 50">
                        </div>

                        <div class="col-12 d-none" id="bonus_summaryDiv">
                            <div class="p-3 rounded" style="background:#1a1000;border:1px solid #7a5a00;">
                                <div style="font-family:'Barlow Condensed',sans-serif;font-size:14px;color:#f5c518;font-weight:700;" id="bonus_summaryText"></div>
                                <div style="font-size:11px;color:#8b949e;margin-top:4px;">Bonus is added on top of existing scores. Capped at each activity's max score.</div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer" style="background:#0f1318;border-top:1px solid #2a3040;">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="bonus_submitBtn" class="btn btn-sm btn-warning fw-bold" disabled>
                        <i data-lucide="zap" style="width:13px;height:13px;vertical-align:-1px;margin-right:4px;"></i>
                        Assign Bonus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════
     BULK EDIT MODAL
══════════════════════════════════════════ --}}
<div class="modal fade" id="bulkEditModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0" style="background:#0f1318;">
            <div class="modal-header" style="background:#0d1e3a;border-bottom:1px solid #1e3a6e;">
                <h5 class="modal-title fw-bold" style="color:#79c0ff;font-family:'Barlow Condensed',sans-serif;letter-spacing:.05em;">
                    <i data-lucide="edit-3" style="width:16px;height:16px;vertical-align:-2px;margin-right:6px;"></i>
                    Bulk Edit Scores
                    <span id="bulkEditCount" class="badge ms-2" style="background:#58a6ff;color:#000;font-size:12px;"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" style="background:#0f1318;">
                <div style="overflow-y:auto;max-height:420px;">
                    <table class="table table-sm mb-0" style="border-collapse:separate;border-spacing:0;">
                        <thead style="position:sticky;top:0;z-index:2;">
                            <tr style="background:#1e2535;">
                                <th style="width:36px;padding:8px 12px;border-bottom:2px solid #30507e;">
                                    <input type="checkbox" id="bulkCheckAll" style="accent-color:#58a6ff;">
                                </th>
                                <th style="padding:8px 12px;border-bottom:2px solid #30507e;color:#8b949e;font-size:10px;letter-spacing:.08em;text-transform:uppercase;">Entity</th>
                                <th style="padding:8px 12px;border-bottom:2px solid #30507e;color:#8b949e;font-size:10px;letter-spacing:.08em;text-transform:uppercase;">Activity</th>
                                <th style="padding:8px 12px;border-bottom:2px solid #30507e;color:#8b949e;font-size:10px;letter-spacing:.08em;text-transform:uppercase;text-align:center;">Current</th>
                                <th style="padding:8px 12px;border-bottom:2px solid #30507e;color:#58a6ff;font-size:10px;letter-spacing:.08em;text-transform:uppercase;text-align:center;">New Points</th>
                                <th style="padding:8px 12px;border-bottom:2px solid #30507e;color:#8b949e;font-size:10px;letter-spacing:.08em;text-transform:uppercase;text-align:center;">Max</th>
                            </tr>
                        </thead>
                        <tbody id="bulkEditTbody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="background:#0d1318;border-top:1px solid #1e2535;">
                <span id="bulkValidationMsg" class="text-danger me-auto" style="font-size:12px;"></span>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="bulkSaveConfirmBtn" class="btn btn-sm btn-primary fw-bold">
                    <i data-lucide="save" style="width:13px;height:13px;vertical-align:-1px;margin-right:4px;"></i>
                    Save All
                </button>
            </div>
        </div>
    </div>
</div>