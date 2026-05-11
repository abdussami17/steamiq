    <script>
        document.addEventListener('DOMContentLoaded', fetchTeams);

        function safe(val) {
            if (val === null || val === undefined || val === '') return 'N/A';
            return val;
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
            return `<span style="color:#000;font-size:22px;font-weight:700">${rank}</span>`;
        }
    }

        // Fetch and populate teams table
        async function fetchTeams() {
            const tbody = document.getElementById('teamsTableBody');
            if (!tbody) return;

            tbody.innerHTML = `<tr><td colspan="11">Loading...</td></tr>`;

            try {
                const res = await fetch('/teams-data', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) throw new Error('Server error');

                const data = await res.json();
    const teams = data.teams || [];
    const userPermissions = data.permissions || [];

                if (!Array.isArray(teams) || teams.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="11">N/A</td></tr>`;
                    return;
                }

                let rows = '';
                teams.forEach(team => {
                    const id = safe(team.id);
                    const name = safe(team.name);
                    const division = safe(team.division);
                    const pod = safe(team.pod);
                    const members = safe(team.members_count ?? 0);
                    const points = Number(safe(team.total_points ?? 0)).toLocaleString();
                    const rank = safe(team.rank ?? 0);

                    const img = team.profile ?
                        `/storage/${team.profile}` :
                        `/assets/avatar-default.png`;


                        const subgroup = safe(team.subgroup_name ?? 'N/A');
                        const group = safe(team.group_name ?? 'N/A');


    rows += `
    <tr>
        <td>
            <input type="checkbox" class="team-checkbox" value="${id}">
        </td>
        <td>
            <img src="${img}"
                width="40"
                height="40"
                class="rounded-circle"
                style="object-fit:cover"
                onerror="this.src='/assets/avatar-default.png'">
        </td>

        <td>${id}</td>
        <td>${name}</td>
        <td>${division}</td>
        <td>${group}</td>
        <td>${subgroup}</td>
        <td class="text-uppercase">${pod}</td>
        <td>${members}</td>
        <td style="color: #000; font-weight:700;">${points}</td>
        <td style="color:#000; font-weight:700;font-size:22px;text-align:center">${renderRank(rank)}</td>
        <td>
            <div style="display:flex;gap:0.25rem;">
                <button class="btn btn-icon btn-view" onclick="viewTeamDetails('${id}')">
                    <i data-lucide="eye"></i>
                </button>
                ${userPermissions.includes('edit_team')  ? `<button class="btn btn-icon btn-edit" onclick="openEditTeamModal('${id}')">
                <i data-lucide="edit-2"></i>
            </button>` : ''}
            ${userPermissions.includes('delete_team') ? `<button class="btn btn-icon btn-delete" onclick="confirmDelete('team','${id}','${name}')">
                <i data-lucide="trash-2"></i>
            </button>` : ''}
            </div>
        </td>
    </tr>`;
                });

                tbody.innerHTML = rows;
                initSelectAllCheckbox();
                if (window.lucide) lucide.createIcons();
            } catch (err) {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="11">N/A</td></tr>`;
            }
        }


        function initSelectAllCheckbox() {
        const selectAll = document.getElementById('selectAllTeams');

        if (!selectAll) return;

        selectAll.addEventListener('change', function () {
            document.querySelectorAll('.team-checkbox')
                .forEach(cb => cb.checked = this.checked);
        });
    }
    async function deleteSelectedTeams() {

    const ids = [...document.querySelectorAll('.team-checkbox:checked')]
        .map(cb => cb.value);

    if (!ids.length) {
        alert('Select at least one team');
        return;
    }

    if (!confirm('Delete selected teams?')) return;

    try {

        const res = await fetch('/teams/bulk-delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ids })
        });

        const data = await res.json();

        if (data.success) {
            fetchTeams();
        } else {
            alert('Delete failed');
        }

    } catch (e) {
        console.error(e);
    }
    }
        // Delete Team
        function confirmDelete(type, id, name) {
            if (confirm(`Are you sure you want to delete ${name}?`)) {
                fetch(`/delete/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(res => res.json()).then(data => {
                    if (data.success) fetchTeams();
                });
            }
        }

        // View Team
        function viewTeamDetails(id) {
            const modalBody = document.getElementById('viewTeamBody');
            modalBody.innerHTML = 'Loading...';

            fetch(`/view/${id}`)
                .then(res => res.json())
                .then(data => {

    let html = `
    <h5 class="mb-4" style="color:var(--text);font-weight:700">
        ${data.team.name}
    </h5>

    <table class="table table-bordered table-dark">
        <thead>
            <tr>
                <th>Player</th>
                <th>Email</th>
                <th>Total Points</th>
            
            </tr>
        </thead>
        <tbody>
    `;
    data.members.forEach(member => {

    const total = Number(member.total_points ?? 0);

    const scores = member.scores
        .map(s => `${s.challenge}: ${s.points}`)
        .join('<br>');

    html += `
        <tr>
            <td>${member.name || 'N/A'}</td>
            <td>${member.email || 'N/A'}</td>
            <td style="font-weight:700;color:#00e676">${total}</td>
        
        </tr>
    `;
    });

    html += `</tbody></table>`;

    modalBody.innerHTML = html;

    new bootstrap.Modal(
        document.getElementById('viewTeamModal')
    ).show();
    })
                .catch(err => {
                    console.error(err);
                    modalBody.innerHTML = 'Failed to load team details.';
                });
        }


        document.addEventListener('DOMContentLoaded', () => {
            // Simple search by Team Name
    document.getElementById('teamSearch').addEventListener('input', function(){
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#teamsTableBody tr');
        rows.forEach(row => {
            const cell = row.cells[3]; // Team Name column
            if (!cell) { row.style.display = 'none'; return; }
            const name = cell.querySelector('input') ? cell.querySelector('input').value.toLowerCase() : cell.textContent.toLowerCase();
            row.style.display = name.includes(filter) ? '' : 'none';
        });
    });
        })
    </script>


<script>

    document.getElementById('exportTeamsBtn').addEventListener('click', async function () {
    
        try {
    
            const response = await fetch("{{ route('teams.export') }}", {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
    
            if (!response.ok) {
                throw new Error('Export failed');
            }
    
            // Convert response to blob
            const blob = await response.blob();
    
            // Create download URL
            const url = window.URL.createObjectURL(blob);
    
            // Create temp link
            const a = document.createElement('a');
            a.href = url;
    
            // Dynamic filename
            a.download = 'teams-export.xlsx';
    
            document.body.appendChild(a);
            a.click();
    
            // Cleanup
            a.remove();
            window.URL.revokeObjectURL(url);
    
        } catch (error) {
    
            console.error(error);
            alert('Export failed');
    
        }
    
    });
    
    </script>