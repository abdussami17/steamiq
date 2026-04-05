<script>
    const editEventModal          = new bootstrap.Modal(document.getElementById('editEventModal'));
    const editEventForm           = document.getElementById('editEventForm');
    const editTypeEl              = document.getElementById('editType');
    const editEsportsSection      = document.getElementById('editEsportsSection');
    const editXrSection           = document.getElementById('editXrSection');
    const editBrainToggle         = document.getElementById('editBrainToggle');
    const editBrainFields         = document.getElementById('editBrainFields');
    const editCampContainer       = document.getElementById('editCampActivitiesContainer');
    
    let editCampIndex = 0;
    
    /* ── section toggle ── */
    function editToggleSections(type) {
        editEsportsSection.style.display = type === 'esports' ? 'block' : 'none';
        editXrSection.style.display      = type === 'xr'      ? 'block' : 'none';
    }
    
    editTypeEl.addEventListener('change', () => editToggleSections(editTypeEl.value));
    editBrainToggle.addEventListener('change', () => {
        editBrainFields.style.display = editBrainToggle.checked ? 'flex' : 'none';
    });
    
    /* ── open modal & prefill ── */
    function openEditEventModal(eventId) {
        fetch(`/events/${eventId}/edit`)
            .then(r => r.json())
            .then(res => {
                if (!res.success) { alert(res.message || 'Failed to fetch event.'); return; }
    
                const d = res.data;
                editCampIndex = 0;
                editCampContainer.innerHTML = '';
    
                document.getElementById('editEventId').value   = d.id;
                document.getElementById('editName').value      = d.name;
                editTypeEl.value                               = d.type;
                document.getElementById('editLocation').value  = d.location;
                document.getElementById('editStartDate').value = d.start_date ?? '';
                document.getElementById('editEndDate').value   = d.end_date   ?? '';
                document.getElementById('editStatus').value    = d.status;
    
                editToggleSections(d.type);
    
                const ts = d.tournament_setting;
                if (ts) {
                    editBrainToggle.checked                             = ts.brain_enabled == 1;
                    editBrainFields.style.display                       = editBrainToggle.checked ? 'flex' : 'none';
                    document.getElementById('editBrainType').value      = ts.brain_type       ?? '';
                    document.getElementById('editBrainScore').value     = ts.brain_score      ?? '';
                    document.getElementById('editGame').value           = ts.game             ?? '';
                    document.getElementById('editPlayersPerTeam').value = ts.players_per_team ?? '';
                    document.getElementById('editMatchRule').value      = ts.match_rule       ?? '';
                    document.getElementById('editPointsWin').value      = ts.points_win       ?? '';
                    document.getElementById('editPointsDraw').value     = ts.points_draw      ?? '';
    
                  // Prefill tournament fields correctly based on event type
if (d.type === 'esports') {
    document.getElementById('editTournamentType').value  = ts.tournament_type ?? '';
    document.getElementById('editNumberOfTeams').value   = ts.number_of_teams ?? '';
} else if (d.type === 'xr') {
    document.getElementById('editXrTournamentType').value = ts.tournament_type ?? '';
    document.getElementById('editXrNumberOfTeams').value  = ts.number_of_teams ?? '';
    document.getElementById('editXrPlayersPerTeam').value = ts.players_per_team ?? '';
}
                }
    
                if (d.type === 'xr' && d.activities?.length) {
                    d.activities.forEach(act => addEditCampActivity(act));
                }
    
                editEventModal.show();
            })
            .catch(err => { console.error(err); alert('Error fetching event.'); });
    }
    
    /* ── add CAMP row (with optional prefill data) ── */
    function addEditCampActivity(prefill = null) {
        const i   = editCampIndex++;
        const div = document.createElement('div');
        div.className         = 'card mb-3 border-secondary camp-row';
        div.dataset.campIndex = i;
        div.innerHTML         = buildEditRowShell(i);
        editCampContainer.appendChild(div);
        bindEditCampRow(div, i);
    
        if (prefill) prefillEditRow(div, i, prefill);
        if (typeof lucide !== "undefined") {
        lucide.createIcons();
    }
    }
    
    function buildEditRowShell(i) {
        return `
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <span class="text-dark fw-semibold camp-row-label">Activity Row</span>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-icon btn-view"  title="Save"      onclick="saveEditCampRow(this)"><i data-lucide="save"></i></button>
            <button type="button" class="btn btn-icon btn-edit"  title="Edit"      onclick="editEditCampRow(this)"><i data-lucide="pencil"></i></button>
            <button type="button" class="btn btn-icon btn-copy"     title="Duplicate" onclick="duplicateEditCampRow(this)"><i data-lucide="copy"></i></button>
            <button type="button" class="btn btn-icon btn-delete"   title="Delete"    onclick="deleteEditCampRow(this)"><i data-lucide="trash-2"></i></button>
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
    
    /* ── bind change events ── */
    function bindEditCampRow(div, i) {
        div.querySelector('.campMissionSelect').addEventListener('change', function () {
            div.querySelector('.campMissionHidden').value           = this.value;
            div.querySelector('.campActivityTypeCol').style.display = this.value === 'activity' ? '' : 'none';
            div.querySelector('.campBadgeCol').style.display        = this.value === 'mission'  ? '' : 'none';
            div.querySelector('.campDynamicFields').innerHTML       = '';
            div.querySelector('.campActivityTypeSelect').value      = '';
            div.querySelector('.campActivityTypeHidden').value      = '';
            updateEditRowLabel(div);
        });
    
        div.querySelector('.campActivityTypeSelect').addEventListener('change', function () {
            div.querySelector('.campActivityTypeHidden').value = this.value;
            div.querySelector('.campDynamicFields').innerHTML  = editCampFieldsTemplate(this.value, i);
            if (this.value === 'egaming') bindEditEgaming(div);
            updateEditRowLabel(div);
        });
    }
    
    /* ── prefill existing activity data into a row ── */
    function prefillEditRow(div, i, act) {
        const missionSel = div.querySelector('.campMissionSelect');
        missionSel.value = act.activity_or_mission ?? '';
        missionSel.dispatchEvent(new Event('change'));
    
        if (act.activity_or_mission === 'activity') {
            const typeSel = div.querySelector('.campActivityTypeSelect');
            typeSel.value = act.activity_type ?? '';
            typeSel.dispatchEvent(new Event('change'));
        }
    
        if (act.activity_or_mission === 'mission') {
            const badge = div.querySelector('[name$="[badge_name]"]');
            if (badge) badge.value = act.badge_name ?? '';
        }
    
        // fill all named fields that exist in the dynamic area
        const fieldMap = {
            brain_type: act.brain_type, brain_description: act.brain_description,
            esports_type: act.esports_type, esports_players: act.esports_players,
            esports_structure: act.esports_structure, esports_description: act.esports_description,
            egaming_type: act.egaming_type, egaming_mode: act.egaming_mode,
            egaming_structure: act.egaming_structure, egaming_description: act.egaming_description,
            playground_description: act.playground_description,
            max_score: act.max_score, point_structure: act.point_structure,
        };
    
        setTimeout(() => {
    Object.entries(fieldMap).forEach(([key, val]) => {
        console.log("Prefill:", key, val)
        if (val == null) return;

        const elements = div.querySelectorAll(`[name="activities[${i}][${key}]"]`);

        elements.forEach(el => {
            el.value = val;
        });

        // trigger change for dependent fields
        if (key === 'egaming_type') {
            elements.forEach(el => el.dispatchEvent(new Event('change')));
        }
        if (key === 'egaming_mode') {
            elements.forEach(el => el.dispatchEvent(new Event('change')));
        }
    });
}, 100);
    
        updateEditRowLabel(div);
    }
    
    /* ── row label ── */
    function updateEditRowLabel(div) {
        const mission = div.querySelector('.campMissionSelect').value;
        const type    = div.querySelector('.campActivityTypeSelect').value;
        const badge   = div.querySelector('[name$="[badge_name]"]');
        let label     = 'Activity Row';
        if (mission === 'mission' && badge?.value) label = `Mission: ${badge.value}`;
        else if (mission === 'activity' && type)   label = `Activity: ${type.charAt(0).toUpperCase() + type.slice(1)}`;
        else if (mission)                          label = mission.charAt(0).toUpperCase() + mission.slice(1);
        div.querySelector('.camp-row-label').textContent = label;
    }
    
    /* ── action buttons ── */
    function saveEditCampRow(btn) {
        const div     = btn.closest('.camp-row');
        const body    = div.querySelector('.camp-row-body');
        const summary = div.querySelector('.camp-row-summary');
        const lines   = [];
    
        div.querySelectorAll('.card-body [name]:not([type="hidden"])').forEach(el => {
            if (!el.value) return;
            const label = el.closest('[class*="col-"]')?.querySelector('label')?.textContent?.trim() || el.name;
            lines.push(`<span class="badge bg-secondary me-1">${label}: <strong>${el.value}</strong></span>`);
        });
    
        summary.innerHTML     = lines.length ? `<div class="mt-2">${lines.join('')}</div>` : '<p class="text-muted mb-0">No data entered.</p>';
        body.style.display    = 'none';
        summary.style.display = 'block';
        div.dataset.savedState = JSON.stringify(captureEditRowState(div));
        btn.disabled           = true;
        div.querySelector('[onclick*="editEditCampRow"]').disabled = false;
        updateEditRowLabel(div);
    }
    
    function editEditCampRow(btn) {
        const div     = btn.closest('.camp-row');
        const body    = div.querySelector('.camp-row-body');
        const summary = div.querySelector('.camp-row-summary');
    
        body.style.display    = '';
        summary.style.display = 'none';
    
        if (div.dataset.savedState) {
            restoreEditRowState(div, JSON.parse(div.dataset.savedState));
        }
    
        div.querySelector('[onclick*="saveEditCampRow"]').disabled = false;
        btn.disabled = true;
    }
    
    function duplicateEditCampRow(btn) {
        const state = captureEditRowState(btn.closest('.camp-row'));
        addEditCampActivity();
        const rows  = editCampContainer.querySelectorAll('.camp-row');
        const newDiv = rows[rows.length - 1];
        const newI   = parseInt(newDiv.dataset.campIndex);
        restoreEditRowState(newDiv, state, newI);
        updateEditRowLabel(newDiv);
    }
    
    function deleteEditCampRow(btn) {
        btn.closest('.camp-row').remove();
        reindexEditCamp();
    }
    
    /* ── state capture / restore ── */
    function captureEditRowState(div) {
        const state = { __missionSel: div.querySelector('.campMissionSelect').value, __typeSel: div.querySelector('.campActivityTypeSelect').value };
        div.querySelectorAll('[name]').forEach(el => { state[el.name] = el.value; });
        return state;
    }
    
    function restoreEditRowState(div, state, newI = null) {
        const missionSel = div.querySelector('.campMissionSelect');
        if (state.__missionSel) { missionSel.value = state.__missionSel; missionSel.dispatchEvent(new Event('change')); }
        const typeSel = div.querySelector('.campActivityTypeSelect');
        if (state.__typeSel) { typeSel.value = state.__typeSel; typeSel.dispatchEvent(new Event('change')); }
    
        div.querySelectorAll('[name]').forEach(el => {
            const lookupName = newI !== null ? el.name : el.name;
            const srcName    = newI !== null ? el.name.replace(/activities\[\d+\]/, `activities[${newI}]`) : el.name;
            const val        = state[el.name] ?? state[srcName];
            if (val !== undefined) el.value = val;
        });
    }
    
    /* ── reindex after delete ── */
    function reindexEditCamp() {
        editCampContainer.querySelectorAll('.camp-row').forEach((div, i) => {
            div.dataset.campIndex = i;
            div.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/activities\[\d+\]/, `activities[${i}]`);
            });
        });
    }
    
    /* ── field templates (same logic as create) ── */
    function editCampFieldsTemplate(type, i) {
        const score = `<div class="col-md-2"><label class="form-label">Max Points</label><input type="number" class="form-input" name="activities[${i}][max_score]" min="0"></div>`;
        const pts   = `<div class="col-md-2"><label class="form-label">Point Structure</label><select class="form-select" name="activities[${i}][point_structure]"><option value="per_team">Per Team</option><option value="per_player">Per Player</option></select></div>`;
        const desc  = f => `<div class="col-md-4"><label class="form-label">Short Description</label><input class="form-input" name="activities[${i}][${f}]" placeholder="Description"></div>`;
    
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
                    <option value="aquaball_league">Aquaball League</option><option value="mario_kart">Mario Kart</option>
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
    
    /* ── egaming cascade ── */
    function bindEditEgaming(div) {
        const typeEl   = div.querySelector('.campEgameType');
        const modeEl   = div.querySelector('.campEgameMode');
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
    
    /* ── form submit ── */
    editEventForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const eventId  = document.getElementById('editEventId').value;
        const formData = new FormData(editEventForm);
    
        fetch(`/events/${eventId}/update`, {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {

const status = document.getElementById('editStatus').value;

if (status === 'closed') {
    openWinnerModal(eventId);
} else {
    location.reload();
}
}
            else             { alert(res.message || 'Failed to update event.'); }
        })
        .catch(err => console.error(err));
    });
    </script>