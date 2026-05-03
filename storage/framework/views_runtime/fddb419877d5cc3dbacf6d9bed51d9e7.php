<div class="modal fade" id="assignBonusModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">

      

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Assign Bonus Points</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo e(route('bonus.assign.store')); ?>" method="POST" id="assignBonusFormUnique98765">
                    <?php echo csrf_field(); ?>
                <div class="modal-body">

                    <input type="hidden" name="assignable_type" id="assignableTypeInputUnique98765">
                    <input type="hidden" name="assignable_id" id="assignableIdFinalUnique98765">

                    <div class="row g-3">

                        <!-- Assign To -->
                        <div class="col-md-6">
                            <label class="form-label">Assign To</label>
                            <select class="form-select" id="assignToSelectUnique98765">
                                <option value="">-- Select --</option>
                                <option value="organization">Organization</option>
                                <option value="group">Group</option>
                                <option value="team">Team</option>
                                <option value="student">Player</option>
                            </select>
                        </div>

                        <!-- First Level -->
                        <div class="col-md-6" id="firstLevelContainerUnique98765" style="display:none;">
                            <label class="form-label" id="firstLevelLabelUnique98765"></label>
                            <select class="form-select" id="firstLevelDropdownUnique98765"></select>
                        </div>

                        <!-- Second Level -->
                        <div class="col-md-6" id="secondLevelContainerUnique98765" style="display:none;">
                            <label class="form-label" id="secondLevelLabelUnique98765"></label>
                            <select class="form-select" id="secondLevelDropdownUnique98765"></select>
                        </div>

                        <!-- Direct -->
                        <div class="col-md-6" id="directContainerUnique98765" style="display:none;">
                            <label class="form-label" id="directLabelUnique98765"></label>
                            <select class="form-select" id="directDropdownUnique98765"></select>
                        </div>

                        <!-- Points -->
                        <div class="col-md-6">
                            <label class="form-label">Enter Points</label>
                            <input type="number" name="points" class="form-input" required>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Assign Bonus</button>
                </div>
            </form>
            </div>

      
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
    <?php echo $__env->make('bonus.script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?><?php /**PATH C:\Users\PC\Downloads\steamiq (8)\resources\views/bonus/modals/assign-bonus.blade.php ENDPATH**/ ?>