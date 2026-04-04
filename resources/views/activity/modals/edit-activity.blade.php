<!-- Edit Activity Modal -->
<div class="modal fade" id="editActivityModal" tabindex="-1" aria-labelledby="editActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="editActivityModalLabel">Edit Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editActivityForm">
                @csrf
                <input type="hidden" name="activity_id" id="editActivityId">

                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Event -->
                        <div class="col-md-6">
                            <label class="form-label">Event</label>
                            <select name="event_id" id="editActivityEvent" class="form-select" required>
                                <option hidden>-- Select Event --</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Activity Name -->
                        <div class="col-md-6">
                            <label class="form-label">Activity Name</label>
                            <input type="text" name="name" id="editActivityName" class="form-input" required>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <label class="form-label">Description <small class="text-muted">(Optional)</small></label>
                            <textarea name="description" id="editActivityDescription" class="form-input"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Activity</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Open Edit Modal and populate
    window.openEditActivityModal = function(activityId) {
        const modal = new bootstrap.Modal(document.getElementById('editActivityModal'));
        const form = document.getElementById('editActivityForm');
        form.reset();

        fetch(`/activities/${activityId}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('editActivityId').value = data.id;
                document.getElementById('editActivityName').value = data.name;
                document.getElementById('editActivityDescription').value = data.description || '';
                document.getElementById('editActivityEvent').value = data.event_id;
                modal.show();
            }).catch(err => console.error(err));
    };

    // Submit Edit Form
    document.getElementById('editActivityForm').addEventListener('submit', function(e){
        e.preventDefault();
        const formData = new FormData(this);
        const id = formData.get('activity_id');

        fetch(`/activities/${id}/update`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                toastr.success(data.message || 'Activity updated successfully');
                location.reload();
                bootstrap.Modal.getInstance(document.getElementById('editActivityModal')).hide();
            } else {
                alert(data.message || 'Failed to update activity');
            }
        }).catch(err => console.error(err));
    });

    // Delete Activity
    window.deleteActivity = function(activityId, name){
        if(confirm(`Are you sure you want to delete "${name}"?`)){
            fetch(`/activities/${activityId}/delete`,{
                method:'DELETE',
                headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
            }).then(res=>res.json()).then(data=>{
                if(data.success) toastr.success(data.message || 'Activity updated successfully'); location.reload();
            });
        }
    };

});
</script>