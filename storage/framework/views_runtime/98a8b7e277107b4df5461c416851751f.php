<div class="modal fade" id="editCardModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editCardForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Card Type</label>
                        <select class="form-select" name="type" id="editCardType" required>
                            <option value="">Select</option>
                            <option value="yellow">Yellow</option>
                            <option value="orange">Orange</option>
                            <option value="red">Red</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Negative Points</label>
                        <input type="number" min="0" name="negative_points" id="editNegativePoints" class="form-input">
                        <small id="editRedMessage" class="text-muted d-none">All points will be deducted for Red card.</small>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Card</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editForm = document.getElementById('editCardForm');
        const cardType = document.getElementById('editCardType');
        const negativePoints = document.getElementById('editNegativePoints');
        const redMessage = document.getElementById('editRedMessage');
    
        document.querySelectorAll('.editCardBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const type = this.dataset.type;
                const points = this.dataset.points;
    
                editForm.action = `/cards/${id}/update`; // dynamic action
    
                cardType.value = type;
                negativePoints.value = type === 'red' ? 0 : points;
                negativePoints.disabled = type === 'red';
                redMessage.classList.toggle('d-none', type !== 'red');
            });
        });
    
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
    </script><?php /**PATH /home/u236413684/domains/voags.com/public_html/steamiq/resources/views/card/edit.blade.php ENDPATH**/ ?>