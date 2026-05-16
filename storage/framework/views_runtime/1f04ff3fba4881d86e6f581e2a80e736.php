<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Player</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="addStudentForm" method="POST" action="<?php echo e(route('student.store')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">

                    <div class="row">
                        <!-- Organization -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Organization</label>
                            <select class="form-select" id="createStdorganizationSelect">
                                <option value="">-- Select Organization --</option>
                                <?php $__currentLoopData = $organizations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $org): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($org->id); ?>"><?php echo e($org->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    
                        <!-- Group -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Group</label>
                            <select class="form-select" id="createStdgroupSelect">
                                <option value="">-- Select Group --</option>
                            </select>
                        </div>
                    
                        <!-- Team -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Team</label>
                            <select class="form-select" id="createStdteamSelect" name="team_id" required>
                                <option value="">-- Select Team --</option>
                            </select>
                        </div>
                    </div>

                    <div id="studentsContainer">
                        <div class="student-row row g-3 mb-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Player Name</label>
                                <input type="text" placeholder="Player Name" class="form-input" name="students[0][name]" required>
                            </div>
                    
                            <div class="col-md-4">
                                <label class="form-label">Email <small class="text-muted">(optional)</small></label>
                                <input type="email" placeholder="Email" class="form-input" name="students[0][email]">
                            </div>
                    
                            <div class="col-md-3">
                                <label class="form-label">Profile <small class="text-muted">(optional)</small></label>
                                <input type="file" class="form-input" name="students[0][profile]" accept="image/*">
                            </div>
                    
                            <div class="col-md-1">
                                <!-- empty for first row -->
                            </div>
                        </div>
                    </div>

                    <button type="button" id="addMoreBtn" class="btn btn-outline-secondary w-100 mt-2">
                        + Add More Player
                    </button>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Player</button>
                </div>
            </form>

        </div>
    </div>
</div>
<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
    
        let studentIndex = 1;
        const container = document.getElementById('studentsContainer');
        const addBtn = document.getElementById('addMoreBtn');
    
        if (!container || !addBtn) return;
    
       
        if (addBtn.dataset.bound) return;
        addBtn.dataset.bound = true;
    
        addBtn.addEventListener('click', function () {
    
            const row = document.createElement('div');
            row.classList.add('student-row','row','g-3','mb-2','align-items-center');
    
            row.innerHTML = `
                <div class="col-md-4">
                    <input type="text" class="form-input" name="students[${studentIndex}][name]" placeholder="Player Name" required>
                </div>
    
                <div class="col-md-4">
                    <input type="email" class="form-input" name="students[${studentIndex}][email]" placeholder="Email">
                </div>
    
                <div class="col-md-3">
                    <input type="file" class="form-input" name="students[${studentIndex}][profile]" accept="image/*">
                </div>
    
                <div class="col-md-1">
                    <button type="button" class="btn btn-icon btn-delete removeRow">
                        <i data-lucide="trash-2"></i>
                    </button>
                </div>
            `;
    
            container.appendChild(row);
            studentIndex++;
    
            if (window.lucide) lucide.createIcons();
        });
    
        container.addEventListener('click', function(e){
            const btn = e.target.closest('.removeRow');
            if(btn){
                btn.closest('.student-row').remove();
            }
        });
    
    })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        
            const orgSelect = document.getElementById('createStdorganizationSelect');
            const groupSelect = document.getElementById('createStdgroupSelect');
            const teamSelect = document.getElementById('createStdteamSelect');
        
            // Organization → Groups
            orgSelect.addEventListener('change', function () {
                const orgId = this.value;
        
                groupSelect.innerHTML = '<option value="">Loading...</option>';
                teamSelect.innerHTML = '<option value="">-- Select Team --</option>';
        
                if (!orgId) return;
        
                fetch(`/get-groups/${orgId}`)
                    .then(res => res.json())
                    .then(data => {
                        groupSelect.innerHTML = '<option value="">-- Select Group --</option>';
        
                        data.forEach(group => {
                            groupSelect.innerHTML += `
                                <option value="${group.id}">${group.group_name}</option>
                            `;
                        });
                    });
            });
        
            // Group → Teams
            groupSelect.addEventListener('change', function () {
                const groupId = this.value;
        
                teamSelect.innerHTML = '<option value="">Loading...</option>';
        
                if (!groupId) return;
        
                fetch(`/get-teams/${groupId}`)
                    .then(res => res.json())
                    .then(data => {
                        teamSelect.innerHTML = '<option value="">-- Select Team --</option>';
        
                        data.forEach(team => {
                            teamSelect.innerHTML += `
                                <option value="${team.id}">${team.name}</option>
                            `;
                        });
                    });
            });
        
        });
        </script>
    <script>
    // addStudentForm submit — lives here so it works on every page (Q-Action, events, etc.)
    (function () {
        var form = document.getElementById('addStudentForm');
        if (!form || form.dataset.handlerBound) return;
        form.dataset.handlerBound = '1';

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: formData
                });

                // ── Error response ──────────────────────────────────────────
                if (!res.ok) {
                    let errData = null;
                    try { errData = await res.json(); } catch (e) {}

                    if (errData) {
                        let messages = [];
                        if (errData.errors) {
                            const errs = Array.isArray(errData.errors)
                                ? errData.errors
                                : Object.values(errData.errors).flat();
                            messages = messages.concat(errs.filter(Boolean));
                        }
                        if (errData.message) messages.push(errData.message);

                        if (messages.length) {
                            messages.slice(0, 5).forEach(m => {
                                try { if (window.toastr) toastr.error(m); } catch (ex) {}
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Warning!',
                                confirmButtonColor: '#000000',
                                text: messages[0]
                            });
                            return;
                        }
                    }

                    // Fallback: non-JSON or no message — reload to surface server flash
                    window.location.reload();
                    return;
                }

                // ── Success response ─────────────────────────────────────────
                let data = null;
                try { data = await res.json(); } catch (ex) {}

                if (!data) {
                    // HTML redirect — reload so layout flash toasts appear
                    window.location.reload();
                    return;
                }

                try { if (window.toastr && data.message) toastr.success(data.message); } catch (ex) {}
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    confirmButtonColor: '#000000',
                    text: data.message
                });

                form.reset();
                const modal = bootstrap.Modal.getInstance(document.getElementById('addStudentModal'));
                if (modal) modal.hide();
                loadLeaderboard();

            } catch (err) {
                let msg = (err && err.message) ? err.message : 'Something went wrong';
                try { if (window.toastr) toastr.error(msg); } catch (ex) {}
                Swal.fire({ icon: 'error', title: 'Error', confirmButtonColor: '#000000', text: msg });
            }
        });
    })();
    </script>
<?php $__env->stopPush(); ?><?php /**PATH C:\Users\PC\Desktop\steam-two\resources\views/students/modals/create-students.blade.php ENDPATH**/ ?>