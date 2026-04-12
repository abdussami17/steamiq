
<?php $__env->startSection('title', 'Settings - SteamIQ'); ?>


<?php $__env->startSection('content'); ?>
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
    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>
    <button class="tab active" onclick="switchTab('users', event)">Users</button>
    <button class="tab" onclick="switchTab('roles', event)">Roles</button>
    <button class="tab" onclick="switchTab('permissions', event)">Permission</button>
    <button class="tab" onclick="switchTab('activities', event)">Activities</button>
    <button class="tab" onclick="switchTab('cards', event)">Cards</button>
    <button class="tab" onclick="switchTab('cards-history', event)">Cards History</button>
<?php endif; ?>

<button class="tab <?php echo e(!auth()->user()->hasRole('admin') ? 'active' : ''); ?>"
    onclick="switchTab('profile', event)">
    Profile
</button>
</div>

<?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>

            <div id="users-tab" class="tab-content show active">
                <div class="spreadsheet-container mt-4">
                    <div class="spreadsheet-toolbar">
                        <input type="text" id="userSearch" class="form-input" placeholder="Search User..."
                            style="width:300px;">
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
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
                            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $us): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($us->id ?? 'N/A'); ?></td>
                                    <td><?php echo e($us->name ?? 'N/A'); ?></td>
                                    <td><?php echo e($us->username ?? 'N/A'); ?></td>
                                    <td><?php echo e($us->email ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if($us->roles->count() > 0): ?>
                                            <?php echo e($us->roles->pluck('name')->map(fn($r) => ucfirst($r))->join(', ')); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo e($us->created_at ? \Carbon\Carbon::parse($us->created_at)->format('M d, Y') : 'N/A'); ?>

                                    </td>
                                    <td>
                                        <?php if(!$us->hasRole('admin')): ?>
                                            <?php $__env->startPush('modals'); ?>
                                                <?php echo $__env->make('settings.modals.edit-user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                            <?php $__env->stopPush(); ?>
                                            <div class="d-flex gap-2">
                                                <!-- Edit Button triggers modal -->
                                                <button type="button" class="btn btn-icon btn-edit" data-bs-toggle="modal"
                                                    data-bs-target="#editUserModal<?php echo e($us->id); ?>">
                                                    <i data-lucide="pencil"></i>
                                                </button>
                                                <form action="<?php echo e(route('setting.users.destroy', $us->id)); ?>" method="POST"
                                                    class="delete-form" style="display:inline-block;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>

                                                    <button type="button" class="btn btn-icon btn-delete delete-btn">
                                                        <i data-lucide="trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Admin</span>
                                        <?php endif; ?>
                                    </td>

                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No users currently</td>
                                </tr>
                            <?php endif; ?>

                        </tbody>
                    </table>
                    <?php $__env->startPush('scripts'); ?>
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
                    <?php $__env->stopPush(); ?>


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
                                <th>Created At</th>

                            </tr>
                        </thead>
                        <tbody id="activities-data-table-body">
                            <tr>
                                <td colspan="7" class="text-center">Loading activities...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-content" id="permissions-tab">
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
                            <?php $__empty_1 = true; $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($perms->id); ?></td>
                                    <td><?php echo e($perms->label); ?></td>
                                    <td>
                                        <form action="<?php echo e(route('permissions.destroy', $perms->id)); ?>" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this permission?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-icon btn-delete"><i
                                                    data-lucide="trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="2">No data</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php $__env->startPush('modals'); ?>
                    <?php echo $__env->make('settings.modals.create-permission', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php $__env->stopPush(); ?>
            </div>

            <div class="tab-content" id="roles-tab">
                <div class="spreadsheet-container mt-4">
                    <div class="spreadsheet-toolbar">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal"><i
                                data-lucide="plus"></i>Add Role</button>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Permissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($role->id); ?></td>
                                    <td><?php echo e($role->name); ?></td>
                                    <td>
                                        <?php if($role->name === 'admin'): ?>
                                            <span class="text-success fw-bold">ALL Permissions</span>
                                        <?php elseif($role->permissions->count()): ?>
                                            <?php echo e($role->permissions->pluck('label')->join(', ')); ?>

                                        <?php else: ?>
                                            <span class="text-muted">No permissions</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($role->name !== 'admin'): ?>
                                        <div class="d-flex gap-2">

                                            <!-- Edit -->
                                            <button class="btn btn-icon btn-edit" data-bs-toggle="modal"
                                                data-bs-target="#editRoleModal<?php echo e($role->id); ?>">
                                                <i data-lucide="pencil"></i>
                                            </button>
                                    
                                            <!-- Delete -->
                                            <form action="<?php echo e(route('roles.destroy', $role->id)); ?>" method="POST" class="delete-role-form">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                    
                                                <button type="button" class="btn btn-icon btn-delete delete-role-btn">
                                                    <i data-lucide="trash-2"></i>
                                                </button>
                                            </form>
                                    
                                        </div>
                                        <?php else: ?>
                                            <span class="text-muted">Not editable</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- Edit Role Modal -->
                                <?php if($role->name !== 'admin'): ?>
                                    <?php $__env->startPush('modals'); ?>
                                        <?php echo $__env->make('settings.modals.edit-roles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    <?php $__env->stopPush(); ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4">No data found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?php $__env->startPush('scripts'); ?>
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
                        
                    <?php $__env->stopPush(); ?>
                </div>
                <?php $__env->startPush('modals'); ?>
                    <?php echo $__env->make('settings.modals.create-roles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php $__env->stopPush(); ?>
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
                                <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($card->id); ?></td>
                                        <td class="text-capitalize"><?php echo e($card->type); ?></td>
                                        <td>
                                            <?php if($card->negative_points == 0): ?>
                                                Deducted All Points
                                            <?php else: ?>
                                                <?php echo e($card->negative_points); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="display:flex;gap:0.25rem;">
                                                <button class="btn btn-icon btn-edit editCardBtn"
                                                    data-id="<?php echo e($card->id); ?>" data-type="<?php echo e($card->type); ?>"
                                                    data-points="<?php echo e($card->negative_points); ?>" data-bs-toggle="modal"
                                                    data-bs-target="#editCardModal">
                                                    <i data-lucide="edit-2"></i>
                                                </button>

                                                <form action="<?php echo e(route('cards.delete', $card->id)); ?>" method="POST"
                                                    style="display:inline-block;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-icon btn-delete">
                                                        <i data-lucide="trash-2"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <?php $__env->startPush('modals'); ?>
                        <?php echo $__env->make('card.create', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php echo $__env->make('card.edit', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php $__env->stopPush(); ?>

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
                                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
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
                                    ?>

                                    <tr>
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td>
                                            <span
                                                class="badge bg-<?php echo e($log->card->type == 'red' ? 'danger' : ($log->card->type == 'orange' ? 'warning' : 'info')); ?>">
                                                <?php echo e(strtoupper($log->card->type)); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e(ucfirst($log->assignable_type)); ?></td>
                                        <td><?php echo e($entityName); ?></td>
                                        <td><?php echo e($log->created_at->format('d M Y H:i')); ?></td>
                                    </tr>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No logs found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>



                </div>
            </div>
            <?php endif; ?>
            <div id="profile-tab" class="tab-content <?php echo e(!auth()->user()->hasRole('admin') ? 'show active' : ''); ?>">
                <div class="spreadsheet-container mt-4">

                    <div class="sems-profile-wrapper">
                        <div class="sems-profile-card">

                            
                            <div class="sems-profile-hero-banner">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="sems-profile-avatar-ring" id="semsProfileAvatarInitials">
                                        <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?><?php echo e(strtoupper(substr(strstr(auth()->user()->name, ' '), 1, 1))); ?>

                                    </div>
                                    <div>
                                        <p class="sems-profile-hero-name" id="semsProfileHeroName">
                                            <?php echo e(auth()->user()->name); ?></p>

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
                                        <p class="sems-profile-stat-value"><?php echo e(auth()->user()->created_at->year); ?></p>
                                    </div>
                                    <div class="sems-profile-stat-tile">
                                        <p class="sems-profile-stat-label">Role</p>
                                        <p class="sems-profile-stat-value">
                                            <?php echo e(auth()->user()->roles->pluck('name')->map(fn($r) => ucfirst($r))->join(', ')); ?>

                                        </p>
                                   
                                    </div>
                                </div>
                            </div>

                            <form id="profile-update-form">
                                <?php echo csrf_field(); ?>

                                
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
                                                name="name" type="text" value="<?php echo e(auth()->user()->name); ?>"
                                                placeholder="Your full name">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="sems-profile-label" for="semsUsernameInput">Username</label>
                                            <div class="sems-profile-input-wrap">
                                                <input class="sems-profile-input form-control" id="semsUsernameInput"
                                                    name="username" type="text"
                                                    value="<?php echo e(auth()->user()->username); ?>" placeholder="username"
                                                    style="padding-left:28px;">
                                                <span class="sems-profile-at-prefix">@</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="sems-profile-divider">

                                
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
            <?php $__env->startPush('scripts'); ?>
                <?php echo $__env->make('settings.scripts.profile-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('settings.scripts.activity-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php $__env->stopPush(); ?>






        </section>


    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\PC\Downloads\steamiq (5)\resources\views/settings/index.blade.php ENDPATH**/ ?>