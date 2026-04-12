<div class="modal fade" id="createGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Create Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="<?php echo e(route('groups.store')); ?>">
                <?php echo csrf_field(); ?>

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
                            <?php $__currentLoopData = $organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $org): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($org->id); ?>"><?php echo e($org->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            POD 
                        </label>
                        <select name="pod"
                               
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
                    <button type="submit" class="btn btn-primary">Create Group</button>
                </div>

            </form>

        </div>
    </div>
</div>

<?php /**PATH C:\Users\PC\Downloads\steamiq (5)\resources\views/groups/modals/create-group.blade.php ENDPATH**/ ?>