<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@29.3.3/styles/ag-grid.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@29.3.3/styles/ag-theme-alpine.css">
<script src="https://cdn.jsdelivr.net/npm/ag-grid-enterprise@29.3.3/dist/ag-grid-enterprise.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

const gridDiv = document.getElementById('playersGrid');
const eventFilter = document.getElementById('eventFilter');
const orgFilter = document.getElementById('organizationFilter');

let gridOptions;

function buildGrid(categories){

    const editableCols = categories.map(cat => ({
        headerName: cat,
        field: cat,
        editable: true,
        cellEditor: 'agNumberCellEditor',
        valueParser: p => Number(p.newValue || 0)
    }));

    const columnDefs = [
        { headerName:"Player", field:"student" },
        { headerName:"Team", field:"team" },
        { headerName:"Activity", field:"activity" },
        ...editableCols,
        { headerName:"Total", field:"total", cellStyle:{fontWeight:700} },
        { headerName:"Rank", field:"rank", cellStyle:{fontWeight:700} }
    ];

    gridOptions = {
        columnDefs,
        rowData: [],
        defaultColDef:{
            sortable:true,
            filter:true,
            resizable:true
        },

        onCellValueChanged: async (params) => {

            const col = params.colDef.field;

            if(['total','rank','student','team','activity'].includes(col)) return;

            await fetch('/score/update-inline', {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    student_id: params.data.id,
                    category: col,
                    points: params.newValue
                })
            });

            recalc();
        }
    };

    new agGrid.Grid(gridDiv, gridOptions);
}

function recalc(){

    const rows = [];

    gridOptions.api.forEachNode(node => {

        const cats = Object.keys(node.data)
            .filter(k => !['id','student','team','activity','total','rank'].includes(k));

        let total = 0;

        cats.forEach(c => total += Number(node.data[c] || 0));

        node.data.total = total;
        rows.push(node.data);
    });

    rows.sort((a,b)=>b.total-a.total);

    rows.forEach((r,i)=> r.rank = i+1);

    gridOptions.api.setRowData(rows);
}

async function loadLeaderboard(){

    const eventId = eventFilter.value;
    const orgId = orgFilter.value;

    if(!eventId) return;

    let url = `/event/${eventId}/students-leaderboard`;

    if(orgId){
        url += `?organization_id=${orgId}`;
    }

    const res = await fetch(url);
    const data = await res.json();

    if(!gridOptions){
        buildGrid(data.categories);
    }

    gridOptions.api.setRowData(data.rows);
}

eventFilter.addEventListener('change', async () => {

const eventId = eventFilter.value;

if(!eventId) return;

orgFilter.innerHTML = '<option value="">-- Select Organization --</option>';
orgFilter.value = "";

const res = await fetch(`/event/${eventId}/organizations`);
const data = await res.json();

data.forEach(org => {
    const option = document.createElement('option');
    option.value = org.id;
    option.textContent = org.name;
    orgFilter.appendChild(option);
});

if(gridOptions){
    gridOptions.api.setRowData([]);
}

});
orgFilter.addEventListener('change', loadLeaderboard);

});
</script>