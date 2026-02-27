<script>
    document.addEventListener('DOMContentLoaded', () => {
        const eventSelect = document.getElementById('eventSelect');
        if (eventSelect) {
            eventSelect.addEventListener('change', fetchScores);
            
            // Auto-load if there's a selected event (e.g., from URL parameter)
            if (eventSelect.value) {
                fetchScores();
            }
        }
    });
    
    async function fetchScores() {
        const eventSelect = document.getElementById('eventSelect');
        const eventId = eventSelect.value;
        const tbody = document.getElementById('scoreBody');
        const thead = document.getElementById('scoreHead');
    
        if (!eventId) {
            tbody.innerHTML = `<tr><td colspan="9">Please select an event</td></tr>`;
            return;
        }
    
        tbody.innerHTML = `<tr><td colspan="9">Loading...</td></tr>`;
    
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            
            if (!token) {
                throw new Error('CSRF token not found');
            }
    
            const res = await fetch(`/scores/fetch`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ event_id: eventId })
            });
    
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
    
            const data = await res.json();
            
            // Validate response structure
            if (!data.table || !data.categories) {
                throw new Error('Invalid response structure');
            }
            
            const tableData = data.table;
            const categories = data.categories;
    
            // Build dynamic table head based on categories
            let headHtml = '<tr>';
            headHtml += '<th>Type</th>';
            headHtml += '<th>Team</th>';
            headHtml += '<th>Name</th>';
            
            // Add category columns
            categories.forEach(cat => {
                headHtml += `<th>${cat.name}</th>`;
            });
            
            headHtml += '<th>Total</th>';
            headHtml += '</tr>';
            
            thead.innerHTML = headHtml;
    
            if (!tableData || tableData.length === 0) {
                tbody.innerHTML = `<tr><td colspan="${categories.length + 4}">No scores available for this event</td></tr>`;
                return;
            }
    
            // Build table body
            let bodyHtml = '';
            
            tableData.forEach(row => {
                // Calculate total score
                let total = 0;
                let scoresHtml = '';
                
                categories.forEach(cat => {
                    const points = row.scores && row.scores[cat.id] ? row.scores[cat.id] : 0;
                    total += points;
                    scoresHtml += `<td>${points}</td>`;
                });
    
                // Determine display values based on row type
                const typeDisplay = row.type === 'team' ? 'Team' : 'Student';
                const nameDisplay = row.type === 'team' ? row.name : row.name;
                const teamDisplay = row.team_name || '-';
                
                // Add row with appropriate styling
                const rowClass = row.type === 'team' ? 'team-row fw-bold' : 'student-row';
                
                bodyHtml += `
                    <tr class="${rowClass}">
                        <td>${typeDisplay}</td>
                        <td>${teamDisplay}</td>
                        <td>${nameDisplay}</td>
                        ${scoresHtml}
                        <td><strong>${total}</strong></td>
                    </tr>
                `;
            });
    
            tbody.innerHTML = bodyHtml;
            
            // Re-initialize Lucide icons if you're using them
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
    
        } catch (error) {
            console.error('Error fetching scores:', error);
            tbody.innerHTML = `<tr><td colspan="9">Error loading scores: ${error.message}</td></tr>`;
        }
    }
    
    // Optional: Add auto-refresh functionality
    function refreshScores() {
        fetchScores();
    }
    
    // Auto-refresh every 30 seconds (optional)
    // setInterval(refreshScores, 30000);
</script>

