<!-- Assign Points Modal -->
<div class="modal fade" id="scoreModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="scoreForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Points</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Event -->
                    <div class="mb-3">
                        <label class="form-label">Event </label>
                        <select id="eventSelect" class="form-select" required>
                            <option value="">-- Select Event --</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Assign To -->
                    <div class="mb-3 d-none" id="typeDiv">
                        <label class="form-label">Assign To </label>
                        <select id="entityType" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="student">Student</option>
                            <option value="team">Team</option>
                        </select>
                    </div>

                    <!-- Student -->
                    <div class="mb-3 d-none" id="studentDiv">
                        <label class="form-label">Student </label>
                        <select id="studentSelect" class="form-select"></select>
                    </div>

                    <!-- Team -->
                    <div class="mb-3 d-none" id="teamDiv">
                        <label class="form-label">Team </label>
                        <select id="teamSelect" class="form-select"></select>
                    </div>

                    <!-- Activity -->
                    <div class="mb-3 d-none" id="activityDiv">
                        <label class="form-label">Activity </label>
                        <select id="activitySelect" class="form-select"></select>
                    </div>

                    <!-- STEAM Points -->
                    <div class="d-none" id="pointsDiv">
                        <table class="table table-bordered table-dark">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Points</th>
                                </tr>
                            </thead>
                            <tbody id="steamPointsBody">
                                <!-- Filled dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Save Points</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const eventSelect = document.getElementById('eventSelect');
        const typeDiv = document.getElementById('typeDiv');
        const entityType = document.getElementById('entityType');
        const studentDiv = document.getElementById('studentDiv');
        const teamDiv = document.getElementById('teamDiv');
        const studentSelect = document.getElementById('studentSelect');
        const teamSelect = document.getElementById('teamSelect');
        const activityDiv = document.getElementById('activityDiv');
        const activitySelect = document.getElementById('activitySelect');
        const pointsDiv = document.getElementById('pointsDiv');
        const steamPointsBody = document.getElementById('steamPointsBody');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('scoreForm');
    
        let currentEvent = '';
        let currentType = '';
    
        function resetAll() {
            typeDiv.classList.add('d-none');
            studentDiv.classList.add('d-none');
            teamDiv.classList.add('d-none');
            activityDiv.classList.add('d-none');
            pointsDiv.classList.add('d-none');
            entityType.value = '';
            studentSelect.innerHTML = '';
            teamSelect.innerHTML = '';
            activitySelect.innerHTML = '';
            steamPointsBody.innerHTML = '';
            submitBtn.disabled = true;
        }
    
        eventSelect.addEventListener('change', function(){
            currentEvent = this.value;
            resetAll();
            if(this.value) typeDiv.classList.remove('d-none');
        });
    
        entityType.addEventListener('change', function(){
            currentType = this.value;
            studentDiv.classList.add('d-none');
            teamDiv.classList.add('d-none');
            activityDiv.classList.add('d-none');
            pointsDiv.classList.add('d-none');
            steamPointsBody.innerHTML = '';
    
            if(this.value === 'student') {
                studentDiv.classList.remove('d-none');
                fetch(`/api/events/${currentEvent}/students`)
                    .then(r=>r.json())
                    .then(data=>{
                        studentSelect.innerHTML = '<option value="">-- Select Student --</option>';
                        data.forEach(s => studentSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`);
                    });
            } else if(this.value === 'team') {
                teamDiv.classList.remove('d-none');
                fetch(`/api/events/${currentEvent}/teams`)
                    .then(r=>r.json())
                    .then(data=>{
                        teamSelect.innerHTML = '<option value="">-- Select Team --</option>';
                        data.forEach(t => teamSelect.innerHTML += `<option value="${t.id}">${t.name}</option>`);
                    });
            }
        });
    
        [studentSelect, teamSelect].forEach(select=>{
            select.addEventListener('change', function(){
                if(this.value){
                    activityDiv.classList.remove('d-none');
                    fetch(`/api/events/${currentEvent}/activities`)
                        .then(r=>r.json())
                        .then(data=>{
                            activitySelect.innerHTML = '<option value="">-- Select Activity --</option>';
                            data.forEach(a=>activitySelect.innerHTML += `<option value="${a.id}">${a.name}</option>`);
                        });
                } else {
                    activityDiv.classList.add('d-none');
                    pointsDiv.classList.add('d-none');
                }
            });
        });
    
        activitySelect.addEventListener('change', function(){
            if(this.value){
                pointsDiv.classList.remove('d-none');
                // Fetch STEAM categories
                fetch('/api/steam-categories')
                    .then(r=>r.json())
                    .then(data=>{
                        steamPointsBody.innerHTML = '';
                        data.forEach(c=>{
                            steamPointsBody.innerHTML += `
                                <tr>
                                    <td>${c.name}</td>
                                    <td><input type="number" min="0" class="form-input steam-point" data-id="${c.id}" value="0"></td>
                                </tr>
                            `;
                        });
                        submitBtn.disabled = false;
                    });
            } else pointsDiv.classList.add('d-none');
        });
    
        form.addEventListener('submit', function(e){
            e.preventDefault();
            const fd = new FormData();
            fd.append('event_id', eventSelect.value);
            fd.append('challenge_activity_id', activitySelect.value);
            if(currentType==='student') fd.append('student_id', studentSelect.value);
            if(currentType==='team') fd.append('team_id', teamSelect.value);
    
            document.querySelectorAll('.steam-point').forEach(input=>{
                fd.append(`points[${input.dataset.id}]`, input.value);
            });
    
            submitBtn.disabled = true;
            fetch("{{ route('scores.store') }}", {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: fd})
                .then(r=>r.json())
                .then(d=>{
                    if(d.success){
                        toastr.success(d.message);
                        form.reset(); resetAll(); eventSelect.value='';
                        const modal = bootstrap.Modal.getInstance(document.getElementById('scoreModal'));
                        modal.hide();
                    } else {
                        toastr.error(d.message||'Failed');
                        submitBtn.disabled=false;
                    }
                }).catch(err=>{
                    toastr.error('Error occurred'); submitBtn.disabled=false;
                });
        });
    
        document.getElementById('scoreModal').addEventListener('hidden.bs.modal', function(){form.reset(); resetAll(); eventSelect.value='';});
    });
    </script>