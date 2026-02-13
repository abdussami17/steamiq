<script>
    document.addEventListener('DOMContentLoaded', () => {

const selectEvent = document.getElementById('selectEvent');
const leaderboardBody = document.getElementById('leaderboardBody');

// Fetch events for dropdown
fetch('/leaderboard-events')
    .then(res => res.json())
    .then(events => {
        if(!events.length) return;
        events.forEach(ev => {
            const option = document.createElement('option');
            option.value = ev.id;
            option.textContent = ev.name;
            selectEvent.appendChild(option);
        });

        // Select latest event by default
        selectEvent.selectedIndex = 1; 
        fetchLeaderboard(selectEvent.value);
    })
    .catch(err => console.error(err));

// Fetch leaderboard when event changes
selectEvent.addEventListener('change', () => {
    fetchLeaderboard(selectEvent.value);
});

// Fetch leaderboard function
async function fetchLeaderboard(eventId){
    if(!leaderboardBody) return;
    leaderboardBody.innerHTML = `<div class="leaderboard-item"><div>Loading...</div></div>`;

    try {
        const res = await fetch(`/leaderboard-data?event_id=${eventId}`, {
            headers:{ 'Accept':'application/json' }
        });
        if(!res.ok) throw new Error();
        const data = await res.json();

        if(!data.length){
            leaderboardBody.innerHTML = `<div class="leaderboard-item"><div>N/A</div></div>`;
            return;
        }

        let html = `<div class="leaderboard-item header">
            <div>Rank</div>
            <div>Avatar</div>
            <div>Team Name</div>
            <div class="text-center">Brain</div>
            <div class="text-center">Play</div>
            <div class="text-center">E-Game</div>
            <div class="text-center">Esports</div>
            <div class="text-center">Total</div>
        </div>`;

        data.forEach(team => {
            const rank = Number(team.rank);
            let rankClass = '';
            if(rank === 1) rankClass = 'rank-1';
            else if(rank === 2) rankClass = 'rank-2';
            else if(rank === 3) rankClass = 'rank-3';
            const img = team.profile
    ? `/storage/${team.profile}`
    : `/assets/avatar-default.png`;


            html += `<div class="leaderboard-item">
                <div class="leaderboard-rank ${rankClass}">${team.rank ?? 'N/A'}</div>
                <div class="leaderboard-img"><img src="${img}"
         width="50"
         height="50"
         style="object-fit:cover"
         class="rounded-circle"
         onerror="this.src='/assets/avatar-default.png'"></div>
                <div class="leaderboard-name">${team.team_name ?? 'N/A'}</div>
                <div class="leaderboard-score">${team.brain ?? 0}</div>
                <div class="leaderboard-score">${team.play ?? 0}</div>
                <div class="leaderboard-score">${team.egame ?? 0}</div>
                <div class="leaderboard-score">${team.esports ?? 0}</div>
                <div class="leaderboard-score leaderboard-total">${team.total ?? 0}</div>
            </div>`;
        });

        leaderboardBody.innerHTML = html;
        if(window.lucide) lucide.createIcons();

    } catch(err){
        console.error(err);
        leaderboardBody.innerHTML = `<div class="leaderboard-item"><div>N/A</div></div>`;
    }
}

});

</script>


<script>
document.getElementById('exportLeaderboard').addEventListener('click', function(){
    const select = document.querySelector('#selectEvent'); // event dropdown
    const eventId = select.value;

    if(!eventId || eventId === '-- Select Event --'){
        alert('Please select an event first!');
        return;
    }

    window.location.href = `/leaderboard-export?event_id=${eventId}`;
});

    </script>