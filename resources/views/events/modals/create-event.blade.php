<!-- ================= CREATE EVENT MODAL ================= -->
<div class="modal fade" id="createEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">

            <form method="POST" action="{{ route('events.store') }}">
                @csrf

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title">Create New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    <!-- ================= BASIC INFO ================= -->

                    <div class="row g-4">

                        <div class="col-md-6">
                            <label class="form-label">Event Name</label>
                            <input name="name" class="form-input" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Event Type</label>
                            <select id="eventType" name="type" class="form-select" required>
                                <option value="">--Select Type--</option>
                                <option value="esports">STEAM ESports</option>
                                <option value="xr">STEAM XR Sports</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-input" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-input">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-input">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">--Select Status--</option>
                                <option value="draft">Draft</option>
                                <option value="live">Live</option>
                                <option value="closed">Closed</option>

                            </select>
                        </div>

                    </div> <!-- end row -->
                    <hr>

                    <!-- ================= ESPORTS SECTION ================= -->
                    <div id="esportsSection" style="display:none">
                        <h5 class="mb-3 text-white fw-bold">Brain Game Settings</h5>

                        <div class="form-check form-switch mb-3">

                            <input type="hidden" name="brain_enabled" value="0">
                            <input type="checkbox" class="form-check-input" id="brainToggle" name="brain_enabled"
                                value="1">
                            <label class="form-check-label text-white">Enable Brain Game</label>
                        </div>

                        <!-- Brain Fields Row -->
                        <div id="brainFields" class="row g-3" style="display:none">
                            <div class="col-md-4">
                                <label class="form-label ">Brain Type</label>
                                <select name="brain_type" class="form-select">
                                    <option value="">--Select Brain Type--</option>
                                    @foreach ($steamCategories as $stcat)
                                        <option value="{{ $stcat->name }}">{{ $stcat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Score</label>
                                <input name="brain_score" type="number" class="form-input" placeholder="Score">
                            </div>


                        </div>

                        <hr>

                        <h5 class="mb-3 text-white fw-bold">Game Settings</h5>
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label">Game</label>
                                <input type="text" placeholder="Aqua Ball League" name="game" class="form-input">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Players Per Team</label>
                                <input name="players_per_team" type="number" class="form-input">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Match Rule</label>
                                <select class="form-select" name="match_rule" id="">
                                    <option value="">--Select Round--</option>

                                    <option value="single_round">Single Round</option>
                                    <option value="best_of_3">Best Of 3</option>
                                    <option value="best_of_5">Best Of 5</option>

                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Points Per Win</label>
                                <input name="points_win" type="number" class="form-input">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Points Per Draw</label>
                                <input name="points_draw" type="number" class="form-input">
                            </div>

                        </div>

                        <hr>
                        <h5 class="mb-3 text-white fw-bold">Tournament Setup</h5>
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Tournament Type</label>
                                <select name="esports_tournament_type" class="form-select" id="">
                                    <option value="">--Select Type--</option>
                                    <option value="single_elimination">Single Elimination</option>
                                    <option value="double_elimination">Double Elimination</option>
                                    <option value="round_robin">Round Robin</option>

                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Number of Teams</label>
                                <input name="esports_number_of_teams" type="number" class="form-input">
                            </div>

                        </div>
                    </div> <!-- end esportsSection -->

                    <!-- ================= XR SECTION ================= -->
                    <div id="xrSection" style="display:none">
                        <h5 class="mb-3 text-white fw-bold">Activities</h5>

                        <button type="button" class="btn btn-primary mb-3" onclick="addActivity()">Add
                            Activity</button>
                        <div id="activitiesContainer"></div>

                        <hr>
                        <h5 class="mb-3 text-white fw-bold">Tournament Setup</h5>
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Tournament Type</label>
                                <select name="xr_tournament_type" class="form-select" id="">
                                    <option value="">--Select Type--</option>
                                    <option value="single_elimination">Single Elimination</option>
                                    <option value="double_elimination">Double Elimination</option>
                                    <option value="round_robin">Round Robin</option>

                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Number of Teams</label>
                                <input name="xr_number_of_teams" type="number" class="form-input">
                            </div>

                        </div>
                    </div>

                </div> <!-- end modal-body -->

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Event</button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- ================= JS ================= -->
<script>
    const eventType = document.getElementById("eventType");
    const esportsSection = document.getElementById("esportsSection");
    const xrSection = document.getElementById("xrSection");
    const brainToggle = document.getElementById("brainToggle");


    function toggleSections(type) {

        // hide both first
        esportsSection.style.display = "none";
        xrSection.style.display = "none";

        // disable all inputs first
        esportsSection.querySelectorAll("input,select").forEach(el => el.disabled = true);
        xrSection.querySelectorAll("input,select").forEach(el => el.disabled = true);

        if (type === "esports") {
            esportsSection.style.display = "block";
            esportsSection.querySelectorAll("input,select").forEach(el => el.disabled = false);
        }

        if (type === "xr") {
            xrSection.style.display = "block";
            xrSection.querySelectorAll("input,select").forEach(el => el.disabled = false);
        }
    }

    eventType.addEventListener("change", function() {
        toggleSections(this.value);
    });
    eventType.addEventListener("change", function() {
        esportsSection.style.display = "none";
        xrSection.style.display = "none";
        if (this.value === "esports") esportsSection.style.display = "block";
        if (this.value === "xr") xrSection.style.display = "block";
    });

    brainToggle.addEventListener("change", function() {

        document.getElementById("brainFields").style.display =
            this.checked ? "flex" : "none";
    });

    /* ================= XR Activities ================= */
    function addActivity() {
        const container = document.getElementById("activitiesContainer");
        const index = container.children.length; // ensures proper numeric indexing
        const div = document.createElement("div");
        div.className = "row g-3 mt-2 rounded p-0";
        div.innerHTML = `
      <div class="col-md-4">
   <input type="text" placeholder="Thug Of War" name="activities[${index}][type]" class="form-input" required>
      </div>

      <div class="col-md-3">
        <input name="activities[${index}][score]" type="number" class="form-input" placeholder="Max Score" required min="0">
      </div>

      <div class="col-md-2">
        <button type="button" class="btn btn-primary" onclick="this.closest('.row').remove(); reindexActivities();">Remove</button>
      </div>
    `;
        container.appendChild(div);
    }

    function reindexActivities() {
        const container = document.getElementById("activitiesContainer");
        Array.from(container.children).forEach((row, i) => {
            const type = row.querySelector('input[name^="activities"][type="text"]');
            const score = row.querySelector('input[name^="activities"][type="number"]');
            if (type) type.name = `activities[${i}][type]`;
            if (score) score.name = `activities[${i}][score]`;
        });
    }
</script>
