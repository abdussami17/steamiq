<!-- Assign Points Modal -->
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
                      <!-- Event -->
                      <div class="mb-3 col-md-4">
                        <label class="form-label">Event</label>
                        <select id="sc_eventSelect" class="form-select" required>
                            <option value="">-- Select Event --</option>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Organization -->
                    <div class="mb-3 col-md-4 d-none" id="sc_organizationDiv">
                        <label class="form-label">Organization</label>
                        <select id="sc_organizationSelect" class="form-select"></select>
                    </div>

                    <!-- Group -->
                    <div class="mb-3 col-md-4 d-none" id="sc_groupDiv">
                        <label class="form-label">Group</label>
                        <select id="sc_groupSelect" class="form-select"></select>
                    </div>

                    <!-- SubGroup -->
                    <div class="mb-3 col-md-4 d-none" id="sc_subgroupDiv">
                        <label class="form-label">Sub Group</label>
                        <select id="sc_subgroupSelect" class="form-select"></select>
                    </div>

                    <!-- Assign To -->
                    <div class="mb-3 col-md-4 d-none" id="sc_typeDiv">
                        <label class="form-label">Assign To</label>
                        <select id="sc_entityType" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="student">Player</option>
                            <option value="team">Team</option>
                        </select>
                    </div>

                    <!-- Student -->
                    <div class="mb-3 col-md-4 d-none" id="sc_studentDiv">
                        <label class="form-label">Player</label>
                        <select id="sc_studentSelect" class="form-select"></select>
                    </div>

                    <!-- Team -->
                    <div class="mb-3 col-md-4 d-none" id="sc_teamDiv">
                        <label class="form-label">Team</label>
                        <select id="sc_teamSelect" class="form-select"></select>
                    </div>

                    <!-- Activity -->
                    <div class="mb-3 col-md-4 d-none" id="sc_activityDiv">
                        <label class="form-label">Activity</label>
                        <select id="sc_activitySelect" class="form-select"></select>
                    </div>
                  </div>

                    <!-- Points -->
                    <div class="d-none" id="sc_pointsDiv">
                        <table class="table table-bordered table-dark">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Points</th>
                                </tr>
                            </thead>
                            <tbody id="sc_steamPointsBody"></tbody>
                        </table>
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
        const sc_steamPointsBody = document.getElementById('sc_steamPointsBody');
        const sc_submitBtn = document.getElementById('sc_submitBtn');
    
        let currentEvent = '';
    
        function resetAll() {
            [
                sc_organizationDiv, sc_groupDiv, sc_subgroupDiv, sc_typeDiv,
                sc_studentDiv, sc_teamDiv, sc_activityDiv, sc_pointsDiv
            ].forEach(el => el.classList.add('d-none'));
    
            [
                sc_organizationSelect, sc_groupSelect, sc_subgroupSelect,
                sc_studentSelect, sc_teamSelect, sc_activitySelect
            ].forEach(el => el.innerHTML = '');
    
            sc_submitBtn.disabled = true;
        }
    
        // EVENT → ORGANIZATION
        sc_eventSelect.addEventListener('change', function () {
            currentEvent = this.value;
            resetAll();
    
            if (!this.value) return;
    
            sc_organizationDiv.classList.remove('d-none');
    
            fetch(`/events/${currentEvent}/organizations`)
                .then(r => r.json())
                .then(data => {
                    sc_organizationSelect.innerHTML = '<option value="">-- Select Organization --</option>';
                    data.forEach(o => {
                        sc_organizationSelect.innerHTML += `<option value="${o.id}">${o.name}</option>`;
                    });
                });
        });
    
        // ORGANIZATION → GROUP
        sc_organizationSelect.addEventListener('change', function () {
    
            sc_groupDiv.classList.add('d-none');
            sc_subgroupDiv.classList.add('d-none');
            sc_typeDiv.classList.add('d-none');
    
            if (!this.value) return;
    
            sc_groupDiv.classList.remove('d-none');
    
            fetch(`/organizations/${this.value}/groups`)
                .then(r => r.json())
                .then(data => {
                    sc_groupSelect.innerHTML = '<option value="">-- Select Group --</option>';
                    data.forEach(g => {
                        sc_groupSelect.innerHTML += `<option value="${g.id}">${g.group_name}</option>`;
                    });
                });
        });
    
        // GROUP → SUBGROUP
        sc_groupSelect.addEventListener('change', function () {
    
            sc_subgroupDiv.classList.add('d-none');
            sc_typeDiv.classList.add('d-none');
    
            if (!this.value) return;
    
            fetch(`/groups/${this.value}/subgroups`)
                .then(r => r.json())
                .then(data => {
    
                    if (data.length > 0) {
                        sc_subgroupDiv.classList.remove('d-none');
    
                        sc_subgroupSelect.innerHTML = '<option value="">-- Select SubGroup --</option>';
                        data.forEach(s => {
                            sc_subgroupSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
                        });
    
                    } else {
                        sc_typeDiv.classList.remove('d-none');
                    }
                });
        });
    
        // SUBGROUP → TYPE
        sc_subgroupSelect.addEventListener('change', function () {
            if (this.value) sc_typeDiv.classList.remove('d-none');
        });
    
        // TYPE → STUDENT / TEAM
        sc_entityType.addEventListener('change', function () {
    
            sc_studentDiv.classList.add('d-none');
            sc_teamDiv.classList.add('d-none');
    
            const params = `event_id=${currentEvent}&organization_id=${sc_organizationSelect.value}&group_id=${sc_groupSelect.value}&sub_group_id=${sc_subgroupSelect.value}`;
    
            if (this.value === 'student') {
    
                sc_studentDiv.classList.remove('d-none');
    
                fetch(`/students?${params}`)
                    .then(r => r.json())
                    .then(data => {
                        sc_studentSelect.innerHTML = '<option>-- Select Player --</option>';
                        data.forEach(s => {
                            sc_studentSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
                        });
                    });
    
            } else if (this.value === 'team') {
    
                sc_teamDiv.classList.remove('d-none');
    
                fetch(`/teams?${params}`)
                    .then(r => r.json())
                    .then(data => {
                        sc_teamSelect.innerHTML = '<option>-- Select Team --</option>';
                        data.forEach(t => {
                            sc_teamSelect.innerHTML += `<option value="${t.id}">${t.name}</option>`;
                        });
                    });
            }
        });
    
        // PLAYER/TEAM → ACTIVITY
        [sc_studentSelect, sc_teamSelect].forEach(select => {
            select.addEventListener('change', function () {
    
                if (!this.value) return;
    
                sc_activityDiv.classList.remove('d-none');
                
                fetch(`/api/events/${currentEvent}/activities`)
                    .then(r => r.json())
                    .then(data => {
                        sc_activitySelect.innerHTML = '<option>-- Select Activity --</option>';
                        data.forEach(a => {
                            let name = a.badge_name || a.brain_type || a.esports_type || a.egaming_type || a.name || 'Playground';
                            sc_activitySelect.innerHTML += `<option value="${a.id}">${name}</option>`;
                        });
                    });
            });
        });
    
        // ACTIVITY → POINTS
        sc_activitySelect.addEventListener('change', function () {
    
            if (!this.value) return;
    
            sc_pointsDiv.classList.remove('d-none');
    
            fetch('/api/steam-categories')
                .then(r => r.json())
                .then(data => {
    
                    sc_steamPointsBody.innerHTML = '';
    
                    data.forEach(c => {
                        sc_steamPointsBody.innerHTML += `
                            <tr>
                                <td>${c.name}</td>
                                <td><input type="number" min="0" class="form-input sc-steam-point" data-id="${c.id}" value="0"></td>
                            </tr>
                        `;
                    });
    
                    sc_submitBtn.disabled = false;
                });
        });
    
    });
    </script>