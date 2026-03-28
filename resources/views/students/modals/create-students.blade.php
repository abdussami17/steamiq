<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Player</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="studentForm" method="POST" action="{{ route('student.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="row">
                        <!-- Organization -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Organization</label>
                            <select class="form-select" id="createStdorganizationSelect">
                                <option value="">-- Select Organization --</option>
                                @foreach($organizations as $org)
                                    <option value="{{ $org->id }}">{{ $org->name }}</option>
                                @endforeach
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
@push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function(){
    
    let studentIndex = 1;
    const container = document.getElementById('studentsContainer');
    
    document.getElementById('addMoreBtn').addEventListener('click', function(){
    
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
    
        if (window.lucide) {
            lucide.createIcons();
        }
    });
    
    container.addEventListener('click', function(e){
        const btn = e.target.closest('.removeRow');
        if(btn){
            btn.closest('.student-row').remove();
        }
    });
    
    });
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
@endpush