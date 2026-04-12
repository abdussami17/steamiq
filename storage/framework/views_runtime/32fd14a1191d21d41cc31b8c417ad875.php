<div class="modal fade" id="scoreModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form id="sc_scoreForm">
                <?php echo csrf_field(); ?>
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
                        
                                <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(strtolower(trim($event->status)) !== 'closed'): ?>
                                        <option value="<?php echo e($event->id); ?>">
                                            <?php echo e($event->name); ?>

                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
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
    // Add this to your score-script.blade.php or in the main script section
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const modal = document.getElementById('scoreModal');
    const eventSelect = document.getElementById('modalEvent');
    const orgSelect = document.getElementById('modalOrganization');
    const groupSelect = document.getElementById('modalGroup');
    const subGroupSelect = document.getElementById('modalSubGroup');
    const teamSelect = document.getElementById('modalTeam');
    const studentSelect = document.getElementById('modalStudent');
    const activitySelect = document.getElementById('modalActivity');
    const pointsInput = document.getElementById('modalPoints');
    const maxPointsHint = document.getElementById('maxPointsHint');
    const pointsError = document.getElementById('pointsError');
    const saveBtn = document.getElementById('saveScoreBtn');

    let currentMaxScore = 0;

    // Load organizations when event is selected
    eventSelect.addEventListener('change', async function() {
        const eventId = this.value;
        if (!eventId) return;

        try {
            const res = await fetch(`/events/${eventId}/organizations`);
            const orgs = await res.json();
            
            orgSelect.innerHTML = '<option value="">Select Organization</option>';
            orgs.forEach(org => {
                orgSelect.innerHTML += `<option value="${org.id}">${org.name}</option>`;
            });
            orgSelect.disabled = false;
            
            // Reset dependent fields
            resetDependentFields(['group', 'subGroup', 'team', 'student', 'activity']);
        } catch (err) {
            console.error('Error loading organizations:', err);
        }
    });

    // Load groups when organization is selected
    orgSelect.addEventListener('change', async function() {
        const orgId = this.value;
        if (!orgId) return;

        try {
            const res = await fetch(`/organizations/${orgId}/groups`);
            const groups = await res.json();
            
            groupSelect.innerHTML = '<option value="">Select Group</option>';
            groups.forEach(group => {
                groupSelect.innerHTML += `<option value="${group.id}">${group.group_name}</option>`;
            });
            groupSelect.disabled = false;
            
            // Reset subgroups, teams, students, activities
            resetDependentFields(['subGroup', 'team', 'student', 'activity']);
        } catch (err) {
            console.error('Error loading groups:', err);
        }
    });

    // Load subgroups when group is selected
    groupSelect.addEventListener('change', async function() {
        const groupId = this.value;
        if (!groupId) return;

        try {
            const res = await fetch(`/groups/${groupId}/subgroups`);
            const subgroups = await res.json();
            
            subGroupSelect.innerHTML = '<option value="">Select Sub Group</option>';
            subgroups.forEach(subgroup => {
                subGroupSelect.innerHTML += `<option value="${subgroup.id}">${subgroup.name}</option>`;
            });
            subGroupSelect.disabled = false;
            
            // Load teams for this group
            await loadTeams(groupId, null);
            
            resetDependentFields(['student', 'activity']);
        } catch (err) {
            console.error('Error loading subgroups:', err);
        }
    });

    // Load teams when subgroup is selected
    subGroupSelect.addEventListener('change', async function() {
        const groupId = groupSelect.value;
        const subGroupId = this.value;
        
        if (!groupId) return;
        
        await loadTeams(groupId, subGroupId);
        resetDependentFields(['student', 'activity']);
    });

    // Load students when team is selected
    teamSelect.addEventListener('change', async function() {
        const teamId = this.value;
        if (!teamId) {
            studentSelect.disabled = true;
            studentSelect.innerHTML = '<option value="">Select Student</option>';
            return;
        }

        try {
            const res = await fetch(`/teams/${teamId}/students`);
            const students = await res.json();
            
            studentSelect.innerHTML = '<option value="">Select Student (Optional)</option>';
            students.forEach(student => {
                studentSelect.innerHTML += `<option value="${student.id}">${student.name}</option>`;
            });
            studentSelect.disabled = false;
        } catch (err) {
            console.error('Error loading students:', err);
        }
    });

    // Load activities when event is selected (or when needed)
    async function loadActivities(eventId) {
        if (!eventId) return;

        try {
            const res = await fetch(`/events/${eventId}/activities`);
            const activities = await res.json();
            
            activitySelect.innerHTML = '<option value="">Select Activity</option>';
            activities.forEach(activity => {
                activitySelect.innerHTML += `<option value="${activity.id}" data-max="${activity.max_score}">
                    ${activity.name} (Max: ${activity.max_score})
                </option>`;
            });
            activitySelect.disabled = false;
        } catch (err) {
            console.error('Error loading activities:', err);
        }
    }

    // When activity is selected, update max score display
    activitySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.value) {
            currentMaxScore = parseInt(selectedOption.dataset.max) || 0;
            maxPointsHint.textContent = `Maximum points allowed: ${currentMaxScore}`;
            pointsInput.max = currentMaxScore;
            
            // Clear previous error
            pointsInput.classList.remove('is-invalid');
            pointsError.style.display = 'none';
        } else {
            currentMaxScore = 0;
            maxPointsHint.textContent = '';
            pointsInput.max = '';
        }
    });

    // Validate points input in real-time
    pointsInput.addEventListener('input', function() {
        const value = parseInt(this.value);
        
        if (value > currentMaxScore && currentMaxScore > 0) {
            this.classList.add('is-invalid');
            pointsError.textContent = `Points cannot exceed maximum score (${currentMaxScore})`;
            pointsError.style.display = 'block';
            maxPointsHint.classList.add('text-danger');
            maxPointsHint.innerHTML = `<span class="text-danger">⚠️ Exceeds maximum! Max allowed: ${currentMaxScore}</span>`;
        } else {
            this.classList.remove('is-invalid');
            pointsError.style.display = 'none';
            maxPointsHint.classList.remove('text-danger');
            maxPointsHint.innerHTML = `Maximum points allowed: ${currentMaxScore}`;
        }
        
        // Also check for negative values
        if (value < 0) {
            this.classList.add('is-invalid');
            pointsError.textContent = 'Points cannot be negative';
            pointsError.style.display = 'block';
        }
    });

    // Helper function to load teams
    async function loadTeams(groupId, subGroupId) {
        try {
            let url = '/teams/filtered?';
            if (groupId) url += `group_id=${groupId}&`;
            if (subGroupId) url += `sub_group_id=${subGroupId}`;
            
            const res = await fetch(url);
            const teams = await res.json();
            
            teamSelect.innerHTML = '<option value="">Select Team</option>';
            teams.forEach(team => {
                teamSelect.innerHTML += `<option value="${team.id}">${team.name}</option>`;
            });
            teamSelect.disabled = false;
        } catch (err) {
            console.error('Error loading teams:', err);
        }
    }

    // Reset dependent fields
    function resetDependentFields(fields) {
        const fieldMap = {
            group: groupSelect,
            subGroup: subGroupSelect,
            team: teamSelect,
            student: studentSelect,
            activity: activitySelect
        };
        
        fields.forEach(field => {
            if (fieldMap[field]) {
                fieldMap[field].innerHTML = `<option value="">Select ${field.replace(/([A-Z])/g, ' $1').trim()}</option>`;
                fieldMap[field].disabled = true;
            }
        });
        
        if (fields.includes('activity')) {
            currentMaxScore = 0;
            maxPointsHint.textContent = '';
            pointsInput.value = '';
            pointsInput.classList.remove('is-invalid');
        }
    }

    // Save score button click handler
    saveBtn.addEventListener('click', async function() {
        const points = parseInt(pointsInput.value);
        
        // Validate points
        if (points > currentMaxScore) {
            pointsInput.classList.add('is-invalid');
            pointsError.textContent = `Points cannot exceed maximum score (${currentMaxScore})`;
            pointsError.style.display = 'block';
            return;
        }
        
        if (points < 0) {
            pointsInput.classList.add('is-invalid');
            pointsError.textContent = 'Points cannot be negative';
            pointsError.style.display = 'block';
            return;
        }
        
        // Collect form data
        const formData = {
            event_id: eventSelect.value,
            challenge_activity_id: activitySelect.value,
            points: points,
            student_id: studentSelect.value || null,
            team_id: teamSelect.value || null
        };
        
        // Validate required fields
        if (!formData.event_id || !formData.challenge_activity_id) {
            toast('Please select event and activity', 'warn');
            return;
        }
        
        if (!formData.team_id && !formData.student_id) {
            toast('Please select a team or student', 'warn');
            return;
        }
        
        // Save the score
        try {
            const response = await fetch('/scores', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                toast(result.message, 'ok');
                // Close modal
                bootstrap.Modal.getInstance(modal).hide();
                // Reset form
                document.getElementById('scoreForm').reset();
                resetDependentFields(['group', 'subGroup', 'team', 'student', 'activity']);
                // Refresh leaderboard
                const eventSelect = document.getElementById('selectEvent');
                if (eventSelect && eventSelect.value) {
                    fetchLeaderboard(eventSelect.value);
                }
            } else {
                toast(result.message || 'Error saving score', 'err');
            }
        } catch (err) {
            console.error('Error saving score:', err);
            toast('Error saving score', 'err');
        }
    });
    
    // Also load activities when event is selected in the modal
    const originalEventChange = eventSelect.onchange;
    eventSelect.addEventListener('change', async function() {
        if (this.value) {
            await loadActivities(this.value);
        } else {
            activitySelect.disabled = true;
            activitySelect.innerHTML = '<option value="">Select Activity</option>';
        }
    });
});
</script><?php /**PATH C:\Users\PC\Downloads\steamiq (5)\resources\views/scores/modals/create-scores.blade.php ENDPATH**/ ?>