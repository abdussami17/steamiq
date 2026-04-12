<!-- Edit Group Modal -->
<div class="modal fade" id="editGroupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editGroupForm" method="POST">
                <?php echo csrf_field(); ?>

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

                    <div class="mb-3">
                        <label class="form-label">
                            Organization 
                        </label>
                        <select name="organization_id"
                               id="org_id"
                                class="form-select"
                                >
                            <option value="" hidden>--Select Organization--</option>
                            <?php $__currentLoopData = $organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $org): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($org->id); ?>"><?php echo e($org->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            POD 
                        </label>
                        <select id="editPod" name="pod"
                               
                                class="form-select"
                                >
                                <option value="">-- Select POD --</option>
                           <option value="Red">Red</option>
                           <option value="Blue">Blue</option>

                        </select>
                    </div>


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Group</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    function openGroupEditModal(id, name,pod,org)
    {
        // fill fields
        document.getElementById('edit_group_id').value = id;
        document.getElementById('edit_group_name').value = name;
        document.getElementById('editPod').value= pod;

    

        const orgSelect = document.getElementById('org_id');
    for (let i = 0; i < orgSelect.options.length; i++) {
        orgSelect.options[i].selected = orgSelect.options[i].value == org;
    }
    

        // set dynamic action
        document.getElementById('editGroupForm').action = `/groups/update/${id}`;
    
        // open modal
        const modal = new bootstrap.Modal(document.getElementById('editGroupModal'));
        modal.show();
    }
    </script><?php /**PATH C:\Users\PC\Downloads\steamiq (5)\resources\views/groups/modals/edit-group.blade.php ENDPATH**/ ?>