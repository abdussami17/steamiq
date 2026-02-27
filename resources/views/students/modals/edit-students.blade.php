<div class="modal fade" id="editPlayerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Player</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editPlayerForm">
                @csrf
                <input type="hidden" name="player_id" id="editPlayerId">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-input" name="name" id="editPlayerName">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email </label>
                        <input type="email" class="form-input" name="email" id="editPlayerEmail">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Event <span class="text-danger">*</span></label>
                        <select class="form-select" name="event_id" id="editPlayerEvent"></select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Team (optional)</label>
                        <select class="form-select" name="team_id" id="editPlayerTeam">
                            <option value="">-- None --</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Player</button>
                </div>
            </form>
        </div>
    </div>
</div>
