<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:900px;">
        <div class="modal-content">

            <div class="modal-header">
                <h2 class="modal-title" id="eventDetailsModalLabel">Event Details</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="eventDetailsContent">
                Loading...
            </div>

        </div>
    </div>
</div>

<script>
    function openEventModal(eventId) {
    
        const modalLabel = document.getElementById('eventDetailsModalLabel');
        const contentDiv = document.getElementById('eventDetailsContent');
    
        contentDiv.innerHTML = "Loading...";
    
        fetch(`/events/${eventId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(event => {
    
            /* ---------------- EVENT NAME ---------------- */
            modalLabel.textContent = event.name ?? "N/A";
    
            /* ---------------- EVENT TYPE ---------------- */
            let eventType = "STEAM " + (event.type ?? "");
            if (event.type === "esports") eventType = "STEAM ESports";
            if (event.type === "xr") eventType = "STEAM XR Sports";
    
            /* ---------------- COLLECT ALL TEAMS ---------------- */
            let allTeams = [];
    
            (event.organizations || []).forEach(org => {
                (org.groups || []).forEach(group => {
                    // Teams directly under group
                    if (group.teams) allTeams = allTeams.concat(group.teams);
                    // Teams under subgroups
                    (group.subgroups || []).forEach(sub => {
                        if (sub.teams) allTeams = allTeams.concat(sub.teams);
                    });
                });
            });
    
            // Remove duplicate teams by ID
            allTeams = allTeams.filter((team, index, self) => 
                index === self.findIndex(t => t.id === team.id)
            );
    
            /* ---------------- COUNT TEAMS & STUDENTS ---------------- */
            const totalTeams = allTeams.length;
            const totalStudents = allTeams.flatMap(t => t.students || []).length;
    
            /* ---------------- TEAMS HTML ---------------- */
            let teamsHtml = "";
            if (allTeams.length) {
                allTeams.forEach(team => {
                    let students = (team.students || []).map(s => s.name).join(", ") || "No Players";
                    teamsHtml += `
    <div style="padding:12px; border-bottom:1px solid #2c2c2c;">
        <div style="font-weight:600">${team.name}</div>
        <div style="font-size:13px;color:#aaa;margin-top:4px">${students}</div>
    </div>
                    `;
                });
            } else {
                teamsHtml = `<div>No Teams Found</div>`;
            }
    
            /* ---------------- ACTIVITIES HTML ---------------- */
            let activitiesHtml = "<div>No Activities</div>";
            if (event.activities && event.activities.length) {
                activitiesHtml = event.activities.map(a => `
    <div style="padding:8px 0;border-bottom:1px solid #2c2c2c">
    ${a.name}
    </div>
                `).join("");
            }
    
            /* ---------------- FINAL HTML ---------------- */
            contentDiv.innerHTML = `
    <div style="margin-bottom:15px">
        <span class="badge badge-${event.status ?? "draft"}">${event.status ?? "N/A"}</span>
        <span class="badge" style="background:rgba(0,212,255,0.2); color:var(--accent); border:1px solid var(--accent); margin-left:8px;">
            ${eventType}
        </span>
    </div>
    
    <div class="stats-grid" style="margin-bottom:20px">
        <div class="stat">
            <div class="stat-label">Total Teams</div>
            <div class="stat-value" style="font-size:20px">${totalTeams}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Total Players</div>
            <div class="stat-value" style="font-size:20px">${totalStudents}</div>
        </div>
        <div class="stat">
            <div class="stat-label">Location</div>
            <div class="stat-value" style="font-size:16px">${event.location ?? "N/A"}</div>
        </div>
    </div>
    
    <div style="background:var(--dark);padding:20px;border-radius:10px;margin-bottom:20px;color:#fff">
        <h4 style="margin-bottom:15px" class="fw-bold">Teams</h4>
        <div style="max-height:250px;overflow:auto">
            ${teamsHtml}
        </div>
    </div>
    
    <div style="background:var(--dark);padding:20px;border-radius:10px;color:#fff">
        <h4 style="margin-bottom:15px" class="fw-bold">Activities</h4>
        <div>${activitiesHtml}</div>
    </div>
            `;
    
            /* ---------------- OPEN MODAL ---------------- */
            new bootstrap.Modal(document.getElementById("eventDetailsModal")).show();
    
        })
        .catch(err => {
            console.error(err);
            contentDiv.innerHTML = "<p>Failed to load event data</p>";
        });
    }
    </script>