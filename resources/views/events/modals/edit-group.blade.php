<!-- Edit Group Modal -->
<div class="modal fade" id="editGroupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editGroupForm" method="POST">
                @csrf

                <div class="modal-body">

                    <input type="hidden" id="edit_group_id">

                    <!-- Group Name -->
                    <div class="mb-3">
                        <label class="form-label">Group Name</label>
                        <input type="text"
                               id="edit_group_name"
                               name="group_name"
                               class="form-input">
                    </div>

                    <!-- Event -->
                    <div class="mb-3">
                        <label class="form-label">Event</label>
                        <select id="edit_event_id"
                                name="event_id"
                                class="form-select">

                            @foreach ($events as $ev)
                                <option value="{{ $ev->id }}">
                                    {{ $ev->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Group</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    function openGroupEditModal(id, name, eventId)
    {
        // fill fields
        document.getElementById('edit_group_id').value = id;
        document.getElementById('edit_group_name').value = name;
        document.getElementById('edit_event_id').value = eventId;
    
        // set dynamic action
        document.getElementById('editGroupForm').action = `/groups/update/${id}`;
    
        // open modal
        const modal = new bootstrap.Modal(document.getElementById('editGroupModal'));
        modal.show();
    }
    </script>