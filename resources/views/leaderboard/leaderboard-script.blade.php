<script>
    document.addEventListener('DOMContentLoaded', () => {
    
        const selectEvent = document.getElementById('selectEvent');
        const leaderboardTable = document.querySelector('.data-table');
        const leaderboardBody = document.getElementById('leaderboardBody');
    
        // Load events
        fetch('/leaderboard-events')
            .then(res => res.json())
            .then(events => {
                events.forEach(ev => {
                    const opt = document.createElement('option');
                    opt.value = ev.id;
                    opt.textContent = ev.name;
                    selectEvent.appendChild(opt);
                });
                if(events.length) fetchLeaderboard(events[0].id);
            })
            .catch(console.error);
    
        selectEvent.addEventListener('change', () => {
            fetchLeaderboard(selectEvent.value);
        });
    
        async function fetchLeaderboard(eventId){
            if(!leaderboardBody || !leaderboardTable) return;
    
            leaderboardBody.innerHTML = `<tr><td colspan="100%">Loading...</td></tr>`;
    
            try {
                const res = await fetch(`/leaderboard-data?event_id=${eventId}`, { headers:{'Accept':'application/json'} });
                if(!res.ok) throw new Error('Failed to fetch leaderboard');
                const data = await res.json();
    
                if(!data.rows.length){
                    leaderboardBody.innerHTML = `<tr><td colspan="100%">No data available</td></tr>`;
                    return;
                }
    
                // Build table header
                let theadHtml = `<tr style="white-space:nowrap">
                    <th>Rank</th>
                    <th>Event</th>
                    <th>Organization</th>
                    <th>Group</th>
                    <th>Subgroup</th>
                    <th>Team</th>
                    <th>Student</th>
                    ${data.categories.map(c => `<th>${c}</th>`).join('')}
                    <th>Total Points</th>
                </tr>`;
                leaderboardTable.querySelector('thead').innerHTML = theadHtml;
    
                // Build table body
                let tbodyHtml = '';
                data.rows.forEach(row => {
                    tbodyHtml += `<tr style="white-space:nowrap">
                        <td>${row.rank ?? '-'}</td>
                        <td>${row.event ?? '-'}</td>
                        <td>${row.organization ?? '-'}</td>
                        <td>${row.group ?? '-'}</td>
                        <td>${row.subgroup ?? '-'}</td>
                        <td>${row.team_name ?? '-'}</td>
                        <td>${row.student_name ?? '-'}</td>
                        ${data.categories.map(c => `<td>${row.scores[c] ?? 0}</td>`).join('')}
                        <td>${row.total_points ?? 0}</td>
                    </tr>`;
                });
    
                leaderboardBody.innerHTML = tbodyHtml;
    
            } catch(e){
                console.error(e);
                leaderboardBody.innerHTML = `<tr><td colspan="100%">Error loading leaderboard</td></tr>`;
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