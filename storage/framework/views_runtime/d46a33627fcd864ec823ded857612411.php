
<?php $__env->startSection('title', 'Leaderboard - SteamIQ'); ?>


<?php $__env->startSection('content'); ?>
    <div class="container">


                           
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">
                <span class="icon"><i data-lucide="award"></i></span>
                 Leaderboard
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
        
    <?php $__env->stopPush(); ?>


    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\PC\Downloads\steamiq (5)\resources\views/leaderboard/index.blade.php ENDPATH**/ ?>