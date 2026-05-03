<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal<?php echo e($role->id); ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('roles.update', $role->id)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="modal-header">
                    <h5 class="modal-title">Edit Role - <?php echo e($role->name); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Role Name -->
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-input" value="<?php echo e($role->name); ?>" placeholder="Enter role name" required>
                    </div>

                    <!-- Permissions -->
                    <label class="form-label">Permissions</label>
                    <div class="row" style="max-height:300px; overflow-y:auto;">
                        <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-4 col-sm-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="<?php echo e($perm->name); ?>" 
                                           class="form-check-input" 
                                           id="perm<?php echo e($role->id); ?><?php echo e($perm->id); ?>"
                                           <?php echo e($role->permissions->contains('id', $perm->id) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="perm<?php echo e($role->id); ?><?php echo e($perm->id); ?>">
                                        <?php echo e($perm->label); ?>

                                    </label>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>

            </form>
        </div>
    </div>
</div><?php /**PATH C:\Users\PC\Downloads\steamiq (8)\resources\views/settings/modals/edit-roles.blade.php ENDPATH**/ ?>