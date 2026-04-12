<div class="modal fade" id="editSubGroupModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Sub Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editSubGroupForm" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Sub Group Name</label>
                        <input type="text" id="subgroupname" value="" name="name" class="form-input" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Group</label>
                        <select name="group_id" id="edit_group" class="form-select" required>
                            <option value="" hidden>--Select Group--</option>
                            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                           
                                    <option value="<?php echo e($grp->id); ?>"
                                        >
                                        <?php echo e($grp->group_name); ?>

                                    </option>
                           
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

<?php /**PATH /home/u236413684/domains/voags.com/public_html/steamiq/resources/views/subgroups/modals/edit-subgroup.blade.php ENDPATH**/ ?>