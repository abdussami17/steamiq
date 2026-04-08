@extends('layouts.app')
@section('title', 'Scoreboard - SteamIQ')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800;900&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root {
    --pg-bg:           #fff;
    --pg-panel:        #0f230f;
    --pg-surface:      #142814;
    --pg-surface2:     #1a3319;
    --pg-border:       #2a4a28;
    --pg-border-lit:   #3a6035;
    --pg-green:        #4caf50;
    --pg-lime:         #8bc34a;
    --pg-yellow:       #f5d400;
    --pg-yellow-dim:   #c9a800;
    --pg-white:        #ffffff;
    --pg-gray:         #9ab598;
    --pg-gray-dim:     #5a7558;
    --pg-row-odd:      #0d1c0d;
    --pg-row-even:     #112211;
    --pg-row-hover:    #1c381a;
    --pg-hdr-bg:       #0d200d;
    --pg-rank-col:     #0f1e0f;
    --pg-shadow:       0 8px 40px rgba(0,0,0,.75);
    --pg-font:         'Barlow Condensed', sans-serif;
    --dark : #000;
}
*,*::before,*::after{box-sizing:border-box;}
body{
    background:var(--pg-bg);
}
.pg-page{background:var(--pg-bg);min-height:100vh;padding:28px 0 64px;font-family:var(--pg-font);}
.pg-wrap{max-width:1320px;margin:0 auto;padding:0 20px;}

