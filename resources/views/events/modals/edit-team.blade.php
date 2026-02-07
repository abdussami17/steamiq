<!-- Edit Team Modal -->
<div class="modal fade" id="editTeamModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="editTeamForm">
          <div class="modal-header">
            <h5 class="modal-title">Edit Team</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="editTeamId" name="team_id">
            
            <div class="mb-3">
              <label class="form-label">Team Name</label>
              <input type="text" class="form-input" name="team_name" id="editTeamName" required>
            </div>
  
            <div class="mb-3">
              <label class="form-label">Event</label>
              <select class="form-select" name="event_id" id="editTeamEvent" required>
                <option value="" hidden>Select Event</option>
                @foreach(\App\Models\Event::orderByDesc('start_date')->get() as $ev)
                  <option value="{{ $ev->id }}">{{ $ev->name }}</option>
                @endforeach
              </select>
            </div>
  
            <div class="mb-3">
              <label class="form-label">Players</label>
              <select class="form-select mb-2" name="players[]" id="editTeamPlayers" multiple required>
                @foreach(\App\Models\Player::all() as $player)
                  <option value="{{ $player->id }}">{{ $player->name }} ({{ $player->email }})</option>
                @endforeach
              </select>
              <small class="small text-white ">Hold Ctrl And Select Players</small>
            </div>
          </div>
  
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Update Team</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  