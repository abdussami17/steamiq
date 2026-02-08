<div class="modal fade" id="matchModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create New Match</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="matchForm">
          <div class="modal-body">
            <input type="hidden" id="matchId">
            <div class="mb-3">
                <label class="form-label">Select Event</label>
                <select class="form-select" id="matchEvent" required>
                  <option value="">-- Select Event --</option>
                  @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                  @endforeach
                </select>
              </div>
              
            <div class="mb-3">
              <label class="form-label">Match Name/Number *</label>
              <input type="text" class="form-input" id="matchName" placeholder="Match #1 - Semifinals" required>
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Team A *</label>
                <select class="form-select" id="matchTeamA" required>
                  <option value="">-- Select Team --</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Team B *</label>
                <select class="form-select" id="matchTeamB" required>
                  <option value="">-- Select Team --</option>
                </select>
              </div>
            </div>
            <div class="mt-3">
              <label class="form-label">Game Title</label>
              <input type="text" class="form-input" id="matchGame">
            </div>
            <div class="mt-3">
              <label class="form-label">Match Format</label>
              <select class="form-select" id="matchFormat">
                <option value="single">Single Round</option>
                <option value="bo3">Best of 3</option>
                <option value="bo5">Best of 5</option>
                <option value="custom">Custom</option>
              </select>
            </div>
            <div class="row g-3 mt-1">
              <div class="col-md-6">
                <label class="form-label">Date</label>
                <input type="date" class="form-input" id="matchDate" value="{{ date('Y-m-d') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Time</label>
                <input type="time" class="form-input" id="matchTime" value="{{ date('H:i') }}">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Create Match</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  