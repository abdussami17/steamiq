<div class="modal fade" id="editScoreModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="editScoreForm">
  
          <div class="modal-header">
            <h5 class="modal-title">Edit Score</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
  
          <div class="modal-body">
  
            <input type="hidden" id="scoreId" name="score_id">
 
  
  
            <div class="mb-3">
              <label class="form-label">Points</label>
              <input type="number" id="scorePoints" name="points" class="form-input" required>
            </div>
  
          </div>
  
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
  
        </form>
      </div>
    </div>
  </div>
  