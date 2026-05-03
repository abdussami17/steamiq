<!-- ================= CREATE EVENT MODAL ================= -->
<div class="modal fade" id="createEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">

            <form method="POST" action="<?php echo e(route('events.store')); ?>">
                <?php echo csrf_field(); ?>

                <div class="modal-header">
                    <h5 class="modal-title">Create New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- ================= BASIC INFO ================= -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Event Name</label>
                            <input name="name" class="form-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Event Type</label>
                            <select id="eventType" name="type" class="form-select" required>
                                <option value="">--Select Type--</option>
                                <option value="esports">STEAM ESports</option>
                                <option value="xr">STEAM XR Sports</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-input" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">--Select Status--</option>
                                <option value="draft">Draft</option>
                                <option value="live">Live</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-input">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-input">
                        </div>
                    </div>
                    <hr>

                    <!-- ================= ESPORTS SECTION ================= -->
                    <div id="esportsSection" style="display:none">
                        <h5 class="mb-3 text-dark fw-bold">Brain Game Settings</h5>
                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="brain_enabled" value="0">
                            <input type="checkbox" class="form-check-input" id="brainToggle" name="brain_enabled" value="1">
                            <label class="form-check-label text-dark">Enable Brain Game</label>
                        </div>
                        <div id="brainFields" class="row g-3" style="display:none">
                            <div class="col-md-4">
                                <label class="form-label">Brain Type</label>
                                <select name="brain_type" class="form-select">
                                    <option value="">--Select Brain Type--</option>
                                    <?php $__currentLoopData = $steamCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stcat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($stcat->name); ?>"><?php echo e($stcat->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Score</label>
                                <input name="brain_score" type="number" class="form-input" placeholder="Score">
                            </div>
                        </div>
                        <hr>
                        <h5 class="mb-3 text-dark fw-bold">Game Settings</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Game</label>
                                <input type="text" required placeholder="Aqua Ball League" name="game" class="form-input">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Players Per Team</label>
                                <input name="players_per_team" required type="number" class="form-input">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Match Rule</label>
                                <select class="form-select" required name="match_rule">
                                    <option value="">--Select Round--</option>
                                    <option value="single_round">Single Round</option>
                                    <option value="best_of_3">Best Of 3</option>
                                    <option value="best_of_5">Best Of 5</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Points Per Win</label>
                                <input name="points_win" required type="number" class="form-input">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Points Per Draw</label>
                                <input name="points_draw" required type="number" class="form-input">
                            </div>
                        </div>
                        <hr>
                        <h5 class="mb-3 text-dark fw-bold">Tournament Setup</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tournament Type</label>
                                <select name="esports_tournament_type" class="form-select" required>
                                    <option value="">--Select Type--</option>
                                    <option value="single_elimination">Single Elimination</option>
                                    <option value="double_elimination">Double Elimination</option>
                                    <option value="round_robin">Round Robin</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Number of Teams</label>
                                <input name="esports_number_of_teams" required type="number" class="form-input">
                            </div>
                        </div>
                    </div>

                    <!-- ================= XR SECTION ================= -->
                    <div id="xrSection" style="display:none">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-dark fw-bold mb-0">C.A.M. ACTIVITIES &amp; MISSION</h5>
                            <button type="button" class="btn btn-primary btn-sm" onclick="addCampActivity()"><i data-lucide="plus"></i> Add Activity</button>
                        </div>

                        <div id="campActivitiesContainer"></div>

                        <hr>
                        <h5 class="mb-3 text-dark fw-bold">Tournament Setup</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tournament Type</label>
                                <select required name="xr_tournament_type" class="form-select">
                                    <option value="">--Select Type--</option>
                                    <option value="single_elimination">Single Elimination</option>
                                    <option value="double_elimination">Double Elimination</option>
                                    <option value="round_robin">Round Robin</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Number of Teams</label>
                                <input required name="xr_number_of_teams" type="number" class="form-input">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Players Per Team</label>
                                <input name="xr_players_per_team" required type="number" class="form-input">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Event</button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- ================= JS ================= -->
<script>
 
document.addEventListener("DOMContentLoaded", function () {

const eventType = document.getElementById("eventType");
const esportsSection = document.getElementById("esportsSection");
const xrSection = document.getElementById("xrSection");
const brainToggle = document.getElementById("brainToggle");
const brainFields = document.getElementById("brainFields");

function toggleSections(type) {

    esportsSection.style.display = "none";
    xrSection.style.display = "none";

    esportsSection.querySelectorAll("input,select").forEach(el => el.disabled = true);
    xrSection.querySelectorAll("input,select").forEach(el => el.disabled = true);

    if (type === "esports") {
        esportsSection.style.display = "block";
        esportsSection.querySelectorAll("input,select").forEach(el => el.disabled = false);
    }

    if (type === "xr") {
        xrSection.style.display = "block";
        xrSection.querySelectorAll("input,select").forEach(el => el.disabled = false);
    }
}

if (eventType) {
    eventType.addEventListener("change", function () {
        toggleSections(this.value);
    });
}

if (brainToggle) {
    brainToggle.addEventListener("change", function () {
        brainFields.style.display = this.checked ? "flex" : "none";
    });
}

});

