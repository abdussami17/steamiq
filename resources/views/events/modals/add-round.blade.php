<!-- Add Round Modal -->
<div id="addRoundModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title">Add Round Result</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <select id="roundWinner" class="form-select mb-4">
          <option value="">-- Select Winner --</option>
        </select>
        <button class="btn btn-primary w-100" onclick="submitRound()">Submit Round</button>
      </div>
    </div>
  </div>
  