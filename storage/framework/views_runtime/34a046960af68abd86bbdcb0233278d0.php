<!-- Edit Team Modal -->
<div class="modal fade" id="editTeamModal" tabindex="-1" aria-labelledby="editTeamModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <form id="editTeamForm" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="modal-header">
          <h5 class="modal-title" id="editTeamModalLabel">Edit Team</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="editTeamId" name="team_id">

          <div class="row g-3">
            <!-- Team Name -->
            <div class="mb-3 col-md-6">
              <label class="form-label">Team Name </label>
              <input type="text" class="form-input" name="team_name" id="editTeamName" required>
            </div>

            <!-- Organization -->
            <div class="mb-3 col-md-6">
              <label class="form-label">Organization</label>
              <select class="form-select" name="organization_id" id="editTeamOrganization" required>
                <option value="">-- Select Organization --</option>
              </select>
            </div>

            <!-- Group -->
            <div class="mb-3 col-md-6">
              <label class="form-label">Group </label>
              <select class="form-select" name="group_id" id="editTeamGroup" required>
                <option value="">-- Select Group --</option>
              </select>
            </div>

            <!-- Subgroup -->
            <div class="mb-3 col-md-6">
              <label class="form-label">Subgroup <small class="text-muted">(optional)</small></label>
              <select class="form-select" name="sub_group_id" id="editTeamSubgroup">
                <option value="">-- Select Subgroup --</option>
              </select>
            </div>

            <!-- Division -->
            <div class="mb-3 col-md-6">
              <label class="form-label">Division </label>
              <select class="form-select" name="division" id="editDivision" required>
                <option value="">-- Select Division --</option>
                <option value="Junior">Junior</option>
                <option value="Primary">Primary</option>
              </select>
            </div>

            <!-- Avatar -->
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
  const orgSelect = document.getElementById('editTeamOrganization');
  const groupSelect = document.getElementById('editTeamGroup');
  const subgroupSelect = document.getElementById('editTeamSubgroup');
  
  
  document.getElementById('editTeamModal')
.addEventListener('hidden.bs.modal', function () {
  document.getElementById('editTeamForm').reset();
  resetFileInput();
});

  function resetFileInput() {
  const input = document.querySelector('#editTeamForm input[type="file"]');
  if (!input) return;

  input.value = '';

  // fallback (important)
  if (input.value) {
    const newInput = input.cloneNode(true);
    input.parentNode.replaceChild(newInput, input);
  }
}
  // Open Edit Modal
  window.openEditTeamModal = function(teamId){
    const form = document.getElementById('editTeamForm');
  form.reset();
  resetFileInput();

    const modalEl = document.getElementById('editTeamModal');
    const modal = new bootstrap.Modal(modalEl);
    resetFileInput(); 
    fetch(`/teams/${teamId}`)
      .then(res => res.json())
      .then(data => {
        const team = data.team;

        document.getElementById('editTeamId').value = team.id;
        document.getElementById('editTeamName').value = team.name;
        document.getElementById('editDivision').value = team.division;

        // Populate organizations dropdown
        orgSelect.innerHTML = '<option value="">-- Select Organization --</option>';
        data.organizations.forEach(org => {
          const option = document.createElement('option');
          option.value = org.id;
          option.textContent = org.name;
          if(org.id == team.organization_id) option.selected = true;
          orgSelect.appendChild(option);
        });

        // Load groups for organization and preselect
        loadGroups(team.organization_id, team.group_id, team.sub_group_id);

        modal.show();
      });
  }

 
  // When organization changes, load corresponding groups
  orgSelect.addEventListener('change', function() {
    const orgId = this.value;
    groupSelect.innerHTML = '<option value="">-- Select Group --</option>';
    subgroupSelect.innerHTML = '<option value="">-- Select Subgroup --</option>';
    if(!orgId) return;
    loadGroups(orgId);
  });

  // When group changes, load corresponding subgroups
  groupSelect.addEventListener('change', function() {
    const groupId = this.value;
    subgroupSelect.innerHTML = '<option value="">-- Select Subgroup --</option>';
    if(!groupId) return;
    loadSubgroups(groupId);
  });

  // Load groups for given organization
  function loadGroups(orgId, selectedGroupId = null, selectedSubgroupId = null){
    groupSelect.innerHTML = '<option value="">-- Select Group --</option>';
    subgroupSelect.innerHTML = '<option value="">-- Select Subgroup --</option>';

    fetch(`/organization/${orgId}/groups`)
      .then(res => res.json())
      .then(groups => {
        groups.forEach(group => {
          const option = document.createElement('option');
          option.value = group.id;
          option.textContent = group.group_name;
          if(group.id == selectedGroupId) option.selected = true;
          groupSelect.appendChild(option);
        });

        // Load subgroups if a group is preselected
        if(selectedGroupId) loadSubgroups(selectedGroupId, selectedSubgroupId);
      });
  }

  // Load subgroups for given group
  function loadSubgroups(groupId, selectedId = null){
    subgroupSelect.innerHTML = '<option value="">-- Select Subgroup --</option>';
    if(!groupId) return;

    fetch(`/groups/${groupId}/subgroups`)
      .then(res => res.json())
      .then(subgroups => {
        subgroups.forEach(sub => {
          const option = document.createElement('option');
          option.value = sub.id;
          option.textContent = sub.name;
          if(sub.id == selectedId) option.selected = true;
          subgroupSelect.appendChild(option);
        });
      });
  }

  // Submit form
  editForm.addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    const teamId = formData.get('team_id');

    fetch(`/teams/update/${teamId}`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
      },
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if(data.success){
        bootstrap.Modal.getInstance(document.getElementById('editTeamModal')).hide();
        resetFileInput();
        fetchTeams(); // Refresh table
        toastr.success(data.message);
      } else {
        alert(data.message || 'Failed to update team');
      }
    })
    .catch(err => console.error(err));
  });
});
</script><?php /**PATH C:\Users\PC\Desktop\steam-two\resources\views/teams/modals/edit-team.blade.php ENDPATH**/ ?>