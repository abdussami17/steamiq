
<?php $__env->startSection('title', 'Scoring - SteamIQ'); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startPush('styles'); ?>
    <?php echo $__env->make('scores.style', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
<div class="container-fluid px-3">

    <section class="section">
        <div class="section-header d-flex align-items-center gap-2 flex-wrap mb-2">
            <h2 class="section-title mb-0">
                <span class="icon"><i data-lucide="award"></i></span>
                Scoring
            </h2>
            <div class="ms-auto d-flex gap-2 flex-wrap">
               
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("create_score")): ?>
                <button class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#scoreModal">
                    <i data-lucide="plus"></i> Add Score
                </button>
                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#assignBonusModal">
                    <i data-lucide="plus"></i> Bonus
                </button>
            <?php endif; ?>
            </div>
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
                    <span class="lb-legend-dot"><span style="background:var(--cat-playground-bg)"></span>Playground</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-egaming-bg)"></span>EGaming</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-esports-bg)"></span>Esports</span>
                    <span class="lb-legend-dot"><span style="background:var(--cat-mission-bg)"></span>Mission</span>
                    <span class="lb-legend-dot"><span style="background:#b8860b"></span>Bonus</span>
                </div>

                <div class="lb-actions">
                    <button id="exportXlsxBtn" class="lb-btn" style="background:#1a3a1a;color:#56d364;border:1px solid #238636;">
                        <i data-lucide="download"></i> Export XLSX
                    </button>
                    
                    <button id="bulkEditBtn" class="lb-btn lb-btn-bulk">
                        <i data-lucide="plus-square"></i> Bulk Edit
                    </button>
                    
                    <button id="openBulkModalBtn" class="lb-btn lb-btn-bulk-go" style="display:none;">
                        <i data-lucide="edit"></i> Edit Selected
                    </button>
               
                </div>
            </div>

            
            <div id="bulk-bar">
                <span style="font-weight:900;color:#f5c518;font-size:12px;letter-spacing:.08em;">BULK EDIT MODE</span>
                <span style="color:#3a4454;">|</span>
                <span><span id="bulk-count" style="font-weight:900;color:#f5c518;font-size:1.4rem;">0</span> selected</span>
                <span style="color:#fff;font-size:11px;">Click score cells to select / deselect</span>
                <button class="lb-btn lb-btn-bulk-go ms-auto" style="padding:5px 12px;font-size:12px;"
                onclick="document.getElementById('openBulkModalBtn').click()">
                Open Edit Panel <i data-lucide="arrow-right"></i>
            </button>
            </div>

            
            <div id="lb-scroll">
                <table id="lb-table">
                    <thead id="lb-thead"></thead>
                    <tbody id="lb-tbody">
                        <tr class="lb-state-row"><td colspan="999">Select an event to load the leaderboard.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?php $__env->startPush('modals'); ?>
    <?php echo $__env->make('scores.modals.create-scores', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('bonus.modals.assign-bonus', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>

    <?php echo $__env->make('scores.scripts.score-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\PC\Downloads\steamiq (5)\resources\views/scores/index.blade.php ENDPATH**/ ?>