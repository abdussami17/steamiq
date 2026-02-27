<div class="modal fade" id="add_team" tabindex="-1" aria-labelledby="teamModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add New Team</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="teamForm" method="POST" action="{{ route('teams.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Team Name </label>
                            <input type="text" name="team_name" class="form-input" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Subgroup </label>
                            <select name="sub_group_id" class="form-select" required>
                                <option hidden>-- Select Subgroup --</option>
                                @foreach($subgroups as $subgroup)
                                    <option value="{{ $subgroup->id }}">
                                        {{ $subgroup->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Team Avatar <small class="text-muted">(optional)</small></label>
                            <input type="file" name="profile" class="form-input" accept="image/*">
                            <small class="text-muted">jpg, jpeg, png (max 2MB)</small>
                        </div>

                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="teamForm" class="btn btn-primary">Save Team</button>
            </div>

        </div>
    </div>
</div>