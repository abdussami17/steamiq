<div class="modal fade" id="createSubGroupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Create Sub Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('subgroups.store') }}">
                @csrf

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Sub Group Name</label>
                        <input type="text" name="name" class="form-input" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Group</label>
                        <select name="group_id" id="groupSelect" class="form-select" required>
                            <option value="" hidden>--Select Group--</option>
                            @foreach ($groups as $grp)
                                @if($grp->event)
                                    <option value="{{ $grp->id }}"
                                            data-event-id="{{ $grp->event->id }}"
                                            data-event-name="{{ $grp->event->name }}">
                                        {{ $grp->group_name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="eventWrapper">
                        <label class="form-label">Event</label>
                        <input type="text" id="eventDisplay" class="form-input" readonly>
                        <input type="hidden" name="event_id" id="eventHidden" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Sub Group</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const groupSelect = document.getElementById('groupSelect');
    const eventWrapper = document.getElementById('eventWrapper');
    const eventDisplay = document.getElementById('eventDisplay');
    const eventHidden = document.getElementById('eventHidden');

    function updateEvent() {
        const selectedOption = groupSelect.options[groupSelect.selectedIndex];
        if (!selectedOption || !selectedOption.dataset.eventId) {
            eventWrapper.classList.add('d-none');
            eventDisplay.value = '';
            eventHidden.value = '';
            return;
        }

        eventWrapper.classList.remove('d-none');
        eventDisplay.value = selectedOption.dataset.eventName;
        eventHidden.value = selectedOption.dataset.eventId;
    }

    groupSelect.addEventListener('change', updateEvent);

    // **Important:** reset event fields whenever modal opens
    const modal = document.getElementById('createSubGroupModal');
    modal.addEventListener('show.bs.modal', function () {
        groupSelect.value = '';
        eventWrapper.classList.add('d-none');
        eventDisplay.value = '';
        eventHidden.value = '';
    });
});
</script>