<div class="modal fade" id="editUserModal<?php echo e($us->id); ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('users.update', $us->id)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="modal-header">
                    <h5 class="modal-title">Edit User - <?php echo e($us->name); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-input" value="<?php echo e($us->name); ?>" required>
                    </div>

                    <!-- Username -->
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-input" value="<?php echo e($us->username); ?>" required>
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                    
                        <select name="role" class="form-input">
                            <option value="">-- No Role --</option>
                    
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                                <?php if($role->name !== 'admin'): ?>
                    
                                    <option value="<?php echo e($role->name); ?>"
                                        <?php echo e($us->hasRole($role->name) ? 'selected' : ''); ?>>
                                        <?php echo e(ucfirst($role->name)); ?>

                                    </option>
                    
                                <?php endif; ?>
                    
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <!-- Password -->
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">New Password</label>
        <input type="password"
               name="password"
               class="form-input"
               placeholder="Leave blank to keep current password">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password"
               name="password_confirmation"
               class="form-input"
               placeholder="Confirm new password">
    </div>
</div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>

            </form>
        </div>
    </div>
</div><?php /**PATH C:\Users\PC\Desktop\steam-two\resources\views/settings/modals/edit-user.blade.php ENDPATH**/ ?>