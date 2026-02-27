<script>
    document.addEventListener('DOMContentLoaded', fetchTeams);
    
    function safe(val) {
        if (val === null || val === undefined || val === '') return 'N/A';
        return val;
    }
    
    function getRankColor(rank) {
        if (rank === 1) return '#FFD700';
        if (rank === 2) return '#C0C0C0';
        if (rank === 3) return '#CD7F32';
        return '#fff';
    }
    
    // Fetch and populate teams table
    async function fetchTeams() {
        const tbody = document.getElementById('teamsTableBody');
        if (!tbody) return;
    
        tbody.innerHTML = `<tr><td colspan="6">Loading...</td></tr>`;
    
        try {
            const res = await fetch('/teams-data', {
                headers: {'Accept': 'application/json','X-Requested-With': 'XMLHttpRequest'}
            });
            if (!res.ok) throw new Error('Server error');
    
            const teams = await res.json();
    
            if (!Array.isArray(teams) || teams.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6">N/A</td></tr>`;
                return;
            }
    
            let rows = '';
            teams.forEach(team => {
                const id = safe(team.id);
                const name = safe(team.team_name);
                const members = safe(team.members_count ?? 0);
                const points = safe(team.total_points ?? 0);
                const rank = safe(team.rank ?? 0);
                const rankColor = getRankColor(Number(rank));
                const img = team.profile
    ? `/storage/${team.profile}`
    : `/assets/avatar-default.png`;

    
                rows += `
                <tr>
                    <td>
                  
    <img src="${img}"
         width="40"
         height="40"
         class="rounded-circle"
         style="object-fit:cover"
         onerror="this.src='/assets/avatar-default.png'">
</td>

                    <td><input type="text" value="${id}" readonly></td>
                    <td><input type="text" value="${name}"></td>
                    <td>${members}</td>
                    <td style="color: var(--primary); font-weight:700;">${points}</td>
                    <td style="color:${rankColor}; font-weight:700;font-size:22px">${rank}</td>
                    <td>
                        <div style="display:flex;gap:0.25rem;">
                            <button class="btn btn-icon btn-view" onclick="viewTeamDetails('${id}')">
                                <i data-lucide="eye"></i>
                            </button>
                            <button class="btn btn-icon btn-edit" onclick="openEditTeamModal('${id}')">
                                <i data-lucide="edit-2"></i>
                            </button>
                            <button class="btn btn-icon btn-delete" onclick="confirmDelete('team','${id}','${name}')">
                                <i data-lucide="trash-2"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
            });
    
            tbody.innerHTML = rows;
            if (window.lucide) lucide.createIcons();
        } catch (err) {
            console.error(err);
            tbody.innerHTML = `<tr><td colspan="6">N/A</td></tr>`;
        }
    }
    

    
    // Delete Team
    function confirmDelete(type,id,name){
        if(confirm(`Are you sure you want to delete ${name}?`)){
            fetch(`/delete/${id}`,{
                method:'DELETE',
                headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
            }).then(res=>res.json()).then(data=>{
                if(data.success) fetchTeams();
            });
        }
    }
    
    // View Team
    function viewTeamDetails(id){
        const modalBody = document.getElementById('viewTeamBody');
        modalBody.innerHTML = 'Loading...';
    
        fetch(`/view/${id}`)
            .then(res => res.json())
            .then(data => {
                let html = `<h5 class="mb-4" style="color:var(--text);font-weight:700">${data.team.team_name} (Event: ${data.team.event.name})</h5>
                            <table class="table table-bordered table-dark">
                            <thead>
                                <tr><th>Player</th><th>Email</th><th>Scores</th></tr>
                            </thead>
                            <tbody>`;
                data.members.forEach(member=>{
                    const scores = member.scores.map(s=>`${s.challenge} (${s.pillar}): ${s.points}`).join('<br>');
                    html += `<tr>
                                <td>${member.name}</td>
                                <td>${member.email}</td>
                                <td>${scores || 'N/A'}</td>
                             </tr>`;
                });
                html += `</tbody></table>`;
                modalBody.innerHTML = html;
                new bootstrap.Modal(document.getElementById('viewTeamModal')).show();
            })
            .catch(err => {
                console.error(err);
                modalBody.innerHTML = 'Failed to load team details.';
            });
    }
    </script>
    