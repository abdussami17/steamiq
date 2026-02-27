<!-- Create Activity Modal -->
<div class="modal fade" id="createActivityModal" tabindex="-1" aria-labelledby="createActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="createActivityModalLabel">Add New Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('activities.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Event -->
                        <div class="col-md-6">
                            <label class="form-label">Event </label>
                            <select name="event_id" class="form-select" required>
                                <option hidden>-- Select Event --</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Activity Name -->
                        <div class="col-md-6">
                            <label class="form-label">Activity Name </label>
                            <input type="text" name="name" class="form-input" placeholder="Activity Name" required>
                        </div>

                        <!-- Description (Optional) -->
                        <div class="col-md-12">
                            <label class="form-label">Description <small class="text-muted">(Optional)</small></label>
                            <textarea name="description" class="form-input" placeholder="Enter description"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Activity</button>
                </div>
            </form>

        </div>
    </div>
</div>