<!-- Edit Team Modal -->
<div class="modal fade" id="editTeamModal" tabindex="-1" aria-labelledby="editTeamModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <form id="editTeamForm" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="editTeamModalLabel">Edit Team</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="editTeamId" name="team_id">

          <div class="row g-3">
            <div class="mb-3 col-md-6">
              <label class="form-label">Team Name </label>
              <input type="text" class="form-input" name="team_name" id="editTeamName" required>
            </div>

            <div class="mb-3 col-md-6">
              <label class="form-label">Subgroup </label>
              <select class="form-select" name="sub_group_id" id="editTeamSubgroup" required>
                <option hidden>-- Select Subgroup --</option>
                @foreach($subgroups as $subgroup)
                  <option value="{{ $subgroup->id }}">{{ $subgroup->name }} (Event: {{ $subgroup->event->name }})</option>
                @endforeach
              </select>
            </div>

            <div class="mb-3 col-md-6">
              <label class="form-label">Team Avatar</label>
              <input type="file" class="form-input" name="profile" accept="image/*">
              <small class="text-muted">jpg, jpeg, png (max 2MB)</small>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Team</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const editForm = document.getElementById('editTeamForm');

  window.openEditTeamModal = function(teamId){
    const modal = new bootstrap.Modal(document.getElementById('editTeamModal'));
    const idInput = document.getElementById('editTeamId');
    const nameInput = document.getElementById('editTeamName');
    const subgroupSelect = document.getElementById('editTeamSubgroup');

    idInput.value = teamId;

    fetch(`/teams/${teamId}`)
      .then(res => res.json())
      .then(data => {
        nameInput.value = data.team.team_name;
        subgroupSelect.value = data.team.sub_group_id;
        modal.show();
      }).catch(err => console.error(err));
  }

  editForm.addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    const teamId = formData.get('team_id');

    fetch(`/teams/update/${teamId}`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
      },
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if(data.success){
        bootstrap.Modal.getInstance(document.getElementById('editTeamModal')).hide();
        fetchTeams(); // refresh table
      } else {
        alert(data.message || 'Failed to update team');
      }
    })
    .catch(err => console.error(err));
  });
});
</script> 