    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const activitiesTableBody = document.getElementById('activities-data-table-body');
            const searchInput = document.getElementById('activities-filter-search-input');
            const dateRangeSelect = document.getElementById('activities-filter-date-range');
        
            let allActivities = [];
        
            // Fetch activities from route
            async function fetchActivities() {
                try {
                    const res = await fetch("<?php echo e(route('settings.activities.fetch')); ?>");
                    const data = await res.json();
        
                    // Add computed display_name
                    allActivities = data.map(act => ({
                        ...act,
                        display_name: getActivityDisplayName(act)
                    }));
        
                    renderActivities();
                } catch (error) {
                    console.error(error);
                    activitiesTableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Failed to load activities</td></tr>`;
                }
            }
            function formatText(str) {
    if (!str) return '';
    return str
        .replace(/_/g, ' ')
        .toLowerCase()
        .replace(/\b\w/g, c => c.toUpperCase());
}
        
function getActivityDisplayName(activity) {
    let name = '';
    let description = '';

    if (activity.activity_or_mission === 'mission') {
        name = activity.badge_name;
    } else {
        switch(activity.activity_type) {
            case 'brain':
                name = activity.brain_type;
                description = activity.brain_description;
                break;

            case 'egaming':
                name = activity.egaming_type;
                description = activity.egaming_description;
                break;

            case 'esports':
                name = activity.esports_type;
                description = activity.esports_description;
                break;

            case 'playground':
                name = 'Playground';
                description = activity.playground_description;
                break;

            default:
                name = activity.name;
        }
    }

    // format both
    name = formatText(name);
    description = formatText(description);

    // return with dash only if description exists
    return description ? `${name} - ${description}` : name || 'N/A';
}
        
            // Render table based on search & date filters
            function renderActivities() {
                const searchTerm = searchInput.value.toLowerCase();
                const dateFilter = dateRangeSelect.value;
        
                const filtered = allActivities.filter(act => {
                    // Search filter
                    const matchesSearch = (
                        (act.display_name || '').toLowerCase().includes(searchTerm) ||
                        (act.badge_name || '').toLowerCase().includes(searchTerm) ||
                        (act.brain_type || '').toLowerCase().includes(searchTerm) ||
                        (act.egaming_type || '').toLowerCase().includes(searchTerm) ||
                        (act.esports_type || '').toLowerCase().includes(searchTerm)
                    );
        
                    // Date filter
                    let matchesDate = true;
                    const createdAt = new Date(act.created_at);
                    const now = new Date();
        
                    switch(dateFilter) {
                        case '24h': matchesDate = (now - createdAt) <= (24*60*60*1000); break;
                        case '3d': matchesDate = (now - createdAt) <= (3*24*60*60*1000); break;
                        case '30d': matchesDate = (now - createdAt) <= (30*24*60*60*1000); break;
                        case '6m': matchesDate = (now - createdAt) <= (183*24*60*60*1000); break;
                    }
        
                    return matchesSearch && matchesDate;
                });
        
                // Render rows
                if (filtered.length === 0) {
                    activitiesTableBody.innerHTML = `<tr><td colspan="8" class="text-center">No activities found</td></tr>`;
                    return;
                }
        
                activitiesTableBody.innerHTML = filtered.map(act => {
                    const createdDate = new Date(act.created_at).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' });
                
                    return `
                        <tr>
                            <td>${act.id}</td>
                            <td>${act.event ? act.event.name : 'N/A'}</td>
                            <td>${act.activity_or_mission ? act.activity_or_mission.charAt(0).toUpperCase() + act.activity_or_mission.slice(1) : 'N/A'}</td>
                            <td>${act.activity_type ? act.activity_type.charAt(0).toUpperCase() + act.activity_type.slice(1) : 'N/A'}</td>
                            <td>${act.display_name}</td>
                            <td>${act.max_score ?? 'N/A'}</td>
                            <td>
    ${act.point_structure
        .replace(/_/g, ' ') 
        .replace(/\b\w/g, c => c.toUpperCase()) 
    }
    </td>
                            <td>${createdDate}</td>
                    
                        </tr>
                    `;
                }).join('');
            }
        
            // Event listeners
            searchInput.addEventListener('input', renderActivities);
            dateRangeSelect.addEventListener('change', renderActivities);
        
            // Initial fetch
            fetchActivities();
        });
        </script><?php /**PATH C:\Users\PC\Downloads\steam-two\resources\views/settings/scripts/activity-script.blade.php ENDPATH**/ ?>