<div class="modal fade" id="editEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <form id="editEventForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="event_id" id="editEventId">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- BASIC INFO -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Event Name</label>
                            <input type="text" name="name" id="editName" class="form-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Event Type</label>
                            <select id="editType" name="type" class="form-select" required>
                                <option value="">--Select Type--</option>
                                <option value="esports">STEAM ESports</option>
                                <option value="xr">STEAM XR Sports</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" id="editLocation" class="form-input" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="editStartDate" class="form-input">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" id="editEndDate" class="form-input">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" id="editStatus" class="form-select">
                                <option value="">--Select Status--</option>
                                <option value="draft">Draft</option>
                                <option value="live">Live</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <!-- ESPORTS SECTION -->
                    <div id="editEsportsSection" style="display:none">
                        <h5 class="mb-3 fw-bold text-dark">Brain Game Settings</h5>
                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="brain_enabled" value="0">
                            <input type="checkbox" class="form-check-input" id="editBrainToggle" name="brain_enabled" value="1">
                            <label class="form-check-label text-dark">Enable Brain Game</label>
                        </div>
                        <div id="editBrainFields" class="row g-3" style="display:none">
                            <div class="col-md-4">
                                <label class="form-label">Brain Type</label>
                                <select name="brain_type" id="editBrainType" class="form-select">
                                    <option value="">--Select Brain Type--</option>
                                    <?php $__currentLoopData = $steamCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stcat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($stcat->name); ?>"><?php echo e($stcat->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Score</label>
                                <input type="number" name="brain_score" id="editBrainScore" class="form-input">
                            </div>
                        </div>
                        <hr>
                        <h5 class="mb-3 fw-bold text-dark">Game Settings</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Game</label>
                                <input type="text" name="game" id="editGame" class="form-input">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Players Per Team</label>
                                <input type="number" name="players_per_team" id="editPlayersPerTeam" class="form-input">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Match Rule</label>
                                <select name="match_rule" id="editMatchRule" class="form-select">
                                    <option value="">--Select Round--</option>
                                    <option value="single_round">Single Round</option>
                                    <option value="best_of_3">Best Of 3</option>
                                    <option value="best_of_5">Best Of 5</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Points Per Win</label>
                                <input type="number" name="points_win" id="editPointsWin" class="form-input">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Points Per Draw</label>
                                <input type="number" name="points_draw" id="editPointsDraw" class="form-input">
                            </div>
                        </div>
                        <hr>
                        <h5 class="mb-3 fw-bold text-dark">Tournament Setup</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tournament Type</label>
                                <select name="esports_tournament_type" id="editTournamentType" class="form-select">
                                    <option value="">--Select Type--</option>
                                    <option value="single_elimination">Single Elimination</option>
                                    <option value="double_elimination">Double Elimination</option>
                                    <option value="round_robin">Round Robin</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Number of Teams</label>
                                <input type="number" name="esports_number_of_teams" id="editNumberOfTeams" class="form-input">
                            </div>
                            
                        </div>
                    </div>

                    <!-- XR SECTION -->
                    <div id="editXrSection" style="display:none">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <h5 class="text-dark fw-bold mb-0">C.A.M. ACTIVITIES &amp; MISSION</h5>
                            <button type="button" class="btn btn-primary btn-sm" onclick="addEditCampActivity()">+ Add Activity</button>
                        </div>
                        <div id="editCampActivitiesContainer"></div>

                        <hr>
                        <h5 class="mb-3 fw-bold text-dark">Tournament Setup</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tournament Type</label>
                                <select name="xr_tournament_type" id="editXrTournamentType" class="form-select">
                                    <option value="">--Select Type--</option>
                                    <option value="single_elimination">Single Elimination</option>
                                    <option value="double_elimination">Double Elimination</option>
                                    <option value="round_robin">Round Robin</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Number of Teams</label>
                                <input type="number" name="xr_number_of_teams" id="editXrNumberOfTeams" class="form-input">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Players Per Team</label>
                                <input type="number" name="xr_players_per_team" id="editXrPlayersPerTeam" class="form-input">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Event</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="winnerModal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" >
                <h5 class="modal-title" >
                    Select Event Winner
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        onclick="location.reload()"></button>
            </div>

            <div class="modal-body">
                <p class="text-muted mb-3" style="font-size:0.9rem;">
                    The event has been marked as <strong>Closed</strong>.
                    Choose the winning team from the list below.
                </p>

                
                <input type="text" id="winnerTeamSearch" class="form-input mb-3"
                       placeholder="Search team…" oninput="filterWinnerTeams(this.value)">

                
                <div id="winnerTeamsLoading" class="text-center py-4" style="display:none;">
                    <div class="spinner-border text-warning" role="status"></div>
                    <p class="mt-2 text-muted">Loading teams…</p>
                </div>

                
                <div id="winnerTeamsList" style="max-height:380px; overflow-y:auto; display:none;">
                    
                </div>

                <div id="winnerTeamsEmpty" class="text-center text-muted py-3" style="display:none;">
                    No teams found for this event.
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        onclick="location.reload()">Skip &amp; Close</button>
                <button type="button" class="btn btn-primary" id="saveWinnerBtn"
                        onclick="saveEventWinner()" disabled>
                    Save Winner
                </button>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\PC\Downloads\steam-two\resources\views/events/modals/edit-event.blade.php ENDPATH**/ ?>