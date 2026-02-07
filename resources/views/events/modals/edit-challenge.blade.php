<div class="modal fade" id="editChallengeModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editChallengeForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Challenge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="challengeId">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" id="challengeName" name="name" class="form-input" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Max Points</label>
                        <input type="number" id="challengeMaxPoints" name="max_points" class="form-input" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
