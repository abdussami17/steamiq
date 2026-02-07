<!-- Import Teams Modal -->
<div class="modal fade" id="importTeamsModal" tabindex="-1">
    <div class="modal-dialog">
      <form action="{{ route('teams.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Import Teams</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label class="form-label">Select Event</label>
              <select name="event_id" class="form-select" required>
                <option value="" hidden>-- Select Event --</option>
                @foreach($events as $event)
                  <option value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach
              </select>
            </div>
  
            <div class="form-group mt-2">
              <label class="form-label">Upload CSV/XLSX</label>
              <input type="file" name="file" accept=".xlsx,.csv" class="form-control" required>
            </div>
  
            <small class="text-muted">
              File format: Team Name | Player Emails (comma separated)
            </small>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Import Teams</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  