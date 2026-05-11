

<?php if(!$selectedEvent): ?>
    <div class="pg-empty">No events found. Please create an event first.</div>
<?php else: ?>

    
    <?php if(!empty($primaryData)): ?>
        <?php echo $__env->make('scoreboard._single_board', [
            'eventName'  => $selectedEvent->name,
            'division'   => 'Primary',
            'rows'       => $primaryData,
            'activities' => $activities,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    
    <?php if(!empty($primaryData) && !empty($juniorData)): ?>
        <div class="pg-gap"></div>
    <?php endif; ?>

    
    <?php if(!empty($juniorData)): ?>
        <?php echo $__env->make('scoreboard._single_board', [
            'eventName'  => $selectedEvent->name,
            'division'   => 'Junior',
            'rows'       => $juniorData,
            'activities' => $activities,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    
    <?php if(empty($primaryData) && empty($juniorData)): ?>
        <div class="pg-empty">No scoreboard data found for this event.</div>
    <?php endif; ?>

<?php endif; ?><?php /**PATH C:\Users\PC\Downloads\steam-two\resources\views/scoreboard/_boards.blade.php ENDPATH**/ ?>