<!-- Add Player Modal -->
<div class="modal fade" id="playerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Player</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('player.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
            
                <div class="modal-body">
            
                    <div class="form-group mb-2">
                        <label class="form-label">Player Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-input" name="player_name" required>
                    </div>
            
                    <div class="form-group mb-2">
                        <label class="form-label">Email </label>
                        <input type="email" class="form-input" name="player_email" >
                    </div>
            
                    <div class="form-group mb-2">
                        <label class="form-label">Event <span class="text-danger">*</span></label>
                        <select class="form-select" name="event_id" required>
                            <option hidden>-- Select Event --</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>
            
                    <!-- NEW PROFILE FIELD -->
                    <div class="form-group mb-2">
                        <label class="form-label">Avatar</label>
                        <input type="file"
                               name="profile"
                               class="form-input"
                               accept="image/*">
                        <small class="text-muted">jpg, png, jpeg (max 2MB)</small>
                    </div>
            
                </div>
            
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Player</button>
                </div>
            </form>
            
        </div>
    </div>
</div>