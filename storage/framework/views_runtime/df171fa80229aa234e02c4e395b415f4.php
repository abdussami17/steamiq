<div class="modal fade" id="addCardModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add New Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="<?php echo e(route('cards.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Card Type</label>
                        <select class="form-select" name="type" id="cardType" required>
                            <option value="">Select</option>
                            <option value="yellow">Yellow</option>
                            <option value="orange">Orange</option>
                            <option value="red">Red</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Negative Points</label>
                        <input type="number" min="0" required name="negative_points" id="negativePoints" class="form-input" value="">
                        <small id="redMessage" class="text-muted d-none">For Red card, all points will be deducted (set to 0).</small>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Card</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cardType = document.getElementById('cardType');
    const negativePoints = document.getElementById('negativePoints');
    const redMessage = document.getElementById('redMessage');

    cardType.addEventListener('change', function() {
        if(this.value === 'red'){
            negativePoints.value = 0;
            negativePoints.disabled = true;
            redMessage.classList.remove('d-none');
        } else {
            negativePoints.disabled = false;
            negativePoints.value = '';
            redMessage.classList.add('d-none');
        }
    });
});
</script><?php /**PATH C:\Users\PC\Downloads\steamiq (5)\resources\views/card/create.blade.php ENDPATH**/ ?>