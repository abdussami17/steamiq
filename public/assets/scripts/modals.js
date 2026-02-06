// This file contains modal HTML that can be injected into pages
// Call loadModals() to inject all modals into the page

function loadModals() {
const modalsHTML = `
<!-- Create Event Modal -->
<div id="createEventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Create New Event</h2>
            <button class="close-btn" onclick="closeModal('createEventModal')">&times;</button>
        </div>

        <form>
            <div class="form-group">
                <label class="form-label">Event Name</label>
                <input type="text" class="form-input" placeholder="Spring Championship 2026">
            </div>

            <div class="form-group">
                <label class="form-label">Event Type</label>
                <select class="form-select">
                    <option value="match">Match Event</option>
                    <option value="tournament">Tournament Event</option>
                    <option value="season">Season Tracking Event</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-input" value="2026-02-02">
            </div>

            <div class="form-group">
                <label class="form-label">End Date (Optional)</label>
                <input type="date" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Location</label>
                <input type="text" class="form-input" placeholder="Main Arena">
            </div>

            <div class="form-group">
                <label class="form-label">Expected Registration Count</label>
                <input type="number" class="form-input" placeholder="30">
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select">
                    <option value="draft">Draft</option>
                    <option value="live">Live</option>
                    <option value="closed">Closed</option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn btn-secondary" style="flex: 1;"
                    onclick="closeModal('createEventModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Create Event</button>
            </div>
        </form>
    </div>
</div>

<!-- Match PIN Modal -->
<div id="matchPinModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Match PIN Generated</h2>
            <button class="close-btn" onclick="closeModal('matchPinModal')">&times;</button>
        </div>

        <div class="pin-display">
            <div class="pin-label">Match Access PIN</div>
            <div class="pin-value">7342</div>
        </div>

        <div class="message message-success" style="margin-top: 1.5rem;">
            <span>✓</span>
            <span>PIN generated successfully! Share this with participants.</span>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button class="btn btn-secondary" style="flex: 1;" onclick="copyPin()">
                <i data-lucide="copy"></i> Copy PIN
            </button>
            <button class="btn btn-primary" style="flex: 1;" onclick="emailPin()">
                <i data-lucide="mail"></i> Email PIN
            </button>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div id="eventDetailsModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h2 class="modal-title">Spring Championship 2026</h2>
            <button class="close-btn" onclick="closeModal('eventDetailsModal')">&times;</button>
        </div>

        <div style="margin-bottom: 2rem;">
            <span class="badge badge-live">Live</span>
            <span class="badge"
                style="background: rgba(0, 212, 255, 0.2); color: var(--accent); border: 1px solid var(--accent); margin-left: 0.5rem;">Tournament</span>
        </div>

        <div class="stats-grid" style="margin-bottom: 2rem;">
            <div class="stat">
                <div class="stat-label">Total Teams</div>
                <div class="stat-value" style="font-size: 1.5rem;">32</div>
            </div>
            <div class="stat">
                <div class="stat-label">Total Players</div>
                <div class="stat-value" style="font-size: 1.5rem;">96</div>
            </div>
            <div class="stat">
                <div class="stat-label">Matches</div>
                <div class="stat-value" style="font-size: 1.5rem;">31</div>
            </div>
            <div class="stat">
                <div class="stat-label">Completed</div>
                <div class="stat-value" style="font-size: 1.5rem;">28</div>
            </div>
        </div>

        <div style="background: var(--dark); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;">
            <h3 style="margin-bottom: 1rem; font-size: 1.1rem;">Event Information</h3>
            <div style="display: grid; gap: 0.75rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-dim);">Start Date:</span>
                    <span style="font-weight: 600;">January 15, 2026</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-dim);">End Date:</span>
                    <span style="font-weight: 600;">February 5, 2026</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-dim);">Location:</span>
                    <span style="font-weight: 600;">Central Sports Complex</span>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button class="btn btn-secondary" style="flex: 1;">Import Roster</button>
            <button class="btn btn-secondary" style="flex: 1;">View Bracket</button>
            <button class="btn btn-primary" style="flex: 1;">Enter Scores</button>
        </div>
    </div>
</div>

<!-- Edit Event Modal -->
<div id="editEventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Event</h2>
            <button class="close-btn" onclick="closeModal('editEventModal')">&times;</button>
        </div>

        <form id="editEventForm">
            <div class="form-group">
                <label class="form-label">Event Name</label>
                <input type="text" class="form-input" id="editEventName" value="Spring Championship 2026">
            </div>

            <div class="form-group">
                <label class="form-label">Event Type</label>
                <select class="form-select" id="editEventType">
                    <option value="match">Match Event</option>
                    <option value="tournament" selected>Tournament Event</option>
                    <option value="season">Season Tracking Event</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-input" id="editEventStart" value="2026-01-15">
            </div>

            <div class="form-group">
                <label class="form-label">End Date</label>
                <input type="date" class="form-input" id="editEventEnd" value="2026-02-05">
            </div>

            <div class="form-group">
                <label class="form-label">Location</label>
                <input type="text" class="form-input" id="editEventLocation" value="Central Sports Complex">
            </div>

            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select" id="editEventStatus">
                    <option value="draft">Draft</option>
                    <option value="live" selected>Live</option>
                    <option value="closed">Closed</option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn btn-danger" onclick="confirmDeleteEvent()">Delete Event</button>
                <button type="button" class="btn btn-secondary" style="flex: 1;"
                    onclick="closeModal('editEventModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Player Modal -->
<div id="playerModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="playerModalTitle">Add New Player</h2>
            <button class="close-btn" onclick="closeModal('playerModal')">&times;</button>
        </div>

        <form id="playerForm">
            <input type="hidden" id="playerId">

            <div class="form-group">
                <label class="form-label">Player Name *</label>
                <input type="text" class="form-input" id="playerName" placeholder="Enter player name" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-input" id="playerEmail" placeholder="player@example.com">
            </div>

            <div class="form-group">
                <label class="form-label">Assign to Team</label>
                <select class="form-select" id="playerTeam">
                    <option value="">-- No Team --</option>
                    <option value="T001">Team Alpha</option>
                    <option value="T002">Team Beta</option>
                    <option value="T003">Team Gamma</option>
                    <option value="T004">Team Delta</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Brain Points</label>
                    <input type="number" class="form-input" id="playerBrain" value="0" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Playground Points</label>
                    <input type="number" class="form-input" id="playerPlayground" value="0" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">E-Gaming Points</label>
                    <input type="number" class="form-input" id="playerEGaming" value="0" min="0">
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn btn-secondary" style="flex: 1;"
                    onclick="closeModal('playerModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Save Player</button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Team Modal -->
<div id="teamModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="teamModalTitle">Add New Team</h2>
            <button class="close-btn" onclick="closeModal('teamModal')">&times;</button>
        </div>

        <form id="teamForm">
            <input type="hidden" id="teamId">

            <div class="form-group">
                <label class="form-label">Team Name *</label>
                <input type="text" class="form-input" id="teamName" placeholder="Enter team name" required>
            </div>

            <div class="form-group">
                <label class="form-label">Team Members</label>
                <div id="teamMembersContainer" style="display: grid; gap: 0.5rem;">
                    <select class="form-select">
                        <option value="">-- Select Player --</option>
                        <option value="P001">Alex Johnson</option>
                        <option value="P002">Maria Garcia</option>
                        <option value="P003">James Chen</option>
                        <option value="P004">Sarah Williams</option>
                    </select>
                    <select class="form-select">
                        <option value="">-- Select Player --</option>
                        <option value="P001">Alex Johnson</option>
                        <option value="P002">Maria Garcia</option>
                        <option value="P003">James Chen</option>
                        <option value="P004">Sarah Williams</option>
                    </select>
                    <select class="form-select">
                        <option value="">-- Select Player --</option>
                        <option value="P001">Alex Johnson</option>
                        <option value="P002">Maria Garcia</option>
                        <option value="P003">James Chen</option>
                        <option value="P004">Sarah Williams</option>
                    </select>
                </div>
                <button type="button" class="btn btn-secondary" style="margin-top: 0.5rem; width: 100%;"
                    onclick="addTeamMemberSlot()">
                    <i data-lucide="plus"></i> Add Member Slot
                </button>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn btn-secondary" style="flex: 1;"
                    onclick="closeModal('teamModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Save Team</button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Match Modal -->
<div id="matchModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="matchModalTitle">Create New Match</h2>
            <button class="close-btn" onclick="closeModal('matchModal')">&times;</button>
        </div>

        <form id="matchForm">
            <input type="hidden" id="matchId">

            <div class="form-group">
                <label class="form-label">Match Name/Number *</label>
                <input type="text" class="form-input" id="matchName" placeholder="Match #1 - Semifinals"
                    required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Team A *</label>
                    <select class="form-select" id="matchTeamA" required>
                        <option value="">-- Select Team --</option>
                        <option value="T001">Team Alpha</option>
                        <option value="T002">Team Beta</option>
                        <option value="T003">Team Gamma</option>
                        <option value="T004">Team Delta</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Team B *</label>
                    <select class="form-select" id="matchTeamB" required>
                        <option value="">-- Select Team --</option>
                        <option value="T001">Team Alpha</option>
                        <option value="T002">Team Beta</option>
                        <option value="T003">Team Gamma</option>
                        <option value="T004">Team Delta</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Game Title</label>
                <select class="form-select" id="matchGame">
                    <option value="">-- Select Game --</option>
                    <option value="aquaball">AquaBall Clash</option>
                    <option value="beachball">Beach Balling</option>
                    <option value="league">League Rotation</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Match Format</label>
                <select class="form-select" id="matchFormat">
                    <option value="single">Single Round Winner</option>
                    <option value="bo3">Best of 3 (First to 2)</option>
                    <option value="bo5">Best of 5 (First to 3)</option>
                    <option value="custom">Custom Format</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-input" id="matchDate" value="2026-02-02">
                </div>
                <div class="form-group">
                    <label class="form-label">Time</label>
                    <input type="time" class="form-input" id="matchTime" value="14:00">
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn btn-secondary" style="flex: 1;"
                    onclick="closeModal('matchModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Create Match</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Score Modal -->
<div id="scoreModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add Score Entry</h2>
            <button class="close-btn" onclick="closeModal('scoreModal')">&times;</button>
        </div>

        <form id="scoreForm">
            <div class="form-group">
                <label class="form-label">Select Player *</label>
                <select class="form-select" id="scorePlayer" required>
                    <option value="">-- Select Player --</option>
                    <option value="P001">Alex Johnson</option>
                    <option value="P002">Maria Garcia</option>
                    <option value="P003">James Chen</option>
                    <option value="P004">Sarah Williams</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">CAM Pillar *</label>
                <select class="form-select" id="scorePillar" onchange="updateCategoryOptions()" required>
                    <option value="">-- Select Pillar --</option>
                    <option value="brain">Brain Games</option>
                    <option value="playground">Playground Games</option>
                    <option value="egaming">E-Gaming</option>
                    <option value="esports">Esports</option>
                </select>
            </div>

            <div class="form-group" id="categoryGroup" style="display: none;">
                <label class="form-label" id="categoryLabel">Category/Game *</label>
                <select class="form-select" id="scoreCategory">
                    <!-- Options populated dynamically -->
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Points *</label>
                <input type="number" class="form-input" id="scorePoints" placeholder="Enter points" min="0"
                    required>
            </div>

            <div class="form-group">
                <label class="form-label">Date</label>
                <input type="date" class="form-input" id="scoreDate" value="2026-02-02">
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea class="form-textarea" id="scoreNotes" placeholder="Optional notes about this score"></textarea>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn btn-secondary" style="flex: 1;"
                    onclick="closeModal('scoreModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex: 1;">Add Score</button>
            </div>
        </form>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h2 class="modal-title">Confirm Action</h2>
            <button class="close-btn" onclick="closeModal('confirmModal')">&times;</button>
        </div>

        <p id="confirmMessage" style="margin-bottom: 2rem; color: var(--text-dim); font-size: 1.1rem;"></p>

        <div style="display: flex; gap: 1rem;">
            <button class="btn btn-secondary" style="flex: 1;" onclick="closeModal('confirmModal')">Cancel</button>
            <button class="btn btn-danger" style="flex: 1;" onclick="executeConfirmedAction()">Confirm</button>
        </div>
    </div>
</div>
`;

// Inject modals into the body
document.body.insertAdjacentHTML("beforeend", modalsHTML);
}
