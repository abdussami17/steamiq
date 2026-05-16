<div class="modal fade" id="add_team" tabindex="-1" aria-labelledby="teamModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add New Team</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="teamForm" method="POST" action="<?php echo e(route('teams.store')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Team Name </label>
                            <input type="text" name="team_name" class="form-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Organization</label>
                            <select name="organization_id"  id="teamOrganizationSelect" class="form-select" required>
                                <option value="">-- Select Organization --</option>
                                <?php $__currentLoopData = $organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $org): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($org->id); ?>"><?php echo e($org->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Group</label>
                            <select name="group_id"  id="groupSelect" class="form-select" required>
                                <option value="">-- Select Group --</option>
                               
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Subgroup <small class="text-muted">(optional)</small></label>
                            <select name="sub_group_id" id="subgroupSelect" class="form-select">
                                <option value="">-- Select Subgroup --</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Division</label>
                            <select name="division" class="form-select" >
                                <option value="">-- Select Division --</option>
                                <option value="Junior">Junior</option>   
                                <option value="Primary">Primary</option>                        

                            </select>
                        </div>
                        <div class="col-md-6">
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

<script>
    document.addEventListener('DOMContentLoaded', function(){
    
        const orgSelect = document.getElementById('teamOrganizationSelect');
        const groupSelect = document.getElementById('groupSelect');
        const subgroupSelect = document.getElementById('subgroupSelect');
    
        orgSelect.addEventListener('change', function(){
    
            const orgId = this.value;
    
            groupSelect.innerHTML = '<option value="">-- Select Group --</option>';
            subgroupSelect.innerHTML = '<option value="">-- Select Subgroup --</option>';
    
            if(!orgId) return;
    
            fetch(`/organization/${orgId}/groups`)
            .then(res => res.json())
            .then(data => {
    
                data.forEach(group => {
                    const option = document.createElement('option');
                    option.value = group.id;
                    option.textContent = group.group_name;
                    groupSelect.appendChild(option);
                });
    
            });
    
        });
    
        groupSelect.addEventListener('change', function(){
    
            const groupId = this.value;
    
            subgroupSelect.innerHTML = '<option value="">-- Select Subgroup --</option>';
    
            if(!groupId) return;
    
            fetch(`/groups/${groupId}/subgroups`)
            .then(res => res.json())
            .then(data => {
    
                data.forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.id;
                    option.textContent = sub.name;
                    subgroupSelect.appendChild(option);
                });
    
            });
    
        });
    
    });
    </script><?php /**PATH C:\Users\PC\Desktop\steam-two\resources\views/teams/modals/create-team.blade.php ENDPATH**/ ?>