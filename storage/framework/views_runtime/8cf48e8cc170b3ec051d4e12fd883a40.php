<div class="modal fade" id="createSubGroupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Create Sub Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="<?php echo e(route('subgroups.store')); ?>">
                <?php echo csrf_field(); ?>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Sub Group Name</label>
                        <input type="text" name="name" class="form-input" required>
                    </div>
<div class="mb-3">
    <label class="form-label">Organization</label>
    <select id="organizationSelectSubgroup" class="form-select" required>
        <option value="" hidden>--Select Organization--</option>
        <?php $__currentLoopData = $organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $org): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($org->id); ?>"><?php echo e($org->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>
                 <div class="mb-3">
    <label class="form-label">Group</label>
    <select name="group_id" id="groupSelectOrganization" class="form-select" required>
        <option value="">--Select Group--</option>
    </select>
</div>

                    <div class="mb-3 d-none" id="eventWrapper">
                        <label class="form-label">Event</label>
                        <input type="text" id="eventDisplay" class="form-input" readonly>
                        <input type="hidden" name="event_id" id="eventHidden" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Sub Group</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    
        const orgSelect = document.getElementById('organizationSelectSubgroup');
        const groupSelect = document.getElementById('groupSelectOrganization');
    
        function fetchGroups(orgId) {
            groupSelect.innerHTML = '<option value="">Loading...</option>';
    
            fetch(`/get-org-groups/${orgId}`)
                .then(res => res.json())
                .then(data => {
    
                    groupSelect.innerHTML = '<option value="">--Select Group--</option>';
    
                    data.forEach(group => {
                        const option = document.createElement('option');
                        option.value = group.id;
                        option.textContent = group.group_name;
                        option.dataset.eventId = group.event_id ?? '';
                        option.dataset.eventName = group.event?.name ?? '';
                        groupSelect.appendChild(option);
                    });
                });
        }
    
 
    
        orgSelect.addEventListener('change', function () {
            const orgId = this.value;
    
            groupSelect.innerHTML = '<option value="">--Select Group--</option>';
    
            if (orgId) {
                fetchGroups(orgId);
            }
    
            updateEvent();
        });
    
        groupSelect.addEventListener('change', updateEvent);

    
    });
    </script><?php /**PATH C:\Users\PC\Downloads\steamiq (5)\resources\views/subgroups/modals/create-subgroup.blade.php ENDPATH**/ ?>