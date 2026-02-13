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
                                Organization Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   class="form-input"
                                   value="{{ old('name') }}"
                                   required>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email"
                                   name="email"
                                   class="form-input"
                                   value="{{ old('email') }}">
                        </div>

                        <!-- Address -->
                        <div class="col-md-12">
                            <label class="form-label">Address</label>
                            <textarea name="address"
                                      rows="2"
                                      class="form-input">{{ old('address') }}</textarea>
                        </div>

                        <!-- Photo -->
                        <div class="col-md-12">
                            <label class="form-label">Photo</label>
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