/* ================================================================
   C.A.M.P ACTIVITIES & MISSION
================================================================ */

const egamingConfig = {
    beach_ballin:   { mode: ["1 vs CPU","1 vs 1","2 vs 2"],         structure: ["Round 1","Round 2","Round 3"] },
    scuba_attack:   { mode: ["Single Player Mode","Tournament Mode"], structure: { "Single Player Mode": ["Time Run 2 mins","First Crash"], "Tournament Mode": ["3 Rounds","2:30 Time","3 Minutes"] } },
    aquaball_clash: { mode: ["1 vs CPU","1 vs 1"],                   structure: ["Standard Match"] },
    ballin_out:     { mode: ["1 vs CPU","1 vs 1"],                   structure: ["Standard Match"] }
};

let campIndex = 0;

/* ---------- helpers to capture / restore row state ---------- */

function captureRowState(div) {
    const state = {};
    div.querySelectorAll('[name]').forEach(el => {
        state[el.name] = el.tagName === 'SELECT' ? el.value : el.value;
    });
    // also capture visual select values not bound to hidden inputs
    const missionSel = div.querySelector('.campMissionSelect');
    const typeSel    = div.querySelector('.campActivityTypeSelect');
    if (missionSel) state.__missionSel = missionSel.value;
    if (typeSel)    state.__typeSel    = typeSel.value;
    return state;
}

function restoreRowState(div, state) {
    // restore mission select first to show correct columns
    const missionSel = div.querySelector('.campMissionSelect');
    if (missionSel && state.__missionSel) {
        missionSel.value = state.__missionSel;
        missionSel.dispatchEvent(new Event('change'));
    }
    // restore activity type select to rebuild dynamic fields
    const typeSel = div.querySelector('.campActivityTypeSelect');
    if (typeSel && state.__typeSel) {
        typeSel.value = state.__typeSel;
        typeSel.dispatchEvent(new Event('change'));
    }
    // restore all named field values
    div.querySelectorAll('[name]').forEach(el => {
        if (state[el.name] !== undefined) el.value = state[el.name];
    });
}

/* ---------- add row ---------- */

function addCampActivity() {
    const i   = campIndex++;
    const div = document.createElement('div');
    div.className        = 'card mb-3 border-secondary camp-row';
    div.dataset.campIndex = i;
    div.innerHTML        = buildRowShell(i);
    document.getElementById('campActivitiesContainer').appendChild(div);
    bindCampRow(div, i);
    if (typeof lucide !== "undefined") {
        lucide.createIcons();
    }
}

function buildRowShell(i) {
    return `
    <div class="card-header border-secondary pb-3 d-flex justify-content-between align-items-center py-2">
        <span class="text-dark fw-semibold camp-row-label">Activity / Mission</span>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-icon btn-view"  title="Save"      onclick="saveCampRow(this)"><i data-lucide="save"></i></button>
            <button type="button" class="btn btn-icon btn-edit"  title="Edit"      onclick="editCampRow(this)"><i data-lucide="pencil"></i></button>
            <button type="button" class="btn btn-icon btn-copy"     title="Duplicate" onclick="duplicateCampRow(this)"><i data-lucide="copy"></i></button>
            <button type="button" class="btn btn-icon btn-delete"   title="Delete"    onclick="deleteCampRow(this)"><i data-lucide="trash-2"></i></button>
        </div>
    </div>
    <div class="card-body camp-row-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Activity / Mission</label>
                <select class="form-select campMissionSelect">
                    <option value="">Select</option>
                    <option value="activity">Activity</option>
                    <option value="mission">Mission</option>
                </select>
                <input type="hidden" name="activities[${i}][activity_or_mission]" class="campMissionHidden">
            </div>
            <div class="col-md-3 campActivityTypeCol" style="display:none">
                <label class="form-label">Activity Type</label>
                <select class="form-select campActivityTypeSelect">
                    <option value="">Select</option>
                    <option value="brain">Brain Game</option>
                    <option value="esports">E-Sports</option>
                    <option value="egaming">E-Gaming</option>
                    <option value="playground">Playground</option>
                </select>
                <input type="hidden" name="activities[${i}][activity_type]" class="campActivityTypeHidden">
            </div>
            <div class="campBadgeCol" style="display:none">
    <div class="row g-3 mt-1">
        <div class="col-md-4">
            <label class="form-label">Badge Name</label>
            <input class="form-input" name="activities[${i}][badge_name]" placeholder="Badge Name">
        </div>
        <div class="col-md-4">
            <label class="form-label">Max Points</label>
            <input type="number" class="form-input" name="activities[${i}][max_score]" min="0" placeholder="Max Points">
        </div>
        <div class="col-md-4">
            <label class="form-label">Point Structure</label>
            <select class="form-select" name="activities[${i}][point_structure]">
                <option value="per_team">Per Team</option>
                <option value="per_player">Per Player</option>
            </select>
        </div>
    </div>
</div>
        </div>
        <div class="campDynamicFields mt-3"></div>
    </div>
    <div class="camp-row-summary px-3 pb-3" style="display:none"></div>`;
}

