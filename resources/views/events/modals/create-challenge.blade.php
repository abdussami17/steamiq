<!-- Add Challenge Modal -->
<div class="modal fade" id="createChallengeModal" tabindex="-1" aria-labelledby="createChallengeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="createChallengeModalLabel">Add New Challenge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('challenges.store') }}">
                @csrf

                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Event -->
                        <div class="col-md-6">
                            <label class="form-label">Event <span class="text-danger">*</span></label>
                            <select name="event_id" class="form-select" >
                                <option hidden>-- Select Event --</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}">
                                        {{ $event->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Challenge Name -->
                        <div class="col-md-6">
                            <label class="form-label">Challenge Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-input" placeholder="Tug of War" >
                        </div>

                        <!-- Type -->
                        <div class="col-md-6">
                            <label class="form-label">Challenge Type <span class="text-danger">*</span></label>
                            <select name="pillar_type" class="form-select" >
                                <option>--Select Challenge--</option>
                                <option value="brain">Brain</option>
                                <option value="playground">Playground</option>
                                <option value="egame">E-Gaming</option>

                            </select>
                        </div>
                        
<div class="col-md-6 d-none" id="subcategoryWrapper">
    <label class="form-label">Brain Subcategory <span class="text-danger">*</span></label>

    <select name="sub_category" id="subcategorySelect" class="form-select">
        <option  value="">-- Select Subcategory --</option>
        <option value="science">Science</option>
        <option value="technology">Technology</option>
        <option value="engineering">Engineering</option>
        <option value="art">Art</option>
        <option value="math">Math</option>

    </select>
</div>


                        <!-- Points -->
                        <div class="col-md-6">
                            <label class="form-label">Max Points <span class="text-danger">*</span></label>
                            <input type="number" name="max_points" placeholder="480" class="form-input" >
                        </div>

                   

                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Challenge</button>
                </div>

            </form>

        </div>
    </div>
</div>


<script>
    const pillarSelect = document.querySelector('select[name="pillar_type"]');
    const subcategoryWrapper = document.getElementById('subcategoryWrapper');
    const subcategorySelect = document.getElementById('subcategorySelect');
    
    pillarSelect.addEventListener('change', function () {
    
        if (this.value === 'brain') {
            subcategoryWrapper.classList.remove('d-none'); // show
            // subcategorySelect.required = true;
        } 
        else {
            subcategoryWrapper.classList.add('d-none'); // hide
            // subcategorySelect.required = false;
            subcategorySelect.value = '';
        }
    
    });
    </script>
    