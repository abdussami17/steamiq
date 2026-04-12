<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="<?php echo e(route('roles.store')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">

                    <!-- Role Name -->
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" class="form-input" name="name" required>
                    </div>

                    <!-- Permissions -->
                    <label class="form-label">Permissions</label>
                    <div class="row" style="max-height:300px; overflow-y:auto;">
                        <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-4 col-sm-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" name="permissions[]" value="<?php echo e($permission->name); ?>" class="form-check-input" id="perm<?php echo e($permission->id); ?>">
                                    <label class="form-check-label" for="perm<?php echo e($permission->id); ?>">
                                        <?php echo e($permission->name); ?>

                                    </label>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Role</button>
                </div>
            </form>

        </div>
    </div>
</div><?php /**PATH /home/u236413684/domains/voags.com/public_html/steamiq/resources/views/settings/modals/create-roles.blade.php ENDPATH**/ ?>