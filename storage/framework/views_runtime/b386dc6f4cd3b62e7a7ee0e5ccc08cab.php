<div class="modal fade" id="editPlayerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Edit Player</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="editPlayerId">

                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" id="editPlayerName" class="form-input">
                </div>

                <div class="mb-3">
                    <label>Team</label>
                    <select id="editPlayerTeam" class="form-select"></select>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary" onclick="updatePlayer()">Update</button>
            </div>

        </div>
    </div>
</div><?php /**PATH C:\Users\PC\Downloads\steamiq (8)\resources\views/students/modals/edit-students.blade.php ENDPATH**/ ?>