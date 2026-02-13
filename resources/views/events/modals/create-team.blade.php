<div class="modal fade" id="add_team" tabindex="-1" aria-labelledby="teamModalTitle" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add New Team</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="teamForm" method="POST" action="{{ route('teams.store') }}" enctype="multipart/form-data">

                    @csrf

                   <div class="row g-3">
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Team Name <span class="text-danger">*</span></label>
                        <input type="text" name="team_name" class="form-input" required>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Team Avatar</label>
                        <input type="file"
                               name="profile"
                               class="form-input"
                               accept="image/*">
                        <small class="text-muted">jpg, jpeg, png (max 2MB)</small>
                    </div>
                    
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Event <span class="text-danger">*</span></label>
                        <select class="form-select" id="eventSelect" name="event_id" required>
                            <option hidden>-- Select Event --</option>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 col-md-4">
                        <label class="form-label">Organization <span class="text-danger">*</span>   </label>
                        <select class="form-select" id="organizationSelect" name="organization_id">
                            <option value="">-- Select Organization --</option>
                        </select>
                    </div>

                    <div class="mb-3 col-md-4">
                        <label class="form-label">Team Members <span class="text-danger">*</span></label>
                        <div id="teamMembersContainer" class="d-grid gap-2">
                            <select name="players[]" class="form-select" required>
                                <option hidden>-- Select Player --</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-outline-secondary w-100 mt-2" id="addMemberBtn">
                            + Add Member Slot
                        </button>
                    </div>

                   </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="teamForm" class="btn btn-primary">Save Team</button>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    
        const eventSelect = document.getElementById('eventSelect');
        const organizationSelect = document.getElementById('organizationSelect');
        const membersContainer = document.getElementById('teamMembersContainer');
    
        function loadOrganizations() {
            organizationSelect.innerHTML = '<option>Loading...</option>';
    
            fetch(`/organizations/list`)
                .then(res => res.json())
                .then(orgs => {
    
                    organizationSelect.innerHTML = '<option value="">-- Select Organization --</option>';
    
                    if (!orgs.length) {
                        organizationSelect.innerHTML =
                            '<option disabled>No organizations available</option>';
                        return;
                    }
    
                    orgs.forEach(org => {
                        const opt = document.createElement('option');
                        opt.value = org.id;
                        opt.textContent = org.name;
                        organizationSelect.appendChild(opt);
                    });
                })
                .catch(() => {
                    organizationSelect.innerHTML =
                        '<option disabled>Error loading organizations</option>';
                });
        }
    
        loadOrganizations();
    
        eventSelect.addEventListener('change', function () {
    
            const eventId = this.value;
    
            fetch(`/events/${eventId}/players`)
                .then(res => res.json())
                .then(players => {
    
                    const firstSelect = membersContainer.querySelector('select');
                    firstSelect.innerHTML = '<option hidden>-- Select Player --</option>';
    
                    players.forEach(player => {
                        const opt = document.createElement('option');
                        opt.value = player.id;
                        opt.textContent = player.name;
                        firstSelect.appendChild(opt);
                    });
    
                    Array.from(membersContainer.querySelectorAll('select'))
                        .slice(1)
                        .forEach(s => s.remove());
                });
        });
    
        document.getElementById('addMemberBtn').addEventListener('click', () => {
    
            const selectedValues = Array.from(membersContainer.querySelectorAll('select'))
                .map(s => s.value)
                .filter(v => v !== '');
    
            const template = membersContainer.querySelector('select');
            if (!template) return;
    
            const newSelect = template.cloneNode(true);
            newSelect.value = '';
    
            Array.from(newSelect.options).forEach(option => {
                if (selectedValues.includes(option.value)) option.remove();
            });
    
            membersContainer.appendChild(newSelect);
        });
    
    });
    </script>
    