<!-- Assign Points Modal -->
<div class="modal fade" id="scoreModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="scoreForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Points</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Event -->
                    <div class="mb-3">
                        <label class="form-label">Event</label>
                        <select id="modalEventSelect" class="form-select" required>
                            <option value="">-- Select Event --</option>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Organization -->
                    <div class="mb-3 d-none" id="organizationDiv">
                        <label class="form-label">Organization</label>
                        <select id="organizationSelect" class="form-select"></select>
                    </div>

                    <!-- Group -->
                    <div class="mb-3 d-none" id="groupDiv">
                        <label class="form-label">Group</label>
                        <select id="groupSelect" class="form-select"></select>
                    </div>

                    <!-- SubGroup -->
                    <div class="mb-3 d-none" id="subgroupDiv">
                        <label class="form-label">Sub Group</label>
                        <select id="subgroupSelect" class="form-select"></select>
                    </div>

                    <!-- Assign To -->
                    <div class="mb-3 d-none" id="typeDiv">
                        <label class="form-label">Assign To</label>
                        <select id="entityType" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="student">Player</option>
                            <option value="team">Team</option>
                        </select>
                    </div>

                    <!-- Student -->
                    <div class="mb-3 d-none" id="studentDiv">
                        <label class="form-label">Player</label>
                        <select id="studentSelect" class="form-select"></select>
                    </div>

                    <!-- Team -->
                    <div class="mb-3 d-none" id="teamDiv">
                        <label class="form-label">Team</label>
                        <select id="teamSelect" class="form-select"></select>
                    </div>

                    <!-- Activity -->
                    <div class="mb-3 d-none" id="activityDiv">
                        <label class="form-label">Activity</label>
                        <select id="activitySelect" class="form-select"></select>
                    </div>

                    <!-- Points -->
                    <div class="d-none" id="pointsDiv">
                        <table class="table table-bordered table-dark">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Points</th>
                                </tr>
                            </thead>
                            <tbody id="steamPointsBody"></tbody>
                        </table>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Save Points</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    
        const eventSelect = document.getElementById('modalEventSelect');
        const organizationDiv = document.getElementById('organizationDiv');
        const groupDiv = document.getElementById('groupDiv');
        const subgroupDiv = document.getElementById('subgroupDiv');
        const typeDiv = document.getElementById('typeDiv');
    
        const organizationSelect = document.getElementById('organizationSelect');
        const groupSelect = document.getElementById('groupSelect');
        const subgroupSelect = document.getElementById('subgroupSelect');
    
        const entityType = document.getElementById('entityType');
        const studentDiv = document.getElementById('studentDiv');
        const teamDiv = document.getElementById('teamDiv');
        const studentSelect = document.getElementById('studentSelect');
        const teamSelect = document.getElementById('teamSelect');
    
        const activityDiv = document.getElementById('activityDiv');
        const activitySelect = document.getElementById('activitySelect');
    
        const pointsDiv = document.getElementById('pointsDiv');
        const steamPointsBody = document.getElementById('steamPointsBody');
        const submitBtn = document.getElementById('submitBtn');
    
        let currentEvent = '';
    
        function resetAll() {
            [organizationDiv, groupDiv, subgroupDiv, typeDiv, studentDiv, teamDiv, activityDiv, pointsDiv]
                .forEach(el => el.classList.add('d-none'));
    
            [organizationSelect, groupSelect, subgroupSelect, studentSelect, teamSelect, activitySelect]
                .forEach(el => el.innerHTML = '');
    
            submitBtn.disabled = true;
        }
    
        // EVENT → ORGANIZATION
        eventSelect.addEventListener('change', function () {
            currentEvent = this.value;
            resetAll();
    
            if (!this.value) return;
    
            organizationDiv.classList.remove('d-none');
    
            fetch(`/api/events/${currentEvent}/organizations`)
                .then(r => r.json())
                .then(data => {
                    organizationSelect.innerHTML = '<option value="">-- Select Organization --</option>';
                    data.forEach(o => {
                        organizationSelect.innerHTML += `<option value="${o.id}">${o.name}</option>`;
                    });
                });
        });
    
        // ORGANIZATION → GROUP
        organizationSelect.addEventListener('change', function () {
            groupDiv.classList.add('d-none');
            subgroupDiv.classList.add('d-none');
            typeDiv.classList.add('d-none');
    
            if (!this.value) return;
    
            groupDiv.classList.remove('d-none');
    
            fetch(`/api/organizations/${this.value}/groups`)
                .then(r => r.json())
                .then(data => {
                    groupSelect.innerHTML = '<option value="">-- Select Group --</option>';
                    data.forEach(g => {
                        groupSelect.innerHTML += `<option value="${g.id}">${g.name}</option>`;
                    });
                });
        });
    
        // GROUP → SUBGROUP
        groupSelect.addEventListener('change', function () {
            subgroupDiv.classList.add('d-none');
            typeDiv.classList.add('d-none');
    
            if (!this.value) return;
    
            fetch(`/api/groups/${this.value}/subgroups`)
                .then(r => r.json())
                .then(data => {
    
                    if (data.length > 0) {
                        subgroupDiv.classList.remove('d-none');
                        subgroupSelect.innerHTML = '<option value="">-- Select SubGroup --</option>';
                        data.forEach(s => {
                            subgroupSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
                        });
                    } else {
                        typeDiv.classList.remove('d-none');
                    }
                });
        });
    
        // SUBGROUP → TYPE
        subgroupSelect.addEventListener('change', function () {
            if (this.value) typeDiv.classList.remove('d-none');
        });
    
        // TYPE → STUDENT / TEAM
        entityType.addEventListener('change', function () {
    
            studentDiv.classList.add('d-none');
            teamDiv.classList.add('d-none');
    
            const params = `event_id=${currentEvent}&organization_id=${organizationSelect.value}&group_id=${groupSelect.value}&sub_group_id=${subgroupSelect.value}`;
    
            if (this.value === 'student') {
                studentDiv.classList.remove('d-none');
    
                fetch(`/api/students?${params}`)
                    .then(r => r.json())
                    .then(data => {
                        studentSelect.innerHTML = '<option>-- Select Player --</option>';
                        data.forEach(s => studentSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`);
                    });
    
            } else if (this.value === 'team') {
                teamDiv.classList.remove('d-none');
    
                fetch(`/api/teams?${params}`)
                    .then(r => r.json())
                    .then(data => {
                        teamSelect.innerHTML = '<option>-- Select Team --</option>';
                        data.forEach(t => teamSelect.innerHTML += `<option value="${t.id}">${t.name}</option>`);
                    });
            }
        });
    
        // PLAYER/TEAM → ACTIVITY
        [studentSelect, teamSelect].forEach(select => {
            select.addEventListener('change', function () {
    
                if (!this.value) return;
    
                activityDiv.classList.remove('d-none');
    
                fetch(`/api/events/${currentEvent}/activities`)
                    .then(r => r.json())
                    .then(data => {
                        activitySelect.innerHTML = '<option>-- Select Activity --</option>';
                        data.forEach(a => {
                            let name = a.badge_name || a.brain_type || a.esports_type || a.egaming_type || a.name || 'Playground';
                            activitySelect.innerHTML += `<option value="${a.id}">${name}</option>`;
                        });
                    });
            });
        });
    
        // ACTIVITY → POINTS
        activitySelect.addEventListener('change', function () {
            if (!this.value) return;
    
            pointsDiv.classList.remove('d-none');
    
            fetch('/api/steam-categories')
                .then(r => r.json())
                .then(data => {
                    steamPointsBody.innerHTML = '';
                    data.forEach(c => {
                        steamPointsBody.innerHTML += `
                            <tr>
                                <td>${c.name}</td>
                                <td><input type="number" min="0" class="form-input steam-point" data-id="${c.id}" value="0"></td>
                            </tr>
                        `;
                    });
                    submitBtn.disabled = false;
                });
        });
    
    });
    </script>