/* ---------- bind events to a row ---------- */

function bindCampRow(div, i) {
    div.querySelector('.campMissionSelect').addEventListener('change', function () {
        div.querySelector('.campMissionHidden').value              = this.value;
        div.querySelector('.campActivityTypeCol').style.display    = this.value === 'activity' ? '' : 'none';
        div.querySelector('.campBadgeCol').style.display           = this.value === 'mission'  ? '' : 'none';
        div.querySelector('.campDynamicFields').innerHTML          = '';
        div.querySelector('.campActivityTypeSelect').value         = '';
        div.querySelector('.campActivityTypeHidden').value         = '';
        updateRowLabel(div);
    });

    div.querySelector('.campActivityTypeSelect').addEventListener('change', function () {
        div.querySelector('.campActivityTypeHidden').value = this.value;
        div.querySelector('.campDynamicFields').innerHTML  = campFieldsTemplate(this.value, i);
        if (this.value === 'egaming') bindEgaming(div, i);
        updateRowLabel(div);
    });
}

function updateRowLabel(div) {
    const mission  = div.querySelector('.campMissionSelect').value;
    const type     = div.querySelector('.campActivityTypeSelect').value;
    const badge    = div.querySelector('[name$="[badge_name]"]');
    let label      = 'Activity / Mission';
    if (mission === 'mission' && badge && badge.value) label = `Mission: ${badge.value}`;
    else if (mission === 'activity' && type)           label = `Activity: ${type.charAt(0).toUpperCase() + type.slice(1)}`;
    else if (mission)                                  label = mission.charAt(0).toUpperCase() + mission.slice(1);
    div.querySelector('.camp-row-label').textContent = label;
}

/* ---------- action buttons ---------- */

function saveCampRow(btn) {
    const div     = btn.closest('.camp-row');
    const body    = div.querySelector('.camp-row-body');
    const summary = div.querySelector('.camp-row-summary');

    // build a readable summary from current values
    const lines = [];
    div.querySelectorAll('.card-body [name]:not([type="hidden"])').forEach(el => {
        if (!el.name || !el.value) return;
        const label = el.closest('.col-md-2, .col-md-3, .col-md-4')?.querySelector('label')?.textContent?.trim() || el.name;
        lines.push(`<span class="badge bg-secondary me-1">${label}: <strong>${el.value}</strong></span>`);
    });

    summary.innerHTML = lines.length
        ? `<div class="mt-2">${lines.join('')}</div>`
        : `<p class="text-muted mb-0">No data entered.</p>`;

    body.style.display    = 'none';
    summary.style.display = 'block';

    // store snapshot for edit restore
    div.dataset.savedState = JSON.stringify(captureRowState(div));
    div.querySelector('[title="Save"]').disabled = true;
    div.querySelector('[title="Edit"]').disabled = false;
    updateRowLabel(div);
}

function editCampRow(btn) {
    const div     = btn.closest('.camp-row');
    const body    = div.querySelector('.camp-row-body');
    const summary = div.querySelector('.camp-row-summary');

    body.style.display    = '';
    summary.style.display = 'none';

    // restore saved state if available
    if (div.dataset.savedState) {
        restoreRowState(div, JSON.parse(div.dataset.savedState));
    }

    div.querySelector('[title="Save"]').disabled = false;
    div.querySelector('[title="Edit"]').disabled = true;
}

function duplicateCampRow(btn) {
    const srcDiv = btn.closest('.camp-row');
    const state  = captureRowState(srcDiv);

    addCampActivity(); // creates new row with new index

    const allRows = document.querySelectorAll('#campActivitiesContainer .camp-row');
    const newDiv  = allRows[allRows.length - 1];

    restoreRowState(newDiv, state);
    updateRowLabel(newDiv);
}

