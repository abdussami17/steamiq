<!-- Assign Card Modal -->
<div class="modal fade" id="assignCardModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
      <form action="{{ route('card.assignments.store') }}" method="POST">
          @csrf
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">Assign Card to <span class="assignable-title"></span></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <input type="hidden" name="assignable_type" class="assignable-type">
                  <div class="mb-3">
                      <label class="form-label select-label">Select <span class="assignable-title"></span></label>
                      <select class="form-select assignable-select" name="assignable_id" required>
                          <option value="">-- Choose --</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label class="form-label">Select Card</label>
                      <select class="form-select" name="card_id" required>
                          <option value="">-- Choose Card --</option>
                          @foreach($cards as $card)
                              <option value="{{ $card->id }}">{{ $card->type }}</option>
                          @endforeach
                      </select>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Assign Card</button>
              </div>
          </div>
      </form>
  </div>
</div>