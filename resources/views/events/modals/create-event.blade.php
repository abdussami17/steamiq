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
                        <label class="form-label">Event Name </label>
                        <input type="text" name="name" class="form-input" placeholder="Spring Championship 2026" >
                    </div>

  <div class="mb-3 col-md-4">
                        <label class="form-label">Organization</label>
                        <select name="organization_id" class="form-select" >
                            <option value="" hidden>--Select Organization--</option>
                           @foreach ($organizations as $org )
                               <option value="{{ $org->id }}">{{ $org->name }}</option>
                           @endforeach
                        </select>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Event Type</label>
                        <select name="event_type" class="form-select" >
                           <option value=""  hidden>--Select Type--</option>
                            <option value="Brain Games">Brain Games</option>
                            <option value="Playground Games">Playground Games</option>
                            <option value="Esports">Esports</option>
                        </select>
                    </div>

                    <div class="mb-3 col-md-4">
                        <label class="form-label">Start Date </label>
                        <input type="date" name="start_date" class="form-input" >
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
                        <label class="form-label">Status </label>
                        <select name="status" class="form-select" >
                            <option value="draft">Draft</option>
                            <option value="live">Live</option>
                            <option value="closed">Closed</option>
                        </select>
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


