<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('roles.update', $role->id) }}">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Role - {{ $role->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Role Name -->
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-input" value="{{ $role->name }}" placeholder="Enter role name" required>
                    </div>

                    <!-- Permissions -->
                    <label class="form-label">Permissions</label>
                    <div class="row" style="max-height:300px; overflow-y:auto;">
                        @foreach($permissions as $perm)
                            <div class="col-md-4 col-sm-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $perm->name }}" 
                                           class="form-check-input" 
                                           id="perm{{ $role->id }}{{ $perm->id }}"
                                           {{ $role->permissions->contains('id', $perm->id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm{{ $role->id }}{{ $perm->id }}">
                                        {{ $perm->label }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>

            </form>
        </div>
    </div>
</div>