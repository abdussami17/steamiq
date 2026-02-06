<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@29.3.3/styles/ag-grid.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@29.3.3/styles/ag-theme-alpine.css">
<script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@29.3.3/dist/ag-grid-enterprise.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const eventFilter = document.getElementById('eventFilter');

    const editIcon = `
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 20h9"/>
        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
    </svg>`;

    const deleteIcon = `
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="3 6 5 6 21 6"/>
        <path d="M19 6l-1 14H6L5 6"/>
        <path d="M10 11v6M14 11v6"/>
        <path d="M9 6V4h6v2"/>
    </svg>`;

    const columnDefs = [
        { headerName:"Player ID", field:"id", filter:'agNumberColumnFilter', valueFormatter:p=>'P'+String(p.value).padStart(3,'0') },
        { headerName:"Name", field:"name", filter:'agSetColumnFilter', floatingFilter:true },
        { headerName:"Team", field:"team", filter:'agSetColumnFilter', floatingFilter:true },
        { headerName:"Brain Points", field:"brain_points", filter:'agNumberColumnFilter', floatingFilter:true },
        { headerName:"Playground Points", field:"playground_points", filter:'agNumberColumnFilter', floatingFilter:true },
        { headerName:"E-Gaming Points", field:"egaming_points", filter:'agNumberColumnFilter', floatingFilter:true },
        { headerName:"Total", field:"total", floatingFilter:true, cellStyle:{fontWeight:600,color:'var(--primary)'} },
        { headerName:"Rank", field:"rank", floatingFilter:true, cellStyle:{fontWeight:700} },
        {
            headerName:"Actions",
            field:"actions",
            sortable:false,
            filter:false,
            cellRenderer: params => `
                <div style="display:flex;gap:6px">
                    <button class="btn btn-icon btn-edit">${editIcon}</button>
                    <button class="btn btn-icon btn-delete">${deleteIcon}</button>
                </div>
            `
        }
    ];

    const gridOptions = {
        columnDefs,
        rowData: [],
        defaultColDef: {
            sortable:true,
            filter:true,
            resizable:true,
            floatingFilter:true
        },
        pagination:true,
        paginationPageSize:10,
        animateRows:true,
        enableRangeSelection:true,
        sideBar:{ toolPanels:['columns','filters'] }
    };

    const gridDiv = document.getElementById('playersGrid');
    new agGrid.Grid(gridDiv, gridOptions);

    function loadLeaderboard() {
        const eventId = eventFilter.value;
        if (!eventId) return;

        fetch(`/event/${eventId}/leaderboard`)
            .then(r => r.json())
            .then(players => {
                players.forEach((p,i)=>p.rank=i+1);
                gridOptions.api.setRowData(players);
            });
    }

    eventFilter.addEventListener('change', loadLeaderboard);

    window.refreshLeaderboard = loadLeaderboard;

    window.exportGridToExcel = function () {
        const exportCols = gridOptions.columnApi
            .getAllDisplayedColumns()
            .filter(col => col.getColDef().headerName !== "Actions")
            .map(col => col.getColId());

        gridOptions.api.exportDataAsExcel({
            columnKeys: exportCols
        });
    };
    window.startImport = function(){

const file = document.getElementById('importFile').files[0];
const eventId = document.getElementById('importEvent').value;

if(!file || !eventId){
    toastr.error('Select event and file');
    return;
}

const loading = document.getElementById('importLoading');
const result = document.getElementById('importResult');

loading.classList.remove('d-none');
result.classList.add('d-none');

const formData = new FormData();
formData.append('file', file);
formData.append('event_id', eventId);

fetch("{{ route('players.import') }}", {
    method: "POST",
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: formData
})
.then(r => r.json())
.then(data => {

    loading.classList.add('d-none');

    result.classList.remove('d-none');
    result.innerHTML =
        `Total rows: ${data.total}<br>
         Inserted: ${data.inserted}<br>
         Skipped duplicates: ${data.duplicates}<br>
         Errors: ${data.errors}`;

    refreshLeaderboard();
});
};


});
</script>

