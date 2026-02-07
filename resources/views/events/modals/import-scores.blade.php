<!-- Import Scores Modal -->
<div class="modal fade" id="importScoresModal" tabindex="-1">
    <div class="modal-dialog">
      <form action="{{ route('scores.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
          
          <div class="modal-header">
            <h5 class="modal-title">Bulk Import Scores</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">

            <!-- File Upload -->
            <div class="mb-3">
              <label class="form-label">Upload CSV/XLSX</label>
              <input type="file" name="file" accept=".csv,.xlsx" class="form-control" required>
            </div>

            <small class="text-white small">
              File format: <strong>player_email, challenge_name, points</strong><br>
              Example:<br>
              ali@mail.com, Brain Quiz 1, 10<br>
              ahmed@mail.com, Obstacle 1, 15<br>
              sara@mail.com, AquaBall, 12<br>
              <br>
              Event will be detected automatically based on the player.
            </small>

          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Import Scores</button>
          </div>

        </div>
      </form>
    </div>
</div>
