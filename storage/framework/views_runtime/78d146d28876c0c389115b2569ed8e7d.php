    <!-- Choose Winner Modal -->
    <div class="modal fade" id="chooseWinnerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Event Winner</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="chooseWinnerBody">
                    <p>Choose the winning team for this event. This will close the event and publish final results.</p>
                    <p id="cw-context-note" style="font-size:13px;font-weight:600;margin-bottom:4px;"></p>
                    <div id="cw-team-list"
                        style="display:flex;flex-direction:column;gap:8px;max-height:320px;overflow:auto;padding-top:6px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="cw-finalize-btn">Finalize & Show Results</button>
                </div>
            </div>
        </div>
    </div><?php /**PATH /home/u236413684/domains/voags.com/public_html/steamiq/resources/views/events/modals/choose-winner.blade.php ENDPATH**/ ?>