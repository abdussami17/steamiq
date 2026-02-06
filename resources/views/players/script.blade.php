
<script>
    const tableBody = document.getElementById('playersTableBody');
    const eventFilter = document.getElementById('eventFilter');
    
    eventFilter.addEventListener('change', loadLeaderboard);
    
    function loadLeaderboard() {
        const eventId = eventFilter.value;
        if (!eventId) return;
    
        fetch(`/event/${eventId}/leaderboard`)
            .then(res => res.json())
            .then(players => {
                tableBody.innerHTML = '';
                let rank = 1;
                players.forEach(player => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><input type="text" value="P${player.id.toString().padStart(3,'0')}" readonly class="form-control"></td>
                        <td><input type="text" value="${player.name}" readonly class="form-control"></td>
                        <td><input type="text" value="${player.team ?? '-'}" readonly class="form-control"></td>
                        <td><input type="number" value="${player.brain_points}" readonly class="form-control"></td>
                        <td><input type="number" value="${player.playground_points}" readonly class="form-control"></td>
                        <td><input type="number" value="${player.egaming_points}" readonly class="form-control"></td>
                        <td style="color: var(--primary); font-weight: 700;">${player.total}</td>
                        <td style="font-weight: 700;">${rank}</td>
                        <td>
                            <div style="display: flex; gap: 0.25rem;">
                                <button class="btn btn-icon btn-edit" onclick="openPlayerModal('edit', '${player.id}')" title="Edit">
                                    <i data-lucide="edit-2"></i>
                                </button>
                                <button class="btn btn-icon btn-delete" onclick="confirmDelete('player', '${player.id}', '${player.name}')" title="Delete">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(tr);
                    rank++;
                });
            })
            .catch(err => console.error('Error loading leaderboard:', err));
    }
    
    function refreshLeaderboard() {
        loadLeaderboard();
    }
    </script>
 
    