

<?php $__env->startSection('title', 'Roster Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">

<section class="section">
        

        <div class="section-header">
            <h2 class="section-title">
                <span class="icon">
                    <i data-lucide="user"></i>
                </span>
                Roster Management
            </h2>
<div class="d-flex gap-2">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
        <i data-lucide="import"></i> Import Roster
    </button>
    
<button class="btn btn-secondary" id="exportGameCardBtn">
<i data-lucide="layout-template"></i> Create Game Card
</button>
</div>
        </div>
    <div class="spreadsheet-container">
        <div class="spreadsheet-toolbar">
            <div class="form-group d-flex justify-content-normal align-items-center gap-3 w-100">
                <label class="form-label">Filter by Event:</label>
                <select id="filterEvent" class="form-select " style="max-width: 300px">
                    <option value="">All Events</option>
                    <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($event->id); ?>"><?php echo e($event->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
         
        </div>

        <div >
            <table class="data-table" id="rosterTable">
                <thead >
                    <tr>
                        <th>#</th>
                        <th>Event</th>
                        <th>Organization</th>
                        <th>Coach</th>
                        <th>Total Players</th>
                        <th>Status</th>
                        <th>Uploaded At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="rosterTableBody">
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <span class="spinner-border spinner-border-sm me-2"></span> Loading...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    
    
</section>

</div>




    

 

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:9999">
    <div id="packetToast" class="toast align-items-center border-0" role="alert" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <span class="spinner-border spinner-border-sm me-2" id="packetToastSpinner"></span>
                <span id="packetToastText">Generating PDF...</span>
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startPush('modals'); ?>
    <?php echo $__env->make('roster.modal.import', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('roster.modal.view', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('roster.modal.qr-code', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
<?php echo $__env->make('roster.script.script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\PC\Downloads\steam-two\resources\views/roster/index.blade.php ENDPATH**/ ?>