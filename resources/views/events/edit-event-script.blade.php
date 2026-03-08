<script>
    const editEventModal = new bootstrap.Modal(document.getElementById('editEventModal'));
    const editEventForm = document.getElementById('editEventForm');
    const editType = document.getElementById("editType");
    const editEsportsSection = document.getElementById("editEsportsSection");
    const editXrSection = document.getElementById("editXrSection");
    const editBrainToggle = document.getElementById("editBrainToggle");
    const editBrainFields = document.getElementById("editBrainFields");
    const editActivitiesContainer = document.getElementById("editActivitiesContainer");
    
    function openEditEventModal(eventId) {
        fetch(`/events/${eventId}/edit`)
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                const data = res.data;
                document.getElementById('editEventId').value = data.id;
                document.getElementById('editName').value = data.name;
                editType.value = data.type;
                document.getElementById('editLocation').value = data.location;
                document.getElementById('editStartDate').value = data.start_date;
                document.getElementById('editEndDate').value = data.end_date;
                document.getElementById('editStatus').value = data.status;
    
                toggleSections(editType.value);
    
                if(data.tournament_setting) {
                    editBrainToggle.checked = data.tournament_setting.brain_enabled == 1;
                    editBrainFields.style.display = editBrainToggle.checked ? "flex" : "none";
                    document.getElementById('editBrainType').value = data.tournament_setting.brain_type ?? '';
                    document.getElementById('editBrainScore').value = data.tournament_setting.brain_score ?? '';
                    document.getElementById('editGame').value = data.tournament_setting.game ?? '';
                    document.getElementById('editPlayersPerTeam').value = data.tournament_setting.players_per_team ?? '';
                    document.getElementById('editMatchRule').value = data.tournament_setting.match_rule ?? '';
                    document.getElementById('editPointsWin').value = data.tournament_setting.points_win ?? '';
                    document.getElementById('editPointsDraw').value = data.tournament_setting.points_draw ?? '';
                    document.getElementById('editTournamentType').value = data.tournament_setting.tournament_type ?? '';
                    document.getElementById('editNumberOfTeams').value = data.tournament_setting.number_of_teams ?? '';
                }
    
                editActivitiesContainer.innerHTML = '';
                if(data.activities && data.activities.length > 0) {
                    data.activities.forEach((act, i) => {
                        const div = document.createElement('div');
                        div.className = 'row g-3 mt-2 rounded p-0';
                        div.innerHTML = `
                            <div class="col-md-4">
                                <input type="text" name="activities[${i}][type]" value="${act.name}" class="form-input" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="activities[${i}][score]" value="${act.max_score}" class="form-input" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary" onclick="this.closest('.row').remove(); reindexEditActivities();">Remove</button>
                            </div>
                        `;
                        editActivitiesContainer.appendChild(div);
                    });
                }
    
                editEventModal.show();
            } else {
                alert(res.message || "Failed to fetch event details.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Error fetching event details.");
        });
    }
    
    editType.addEventListener("change", () => toggleSections(editType.value));
    editBrainToggle.addEventListener("change", () => {
        editBrainFields.style.display = editBrainToggle.checked ? "flex" : "none";
    });
    
    function toggleSections(type) {
        editEsportsSection.style.display = type === "esports" ? "block" : "none";
        editXrSection.style.display = type === "xr" ? "block" : "none";
    }
    
    editEventForm.addEventListener("submit", function(e) {
        e.preventDefault();
        const eventId = document.getElementById('editEventId').value;
        const formData = new FormData(editEventForm);
    
        fetch(`/events/${eventId}/update`, {
            method: 'POST',
            body: formData,
            headers: {'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value}
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                alert(res.message);
                location.reload();
            } else {
                alert(res.message || "Failed to update event.");
            }
        })
        .catch(err => console.error(err));
    });
    
    function addEditActivity() {
        const container = editActivitiesContainer;
        const index = container.children.length;
        const div = document.createElement("div");
        div.className = "row g-3 mt-2 rounded p-0";
        div.innerHTML = `
            <div class="col-md-4">
                <input type="text" placeholder="Activity Name" name="activities[${index}][type]" class="form-input" required>
            </div>
            <div class="col-md-3">
                <input type="number" placeholder="Max Score" name="activities[${index}][score]" class="form-input" required min="0">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary" onclick="this.closest('.row').remove(); reindexEditActivities();">Remove</button>
            </div>
        `;
        container.appendChild(div);
    }
    
    function reindexEditActivities() {
        Array.from(editActivitiesContainer.children).forEach((row, i) => {
            const type = row.querySelector('input[type="text"]');
            const score = row.querySelector('input[type="number"]');
            if(type) type.name = `activities[${i}][type]`;
            if(score) score.name = `activities[${i}][score]`;
        });
    }
    </script>