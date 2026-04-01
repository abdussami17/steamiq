<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@29.3.3/styles/ag-grid.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@29.3.3/styles/ag-theme-alpine.css">
<script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@29.3.3/dist/ag-grid-enterprise.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', () => {

    const gridDiv = document.getElementById('playersGrid');
    const eventFilter = document.getElementById('eventFilter');
    const orgFilter = document.getElementById('organizationFilter');

    let gridOptions;

    // Build the grid with fixed columns (no dynamic categories)
    function buildGrid() {
        const columnDefs = [
    { headerName:"Player", field:"student", flex:1, minWidth:150 },
    { headerName:"Team", field:"team", flex:1, minWidth:120 },
    { headerName:"Activity", field:"activity", flex:1, minWidth:150 },
    { headerName:"Total", field:"total", cellStyle:{fontWeight:700}, flex:1, minWidth:100 },
    { headerName:"Rank", field:"rank", cellStyle:{fontWeight:700}, flex:1, minWidth:80 }
];

        gridOptions = {
            columnDefs,
            rowData: [],
            defaultColDef:{
                sortable:true,
                filter:true,
                resizable:true
            }
        };

        new agGrid.Grid(gridDiv, gridOptions);
gridOptions.api.sizeColumnsToFit();
    }

    // Load leaderboard data
    async function loadLeaderboard() {
        const eventId = eventFilter.value;
        const orgId = orgFilter.value;
        if (!eventId) return;

        let url = `/event/${eventId}/students-leaderboard`;
        if (orgId) url += `?organization_id=${orgId}`;

        try {
            const res = await fetch(url);
            const data = await res.json();

            if (!gridOptions) buildGrid();

            gridOptions.api.setRowData(data.rows);
gridOptions.api.sizeColumnsToFit(); 

        } catch (err) {
            console.error("Error loading leaderboard:", err);
            if (!gridOptions) buildGrid();
            gridOptions.api.setRowData([]);
        }
    }

    // Load organizations when event changes
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

            if (gridOptions) gridOptions.api.setRowData([]);
        } catch (err) {
            console.error("Error loading organizations:", err);
        }
    });

    orgFilter.addEventListener('change', loadLeaderboard);

    // Initialize grid on page load
    buildGrid();
});
window.addEventListener('resize', () => {
    if(gridOptions && gridOptions.api) gridOptions.api.sizeColumnsToFit();
});
</script>
