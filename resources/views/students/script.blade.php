<script>
    let userPermissions = [];
    
    document.addEventListener('DOMContentLoaded', () => {
    
        const eventFilter = document.getElementById('eventFilter');
        const orgFilter = document.getElementById('organizationFilter');
        const tbody = document.getElementById('playersTableBody');
        const actionHeader = document.getElementById('actionHeader');
    
        window.deletePlayer = async function(playerId) {
    
            if (!confirm("Are you sure you want to delete this player?")) return;
    
            try {
                const res = await fetch(`/player-destroy/${playerId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
    
                let data;
    
                try {
                    data = await res.json();
                } catch (e) {
                    const text = await res.text();
                    console.error("Non JSON response:", text);
                    alert("Server returned invalid response (likely 404 or HTML)");
                    return;
                }
    
                if (!res.ok) {
                    alert(data.message || "Delete failed");
                    return;
                }
    
                if (data.success) {
                    toastr.success(data.message);
                    loadLeaderboard();
                } else {
                    alert(data.message || "Delete failed");
                }
    
            } catch (err) {
                console.error(err);
                alert("Error deleting player");
            }
        };
        window.editPlayer = async function(playerId) {
    try {
        const res = await fetch(`/players/${playerId}/edit`, {
            headers: { 'Accept': 'application/json' }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
            toastr.error(data.message || 'Failed to load player');
            return;
        }

        document.getElementById('editPlayerId').value = data.data.id;
        document.getElementById('editPlayerName').value = data.data.name;

        const teamSelect = document.getElementById('editPlayerTeam');
        teamSelect.innerHTML = '';

        data.teams.forEach(team => {
            const option = document.createElement('option');
            option.value = team.id;
            option.textContent = team.name;
            if (team.id == data.data.team_id) option.selected = true;
            teamSelect.appendChild(option);
        });

        new bootstrap.Modal(document.getElementById('editPlayerModal')).show();

    } catch (err) {
        console.error(err);
        toastr.error('Error loading player');
    }
};

window.updatePlayer = async function() {

    const id = document.getElementById('editPlayerId').value;
    const name = document.getElementById('editPlayerName').value.trim();
    const team_id = document.getElementById('editPlayerTeam').value;

    if (!name) {
        toastr.error('Player name is required');
        return;
    }

    if (!team_id) {
        toastr.error('Team is required');
        return;
    }

    try {
        const res = await fetch(`/players/${id}/update`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ name, team_id })
        });

        let data;

        try {
            data = await res.json();
        } catch (e) {
            const text = await res.text();
            console.error("Non JSON:", text);
            toastr.error('Server error');
            return;
        }

        if (!res.ok) {
            if (data.errors) {
                Object.values(data.errors).flat().forEach(e => toastr.error(e));
            } else {
                toastr.error(data.message || 'Update failed');
            }
            return;
        }

        toastr.success(data.message);

        bootstrap.Modal.getInstance(document.getElementById('editPlayerModal')).hide();

        loadLeaderboard();

    } catch (err) {
        console.error(err);
        toastr.error('Update failed');
    }
};
    
        function renderRank(rank) {
            if (!rank) return '';
            if (rank >= 1 && rank <= 3) {
                return `<img src="/assets/position-${rank}-icon.png" style="width:34px;height:34px" />`;
            }
            return `<span style="font-size:22px;font-weight:800">${rank}</span>`;
        }
    
        window.loadLeaderboard = async function() {
    
            const eventId = eventFilter.value;
            const orgId = orgFilter.value;
    
            if (!eventId) return;
    
            let url = `/event/${eventId}/students-leaderboard`;
            if (orgId) url += `?organization_id=${orgId}`;
    
            tbody.innerHTML = `<tr><td colspan="6" class="text-center">Loading...</td></tr>`;
    
            try {
                const res = await fetch(url, {
                    headers: { 'Accept': 'application/json' }
                });
    
                const data = await res.json();
    
                userPermissions = data.permissions || [];
    
                actionHeader.style.display = userPermissions.includes('delete_player') ? '' : 'none';
    
                if (!data.rows || data.rows.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="text-center">No data</td></tr>`;
                    return;
                }
    
                let rows = '';
    
                data.rows.forEach(row => {
    rows += `
    <tr>
        <td>${row.student || 'N/A'}</td>
        <td>${row.team || 'N/A'}</td>
        <td>${row.activity || 'N/A'}</td>
        <td style="font-weight:700">${row.total || 0}</td>
        <td style="font-weight:700;font-size:22px;text-align:center">${renderRank(row.rank)}</td>

        <td>
            <div class="d-flex gap-2 align-items-center">
            ${userPermissions.includes('edit_player') ? `
                <button class="btn btn-icon btn-edit" onclick="editPlayer(${row.id})">
                    <i data-lucide="edit"></i>
                </button>
            ` : ''}

            ${userPermissions.includes('delete_player') ? `
                <button class="btn btn-icon btn-delete" onclick="deletePlayer(${row.id})">
                    <i data-lucide="trash"></i>
                </button>
            ` : ''}
            </div>
        </td>
    </tr>`;
});
    
                tbody.innerHTML = rows;
    
                if (window.lucide) lucide.createIcons();
    
            } catch (err) {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="6" class="text-center">Error loading data</td></tr>`;
            }
        };
    
        eventFilter.addEventListener('change', async () => {
    
            const eventId = eventFilter.value;
            if (!eventId) return;
    
            orgFilter.innerHTML = '<option value="">-- Select Organization --</option>';
            orgFilter.value = "";
    
            try {
                const res = await fetch(`/event/${eventId}/organizations`, {
                    headers: { 'Accept': 'application/json' }
                });
    
                const data = await res.json();
    
                data.forEach(org => {
                    const option = document.createElement('option');
                    option.value = org.id;
                    option.textContent = org.name;
                    orgFilter.appendChild(option);
                });
    
                tbody.innerHTML = `<tr><td colspan="6" class="text-center">Select organization</td></tr>`;
    
            } catch (err) {
                console.error(err);
            }
        });
    
        orgFilter.addEventListener('change', loadLeaderboard);
    
        if (eventFilter.value) {
            loadLeaderboard();
        }
    
    });
    </script>