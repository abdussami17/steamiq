<div class="modal fade" id="createGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Create Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('groups.store') }}">
                @csrf

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">
                            Group Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="group_name"
                               class="form-input"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Team <span class="text-danger">*</span>
                        </label>
                        <select name="team_id"
                                id="teamSelect"
                                class="form-select"
                                required>
                            <option>Loading teams...</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Group</button>
                </div>

            </form>

        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
    
        const teamSelect = document.getElementById('teamSelect');
        const modal = document.getElementById('createGroupModal');
    
        modal.addEventListener('shown.bs.modal', function () {
    
            teamSelect.innerHTML = '<option>Loading...</option>';
    
            fetch('/teams/list')
                .then(res => res.json())
                .then(teams => {
    
                    teamSelect.innerHTML = '<option value="">-- Select Team --</option>';
    
                    if (!teams.length) {
                        teamSelect.innerHTML =
                            '<option disabled>No teams available</option>';
                        return;
                    }
    
                    teams.forEach(team => {
                        const opt = document.createElement('option');
                        opt.value = team.id;
                        opt.textContent = team.team_name;
                        teamSelect.appendChild(opt);
                    });
                });
        });
    
    });
    </script>
    