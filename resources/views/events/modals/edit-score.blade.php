<!-- View/Edit Score Modal -->
<div class="modal fade" id="viewEditScoreModal" tabindex="-1">
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
                        <label>Player</label>
                        <select id="scorePlayer" name="player_id" class="form-select" required>
                            <option value="" hidden>Select Player</option>
                            @foreach(\App\Models\Player::all() as $player)
                                <option value="{{ $player->id }}">{{ $player->name }} ({{ $player->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>CAM Pillar</label>
                        <select id="scorePillar" name="pillar_id" class="form-select" required>
                            <option value="" hidden>Select CAM Pillar</option>
                            @foreach(\App\Models\Challenges::all() as $challenge)
                                <option value="{{ $challenge->id }}">{{ $challenge->pillar_type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Category/Game</label>
                        <input type="text" id="scoreCategory" class="form-control" name="category" required>
                    </div>

                    <div class="mb-3">
                        <label>Points</label>
                        <input type="number" id="scorePoints" class="form-control" name="points" required>
                    </div>

                    <div class="mb-3">
                        <label>Date</label>
                        <input type="date" id="scoreDate" class="form-control" name="date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Score</button>
                </div>
            </form>
        </div>
    </div>
</div>
