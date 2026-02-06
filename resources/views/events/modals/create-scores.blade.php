<div class="modal fade" id="scoreModal" tabindex="-1" aria-labelledby="scoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scoreModalLabel">Add Score Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('scores.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Event <span class="text-danger">*</span></label>
                            <select name="event_id" id="scoreEvent" class="form-input" >
                                <option value="">-- Select Event --</option>
                                @foreach($events as $event)
                                    @if($event->status !== 'closed')
                                        <option value="{{ $event->id }}">{{ $event->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Player <span class="text-danger">*</span></label>
                            <select name="player_id" id="scorePlayer" class="form-input" >
                                <option value="">-- Select Player --</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">CAM Pillar <span class="text-danger">*</span></label>
                            <select name="pillar_type" id="scorePillar" class="form-input" >
                                <option value="">-- Select Pillar --</option>
                                <option value="brain">Brain</option>
                                <option value="playground">Playground</option>
                                <option value="egame">E-Gaming</option>
                            </select>
                        </div>

                        <div class="col-md-4" id="subcategoryWrapper" style="display:none;">
                            <label class="form-label">Brain Subcategory <span class="text-danger">*</span></label>
                            <select name="sub_category" id="scoreSubCategory" class="form-input">
                                <option value="">-- Select Subcategory --</option>
                                <option value="science">Science</option>
                                <option value="technology">Technology</option>
                                <option value="engineering">Engineering</option>
                                <option value="art">Art</option>
                                <option value="math">Math</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Challenge <span class="text-danger">*</span></label>
                            <select name="challenge_id" id="scoreChallenge" class="form-input" >
                                <option value="">-- Select Challenge --</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Points <span class="text-danger">*</span></label>
                            <input type="number" name="points" class="form-input" min="0" >
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Score</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const eventSelect = document.getElementById('scoreEvent');
    const playerSelect = document.getElementById('scorePlayer');
    const pillarSelect = document.getElementById('scorePillar');
    const challengeSelect = document.getElementById('scoreChallenge');
    const subcategoryWrapper = document.getElementById('subcategoryWrapper');
    const subcategorySelect = document.getElementById('scoreSubCategory');

    const players = @json($allplayers);
    const challenges = @json($challenges);

    function populatePlayers(eventId) {
        playerSelect.innerHTML = '<option value="">-- Select Player --</option>';
        players.filter(p => p.event_id == eventId)
               .forEach(p => {
                   const option = document.createElement('option');
                   option.value = p.id;
                   option.textContent = p.name;
                   playerSelect.appendChild(option);
               });
    }

    function populateChallenges(eventId, pillar) {
        challengeSelect.innerHTML = '<option value="">-- Select Challenge --</option>';
        challenges.filter(c => c.event_id == eventId && c.pillar_type == pillar)
                  .forEach(c => {
                      const option = document.createElement('option');
                      option.value = c.id;
                      option.textContent = c.name;
                      challengeSelect.appendChild(option);
                  });
    }

    eventSelect.addEventListener('change', function () {
        populatePlayers(this.value);
        populateChallenges(this.value, pillarSelect.value);
    });

    pillarSelect.addEventListener('change', function () {
        const eventId = eventSelect.value;
        populateChallenges(eventId, this.value);

        if (this.value === 'brain') {
            subcategoryWrapper.style.display = 'block';
         
        } else {
            subcategoryWrapper.style.display = 'none';
            
            subcategorySelect.value = '';
        }
    });
});
</script>
