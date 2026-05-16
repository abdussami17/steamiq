<script>
    document.addEventListener('DOMContentLoaded', function() {
    
        const dropdownTeams = document.getElementById('selectEventForTopTeamsThree');
        const playersEventName = document.getElementById('playersEventName');
    
        const tbodyTeams = document.getElementById('tbodyTopTeams');
        const tbodyPlayers = document.getElementById('tbodyTopPlayers');
    
        // =============================
        // Initial Load
        // =============================
        if (dropdownTeams.value) {
            const eventId = dropdownTeams.value;
    
            fetchAndRenderTopTeams(eventId);
            fetchAndRenderTopPlayers(eventId);
    
         
            if (playersEventName) {
                const selectedText = dropdownTeams.options[dropdownTeams.selectedIndex].text;
                playersEventName.innerText = `(${selectedText})`;
            }
        }
    
        // =============================
        // Event Listener
        // =============================
        dropdownTeams.addEventListener('change', () => {
            const eventId = dropdownTeams.value;
    
            fetchAndRenderTopTeams(eventId);
            fetchAndRenderTopPlayers(eventId);
    
            if (playersEventName) {
                const selectedText = dropdownTeams.options[dropdownTeams.selectedIndex].text;
                playersEventName.innerText = `(${selectedText})`;
            }
        });

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
// =============================
// Fetch Top Teams
// =============================
function fetchAndRenderTopTeams(eventId) {
    fetch(`<?php echo e(route('leaderboard.fetchTopThreeTeams')); ?>?event_id=${eventId}`)
        .then(res => res.json())
        .then(data => {
            tbodyTeams.innerHTML = '';

            data.forEach(team => {
                const avatarUrl = team.avatar 
                    ? `/storage/${team.avatar}` 
                    : '/assets/avatar-default.png';

                tbodyTeams.innerHTML += `
                    <tr>
                        <td>${team.id}</td>
                        <td>
                            <img src="${avatarUrl}" style="width:40px;height:40px;border-radius:50%" class="border border-1" />
                        </td>
                        <td class="leaderboard-name">${team.name}</td>
                        <td>${team.division}</td>
                        <td>${team.pod}</td>
                        <td class="leaderboard-total">${team.total_points}</td>
                        <td>${renderRank(team.rank)}</td>
                    </tr>
                `;
            });
        });
}

// =============================
// Fetch Top Players
// =============================
function fetchAndRenderTopPlayers(eventId) {
    fetch(`<?php echo e(route('leaderboard.fetchTopThreePlayers')); ?>?event_id=${eventId}`)
        .then(res => res.json())
        .then(data => {
            tbodyPlayers.innerHTML = '';

            data.forEach(player => {
                const avatarUrl = player.avatar 
                    ? `/storage/${player.avatar}` 
                    : '/assets/avatar-default.png';

                tbodyPlayers.innerHTML += `
                    <tr>
                        <td>${player.id}</td>
                        <td>
                            <img src="${avatarUrl}" style="width:40px;height:40px;border-radius:50%" class="border border-1" />
                        </td>
                        <td class="leaderboard-name">${player.name}</td>
                        <td>${player.team}</td>
                        <td class="leaderboard-total">${player.total_points}</td>
                        <td>${renderRank(player.rank)}</td>
                    </tr>
                `;
            });
        });
}
    
    });
    </script><?php /**PATH C:\Users\PC\Desktop\steam-two\resources\views/leaderboard/public-leaderboard-script.blade.php ENDPATH**/ ?>