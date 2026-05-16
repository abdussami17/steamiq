<!-- Assign Card Modal -->
<div class="modal fade" id="assignCardModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    
    <div class="modal-content">
      
      <form action="<?php echo e(route('card.assignments.store')); ?>" method="POST" id="assignCardForm">
        <?php echo csrf_field(); ?>

        <!-- Header -->
        <div class="modal-header">
          <h5 class="modal-title">Assign Card</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <input type="hidden" name="assignable_type" id="assignable_type">
          <input type="hidden" name="assignable_id" id="final_assignable_id">

          <div class="row g-3">

            <!-- Assigned To -->
            <div class="col-md-6">
              <label class="form-label">Assign To</label>
              <select class="form-select" id="assignedToSelect" required>
                <option value="">-- Select --</option>
                <option value="organization">Organization</option>
                <option value="group">Group</option>
                <option value="team">Team</option>
                <option value="student">Player</option>
              </select>
            </div>

            <!-- First Level -->
            <div class="col-md-6" id="firstLevelContainer" style="display:none;">
              <label class="form-label" id="firstLevelLabel">Select</label>
              <select class="form-select" id="firstLevelDropdown">
                <option value="">-- Choose --</option>
              </select>
            </div>

            <!-- Second Level -->
            <div class="col-md-6" id="secondLevelContainer" style="display:none;">
              <label class="form-label" id="secondLevelLabel">Select</label>
              <select class="form-select" id="secondLevelDropdown" name="temp_assignable_id">
                <option value="">-- Choose --</option>
              </select>
            </div>

            <!-- Direct -->
            <div class="col-md-6" id="directContainer" style="display:none;">
              <label class="form-label" id="directLabel">Select</label>
              <select class="form-select" id="directDropdown" name="temp_assignable_id">
                <option value="">-- Choose --</option>
              </select>
            </div>

            <!-- Card -->
            <div class="col-md-6">
              <label class="form-label">Select Card</label>
              <select class="form-select" name="card_id" required>
                <option value="">-- Choose Card --</option>
                <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($card->id); ?>"><?php echo e($card->type); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Assign Card</button>
        </div>

      </form>
    </div>

  </div>
</div><?php /**PATH C:\Users\PC\Desktop\steam-two\resources\views/card/assign-card-modal.blade.php ENDPATH**/ ?>