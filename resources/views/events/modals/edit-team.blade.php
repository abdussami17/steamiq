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
              <label class="form-label">Group </label>
              <select class="form-select" name="group_id" id="editTeamGroup" required>
                <option value="">-- Select Group --</option>
                @foreach($groups as $group)
                  <option value="{{ $group->id }}">{{ $group->group_name }}</option>
                @endforeach
              </select>
            </div>
            
            <div class="mb-3 col-md-6">
              <label class="form-label">Subgroup <small class="text-muted">(optional)</small></label>
              <select class="form-select" name="sub_group_id" id="editTeamSubgroup">
                <option value="">-- Select Subgroup --</option>
              </select>
            </div>
            <div class="mb-3 col-md-6">
              <label class="form-label">Division </label>
              <select class="form-select" name="division" id="editDivision" required>
                <option value="">-- Select Division --</option>
                <option value="Junior">Junior</option>   
                <option value="Primary">Primary</option>   
              
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
  const groupSelect = document.getElementById('editTeamGroup');
  const subgroupSelect = document.getElementById('editTeamSubgroup');

  window.openEditTeamModal = function(teamId){
    const modalEl = document.getElementById('editTeamModal');
    const modal = new bootstrap.Modal(modalEl);

    fetch(`/teams/${teamId}`)
      .then(res => res.json())
      .then(data => {
        // Prefill fields
        document.getElementById('editTeamId').value = data.team.id;
        document.getElementById('editTeamName').value = data.team.name;
        document.getElementById('editDivision').value = data.team.division;

        // Prefill group
        groupSelect.value = data.team.group_id;

        // Prefill subgroup (optional)
        loadSubgroups(data.team.group_id, data.team.sub_group_id);

        modal.show();
      });
  }

  // Load subgroups dynamically
  groupSelect.addEventListener('change', function(){
    const groupId = this.value;
    loadSubgroups(groupId);
  });

  function loadSubgroups(groupId, selectedId = null){
    subgroupSelect.innerHTML = '<option value="">-- Select Subgroup --</option>';
    if(!groupId) return;

    fetch(`/groups/${groupId}/subgroups`)
      .then(res => res.json())
      .then(data => {
        data.forEach(sub => {
          const option = document.createElement('option');
          option.value = sub.id;
          option.textContent = sub.name;
          if(sub.id == selectedId) option.selected = true;
          subgroupSelect.appendChild(option);
        });
      });
  }

  // Update submit
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
        fetchTeams();// refresh table
        toastr.success(data.message);
      } else {
        alert(data.message || 'Failed to update team');
      }
    })
    .catch(err => console.error(err));
  });
});
  </script>