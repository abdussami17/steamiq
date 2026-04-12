<div class="modal fade" id="scoreModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form id="sc_scoreForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Points</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Event</label>
                            <select id="sc_eventSelect" class="form-select" required>
                                <option value="">-- Select Event --</option>
                        
                                @foreach ($events as $event)
                                    @if(strtolower(trim($event->status)) !== 'closed')
                                        <option value="{{ $event->id }}">
                                            {{ $event->name }}
                                        </option>
                                    @endif
                                @endforeach
                        
                            </select>
                        </div>

                        <div class="mb-3 col-md-4 d-none" id="sc_organizationDiv">
                            <label class="form-label">Organization</label>
                            <select id="sc_organizationSelect" class="form-select"></select>
                        </div>

                        <div class="mb-3 col-md-4 d-none" id="sc_groupDiv">
                            <label class="form-label">Group</label>
                            <select id="sc_groupSelect" class="form-select"></select>
                        </div>

                        <div class="mb-3 col-md-4 d-none" id="sc_subgroupDiv">
                            <label class="form-label">Sub Group</label>
                            <select id="sc_subgroupSelect" class="form-select"></select>
                        </div>

                        <div class="mb-3 col-md-4 d-none" id="sc_typeDiv">
                            <label class="form-label">Assign To</label>
                            <select id="sc_entityType" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="student">Player</option>
                                <option value="team">Team</option>
                            </select>
                        </div>

                        <div class="mb-3 col-md-4 d-none" id="sc_studentDiv">
                            <label class="form-label">Player</label>
                            <select id="sc_studentSelect" class="form-select"></select>
                        </div>

                        <div class="mb-3 col-md-4 d-none" id="sc_teamDiv">
                            <label class="form-label">Team</label>
                            <select id="sc_teamSelect" class="form-select"></select>
                        </div>

                        <div class="mb-3 col-md-4 d-none" id="sc_activityDiv">
                            <label class="form-label">Activity</label>
                            <select id="sc_activitySelect" class="form-select"></select>
                        </div>

                        <div class="mb-3 col-md-4 d-none" id="sc_pointsDiv">
                            <label class="form-label">Points</label>
                            <input type="number" id="sc_pointsInput" class="form-input"
                                placeholder="Enter points">
                                <div class="form-text text-muted" id="maxPointsHint"></div>
                                <div class="invalid-feedback" id="pointsError"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="sc_submitBtn" disabled>Save Points</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sc_eventSelect = document.getElementById('sc_eventSelect');
        const sc_organizationDiv = document.getElementById('sc_organizationDiv');
        const sc_groupDiv = document.getElementById('sc_groupDiv');
        const sc_subgroupDiv = document.getElementById('sc_subgroupDiv');
        const sc_typeDiv = document.getElementById('sc_typeDiv');
        const sc_organizationSelect = document.getElementById('sc_organizationSelect');
        const sc_groupSelect = document.getElementById('sc_groupSelect');
        const sc_subgroupSelect = document.getElementById('sc_subgroupSelect');
        const sc_entityType = document.getElementById('sc_entityType');
        const sc_studentDiv = document.getElementById('sc_studentDiv');
        const sc_teamDiv = document.getElementById('sc_teamDiv');
        const sc_studentSelect = document.getElementById('sc_studentSelect');
        const sc_teamSelect = document.getElementById('sc_teamSelect');
        const sc_activityDiv = document.getElementById('sc_activityDiv');
        const sc_activitySelect = document.getElementById('sc_activitySelect');
        const sc_pointsDiv = document.getElementById('sc_pointsDiv');
        const sc_submitBtn = document.getElementById('sc_submitBtn');
        const sc_pointsInput = document.getElementById('sc_pointsInput');

        let currentEvent = '';
        let selectedEntityType = '';
        let maxPoints = 0;
        let activitiesCache = [];

        function resetAll() {
            [sc_organizationDiv, sc_groupDiv, sc_subgroupDiv, sc_typeDiv, sc_studentDiv, sc_teamDiv,
                sc_activityDiv, sc_pointsDiv
            ]
            .forEach(el => el.classList.add('d-none'));
            [sc_organizationSelect, sc_groupSelect, sc_subgroupSelect, sc_studentSelect, sc_teamSelect,
                sc_activitySelect
            ]
            .forEach(el => el.innerHTML = '');
            sc_submitBtn.disabled = true;
            selectedEntityType = '';
            maxPoints = 0;
    activitiesCache = [];
    document.getElementById('maxPointsHint').innerText = '';
    document.getElementById('pointsError').innerText = '';
        }

        // ===== STEP 1: EVENT SELECTION =====
        sc_eventSelect.addEventListener('change', function() {
            currentEvent = this.value;
            resetAll();

            if (!currentEvent) return;

            sc_organizationDiv.classList.remove('d-none');

            fetch(`/events/${currentEvent}/organizations`)
                .then(r => r.json())
                .then(data => {
                    sc_organizationSelect.innerHTML =
                        '<option value="">-- Select Organization --</option>';
                    data.forEach(o => sc_organizationSelect.innerHTML +=
                        `<option value="${o.id}">${o.name}</option>`);
                })
                .catch(err => console.error('Error fetching organizations:', err));
        });

        // ===== STEP 2: ORGANIZATION SELECTION =====
        sc_organizationSelect.addEventListener('change', function() {
            [sc_groupDiv, sc_subgroupDiv, sc_typeDiv, sc_studentDiv, sc_teamDiv, sc_activityDiv,
                sc_pointsDiv
            ]
            .forEach(el => el.classList.add('d-none'));
            [sc_groupSelect, sc_subgroupSelect, sc_studentSelect, sc_teamSelect, sc_activitySelect]
            .forEach(el => el.innerHTML = '');

            if (!this.value) return;

            sc_groupDiv.classList.remove('d-none');

            fetch(`/organizations/${this.value}/groups`)
                .then(r => r.json())
                .then(data => {
                    sc_groupSelect.innerHTML = '<option value="">-- Select Group --</option>';
                    data.forEach(g => sc_groupSelect.innerHTML +=
                        `<option value="${g.id}">${g.group_name}</option>`);
                })
                .catch(err => console.error('Error fetching groups:', err));
        });

        // ===== STEP 3: GROUP SELECTION =====
        sc_groupSelect.addEventListener('change', function() {
            [sc_subgroupDiv, sc_typeDiv, sc_studentDiv, sc_teamDiv, sc_activityDiv, sc_pointsDiv]
            .forEach(el => el.classList.add('d-none'));
            [sc_subgroupSelect, sc_studentSelect, sc_teamSelect, sc_activitySelect]
            .forEach(el => el.innerHTML = '');

            if (!this.value) return;

            fetch(`/groups/${this.value}/subgroups`)
                .then(r => r.json())
                .then(data => {
                    // Always show entity type selector
                    sc_typeDiv.classList.remove('d-none');

                    // Populate subgroup dropdown if any exist
                    if (data.length > 0) {
                        sc_subgroupDiv.classList.remove('d-none');
                        sc_subgroupSelect.innerHTML =
                            '<option value="">-- Select SubGroup --</option>';
                        data.forEach(s => sc_subgroupSelect.innerHTML +=
                            `<option value="${s.id}">${s.name}</option>`);
                    }

                 
                })
                .catch(err => console.error('Error fetching subgroups:', err));
        });

        function loadTeams(groupId, subGroupId = '') {
            const params = new URLSearchParams({
                group_id: groupId
            });
            if (subGroupId) params.append('sub_group_id', subGroupId);

            sc_teamDiv.classList.remove('d-none');
            fetch(`/teams?${params.toString()}`)
                .then(r => r.json())
                .then(data => {
                    sc_teamSelect.innerHTML = '<option value="">-- Select Team --</option>';
                    data.forEach(t => sc_teamSelect.innerHTML +=
                        `<option value="${t.id}">${t.name || t.team_name}</option>`);
                })
                .catch(err => console.error('Error fetching teams:', err));
        }
        // SUBGROUP selection now filters teams by subgroup
        sc_subgroupSelect.addEventListener('change', function() {
            [sc_teamSelect, sc_studentSelect].forEach(el => el.innerHTML = '');
            if (this.value) {
                loadTeams(sc_groupSelect.value, this.value);
            } else {
                // Show teams directly under group if no subgroup selected
                loadTeams(sc_groupSelect.value, '');
            }
        });

        // ===== STEP 5: ENTITY TYPE SELECTION (Student vs Team) =====
        sc_entityType.addEventListener('change', function() {
            selectedEntityType = this.value;
            [sc_studentDiv, sc_teamDiv, sc_activityDiv, sc_pointsDiv]
            .forEach(el => el.classList.add('d-none'));
            [sc_studentSelect, sc_teamSelect, sc_activitySelect]
            .forEach(el => el.innerHTML = '');

            if (!selectedEntityType) return;

            const groupId = sc_groupSelect.value;
            const subGroupId = sc_subgroupSelect.value;
            const params = new URLSearchParams({
                group_id: groupId,
                sub_group_id: subGroupId
            });

            // Always load the team dropdown first
            sc_teamDiv.classList.remove('d-none');
            fetch(`/teams?${params.toString()}`)
                .then(r => r.json())
                .then(data => {
                    sc_teamSelect.innerHTML = '<option value="">-- Select Team --</option>';
                    data.forEach(t => sc_teamSelect.innerHTML +=
                        `<option value="${t.id}">${t.name || t.team_name}</option>`);
                })
                .catch(err => console.error('Error fetching teams:', err));
        });

        // ===== STEP 6: STUDENT/PLAYER SELECTION =====
        sc_studentSelect.addEventListener('change', function() {
            [sc_activityDiv, sc_pointsDiv].forEach(el => el.classList.add('d-none'));
            sc_activitySelect.innerHTML = '';

            if (!this.value) return;

            loadActivities();
        });

        // ===== STEP 7: TEAM SELECTION =====
        sc_teamSelect.addEventListener('change', function() {
            [sc_studentDiv, sc_activityDiv, sc_pointsDiv].forEach(el => el.classList.add('d-none'));
            [sc_studentSelect, sc_activitySelect].forEach(el => el.innerHTML = '');

            if (!this.value) return;

            if (selectedEntityType === 'student') {
                // Show players belonging to this team
                sc_studentDiv.classList.remove('d-none');
                fetch(`/team/${this.value}/students`)
                    .then(r => r.json())
                    .then(data => {
                        sc_studentSelect.innerHTML =
                        '<option value="">-- Select Player --</option>';
                        data.forEach(s => sc_studentSelect.innerHTML +=
                            `<option value="${s.id}">${s.name}</option>`);
                    })
                    .catch(err => console.error('Error fetching players:', err));
            } else {
                // Team selected — go straight to activities
                loadActivities();
            }
        });

        // ===== LOAD ACTIVITIES =====
        function loadActivities() {
    sc_activityDiv.classList.remove('d-none');

    fetch(`/api/events/${currentEvent}/activities`)
        .then(r => r.json())
        .then(data => {
            activitiesCache = data;
            sc_activitySelect.innerHTML = '<option value="">-- Select Activity --</option>';

            if (!selectedEntityType) return;

            data.forEach(a => {
                const structure = (a.point_structure || '').toLowerCase().trim();

                if (
                    (selectedEntityType === 'team' && structure !== 'per_team') ||
                    (selectedEntityType === 'student' && structure !== 'per_player')
                ) {
                    return;
                }

                let name = a.badge_name || a.brain_type || a.esports_type || a.egaming_type || a.name || 'Playground';
                name = name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

                let desc = ['brain'].includes((a.activity_type || '').toLowerCase())
                    ? a.brain_description || ''
                    : a.egaming_description || '';

                sc_activitySelect.innerHTML +=
                    `<option value="${a.id}">${desc ? name + ' - ' + desc : name}</option>`;
            });

            if (sc_activitySelect.options.length === 1) {
                sc_activitySelect.innerHTML += `<option value="" disabled>No matching activities</option>`;
            }
        })
        .catch(err => console.error('Error fetching activities:', err));
}

        // ===== STEP 8: ACTIVITY SELECTION =====
        sc_activitySelect.addEventListener('change', function() {

sc_pointsDiv.classList.add('d-none');

if (!this.value) return;

sc_pointsDiv.classList.remove('d-none');


const selected = activitiesCache.find(a => a.id == this.value);

maxPoints = selected?.max_score || 0;

document.getElementById('maxPointsHint').innerText =
    maxPoints ? `Max allowed: ${maxPoints}` : 'No limit set';

// optional reset
sc_pointsInput.value = '';
sc_submitBtn.disabled = true;
});

        // ===== POINTS INPUT VALIDATION =====
        sc_pointsInput.addEventListener('input', function() {
    const value = parseFloat(this.value);
    const errorDiv = document.getElementById('pointsError');

    if (!value) {
        errorDiv.innerText = '';
        this.classList.remove('is-invalid');
        sc_submitBtn.disabled = true;
        return;
    }

    if (value > maxPoints) {
        errorDiv.innerText = `Points cannot exceed ${maxPoints}`;
        this.classList.add('is-invalid');
        sc_submitBtn.disabled = true;
    } else {
        errorDiv.innerText = '';
        this.classList.remove('is-invalid');
        sc_submitBtn.disabled = false;
    }
});

        // ===== FORM SUBMISSION =====
        document.getElementById('sc_scoreForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!selectedEntityType) {
                toastr.error('Please select either a Player or Team');
                return;
            }

            const fd = new FormData();
            fd.append('event_id', sc_eventSelect.value);
            fd.append('challenge_activity_id', sc_activitySelect.value);
            fd.append('points', sc_pointsInput.value);

            if (selectedEntityType === 'student') {
                fd.append('student_id', sc_studentSelect.value);
            } else if (selectedEntityType === 'team') {
                fd.append('team_id', sc_teamSelect.value);
            }

            fetch("{{ route('scores.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: fd
                })
                .then(async r => {
                    let d;
                    try {
                        d = await r.json();
                    } catch (e) {
                        throw new Error('Invalid JSON response');
                    }
                    if (!r.ok) throw new Error(d.message || 'Request failed');
                    return d;
                })
                .then(d => {
                    if (d.success) {
                        toastr.success(d.message);
                        bootstrap.Modal.getInstance(document.getElementById('scoreModal')).hide();
                        resetAll(); // Reset form for next entry
                        setTimeout(() => {
                            window.location.reload(true);
                        }, 2000);
                    } else {
                        toastr.error(d.message || 'Failed to save score');
                    }
                })
                .catch(err => toastr.error(err.message || 'Error occurred'));
        });
    });
</script>
