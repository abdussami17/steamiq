<div class="modal fade" id="createGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Create Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('groups.store') }}">
                @csrf

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">
                            Group Name
                        </label>
                        <input type="text"
                               name="group_name"
                               class="form-input"
                               >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Organization 
                        </label>
                        <select name="organization_id"
                               
                                class="form-select"
                                >
                            <option value="" hidden>--Select Organization--</option>
                            @foreach ($organizations as $org )
                                <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Group</button>
                </div>

            </form>

        </div>
    </div>
</div>

