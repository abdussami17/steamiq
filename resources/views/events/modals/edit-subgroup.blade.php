<div class="modal fade" id="editSubGroupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Sub Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editSubGroupForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Sub Group Name</label>
                        <input type="text" id="subgroupname" value="" name="name" class="form-input" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Group</label>
                        <select name="group_id" id="edit_group" class="form-select" required>
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

                    <div class="mb-3 d-none" id="editEventWrapper">
                        <label class="form-label">Event</label>
                        <input type="text" id="edit_eventDisplay" class="form-input" readonly>
                        <input type="hidden" name="event_id" id="edit_eventHidden">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Sub Group</button>
                </div>

            </form>

        </div>
    </div>
</div>

