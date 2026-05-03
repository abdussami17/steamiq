@extends('layouts.app')
@section('title', 'STEAM XRS Manager')


@section('content')
    <div class="container">


        <!-- Settings -->
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon">
                        <i data-lucide="settings"></i>
                    </span>
                    Settings
                </h2>
            </div>


<div class="tabs-setting-wrappper">
    @role('admin')
    <button class="tab active" onclick="switchTab('users', event)">Users</button>
    <button class="tab" onclick="switchTab('roles', event)">Roles</button>
    {{-- <button class="tab" onclick="switchTab('permissions', event)">Permission</button> --}}
    <button class="tab" onclick="switchTab('activities', event)">Activities</button>
    <button class="tab" onclick="switchTab('cards', event)">Cards</button>
    <button class="tab" onclick="switchTab('cards-history', event)">Cards History</button>
@endrole

<button class="tab {{ !auth()->user()->hasRole('admin') ? 'active' : '' }}"
    onclick="switchTab('profile', event)">
    Profile
</button>
</div>

@role('admin')

            <div id="users-tab" class="tab-content show active">
                <div class="spreadsheet-container mt-4">
                    <div class="spreadsheet-toolbar">
                        <input type="text" id="userSearch" class="form-input" placeholder="Search User..."
                            style="width:300px;">
                            <button class="btn btn-danger" id="deleteSelectedUsersBtn" onclick="deleteSelectedUsers()">
                                Delete Selected (0)
                            </button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAllUsers">
                                </th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th></th>

                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            @forelse ($users as $us)
                                <tr>
                                    <td>
                                        @if(!$us->hasRole('admin'))
                                            <input type="checkbox" class="user-checkbox" value="{{ $us->id }}">
                                        @endif
                                    </td>
                                    <td>{{ $us->id ?? 'N/A' }}</td>
                                    <td>{{ $us->name ?? 'N/A' }}</td>
                                    <td>{{ $us->username ?? 'N/A' }}</td>
                                    <td>{{ $us->email ?? 'N/A' }}</td>
                                    <td>
                                        @if ($us->roles->count() > 0)
                                            {{ $us->roles->pluck('name')->map(fn($r) => ucfirst($r))->join(', ') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        {{ $us->created_at ? \Carbon\Carbon::parse($us->created_at)->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        @if (!$us->hasRole('admin'))
                                            @push('modals')
                                                @include('settings.modals.edit-user')
                                            @endpush
                                            <div class="d-flex gap-2">
                                                <!-- Edit Button triggers modal -->
                                                <button type="button" class="btn btn-icon btn-edit" data-bs-toggle="modal"
                                                    data-bs-target="#editUserModal{{ $us->id }}">
                                                    <i data-lucide="pencil"></i>
                                                </button>
                                                <form action="{{ route('setting.users.destroy', $us->id) }}" method="POST"
                                                    class="delete-form" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="button" class="btn btn-icon btn-delete delete-btn">
                                                        <i data-lucide="trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted">Admin</span>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No users currently</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                    @push('scripts')
                    @include('settings.scripts.users-bulk')
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                        
                            document.querySelectorAll('.delete-btn').forEach(button => {
                                button.addEventListener('click', function () {
                        
                                    let form = this.closest('form');
                        
                                    Swal.fire({
                                        title: 'Are you sure?',
                                        text: "User will be permanently deleted!",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#d33',
                                        confirmButtonText: 'Yes, delete it!'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            form.submit(); 
                                        }
                                    });
                        
                                });
                            });
                        
                        });
                        </script>
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                // Simple search by Team Name
                                document.getElementById('userSearch').addEventListener('input', function() {
                                    const filter = this.value.toLowerCase();
                                    const rows = document.querySelectorAll('#userTableBody tr');
                                    rows.forEach(row => {
                                        const cell = row.cells[1]; // Team Name column
                                        if (!cell) {
                                            row.style.display = 'none';
                                            return;
                                        }
                                        const name = cell.querySelector('input') ? cell.querySelector('input').value
                                            .toLowerCase() : cell.textContent.toLowerCase();
                                        row.style.display = name.includes(filter) ? '' : 'none';
                                    });
                                });
                            })
                        </script>
                    @endpush


                </div>
            </div>

            <div class="tab-content" id="activities-tab">
                <div class="spreadsheet-container mt-4">

                    <!-- Filters -->
                    <div class="activities-filter-container d-flex align-items-center gap-2 mb-3">
                        <input type="text" id="activities-filter-search-input" class="w-25 form-input"
                            placeholder="Search activities...">

                        <select id="activities-filter-date-range" class=" form-select w-25">
                            <option value="all">All Dates</option>
                            <option value="24h">Last 24 Hours</option>
                            <option value="3d">Last 3 Days</option>
                            <option value="30d">Last 30 Days</option>
                            <option value="6m">Last 6 Months</option>
                        </select>
                    </div>

                    <table id="activities-data-table" class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Event Name</th>
                                <th>Type</th>
                                <th>Activity Type</th>
                                <th>Activity Name</th>
                                <th>Max Score</th>
                                <th>Point Structure</th>
                                <th>Created At</th>

                            </tr>
                        </thead>
                        <tbody id="activities-data-table-body">
                            <tr>
                                <td colspan="8" class="text-center">Loading activities...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- <div class="tab-content" id="permissions-tab">
                <div class="spreadsheet-container mt-4">
                    <div class="spreadsheet-toolbar">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPermissionModal"><i
                                data-lucide="plus"></i>Add Permission</button>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($permissions as $perms)
                                <tr>
                                    <td>{{ $perms->id }}</td>
                                    <td>{{ $perms->label }}</td>
                                    <td>
                                        <form action="{{ route('permissions.destroy', $perms->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this permission?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-delete"><i
                                                    data-lucide="trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">No data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @push('modals')
                    @include('settings.modals.create-permission')
                @endpush
            </div> --}}

            <div class="tab-content" id="roles-tab">
                <div class="spreadsheet-container mt-4">
                    <div class="spreadsheet-toolbar">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal"><i
                                data-lucide="plus"></i>Add Role</button>
                                <button class="btn btn-danger" id="deleteSelectedRolesBtn" onclick="deleteSelectedRoles()">
                                    Delete Selected (0)
                                </button>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAllRoles">
                                </th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Permissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $role)
                                <tr>
                                    <td>
                                        @if($role->name !== 'admin')
                                            <input type="checkbox" class="role-checkbox" value="{{ $role->id }}">
                                        @endif
                                    </td>
                                    <td>{{ $role->id }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        @if ($role->name === 'admin')
                                            <span class="text-success fw-bold">ALL Permissions</span>
                                        @elseif($role->permissions->count())
                                            {{ $role->permissions->pluck('label')->join(', ') }}
                                        @else
                                            <span class="text-muted">No permissions</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($role->name !== 'admin')
                                        <div class="d-flex gap-2">

                                            <!-- Edit -->
                                            <button class="btn btn-icon btn-edit" data-bs-toggle="modal"
                                                data-bs-target="#editRoleModal{{ $role->id }}">
                                                <i data-lucide="pencil"></i>
                                            </button>
                                    
                                            <!-- Delete -->
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="delete-role-form">
                                                @csrf
                                                @method('DELETE')
                                    
                                                <button type="button" class="btn btn-icon btn-delete delete-role-btn">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                            </form>
                                    
                                        </div>
                                        @else
                                            <span class="text-muted">Not editable</span>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Edit Role Modal -->
                                @if ($role->name !== 'admin')
                                    @push('modals')
                                        @include('settings.modals.edit-roles')
                                    @endpush
                                @endif
                            @empty
                                <tr>
                                    <td colspan="4">No data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @push('scripts')
                    @include('settings.scripts.roles-bulk')
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                        
                            document.querySelectorAll('.delete-role-btn').forEach(button => {
                                button.addEventListener('click', function () {
                        
                                    let form = this.closest('form');
                        
                                    Swal.fire({
                                        title: 'Are you sure?',
                                        text: "Role will be permanently deleted!",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#d33',
                                        confirmButtonText: 'Yes, delete it!'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            form.submit(); 
                                        }
                                    });
                        
                                });
                            });
                        
                        });
                        </script>
                        
                    @endpush
                </div>
                @push('modals')
                    @include('settings.modals.create-roles')
                @endpush
            </div>
            <div id="cards-tab" class="tab-content">
                <div class="spreadsheet-container mt-4">
                    <div class="spreadsheet-toolbar">
                        <button class="btn btn-primary" data-bs-target="#addCardModal" data-bs-toggle="modal"><i
                                data-lucide="plus"></i>Add Card</button>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Points</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cards as $card)
                                    <tr>
                                        <td>{{ $card->id }}</td>
                                        <td class="text-capitalize">{{ $card->type }}</td>
                                        <td>
                                            @if ($card->negative_points == 0)
                                                Deducted All Points
                                            @else
                                                {{ $card->negative_points }}
                                            @endif
                                        </td>
                                        <td>
                                            <div style="display:flex;gap:0.25rem;">
                                                <button class="btn btn-icon btn-edit editCardBtn"
                                                    data-id="{{ $card->id }}" data-type="{{ $card->type }}"
                                                    data-points="{{ $card->negative_points }}" data-bs-toggle="modal"
                                                    data-bs-target="#editCardModal">
                                                    <i data-lucide="edit-2"></i>
                                                </button>

                                                <form action="{{ route('cards.delete', $card->id) }}" method="POST"
                                                    style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-delete">
                                                        <i data-lucide="trash-2"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @push('modals')
                        @include('card.create')
                        @include('card.edit')
                    @endpush

                </div>
            </div>
            <div id="cards-history-tab" class="tab-content">
                <div class="spreadsheet-container mt-4">
                    <div class="spreadsheet-toolbar">

                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Card</th>
                                    <th>Assigned To</th>
                                    <th>Entity Name</th>
                                    <th>Date</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($logs as $log)
                                    @php
                                        $entityName = '-';

                                        if ($log->assignable_type === 'student') {
                                            $entity = \App\Models\Student::find($log->assignable_id);
                                            $entityName = $entity->name ?? '-';
                                        } elseif ($log->assignable_type === 'team') {
                                            $entity = \App\Models\Team::find($log->assignable_id);
                                            $entityName = $entity->name ?? '-';
                                        } elseif ($log->assignable_type === 'group') {
                                            $entity = \App\Models\Group::find($log->assignable_id);
                                            $entityName = $entity->group_name ?? '-';
                                        } elseif ($log->assignable_type === 'organization') {
                                            $entity = \App\Models\Organization::find($log->assignable_id);
                                            $entityName = $entity->name ?? '-';
                                        }
                                    @endphp

                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="badge
                                                {{ $log->card->type == 'red' ? 'bg-danger' : '' }}
                                                {{ $log->card->type == 'yellow' ? 'bg-warning text-dark' : '' }}
                                                {{ $log->card->type == 'orange' ? 'text-dark' : '' }}
                                                {{ !in_array($log->card->type, ['red','yellow','orange']) ? 'bg-secondary' : '' }}"
                                                
                                                style="{{ $log->card->type == 'orange' ? 'background-color: #fd7e14;color:#fff !important' : '' }}">
                                                
                                                {{ strtoupper($log->card->type) }}
                                            </span>
                                        </td>
                                        <td>{{ ucfirst($log->assignable_type) }}</td>
                                        <td>{{ $entityName }}</td>
                                        <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No logs found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>



                </div>
            </div>
            @endrole
            <div id="profile-tab" class="tab-content {{ !auth()->user()->hasRole('admin') ? 'show active' : '' }}">
                <div class="spreadsheet-container mt-4">

                    <div class="sems-profile-wrapper">
                        <div class="sems-profile-card">

                            {{-- Hero Banner --}}
                            <div class="sems-profile-hero-banner">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="sems-profile-avatar-ring" id="semsProfileAvatarInitials">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()->name, ' '), 1, 1)) }}
                                    </div>
                                    <div>
                                        <p class="sems-profile-hero-name" id="semsProfileHeroName">
                                            {{ auth()->user()->name }}</p>

                                    </div>
                                    <div class="ms-auto">
                                        <div class="sems-profile-badge-pill">
                                            <span class="sems-profile-badge-dot"></span>
                                            Active
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 flex-wrap">

                                    <div class="sems-profile-stat-tile">
                                        <p class="sems-profile-stat-label">Member Since</p>
                                        <p class="sems-profile-stat-value">{{ auth()->user()->created_at->year }}</p>
                                    </div>
                                    <div class="sems-profile-stat-tile">
                                        <p class="sems-profile-stat-label">Role</p>
                                        <p class="sems-profile-stat-value">
                                            {{ auth()->user()->roles->pluck('name')->map(fn($r) => ucfirst($r))->join(', ') }}
                                        </p>
                                   
                                    </div>
                                </div>
                            </div>

                            <form id="profile-update-form">
                                @csrf

                                {{-- Account Details --}}
                                <div class="sems-profile-body-section">
                                    <div class="sems-profile-section-header">
                                        <div class="sems-profile-section-icon">
                                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                                <circle cx="8" cy="5" r="3" stroke="currentColor"
                                                    stroke-width="1.2" />
                                                <path d="M2 14c0-3.3 2.7-6 6-6s6 2.7 6 6" stroke="currentColor"
                                                    stroke-width="1.2" stroke-linecap="round" />
                                            </svg>
                                        </div>
                                        <span class="sems-profile-section-title">Account details</span>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="sems-profile-label" for="semsNameInput">Full name</label>
                                            <input class="sems-profile-input form-control" id="semsNameInput"
                                                name="name" type="text" value="{{ auth()->user()->name }}"
                                                placeholder="Your full name">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="sems-profile-label" for="semsUsernameInput">Username</label>
                                            <div class="sems-profile-input-wrap">
                                                <input class="sems-profile-input form-control" id="semsUsernameInput"
                                                    name="username" type="text"
                                                    value="{{ auth()->user()->username }}" placeholder="username"
                                                    style="padding-left:28px;">
                                                <span class="sems-profile-at-prefix">@</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="sems-profile-divider">

                                {{-- Password Section --}}
                                <div class="sems-profile-body-section">
                                    <div class="sems-profile-section-header">
                                        <div class="sems-profile-section-icon">
                                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                                <rect x="2" y="7" width="12" height="8" rx="1.5"
                                                    stroke="currentColor" stroke-width="1.2" />
                                                <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke="currentColor" stroke-width="1.2"
                                                    stroke-linecap="round" />
                                            </svg>
                                        </div>
                                        <span class="sems-profile-section-title">Change password</span>
                                        <span class="sems-profile-optional-tag ms-auto">Optional</span>
                                    </div>

                                    <div class="mb-3">
                                        <label class="sems-profile-label">Current password</label>
                                        <div class="sems-profile-input-wrap">
                                            <input class="sems-profile-input form-control" id="profile-current-password"
                                                name="current_password" type="password"
                                                placeholder="Enter current password">
                                            <button type="button" class="sems-profile-eye-btn toggle-pass"
                                                data-target="profile-current-password"
                                                data-sems-target="profile-current-password">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                    <ellipse cx="8" cy="8" rx="6" ry="4"
                                                        stroke="currentColor" stroke-width="1.2" />
                                                    <circle cx="8" cy="8" r="1.8" fill="currentColor" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="sems-profile-label">New password</label>
                                            <div class="sems-profile-input-wrap">
                                                <input class="sems-profile-input form-control" id="profile-password-input"
                                                    name="password" type="password" placeholder="Min. 6 characters">
                                                <button type="button" class="sems-profile-eye-btn toggle-pass"
                                                    data-target="profile-password-input"
                                                    data-sems-target="profile-password-input">
                                                    <svg width="16" height="16" viewBox="0 0 16 16"
                                                        fill="none">
                                                        <ellipse cx="8" cy="8" rx="6"
                                                            ry="4" stroke="currentColor" stroke-width="1.2" />
                                                        <circle cx="8" cy="8" r="1.8"
                                                            fill="currentColor" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="sems-profile-strength-track">
                                                <div class="sems-profile-strength-fill" id="semsStrengthFill"></div>
                                            </div>
                                            <p class="sems-profile-strength-label" id="semsStrengthLabel"></p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="sems-profile-label">Confirm password</label>
                                            <div class="sems-profile-input-wrap">
                                                <input class="sems-profile-input form-control"
                                                    id="profile-password-confirm-input" name="password_confirmation"
                                                    type="password" placeholder="Repeat new password">
                                                <button type="button" class="sems-profile-eye-btn toggle-pass"
                                                    data-target="profile-password-confirm-input"
                                                    data-sems-target="profile-password-confirm-input">
                                                    <svg width="16" height="16" viewBox="0 0 16 16"
                                                        fill="none">
                                                        <ellipse cx="8" cy="8" rx="6"
                                                            ry="4" stroke="currentColor" stroke-width="1.2" />
                                                        <circle cx="8" cy="8" r="1.8"
                                                            fill="currentColor" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <p class="sems-profile-strength-label" id="semsConfirmMatchLabel"></p>
                                        </div>
                                    </div>
                                </div>

                                <div id="profile-update-message" class="px-4"></div>

                                <div class="sems-profile-actions-row">
                                    <button type="button" class="sems-profile-cancel-btn"
                                        id="semsResetBtn">Reset</button>
                                    <button type="submit" class="sems-profile-submit-btn">Save changes</button>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
            @push('scripts')
                @include('settings.scripts.profile-script')
                @include('settings.scripts.activity-script')
            @endpush






        </section>


    </div>
@endsection