function deleteCampRow(btn) {
    btn.closest('.camp-row').remove();
    reindexCamp();
}

/* ---------- reindex after delete ---------- */

function reindexCamp() {
    document.querySelectorAll('#campActivitiesContainer .camp-row').forEach((div, i) => {
        div.dataset.campIndex = i;
        div.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/activities\[\d+\]/, `activities[${i}]`);
        });
    });
}

/* ---------- dynamic field templates ---------- */

function campFieldsTemplate(type, i) {
    const score   = `<div class="col-md-2"><label class="form-label">Max Points</label><input type="number" class="form-input" name="activities[${i}][max_score]" min="0"></div>`;
    const pts     = `<div class="col-md-2"><label class="form-label">Point Structure</label><select class="form-select" name="activities[${i}][point_structure]"><option value="per_team">Per Team</option><option value="per_player">Per Player</option></select></div>`;
    const desc    = f => `<div class="col-md-4"><label class="form-label">Short Description</label><input class="form-input" name="activities[${i}][${f}]" placeholder="Description"></div>`;

    const templates = {
        brain: `<div class="row g-3">
            <div class="col-md-3"><label class="form-label">Brain Type</label>
            <select class="form-select" name="activities[${i}][brain_type]">
                <option value="science">Science</option><option value="technology">Technology</option>
                <option value="engineering">Engineering</option><option value="art">Art</option><option value="math">Math</option>
            </select></div>
            ${desc('brain_description')} ${score} ${pts}
        </div>`,

        esports: `<div class="row g-3">
            <div class="col-md-3"><label class="form-label">Esports Type</label>
            <select class="form-select" name="activities[${i}][esports_type]">
                <option value="aquaball_league">AquaBall League</option><option value="mario_kart">Mario Kart</option>
                <option value="rocket_league">Rocket League</option><option value="nba_2k">NBA 2K</option>
            </select></div>
            <div class="col-md-2"><label class="form-label">Players</label>
            <select class="form-select" name="activities[${i}][esports_players]">
                <option value="1v1">1 vs 1</option><option value="2v2">2 vs 2</option>
            </select></div>
            <div class="col-md-2"><label class="form-label">Structure</label>
            <select class="form-select" name="activities[${i}][esports_structure]">
                <option value="best_of_3">Best of 3</option><option value="best_of_5">Best of 5</option><option value="best_of_7">Best of 7</option>
            </select></div>
            ${desc('esports_description')} ${score} ${pts}
        </div>`,

        egaming: `<div class="row g-3">
            <div class="col-md-3"><label class="form-label">E-Gaming Type</label>
            <select class="form-select campEgameType" name="activities[${i}][egaming_type]">
                <option value="">Select</option>
                <option value="beach_ballin">Beach Ballin</option><option value="scuba_attack">Scuba Attack</option>
                <option value="aquaball_clash">Aquaball Clash</option><option value="ballin_out">Ballin Out</option>
            </select></div>
            <div class="col-md-3"><label class="form-label">Mode</label>
            <select class="form-select campEgameMode" name="activities[${i}][egaming_mode]"></select></div>
            <div class="col-md-3"><label class="form-label">Structure</label>
            <select class="form-select campEgameStructure" name="activities[${i}][egaming_structure]"></select></div>
            ${desc('egaming_description')} ${score} ${pts}
        </div>`,

        playground: `<div class="row g-3">
            ${desc('playground_description')} ${score} ${pts}
        </div>`
    };

    return templates[type] || '';
}

/* ---------- egaming cascade ---------- */

function bindEgaming(div, i) {
    const typeEl  = div.querySelector('.campEgameType');
    const modeEl  = div.querySelector('.campEgameMode');
    const structEl = div.querySelector('.campEgameStructure');

    const populateStructures = (game, mode) => {
        const cfg     = egamingConfig[game];
        const structs = Array.isArray(cfg.structure) ? cfg.structure : (cfg.structure[mode] || []);
        structEl.innerHTML = structs.map(s => `<option value="${s}">${s}</option>`).join('');
    };

    const populateModes = game => {
        const cfg = egamingConfig[game];
        modeEl.innerHTML = cfg.mode.map(m => `<option value="${m}">${m}</option>`).join('');
        populateStructures(game, cfg.mode[0]);
    };

    typeEl.addEventListener('change', () => { if (typeEl.value) populateModes(typeEl.value); });
    modeEl.addEventListener('change', () => { if (typeEl.value) populateStructures(typeEl.value, modeEl.value); });
}
</script><?php /**PATH /home/u236413684/domains/voags.com/public_html/steamiq/resources/views/events/modals/create-event.blade.php ENDPATH**/ ?>