/* ── Top bar ── */
.pg-topbar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:26px;}
.pg-heading{display:flex;align-items:center;gap:14px;}
.pg-icon svg{height: 20px;width: 20px;color: var(--pg-bg);}
.pg-icon{width:48px;height:48px;background:var(--dark);border:1.5px solid var(--pg-border-lit);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:22px;}
.pg-title-main{font-size:2rem;font-weight:900;line-height:1;color:var(--dark);text-transform:uppercase;letter-spacing:3px;}
.pg-title-sub{font-size:.72rem;font-weight:700;letter-spacing:2px;color:var(--pg-gray-dim);text-transform:uppercase;margin-top:3px;}
.pg-selector{display:flex;align-items:center;gap:10px;}
.pg-sel-lbl{font-size:.72rem;font-weight:700;letter-spacing:2px;color:var(--pg-gray-dim);text-transform:uppercase;}
.pg-select{
    appearance:none;
    background:var(--pg-surface) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%238bc34a'/%3E%3C/svg%3E") no-repeat right 12px center;
    border:1.5px solid var(--pg-border-lit);border-radius:6px;
    color:var(--pg-white);font-family:var(--pg-font);font-size:1rem;font-weight:700;letter-spacing:.5px;
    padding:9px 36px 9px 14px;cursor:pointer;min-width:260px;transition:border-color .2s;
}
.pg-select:focus{outline:none;border-color:var(--pg-lime);box-shadow:0 0 0 3px rgba(139,195,74,.15);}
.pg-select option{background:#1a3319;color:#fff;}

/* ── Loading ── */
#pgLoading{display:none;text-align:center;padding:60px 20px;color:var(--pg-gray);font-size:1rem;letter-spacing:1px;}
.pg-spinner{width:40px;height:40px;border:3px solid var(--pg-border);border-top-color:var(--pg-lime);border-radius:50%;animation:pgspin .7s linear infinite;margin:0 auto 14px;}
@keyframes pgspin{to{transform:rotate(360deg);}}

/* ── Gap between boards ── */
.pg-gap{height:36px;}

/* ════ BOARD ════ */
.pg-board{background:var(--pg-panel);border:1.5px solid var(--pg-border);border-radius:10px;overflow:hidden;box-shadow:var(--pg-shadow);position:relative;}
/* corner accents */
.pg-board::before,.pg-board::after{content:'';position:absolute;width:20px;height:20px;border-color:var(--pg-lime);border-style:solid;z-index:2;pointer-events:none;}
.pg-board::before{top:-1px;left:-1px;border-width:2px 0 0 2px;border-radius:10px 0 0 0;}
.pg-board::after{top:-1px;right:-1px;border-width:2px 2px 0 0;border-radius:0 10px 0 0;}

.pg-titlebar{background:var(--pg-hdr-bg);border-bottom:2px solid var(--pg-border);padding:13px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;}
.pg-board-name{font-size:1.6rem;font-weight:900;letter-spacing:3px;color:#fff;text-transform:uppercase;}
.pg-board-badge{background:var(--pg-surface2);border:1.5px solid var(--pg-border-lit);border-radius:5px;padding:4px 14px;font-size:.82rem;font-weight:700;letter-spacing:2px;color:var(--pg-lime);white-space:nowrap;}
.pg-count{background:var(--pg-surface);border-bottom:1px solid var(--pg-border);padding:6px 20px;font-size:.72rem;font-weight:700;letter-spacing:2px;color:var(--pg-gray-dim);text-transform:uppercase;}

.pg-scroll{overflow-x:auto;}

/* ════ TABLE ════ */
.pg-table{width:100%;border-collapse:collapse;font-family:var(--pg-font);font-size:.9rem;}

/* Column headers */
.pg-table thead tr{background:var(--pg-hdr-bg);border-bottom:2px solid var(--pg-border-lit);}
.pg-table thead th{
    padding:10px 14px;font-size:.7rem;font-weight:700;letter-spacing:1.5px;
    text-transform:uppercase;color:var(--pg-lime);text-align:center;
    border-right:1px solid var(--pg-border);white-space:nowrap;
}
.pg-table thead th.th-l{text-align:left;}
.pg-table thead th.th-rank{background:var(--pg-rank-col);color:var(--pg-yellow);border-left:2px solid var(--pg-border-lit);min-width:76px;width:76px;}

/* Group row */
.pg-grp td{
    background:var(--pg-surface2);padding:7px 20px;
    font-size:.7rem;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;
    color:var(--pg-lime);border-top:1.5px solid var(--pg-border-lit);border-bottom:1px solid var(--pg-border);
}

/* Data rows */
.pg-table tbody tr.pg-dr:nth-child(odd)  {background:var(--pg-row-odd);}
.pg-table tbody tr.pg-dr:nth-child(even) {background:var(--pg-row-even);}
.pg-table tbody tr.pg-dr:hover           {background:var(--pg-row-hover);}
.pg-table tbody td{
    padding:10px 14px;color:var(--pg-white);
    border-right:1px solid rgba(42,74,40,.45);
    border-bottom:1px solid rgba(42,74,40,.3);
    text-align:center;vertical-align:middle;white-space:nowrap;
}
.td-no {color:var(--pg-gray);font-weight:700;font-size:.85rem;min-width:68px;width:68px;}
.td-name{text-align:left;font-weight:700;font-size:1rem;letter-spacing:.3px;min-width:160px;}
.td-mem {text-align:left;font-size:.8rem;color:var(--pg-gray);min-width:110px;white-space:normal;}
.td-div {font-size:.75rem;font-weight:700;letter-spacing:1px;color:var(--pg-lime);}
.td-pts {font-weight:700;font-size:.92rem;color:var(--pg-lime);min-width:90px;}
.td-pts0{color:var(--pg-gray-dim)!important;font-weight:400;}
.td-flg {font-weight:700;color:var(--pg-yellow-dim);min-width:68px;}
.td-org {text-align:left;font-size:.8rem;font-weight:600;color:var(--pg-gray);min-width:110px;white-space:normal;}
.td-rank{background:var(--pg-rank-col)!important;border-left:2px solid var(--pg-border-lit)!important;padding:8px 10px!important;width:76px;min-width:76px;}

/* Rank pill */
.rk-pill{
    display:inline-flex;align-items:center;justify-content:center;
    background:var(--pg-surface2);border:1.5px solid var(--pg-border-lit);
    color:var(--pg-white);font-family:var(--pg-font);font-weight:800;font-size:.88rem;
    min-width:42px;height:28px;border-radius:5px;letter-spacing:.5px;padding:0 6px;
}
.rk-pill.r1{background:#6b4a00;border-color:#f5d400;color:#fde68a;}
.rk-pill.r2{background:#2e3d47;border-color:#90a4ae;color:#e0f2fe;}
.rk-pill.r3{background:#3e2516;border-color:#a1887f;color:#ffe0b2;}

.pg-empty{text-align:center;padding:48px;color:var(--pg-gray-dim);font-size:.95rem;letter-spacing:1px;}

@media(max-width:700px){
    .pg-title-main{font-size:1.3rem;}
    .pg-topbar{flex-direction:column;align-items:flex-start;}
    .pg-select{min-width:100%;}
    .pg-table{font-size:.78rem;}
    .pg-table thead th,.pg-table tbody td{padding:6px 8px;}
}
</style>
@endpush

@section('content')
<div class="pg-page">
<div class="pg-wrap">

    <div class="pg-topbar">
        <div class="pg-heading">
            <div class="pg-icon">
                <i data-lucide="trophy"></i>
            </div>
            <div>
                <div class="pg-title-main">Scoreboard</div>
                <div class="pg-title-sub">Overall Ranking</div>
            </div>
        </div>
        <div class="pg-selector">
            <span class="pg-sel-lbl">Event</span>
            <select id="pgSel" class="pg-select">
                @forelse($events as $ev)
                    <option value="{{ $ev->id }}" {{ $ev->id == $selectedEventId ? 'selected' : '' }}>
                        {{ $ev->name }}
                    </option>
                @empty
                    <option disabled>No events found</option>
                @endforelse
            </select>
        </div>
    </div>

    <div id="pgLoading"><div class="pg-spinner"></div>Loading scoreboard…</div>

    <div id="pgOut">
        @include('scoreboard._boards', [
            'selectedEvent' => $selectedEvent,
            'primaryData'   => $primaryData,
            'juniorData'    => $juniorData,
            'activities'    => $activities,
        ])
    </div>

</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var sel     = document.getElementById('pgSel');
    var out     = document.getElementById('pgOut');
    var loading = document.getElementById('pgLoading');

    sel.addEventListener('change', function () {
        loading.style.display = 'block';
        out.style.display = 'none';
        fetch('{{ route("scoreboard.data") }}?event_id=' + this.value, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            loading.style.display = 'none';
            out.style.display = '';
            out.innerHTML = renderBoards(data);
        })
        .catch(function(){
            loading.style.display = 'none';
            out.style.display = '';
            out.innerHTML = '<div class="pg-empty">Failed to load. Please try again.</div>';
        });
    });

    function renderBoards(data) {
        var ev = data.event, acts = data.activities, pri = data.primary, jun = data.junior;
        var h = '';
        if (pri && pri.length) h += renderBoard(ev.name, 'Primary', pri, acts);
        if (pri && pri.length && jun && jun.length) h += '<div class="pg-gap"></div>';
        if (jun && jun.length) h += renderBoard(ev.name, 'Junior', jun, acts);
        if ((!pri || !pri.length) && (!jun || !jun.length))
            h = '<div class="pg-empty">No data found for this event.</div>';
        return h;
    }

    function renderBoard(evName, division, rows, acts) {
        var divUp  = division.toUpperCase();
        var ageStr = division === 'Junior'  ? '11–14 YRS'
                   : division === 'Primary' ? '7–10 YRS' : '';
        var totalCols = 5 + acts.length + 2 + 1 + 1; // no+name+mem+div + acts + flags+org + rank

        /* group rows */
        var groups = {}, groupOrder = [];
        rows.forEach(function(r) {
            if (!groups[r.group_id]) { groups[r.group_id] = { name: r.group_name, rows: [] }; groupOrder.push(r.group_id); }
            groups[r.group_id].rows.push(r);
        });

        var actTH = acts.length
            ? acts.map(function(a){ return '<th>' + esc(trunc(a.name,13)) + '</th>'; }).join('')
            : '<th>YOUR POINTS</th>';

        var h = '<div class="pg-board">'
            + '<div class="pg-titlebar">'
            + '<div class="pg-board-name">' + esc(evName) + ' &mdash; ' + divUp + '</div>'
            + '<div class="pg-board-badge">' + esc(ageStr) + '</div>'
            + '</div>'
            + '<div class="pg-count">' + rows.length + ' TEAM' + (rows.length !== 1 ? 'S' : '') + '</div>'
            + '<div class="pg-scroll"><table class="pg-table"><thead><tr>'
            + '<th>Team No.</th>'
            + '<th class="th-l">Team Name</th>'
            + '<th class="th-l">Members</th>'
            + '<th>Division</th>'
            + actTH
            + '<th>Flags</th>'
            + '<th class="th-l">ORGANIZATION</th>'
            + '<th class="th-rank">Rank</th>'
            + '</tr></thead><tbody>';

        groupOrder.forEach(function(gid) {
            var g = groups[gid];
            h += '<tr class="pg-grp"><td colspan="' + totalCols + '">' + esc(g.name) + '</td></tr>';
            g.rows.forEach(function(row) {
                var rk = row.rank;
                var rkCls = rk === 1 ? 'r1' : rk === 2 ? 'r2' : rk === 3 ? 'r3' : '';
                var actTD = acts.length
                    ? acts.map(function(a){
                        var pts = (row.activity_scores && row.activity_scores[a.id]) || 0;
                        return '<td class="td-pts' + (pts > 0 ? '' : ' td-pts0') + '">' + (pts > 0 ? fmt(pts) : '&mdash;') + '</td>';
                      }).join('')
                    : '<td class="td-pts">' + fmt(row.total_points) + '</td>';

                h += '<tr class="pg-dr">'
                    + '<td class="td-no">' + row.team_no + '</td>'
                    + '<td class="td-name">' + esc(row.team_name) + '</td>'
                    + '<td class="td-mem">'  + esc(row.members || '&mdash;') + '</td>'
                    + '<td class="td-div">'  + esc((row.division||'').toUpperCase()) + '</td>'
                    + actTD
                    + '<td class="td-flg">'  + (row.flag_totals || 0) + '</td>'
                    + '<td class="td-org">'  + esc(row.org_name) + '</td>'
                    + '<td class="td-rank"><span class="rk-pill ' + rkCls + '">' + rk + '</span></td>'
                    + '</tr>';
            });
        });

        h += '</tbody></table></div></div>';
        return h;
    }

    function esc(s) {
        if (!s) return '';
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function fmt(n){ return Number(n).toLocaleString(); }
    function trunc(s,n){ return s && s.length > n ? s.slice(0,n)+'…' : (s||''); }
});
</script>
@endpush