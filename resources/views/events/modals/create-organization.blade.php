<!-- Create Organization Modal -->
<div class="modal fade" id="createOrganizationModal" tabindex="-1" aria-labelledby="createOrganizationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="createOrganizationLabel">Create Organization</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Form -->
            <form method="POST"
                  action="{{ route('organizations.store') }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label">
                                Organization Name
                            </label>
                            <input type="text"
                                   name="name"
                                   class="form-input"
                                   value="{{ old('name') }}"
                                   >
                        </div>

                        <!-- Type -->
                        <div class="col-md-6">
                            <label class="form-label">Organization Type</label>
                            <select class="form-select" name="organization_type">
                                <option value="" hidden>--Select Type--</option>
                                <option value="School">School</option>
                                <option value="Parks and Recreation">Parks and Recreation</option>
                                <option value="Youth Organization">Youth Organization</option>
                                <option value="Other">Other</option>

                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <small class="text-muted">(optional)</small></label>
                           <input type="text" class="form-input" name="email" value="{{ old('address') }}">
                        </div>
                  

                        <!-- Photo -->
                        <div class="col-md-6">
                            <label class="form-label">Photo  <small class="text-muted">(optional)</small></label>
                            <input type="file"
                                   name="profile"
                                   class="form-input"
                                   accept="image/*">
                            <small class="text-muted">jpg, png, jpeg (max 2MB)</small>
                        </div>

                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Organization</button>
                </div>

            </form>

        </div>
    </div>
</div>
