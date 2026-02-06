<!-- Create Event Modal -->
<div class="modal fade" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="createEventModalLabel">Create New Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="createEventForm" method="POST" action="{{ route('events.store') }}">
                @csrf
                <div class="modal-body">
<div class="row g-3">


                    <div class="mb-3 col-md-4">
                        <label class="form-label">Event Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-input" placeholder="Spring Championship 2026" required>
                    </div>

                    <div class="mb-3 col-md-4">
                        <label class="form-label">Event Type <span class="text-danger">*</span></label>
                        <select name="event_type" class="form-select" required>
                            <option value="match">Match Event</option>
                            <option value="tournament">Tournament Event</option>
                            <option value="season_tracking">Season Tracking Event</option>
                        </select>
                    </div>

                    <div class="mb-3 col-md-4">
                        <label class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-input" required>
                    </div>

                    <div class="mb-3 col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-input">
                    </div>

                    <div class="mb-3 col-md-4">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-input" placeholder="Main Arena">
                    </div>

                    <div class="mb-3 col-md-4">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="draft">Draft</option>
                            <option value="live">Live</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Notes / Description</label>
                        <textarea name="notes" class="form-input" rows="3" placeholder="Optional admin notes"></textarea>
                    </div>
                </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Event</button>
                </div>
            </form>

        </div>
    </div>
</div>


<script>
    document.querySelector('select[name="event_type"]').addEventListener('change', function() {
    const endDateInput = document.querySelector('input[name="end_date"]');
    if (this.value === 'season_tracking') {
        endDateInput.required = true;
    } else {
        endDateInput.required = false;
    }
});

</script>