<div class="modal fade" id="add_team" tabindex="-1" aria-labelledby="teamModalTitle" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="teamModalTitle">Add New Team</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="teamForm" method="POST" action="{{ route('teams.store') }}">
                    @csrf

                    <!-- Team Name -->
                    <div class="mb-3">
                        <label for="teamName" class="form-label">Team Name <span class="text-danger">*</span></label>
                        <input type="text" name="team_name" id="teamName" class="form-input"
                            placeholder="Enter team name" required>
                    </div>

                    <!-- Event Select -->
                    <div class="mb-3">
                        <label class="form-label">Event <span class="text-danger">*</span></label>
                        <select class="form-select" id="eventSelect" name="event_id" required>
                            <option hidden>-- Select Event --</option>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Members -->
                    <div class="mb-3">
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

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="submit" form="teamForm" class="btn btn-primary">
                    Save Team
                </button>
            </div>

        </div>
    </div>
</div>


<script>
    const eventSelect = document.getElementById('eventSelect');
    const membersContainer = document.getElementById('teamMembersContainer');

    eventSelect.addEventListener('change', function() {
        const eventId = this.value;

        // Fetch URL using route name
        fetch(`{{ url('/events') }}/${eventId}/players`)
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

                // Remove extra selects if any
                Array.from(membersContainer.querySelectorAll('select'))
                    .slice(1)
                    .forEach(s => s.remove());
            })
            .catch(err => {
                console.error('Error fetching players:', err);
            });
    });

    // Add member slot
    document.getElementById('addMemberBtn').addEventListener('click', () => {
        const selectedValues = Array.from(membersContainer.querySelectorAll('select'))
            .map(s => s.value)
            .filter(v => v !== '');

        const template = membersContainer.querySelector('select');
        if (!template) return;

        const newSelect = template.cloneNode(true);
        newSelect.value = '';
        newSelect.required = true;

        Array.from(newSelect.options).forEach(option => {
            if (selectedValues.includes(option.value)) option.remove();
        });

        membersContainer.appendChild(newSelect);
    });
</script>

