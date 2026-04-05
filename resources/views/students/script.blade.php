<script>

    let userPermissions = [];
    
    document.addEventListener('DOMContentLoaded', () => {
    
        const eventFilter = document.getElementById('eventFilter');
        const orgFilter = document.getElementById('organizationFilter');
        const tbody = document.getElementById('playersTableBody');
        const actionHeader = document.getElementById('actionHeader');
    
        // ✅ Delete Player
        window.deletePlayer = async function(playerId) {
    
            if (!confirm("Are you sure you want to delete this player?")) return;
    
            try {
                const res = await fetch(`/player/${playerId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
    
                const data = await res.json();
    
                if (data.success) {
                    alert(data.message);
                    loadLeaderboard();
                } else {
                    alert(data.message || "Delete failed");
                }
    
            } catch (err) {
                console.error(err);
                alert("Error deleting player");
            }
        }
        function renderRank(rank) {
    if (!rank) return '';

    // For top 3, use image icons from public/assets
    if (rank >= 1 && rank <= 3) {
        return `<img src="/assets/position-${rank}-icon.png" 
                     alt="Rank ${rank}" 
                     style="width:34px;height:34px" />`;
    } else {
        // For ranks 4+, fallback to number badge
        return `<span class="rank-medal rank-n">${rank}</span>`;
    }
}
        // ✅ Load Data
        window.loadLeaderboard = async function() {
    
            const eventId = eventFilter.value;
            const orgId = orgFilter.value;
    
            if (!eventId) return;
    
            let url = `/event/${eventId}/students-leaderboard`;
            if (orgId) url += `?organization_id=${orgId}`;
    
            tbody.innerHTML = `<tr><td colspan="6" class="text-center">Loading...</td></tr>`;
    
            try {
                const res = await fetch(url);
                const data = await res.json();
    
                userPermissions = data.permissions || [];
    
                // ✅ show/hide action column
                if (userPermissions.includes('delete_player')) {
                    actionHeader.style.display = '';
                } else {
                    actionHeader.style.display = 'none';
                }
    
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
        <td style="color:#000; font-weight:700;font-size:22px;text-align:center">${renderRank(row.rank)}</td>
    
        ${userPermissions.includes('delete_player') ? `
        <td>
            <button class="btn btn-icon btn-delete" onclick="deletePlayer(${row.id})">
                <i data-lucide="trash"></i>
            </button>
        </td>` : ''}
    </tr>
    `;
                });
    
                tbody.innerHTML = rows;
    
                if (window.lucide) lucide.createIcons();
    
            } catch (err) {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="6" class="text-center">Error loading data</td></tr>`;
            }
        }
    
        // ✅ Load organizations
        eventFilter.addEventListener('change', async () => {
    
            const eventId = eventFilter.value;
            if (!eventId) return;
    
            orgFilter.innerHTML = '<option value="">-- Select Organization --</option>';
            orgFilter.value = "";
    
            try {
                const res = await fetch(`/event/${eventId}/organizations`);
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
    
        // ✅ Auto load
        if (eventFilter.value) {
            loadLeaderboard();
        }
    
    });
    
    // optional safety
    if (typeof handleKeyboardShortcuts === "undefined") {
        function handleKeyboardShortcuts() {}
    }

document.getElementById('playerSearch').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#playersTableBody tr');

    rows.forEach(row => {
        const cell = row.cells[0];

        if (!cell) return;

        const playerName = cell.textContent.toLowerCase();

        if (playerName.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
    </script>