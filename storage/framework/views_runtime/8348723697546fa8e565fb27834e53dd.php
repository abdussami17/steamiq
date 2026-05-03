<?php $__env->startSection('title', 'STEAM XRS Manager'); ?>


<?php $__env->startSection('content'); ?>
    <div class="container">
        <!-- Active Events -->
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon">
                        <i data-lucide="trophy"></i>
                    </span>
                    Events Management
                </h2>

            </div>

            <div class="card-grid events_grid">
                <?php $__empty_1 = true; $__currentLoopData = $allevents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allevent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="events_card">
                        <div class="events_card-header">
                            <div>
                                <h3 class="events_card-title"><?php echo e($allevent->name); ?></h3>
                                <p style="color: var(--text-dim); font-size: 0.9rem; margin-top: 0.25rem;">
                                    <?php echo e(ucfirst($allevent->event_type)); ?>

                                </p>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                
                                <div class="ec-status-wrap" style="position:relative;">
                                    <span id="ec-badge-<?php echo e($allevent->id); ?>"
                                        class="badge me-1 badge-<?php echo e($allevent->status); ?>">
                                        <?php echo e(ucfirst($allevent->status)); ?>

                                    </span>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_event')): ?>
                                        <button type="button" class="btn btn-link p-0 m-0 align-baseline ec-status-btn"
                                            title="Change status" onclick="toggleStatusDrop(<?php echo e($allevent->id); ?>, this)">
                                            <i data-lucide="pencil" style="height: 13px;width:20px;color:#000"></i>
                                        </button>
                                        <div id="ec-drop-<?php echo e($allevent->id); ?>" class="ec-status-drop d-none">
                                            <?php $__currentLoopData = ['draft', 'live', 'closed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <button type="button"
                                                    class="ec-status-opt <?php echo e($allevent->status === $s ? 'active' : ''); ?>"
                                                    data-event="<?php echo e($allevent->id); ?>" data-status="<?php echo e($s); ?>"
                                                    onclick="setEventStatus(<?php echo e($allevent->id); ?>, '<?php echo e($s); ?>', this)">
                                                    <span class="ec-status-dot ec-dot-<?php echo e($s); ?>"></span>
                                                    <?php echo e(ucfirst($s)); ?>

                                                </button>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_event')): ?>
                                    <form action="<?php echo e(route('events.destroy', $allevent->id)); ?>" method="POST"
                                        style="display:inline-flex; align-items:center; margin:0; padding:0;"
                                        onsubmit="return confirm('Are you sure you want to delete this event?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                                            <i data-lucide="trash" class="text-danger"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="events_stats-grid">

                            <?php
                                // Merge all teams under groups + subgroups and remove duplicates
                                $allTeams = $allevent->organizations->flatMap->groups
                                    ->flatMap(function ($group) {
                                        return $group->teams->concat($group->subgroups->flatMap->teams);
                                    })
                                    ->unique('id');
                            ?>

                            
                            <div class="events_stat">
                                <div class="events_stat-label">Teams</div>
                                <div class="events_stat-value">
                                    <?php echo e($allTeams->count() ? number_format($allTeams->count()) : 'N/A'); ?>

                                </div>
                            </div>

                            
                            <div class="events_stat">
                                <div class="events_stat-label">Players</div>
                                <div class="events_stat-value">
                                    <?php echo e(($allTeams->flatMap->students->count()) ? number_format($allTeams->flatMap->students->count()) : 'N/A'); ?>

                                </div>
                            </div>

                            
                            <div class="events_stat">
                                <div class="events_stat-label">Groups</div>
                                <div class="events_stat-value">
                                    <?php echo e($allevent->organizations->flatMap->groups->count() ? number_format($allevent->organizations->flatMap->groups->count()) : 'N/A'); ?>

                                </div>
                            </div>

                            
                            <div class="events_stat">
                                <div class="events_stat-label">Sub Groups</div>
                                <div class="events_stat-value">
                                    <?php echo e($allevent->organizations->flatMap->groups->flatMap->subgroups->count() ? number_format($allevent->organizations->flatMap->groups->flatMap->subgroups->count()) : 'N/A'); ?>

                                </div>
                            </div>

                        </div>
                        <div class="events_results_preview" id="event-results-<?php echo e($allevent->id); ?>"></div>
                        <div class="events_card-actions">
                            <button class="btn btn-primary btn-main" onclick="openEventModal(<?php echo e($allevent->id); ?>)">
                                View Event
                            </button>

                            <div class="action-icons">
                                <button class="btn btn-icon btn-view" title="Tournament Bracket"
                                    onclick="openBracketModal(<?php echo e($allevent->id); ?>)">
                                    <i data-lucide="trophy"></i>
                                </button>
                                
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p>No events available</p>
                <?php endif; ?>
            </div>


        </section>

        <!-- Event Operations Spreadsheet -->
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon">
                        <i data-lucide="clipboard-list"></i>
                    </span>
                    Event Operations
                </h2>
            </div>

            <?php
                $activeTab = session('active_tab') ?? 'events-tab';
            ?>

            <div class="tabs" id="eventTabs">
                <button class="tab <?php echo e($activeTab == 'events-tab' ? 'active' : ''); ?>"
                    onclick="switchTab('events')">Events</button>
                <button class="tab <?php echo e($activeTab == 'organizations-tab' ? 'active' : ''); ?>"
                    onclick="switchTab('organizations')">Organizations</button>
                <button class="tab <?php echo e($activeTab == 'groups-tab' ? 'active' : ''); ?>"
                    onclick="switchTab('groups')">Groups</button>
                <button class="tab <?php echo e($activeTab == 'subgroup-tab' ? 'active' : ''); ?>" onclick="switchTab('subgroup')">Sub
                    Group</button>
                <button class="tab <?php echo e($activeTab == 'teams-tab' ? 'active' : ''); ?>"
                    onclick="switchTab('teams')">Teams</button>
                <button class="tab <?php echo e($activeTab == 'players-tab' ? 'active' : ''); ?>"
                    onclick="switchTab('players')">Players</button>

            </div>
            <?php if(session('active_tab')): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const tabs = document.getElementById('eventTabs');
                        if (tabs) {
                            // scroll to the tabs area smoothly
                            tabs.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    });
                </script>
            <?php endif; ?>


            <!-- Events Tab -->
            <div id="events-tab" class="tab-content <?php echo e($activeTab == 'events-tab' ? 'active show' : ''); ?>">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <input type="text" id="eventSearch" class="form-input" placeholder="Search Event..."
                            style="width:300px;">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_event')): ?>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEventModal">
                                <i data-lucide="plus"></i> Add Event
                            </button>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_event')): ?>
                        <button class="btn btn-danger" id="deleteSelectedEventsBtn" onclick="deleteSelectedEvents()">
                            Delete Selected (0)
                        </button>
                    <?php endif; ?>

                    </div>


                 <div style="width:100%;overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAllEvents">
                                </th>
                                <th>ID</th>

                                <th>Event Name</th>
                                <th>Event Type</th>
                                <th>Location</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>


                                <th>Game Settings</th>
                                <th>Tournament</th>
                                <th>Activities</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="eventTableBody">
                            <?php $__empty_1 = true; $__currentLoopData = $allevents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allevent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="event-checkbox" value="<?php echo e($allevent->id); ?>">
                                    </td>
                                    <td>
                                        <?php echo e($allevent->id ? number_format($allevent->id) : 'N/A'); ?>

                                    </td>
                                    
                                    <td><?php echo e($allevent->name ?: 'N/A'); ?></td>
                                    
                                    <td>
                                        <?php if($allevent->type === 'esports'): ?>
                                            STEAM ESports
                                        <?php elseif($allevent->type === 'xr'): ?>
                                            STEAM XR Sports
                                        <?php else: ?>
                                            STEAM <?php echo e($allevent->type ?: 'N/A'); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($allevent->location ?: 'N/A'); ?></td>
                                    <td><?php echo e($allevent->start_date ?: 'N/A'); ?></td>
                                    <td><?php echo e($allevent->end_date ?: 'N/A'); ?></td>
                                    <td>
                                        <?php
                                            $status = strtolower(trim($allevent->status)); 
                                    
                                            $map = [
                                                'live'   => ['label' => 'LIVE', 'class' => 'badge-live'],
                                                'closed' => ['label' => 'CLOSED', 'class' => 'badge-closed'], 
                                                'draft'  => ['label' => 'DRAFT', 'class' => 'badge-draft'],
                                            ];
                                        ?>
                                    
                                        <?php if($status && isset($map[$status])): ?>
                                            <span class="badge <?php echo e($map[$status]['class']); ?>">
                                                <?php echo e($map[$status]['label']); ?>

                                            </span>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>

                                    
                                    <td>
                                        <?php if($allevent->tournamentSetting): ?>
                                                Game: <?php echo e($allevent->tournamentSetting->game ?? '-'); ?><br>
                                                Players/Team: <?php echo e(is_numeric($allevent->tournamentSetting->players_per_team) ? number_format($allevent->tournamentSetting->players_per_team) : ($allevent->tournamentSetting->players_per_team ?? '-')); ?><br>
                                                Match Rule: <?php echo e($allevent->tournamentSetting->match_rule ?? '-'); ?><br>
                                                Points Win: <?php echo e(is_numeric($allevent->tournamentSetting->points_win) ? number_format($allevent->tournamentSetting->points_win) : ($allevent->tournamentSetting->points_win ?? '-')); ?><br>
                                                Points Draw: <?php echo e(is_numeric($allevent->tournamentSetting->points_draw) ? number_format($allevent->tournamentSetting->points_draw) : ($allevent->tournamentSetting->points_draw ?? '-')); ?>

                                            <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>

                                    
                                    <td>
                                        <?php if($allevent->tournamentSetting): ?>
                                            Type: <?php echo e($allevent->tournamentSetting->tournament_type ?? '-'); ?><br>
                                            Teams: <?php echo e(is_numeric($allevent->tournamentSetting->number_of_teams) ? number_format($allevent->tournamentSetting->number_of_teams) : ($allevent->tournamentSetting->number_of_teams ?? '-')); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if($allevent->activities->count()): ?>
                                            <ul class="mb-0">
                                                <?php $__currentLoopData = $allevent->activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li><?php echo e($activity->display_name); ?> - Score: <?php echo e($activity->max_score); ?>

                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display:flex;gap:0.25rem;">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_event')): ?>
                                                <button class="btn btn-icon btn-edit"
                                                    onclick="openEditEventModal(<?php echo e($allevent->id); ?>)">
                                                    <i data-lucide="edit-2"></i>
                                                </button>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_event')): ?>
                                                <form action="<?php echo e(route('events.destroy', $allevent->id)); ?>" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this event?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>

                                                    <button type="submit" class="btn btn-icon btn-delete">
                                                        <i data-lucide="trash-2"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('duplicate_event')): ?>
                                                <button class="btn btn-icon btn-copy"
                                                    onclick="duplicateEvent(<?php echo e($allevent->id); ?>)">
                                                    <i data-lucide="copy"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-icon btn-view"
                                                onclick="openBracketModal(<?php echo e($allevent->id); ?>)">
                                                <i data-lucide="trophy"></i>
                                            </button>
                                            
                                        </div>
                                    </td>

                                </tr>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No Events available
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                 </div>
                    <!-- Modal -->
                    <?php $__env->startPush('scripts'); ?>
                    <?php echo $__env->make("events.scripts.bulk-delete", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                // Simple search by Team Name
                                document.getElementById('eventSearch').addEventListener('input', function() {
                                    const filter = this.value.toLowerCase();
                                    const rows = document.querySelectorAll('#eventTableBody tr');
                                    rows.forEach(row => {
                                        const cell = row.cells[2]; // Team Name column
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

            <!-- Organizations Tab -->
            <div id="organizations-tab" class="tab-content <?php echo e($activeTab == 'organizations-tab' ? 'active show' : ''); ?>">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <input type="text" id="orgSearch" class="form-input" placeholder="Search Organization..."
                            style="width:300px;">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_organization')): ?>
                            <button class="btn btn-primary" data-next-tab="groups-tab" data-bs-toggle="modal"
                                data-bs-target="#createOrganizationModal">
                                <i data-lucide="plus"></i> Add Organization
                            </button>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_organization')): ?>
                        <button class="btn btn-danger" id="deleteSelectedOrgsBtn" onclick="deleteSelectedOrganizations()">
                            Delete Selected (0)
                        </button>
                    <?php endif; ?>
                    </div>


                 <div style="overflow-x: auto;width:100% ">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAllOrgs">
                                </th>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Email</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="orgTableBody">
                            <?php $__empty_1 = true; $__currentLoopData = $organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $org): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>

                                    <td>
                                        <input type="checkbox" class="org-checkbox" value="<?php echo e($org->id); ?>">
                                    </td>
                                    <td>
                                        <img src="<?php echo e($org->profile ? asset('storage/' . $org->profile) : asset('assets/avatar-default.png')); ?>"
                                            height="40" width="40" class="rounded-circle"
                                            style="object-fit: cover"
                                            onerror="this.src='<?php echo e(asset('assets/avatar-default.png')); ?>'">
                                    </td>
                                    <td><?php echo e($org->name ?: 'N/A'); ?></td>

                                    <td><?php echo e($org->organization_type ?: 'N/A'); ?></td>




                                    <td><?php echo e($org->email ?: 'N/A'); ?></td>

                                    <td>
                                        <div style="display:flex;gap:0.25rem;">

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_organization')): ?>
                                                <button class="btn btn-icon btn-edit"
                                                    onclick="openEditOrgModal(<?php echo e($org->id); ?>, '<?php echo e(addslashes($org->name)); ?>', '<?php echo e(addslashes($org->email)); ?>', '<?php echo e($org->organization_type); ?>' , '<?php echo e($org->event_id); ?>')">
                                                    <i data-lucide="edit-2"></i>
                                                </button>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_organization')): ?>
                                                <form action="<?php echo e(route('organizations.destroy', $org->id)); ?>" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this organization?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>

                                                    <button type="submit" class="btn btn-icon btn-delete">
                                                        <i data-lucide="trash-2"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>


                                    </td>

                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No organizations available
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                    <?php $__env->startPush('scripts'); ?>
                    <?php echo $__env->make('organization.script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                // Simple search by Team Name
                                document.getElementById('orgSearch').addEventListener('input', function() {
                                    const filter = this.value.toLowerCase();
                                    const rows = document.querySelectorAll('#orgTableBody tr');
                                    rows.forEach(row => {
                                        const cell = row.cells[2]; // Team Name column
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

            

            <div id="groups-tab" class="tab-content <?php echo e($activeTab == 'groups-tab' ? 'active show' : ''); ?>">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <input type="text" id="groupSearch" class="form-input" placeholder="Search Group..."
                            style="width:300px;">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_group')): ?>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                                <i data-lucide="plus"></i> Add Group
                            </button>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_group')): ?>
                        <button class="btn btn-danger" id="deleteSelectedGroupsBtn" onclick="deleteSelectedGroups()">
                            Delete Selected (0)
                        </button>
                    <?php endif; ?>
                        

                    </div>

                   <div style="overflow-x: auto;width:100%">
                    <table class="data-table">
                        <thead>
                            <tr>

                                <th>
                                    <input type="checkbox" id="selectAllGroups">
                                </th>
                                <th>ID</th>
                                <th>Organization</th>
                                <th>Group Name</th>
                                <th>POD</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="groupTableBody">
                            <?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="group-checkbox" value="<?php echo e($group->id); ?>">
                                    </td>
                                
                                    <td><?php echo e($group->id ?? 'N/A'); ?></td>
                                    <td>
                                        <?php echo e(optional($organizations->firstWhere('id', $group->organization_id))->name ?? 'N/A'); ?>

                                    </td>
                                    <td><?php echo e($group->group_name ?? 'N/A'); ?></td>
                                    <td class="text-uppercase"><?php echo e($group->pod ?? 'N/A'); ?></td>


                                    <td>
                                        <div style="display:flex;gap:0.25rem;">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_group')): ?>
                                                <button class="btn btn-icon btn-edit"
                                                    onclick="openGroupEditModal(
                                       <?php echo e($group->id ?? 'null'); ?>,
                                       '<?php echo e(addslashes($group->group_name ?? 'N/A')); ?>',
                                       '<?php echo e($group->pod ?? 'N/A'); ?>',
                                   
                                       <?php echo e($group->organization_id); ?>

                                   )">
                                                    <i data-lucide="pencil"></i>
                                                </button>
                                            <?php endif; ?>

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_group')): ?>
                                                <form action="<?php echo e(route('groups.destroy', $group->id ?? 0)); ?>" method="POST"
                                                    onsubmit="return confirm('Delete this group?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button class="btn btn-icon btn-delete">
                                                        <i data-lucide="trash-2"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-white py-4">
                                        No groups available
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                   </div>
                </div>
            </div>
            <?php $__env->startPush('scripts'); ?>
            <?php echo $__env->make('groups.script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        // Simple search by Team Name
                        document.getElementById('groupSearch').addEventListener('input', function() {
                            const filter = this.value.toLowerCase();
                            const rows = document.querySelectorAll('#groupTableBody tr');
                            rows.forEach(row => {
                                const cell = row.cells[3]; // Team Name column
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
            

            <div id="subgroup-tab" class="tab-content <?php echo e($activeTab == 'subgroup-tab' ? 'active show' : ''); ?>">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_subgroup')): ?>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSubGroupModal">
                                <i data-lucide="plus"></i> Add Sub Group
                            </button>
                        <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_subgroup')): ?>
    <button class="btn btn-danger" id="deleteSelectedSubGroupsBtn" onclick="deleteSelectedSubGroups()">
        Delete Selected (0)
    </button>
<?php endif; ?>

                    </div>

                  <div style="overflow-x: auto;width:100%">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAllSubGroups">
                                </th>
                                <th>ID</th>
                                <th>Group Name</th>
                                <th>Sub Group Name</th>
                                <th>POD</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $subgroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subgrp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="subgroup-checkbox" value="<?php echo e($subgrp->id); ?>">
                                    </td>
                                    <td><?php echo e($subgrp->id ?? 'N/A'); ?></td>
                                    <td><?php echo e($subgrp->group->group_name ?? 'N/A'); ?></td>
                                    <td><?php echo e($subgrp->name ?? 'N/A'); ?></td>
                                    <td class="text-uppercase"><?php echo e($subgrp->group->pod ?? 'N/A'); ?></td>


                                    <td>
                                        <div style="display:flex;gap:0.25rem;">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_subgroup')): ?>
                                                <button class="btn btn-icon btn-edit"
                                                    onclick="openSubGroupEditModal(<?php echo e($subgrp->id); ?>)">
                                                    <i data-lucide="pencil"></i>
                                                </button>
                                            <?php endif; ?>

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_subgroup')): ?>
                                                <form action="<?php echo e(route('subgroups.destroy', $subgrp->id ?? 0)); ?>"
                                                    method="POST" onsubmit="return confirm('Delete this sub group?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button class="btn btn-icon btn-delete">
                                                        <i data-lucide="trash-2"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-white py-4">
                                        No subgroups available
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                  </div>
                    <?php $__env->startPush('scripts'); ?>
                    <?php echo $__env->make('subgroups.scripts.bulk-edit', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        
                    <?php $__env->stopPush(); ?>
                </div>
            </div>

            <!-- Teams Tab -->

            <div id="teams-tab" class="tab-content <?php echo e($activeTab == 'teams-tab' ? 'active show' : ''); ?>">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <input type="text" id="teamSearch" class="form-input" placeholder="Search Team..."
                            style="width:300px;">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_team')): ?>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_team">
                                <i data-lucide="plus"></i> Add Team
                            </button>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_team')): ?>
                            <button class="btn btn-danger" onclick="deleteSelectedTeams()">
                                Delete Selected
                            </button>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('import_team')): ?>
                            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#importTeamsModal">
                                <i data-lucide="upload"></i> Import Teams
                            </button>
                        <?php endif; ?>
                        
                        



                    </div>
                   <div style="overflow-x: auto;width:100%">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAllTeams">
                                </th>
                                <th>Avatar</th>
                                <th>Team ID</th>
                                <th>Team Name</th>
                                <th>Division</th>
                                <th>Group</th>
                                <th>Sub Group</th>
                                <th>POD</th>
                                <th>Members</th>
                                <th>Total Points</th>
                                <th>Rank</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="teamsTableBody"></tbody>


                    </table>
                   </div>
                </div>
            </div>
            <!-- Players Tab -->

            <div id="players-tab" class="tab-content <?php echo e($activeTab == 'players-tab' ? 'active show' : ''); ?>">
                <div class="spreadsheet-container">
                    <div class="spreadsheet-toolbar">
                        <!-- Add this inside your spreadsheet-toolbar, beside buttons -->
                        <input type="text" id="playerSearch" class="form-input" placeholder="Search Player"
                            style="width:300px">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_player')): ?>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                <i data-lucide="plus"></i> Add New Player
                            </button>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_player')): ?>
                        <button class="btn btn-danger" id="deleteSelectedPlayersBtn" onclick="deleteSelectedPlayers()">
                            Delete Selected (0)
                        </button>
                        <?php endif; ?>

                        <button class="btn btn-secondary" onclick="loadLeaderboard()">
                            <i data-lucide="refresh-cw"></i> Refresh
                        </button>
                        


                    </div>
                    <div class="row g-3">
                        <!-- Event Filter -->
                        <div class="mb-4 col-md-4">
                            <label for="eventFilter" class="form-label">Select Event <span
                                    class="text-danger">*</span></label>
                            <select id="eventFilter" class="form-select">
                                <option hidden>-- Select Event --</option>
                                <?php $__currentLoopData = $allevents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($event->id); ?>"><?php echo e($event->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="mb-4 col-md-4">
                            <label for="organizationFilter" class="form-label">Select Organization<span
                                    class="text-danger">*</span></label>
                            <select id="organizationFilter" class="form-select">
                                <option value="">-- Select Organization --</option>
                            </select>
                        </div>
                    </div>
                    <div id="playersGrid" style="width:100%; overflow:auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAllPlayers">
                                    </th>
                                    <th>Player</th>
                                    <th>Team</th>
                                    <th>Activity</th>
                                    <th>Total</th>
                                    <th>Rank</th>
                                    <th id="actionHeader" style="display:none;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="playersTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">Select event to load data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>











        </section>


    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('modals'); ?>
    
    <?php echo $__env->make('teams.modals.create-team', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('teams.modals.view-team', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('teams.modals.import-team', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('teams.modals.edit-team', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    

    <?php echo $__env->make('events.modals.view-event', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('events.modals.edit-event', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('events.modals.bracket', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('events.modals.choose-winner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    
    <?php echo $__env->make('matches.modals.add-match', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('matches.modals.match-pin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('matches.modals.add-round', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('organization.modals.create-organization', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('organization.modals.edit-organization', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('groups.modals.create-group', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('groups.modals.edit-group', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('subgroups.modals.create-subgroup', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('subgroups.modals.edit-subgroup', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('students.modals.create-students', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('students.modals.edit-students', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('card.assign-card-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('styles'); ?>
    <?php echo $__env->make('events.style', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
    
    <?php echo $__env->make('events.scripts.bracket-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('events.scripts.edit-event-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('events.scripts.duplicate-event-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('events.scripts.winner-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('teams.scripts.team-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('scores.scripts.score-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('subgroups.scripts.edit-subgroup-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('students.script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php echo $__env->make('card.assign-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u236413684/domains/voags.com/public_html/steamiq/resources/views/events/index.blade.php ENDPATH**/ ?>