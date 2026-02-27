<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="studentForm" method="POST" action="{{ route('student.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Select Team </label>
                        <select class="form-select" id="teamSelect" name="team_id" required>
                            <option hidden>-- Select Team --</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" data-event="{{ $team->event_id }}">
                                    {{ $team->team_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="studentsContainer">
                        <div class="student-row row g-3 mb-2">
                            <div class="col-md-4">
                                <label class="form-label">Student Name</label>
                                <input type="text" class="form-input" name="students[0][name]" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email <small class="text-muted">(optional)</small></label>
                                <input type="email" class="form-input" name="students[0][email]">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Profile <small class="text-muted">(optional)</small></label>
                                <input type="file" class="form-input" name="students[0][profile]" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <button type="button" id="addMoreBtn" class="btn btn-outline-secondary w-100 mt-2">
                        + Add More Students
                    </button>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Students</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){

    let studentIndex = 1;
    const container = document.getElementById('studentsContainer');

    document.getElementById('addMoreBtn').addEventListener('click', function(){
        for(let i=0; i<1; i++){
            const row = document.createElement('div');
            row.classList.add('student-row', 'row', 'g-3', 'mb-2');

            row.innerHTML = `
                <div class="col-md-4">
                    <input type="text" class="form-input" name="students[${studentIndex}][name]" placeholder="Student Name" required>
                </div>
                <div class="col-md-4">
                    <input type="email" class="form-input" name="students[${studentIndex}][email]" placeholder="Email">
                </div>
                <div class="col-md-4">
                    <input type="file" class="form-input" name="students[${studentIndex}][profile]" accept="image/*">
                </div>
            `;
            container.appendChild(row);
            studentIndex++;
        }
    });

});
</script>