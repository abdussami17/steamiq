

<?php
    $divUp     = strtoupper($division);
    $ageStr    = match($division) {
        'Junior'  => '11–14 YRS',
        'Primary' => '7–10 YRS',
        default   => '',
    };
    $totalTeams = count($rows);
    $totalCols  = 8; // team no, team name, members, total points, division, flags, org, rank

    // Group rows by group_id, preserving the rank-sorted order
    $grouped = collect($rows)->groupBy('group_id');
?>

<div class="pg-board">

    
    <div class="pg-titlebar">
        <div class="pg-board-name"><?php echo e(Str::upper($eventName)); ?> &mdash; <?php echo e($divUp); ?></div>
        <?php if($ageStr): ?>
            <div class="pg-board-badge"><?php echo e($ageStr); ?></div>
        <?php endif; ?>
    </div>

    
    <div class="pg-count"><?php echo e($totalTeams); ?> TEAM<?php echo e($totalTeams !== 1 ? 'S' : ''); ?></div>

    
    <div class="pg-scroll">
    <table class="pg-table">
        <thead>
            <tr>
                <th>Team No.</th>
                <th class="th-l">Team Name</th>
                <th class="th-l">Members</th>
                <th>Total Points</th>
                <th>Division</th>
                <th>Flags</th>
                <th class="th-l">ORG</th>
                <th class="th-rank">Rank</th>
            </tr>
        </thead>
        <tbody>

            <?php if($totalTeams === 0): ?>
                <tr>
                    <td colspan="<?php echo e($totalCols); ?>" class="pg-empty">
                        No <?php echo e($division); ?> teams found for this event.
                    </td>
                </tr>
            <?php else: ?>
                <?php $teamCounter = 0; ?>
                <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupId => $groupRows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                    <tr class="pg-grp">
                        <td colspan="<?php echo e($totalCols); ?>">
                            <?php echo e($groupRows->first()['group_name']); ?>

                        </td>
                    </tr>

                    
                    <?php $__currentLoopData = $groupRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $teamCounter++;
                            $rkCls = match($row['rank']) { 1 => 'r1', 2 => 'r2', 3 => 'r3', default => '' };
                        ?>
                        <tr class="pg-dr">
                            <td class="td-no"><?php echo e($teamCounter); ?></td>
                            <td class="td-name"><?php echo e($row['team_name']); ?></td>
                            <td class="td-mem"><?php echo e($row['members'] ?: '—'); ?></td>
                            <td class="td-pts"><?php echo e(number_format($row['total_points'] ?? 0)); ?></td>
                            <td class="td-div"><?php echo e(Str::upper($row['division'] ?? '')); ?></td>

                            <td class="td-flg">
                                <?php echo e($row['flag_totals'] ?? 0); ?>

                                <?php if(!empty($row['cards'])): ?>
                                    <?php
                                        $cardCounts = [];
                                        foreach ($row['cards'] as $c) {
                                            $t = $c['type'] ?? 'unknown';
                                            $cardCounts[$t] = ($cardCounts[$t] ?? 0) + 1;
                                        }
                                    ?>
                                    <span class="card-badges" style="margin-left:8px;">
                                        <?php $__currentLoopData = $cardCounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $cls = $type === 'red' ? 'card-red' : ($type === 'yellow' ? 'card-yellow' : ($type === 'orange' ? 'card-orange' : 'card-unknown'));
                                            ?>
                                            <span title="<?php echo e(strtoupper($type)); ?> card" class="card-badge <?php echo e($cls); ?>"><?php echo e($count); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="td-org"><?php echo e($row['org_name']); ?></td>
                            <td class="td-rank">
                                <span class="rk-pill <?php echo e($rkCls); ?>"><?php echo e($row['rank']); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>

        </tbody>
    </table>
    </div>

</div><?php /**PATH /home/u236413684/domains/voags.com/public_html/steamiq/resources/views/scoreboard/_single_board.blade.php ENDPATH**/ ?>