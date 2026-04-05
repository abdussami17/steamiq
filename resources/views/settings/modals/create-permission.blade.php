<!-- Add Student Modal -->
<div class="modal fade" id="addPermissionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('permissions.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="row">
                    
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Permission Name</label>
                           <input type="text" class="form-input" name="name" required>
                        </div>
                    
                    </div>

                

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Permission</button>
                </div>
            </form>

        </div>
    </div>
</div>
