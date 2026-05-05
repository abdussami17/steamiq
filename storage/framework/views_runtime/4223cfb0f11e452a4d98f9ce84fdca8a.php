
<?php $__env->startSection('title', 'STEAM XRS Manager'); ?>


<?php $__env->startSection('content'); ?>

        <?php if(auth()->guard()->guest()): ?>
            <?php echo $__env->make('dashboard.welcome', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>
    <div class="container">





        <!-- Dashboard Stats -->
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon">
                        <i data-lucide="bar-chart-3"></i>
                    </span>
                    Dashboard Overview
                </h2>
            </div>

            <div class="stats-grid">
                <div class="stat">
                    <div class="stat-label">Active Events</div>
                    <div class="stat-value"><?php echo e($activeEventsCount ?: 'N/A'); ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Total Players</div>
                    <div class="stat-value"><?php echo e($studentsCount ?: 'N/A'); ?></div>
                </div>
                <div class="stat">
                    <div class="stat-label">Teams Registered</div>
                    <div class="stat-value"><?php echo e($teamsCount ?: 'N/A'); ?></div>
                </div>
                <?php if(auth()->guard()->check()): ?>
                    <div class="stat">
                        <div class="stat-label">Total Organization</div>
                        <div class="stat-value"><?php echo e($orgCount ?: 'N/A'); ?></div>
                    </div>
                <?php endif; ?>

            </div>
        </section>

        <!-- CAM Pillars -->
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon">
                        <i data-lucide="target"></i>
                    </span>
                    C.A.M. Performance Pillars
                </h2>
            </div>

            <div class="cam-pillars">
                <div class="cam-card">
                    <div class="cam-icon">
                        <i data-lucide="brain"></i>
                    </div>
                    <h3 class="cam-title">Brain Games</h3>
                    <p class="cam-description">STEAM challenges across Science, Technology, Engineering, Art, and Math</p>
                </div>
                <div class="cam-card">
                    <div class="cam-icon">
                        <i data-lucide="swords"></i>
                    </div>
                    <h3 class="cam-title">Playground Games</h3>
                    <p class="cam-description">Physical sports and outdoor activities</p>
                </div>
                <div class="cam-card">
                    <div class="cam-icon">
                        <i data-lucide="gamepad-2"></i>
                    </div>
                    <h3 class="cam-title">E-Gaming & Esports</h3>
                    <p class="cam-description">Digital competitions and esports tournaments</p>
                </div>
            </div>
        </section>


        <!-- Leaderboard Teams -->
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon"><i data-lucide="Trophy"></i></span>
                    Top 3 Teams
                </h2>

            </div>

            <div class="form-group">
                <label class="form-label">Select Event <span class="text-danger">*</span></label>
                <select class="form-select w-25" id="selectEventForTopTeamsThree">
                    <option value="" hidden>--Select Event--</option>
                    <?php $__currentLoopData = $allevents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($event->id); ?>" <?php echo e($loop->first ? 'selected' : ''); ?>><?php echo e($event->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="table-responsive">
                <table class="data-table ">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Avatar</th>
                            <th>Team Name</th>
                            <th>Division</th>
                            <th>POD</th>
                            <th>Total Points</th>
                            <th>Rank</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyTopTeams"></tbody>
                </table>
            </div>
        </section>

        <!-- Leaderboard Players -->
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon"><i data-lucide="Trophy"></i></span>
                    Top 3 Players - <small id="playersEventName"></small>
                </h2>

            </div>


            <div class="table-responsive">
                <table class="data-table ">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Team</th>
                            <th>Total Points</th>
                            <th>Rank</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyTopPlayers"></tbody>
                </table>
            </div>
        </section>




        <?php echo $__env->make('leaderboard.public-leaderboard-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php if(auth()->guard()->check()): ?>
           <?php if (\Illuminate\Support\Facades\Blade::check('role', 'admin')): ?>
                <section class="section">
                    <div class="section-header d-flex justify-content-between align-items-center">
                        <h2 class="section-title">
                            <span class="icon"><i data-lucide="activity"></i></span>
                            Recent Activity
                        </h2>

                        <div class="dashboard-activity-filter-wrapper d-flex gap-2">
                            <input type="text" id="dashboard-activity-search-input" class="form-input"
                                placeholder="Search activity...">

                            <select id="dashboard-activity-date-filter" class="form-select">
                                <option value="all">All</option>
                                <option value="24h">24 Hours</option>
                                <option value="3d">3 Days</option>
                                <option value="30d">30 Days</option>
                                <option value="6m">6 Months</option>
                            </select>
                        </div>
                    </div>

                    <div class="card">
                        <div class="notifications" id="dashboard-activity-list-container">

                            <?php $__empty_1 = true; $__currentLoopData = $recentActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="notification-item dashboard-activity-item"
                                    data-created="<?php echo e($activity->created_at); ?>"
                                    data-text="<?php echo e(strtolower($activity->description)); ?>">

                                    <div class="notification-content">
                                        <h6 class="notification-title">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $activity->type ?? 'N/A'))); ?>

                                        </h6>

                                        <p class="notification-text" style="text-transform: capitalize">
                                            <?php echo e($activity->description ?? 'N/A'); ?>

                                            <br>
                                            <small class="text-muted" style="font-size:1rem;text-transform:capitalize">
                                                By <?php echo e($activity->user->name ?? 'System'); ?>

                                            </small>
                                        </p>
                                    </div>

                                    <p class="notification-time">
                                        <?php echo e($activity->created_at?->format('M d, Y h:i A') ?? 'N/A'); ?>

                                    </p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="notification-item">
                                    <p>No recent activity</p>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </section>
              <?php endif; ?>
         
        <?php endif; ?>




        
        <section class="section">
            <div class="section-header">
                <h2 class="section-title">
                    <span class="icon"><i data-lucide="award"></i></span>
                    Event Leaderboard
                </h2>
            </div>

            <div id="lb-wrapper">

                
                <div id="lb-controls">
                    <label for="selectEvent">Event</label>
                    <select id="selectEvent">
                        <option value="" hidden>-- Select Event --</option>
                    </select>

                    
                    <div class="lb-legend">
                        <span class="lb-legend-dot"><span style="background:var(--cat-science-bg)"></span>Science</span>
                        <span class="lb-legend-dot"><span style="background:var(--cat-tech-bg)"></span>Technology</span>
                        <span class="lb-legend-dot"><span style="background:var(--cat-eng-bg)"></span>Engineering</span>
                        <span class="lb-legend-dot"><span style="background:var(--cat-art-bg)"></span>Art</span>
                        <span class="lb-legend-dot"><span style="background:var(--cat-math-bg)"></span>Math</span>
                        <span class="lb-legend-dot"><span
                                style="background:var(--cat-playground-bg)"></span>Playground</span>
                        <span class="lb-legend-dot"><span style="background:var(--cat-egaming-bg)"></span>E-Gaming</span>
                        <span class="lb-legend-dot"><span style="background:var(--cat-esports-bg)"></span>ESports</span>
                        <span class="lb-legend-dot"><span style="background:var(--cat-mission-bg)"></span>Missions</span>


                    </div>

                </div>

                
                <div id="lb-scroll">
                    <table id="lb-table">
                        <thead id="lb-thead"></thead>
                        <tbody id="lb-tbody"></tbody>
                    </table>
                </div>

            </div>
        </section>
<?php $__env->startPush('scripts'); ?>
     <?php echo $__env->make('leaderboard.leaderboard-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
     <?php echo $__env->make('dashboard.script.activity-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
       





    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\PC\Downloads\steam-two\resources\views/dashboard/index.blade.php ENDPATH**/ ?>