<div class="modal fade" id="editOrganizationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Organization</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editOrgForm" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label">Organization Name</label>
                            <input type="text" name="name" id="edit_name" class="form-input">
                        </div>

                        <!-- Type -->
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="organization_type" id="edit_type" class="form-select">
                                <option value="School">School</option>
                                <option value="Parks and Recreation">Parks and Recreation</option>
                                <option value="Youth Organization">Youth Organization</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <!-- Event -->
                        <div class="col-md-6">
                            <label class="form-label">Event</label>
                            <select name="event_id" id="edit_event" class="form-select">
                                <option value="" hidden>--Select Event--</option>
                                @foreach($events as $evt)
                                    <option value="{{ $evt->id }}">{{ $evt->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-input">
                        </div>

                        <!-- Profile -->
                        <div class="col-md-6">
                            <label class="form-label">Change Photo (optional)</label>
                            <input type="file" name="profile" class="form-input" accept="image/*">
                            <small class="text-muted">Leave empty to keep old image</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
function openEditOrgModal(id, name, email, type, event_id) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_type').value = type;

    const eventSelect = document.getElementById('edit_event');
    for (let i = 0; i < eventSelect.options.length; i++) {
        eventSelect.options[i].selected = eventSelect.options[i].value == event_id;
    }

    document.getElementById('editOrgForm').action = `/organizations/update/${id}`;

    const modal = new bootstrap.Modal(document.getElementById('editOrganizationModal'));
    modal.show();
}
</script>