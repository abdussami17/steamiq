{{-- Event Details Modal --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

<div class="modal fade" id="eventDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:900px;">
    <div class="modal-content" style="background:#0d1117;border:1px solid #1e2639;border-radius:20px;overflow:hidden;box-shadow:0 40px 80px rgba(0,0,0,0.7),0 0 0 1px #252d42;">
      <div id="eventDetailsContent">
        <div style="padding:48px;text-align:center;color:#555f74;font-family:'Inter',sans-serif;">
          <div style="margin-bottom:12px;opacity:0.4;">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
          </div>
          Loading event...
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* ── RESET & VARS ── */
#eventDetailsModal * { box-sizing: border-box; }

:root {
  --evm-bg-base:     #0d1117;
  --evm-bg-surface:  #111520;
  --evm-bg-raised:   #161b26;
  --evm-bg-hover:    #1c2233;
  --evm-border:      #1e2639;
  --evm-border-lt:   #252d42;
  --evm-text-1:      #f0f4ff;
  --evm-text-2:      #8892a8;
  --evm-text-3:      #555f74;
  --evm-accent:      #4f8ef7;
  --evm-accent-dim:  rgba(79,142,247,0.12);
  --evm-green:       #22c55e;
  --evm-green-dim:   rgba(34,197,94,0.12);
  --evm-red:         #f05252;
  --evm-red-dim:     rgba(240,82,82,0.12);
  --evm-amber:       #f59e0b;
  --evm-amber-dim:   rgba(245,158,11,0.12);
  --evm-r-sm:        6px;
  --evm-r-md:        10px;
  --evm-r-lg:        16px;
  --evm-font-h:      'Poppins', sans-serif;
  --evm-font-b:      'Inter', sans-serif;
}

/* ── BANNER / HEADER ── */
.evm-banner {
  background: linear-gradient(160deg, #0c1428 0%, #0d1117 60%);
  border-bottom: 1px solid var(--evm-border);
  padding: 24px 28px 0;
  position: relative;
  overflow: hidden;
}
.evm-banner::before {
  content: '';
  position: absolute;
  top: -40px; right: -40px;
  width: 220px; height: 220px;
  background: radial-gradient(circle, rgba(79,142,247,0.07) 0%, transparent 70%);
  pointer-events: none;
}

.evm-banner-top {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 20px;
}

.evm-trophy {
  width: 52px; height: 52px;
  background: linear-gradient(135deg,#1a2540 0%,#0f1829 100%);
  border: 1px solid var(--evm-border-lt);
  border-radius: var(--evm-r-md);
  display: flex; align-items: center; justify-content: center;
  font-size: 24px;
  flex-shrink: 0;
  box-shadow: 0 4px 16px rgba(0,0,0,0.3);
}

.evm-title-block { flex: 1; min-width: 0; }

.evm-event-title {
  font-family: var(--evm-font-h);
  font-size: 17px;
  font-weight: 700;
  color: var(--evm-text-1);
  line-height: 1.35;
  margin: 0 0 6px;
  letter-spacing: -0.2px;
}

.evm-event-sub {
  display: flex;
  align-items: center;
  gap: 5px;
  font-family: var(--evm-font-b);
  font-size: 12.5px;
  color: var(--evm-text-2);
  margin-bottom: 10px;
}

/* ── BADGES ── */
.evm-badge-row { display: flex; gap: 6px; flex-wrap: wrap; }

.evm-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  font-family: var(--evm-font-h);
  letter-spacing: 0.2px;
  white-space: nowrap;
}

.evm-badge-live     { background: var(--evm-green-dim); color: var(--evm-green);  border: 1px solid rgba(34,197,94,0.25); }
.evm-badge-draft    { background: rgba(85,95,116,0.15); color: var(--evm-text-2); border: 1px solid rgba(85,95,116,0.25); }
.evm-badge-closed   { background: var(--evm-red-dim);   color: var(--evm-red);    border: 1px solid rgba(240,82,82,0.25); }
.evm-badge-upcoming { background: var(--evm-amber-dim); color: var(--evm-amber);  border: 1px solid rgba(245,158,11,0.25); }
.evm-badge-type     { background: var(--evm-accent-dim);color: var(--evm-accent); border: 1px solid rgba(79,142,247,0.25); }

.evm-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: var(--evm-green);
  display: inline-block;
  animation: evmPulse 2s infinite;
}
@keyframes evmPulse {
  0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.4); }
  50%      { box-shadow: 0 0 0 4px rgba(34,197,94,0); }
}

/* ── CLOSE BTN ── */
.evm-close-btn {
  background: transparent;
  border: 1px solid var(--evm-border-lt);
  color: var(--evm-text-3);
  width: 34px; height: 34px;
  border-radius: var(--evm-r-sm);
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  transition: all 0.15s;
}
.evm-close-btn:hover { background: var(--evm-bg-hover); color: var(--evm-text-1); border-color: var(--evm-border-lt); }

/* ── TABS ── */
.evm-tabs { display: flex; gap: 0; }

.evm-tab {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 12px 16px;
  font-size: 12.5px;
  font-weight: 500;
  font-family: var(--evm-font-h);
  color: var(--evm-text-3);
  cursor: pointer;
  border: none;
  border-bottom: 2px solid transparent;
  background: transparent;
  transition: all 0.15s;
  white-space: nowrap;
  letter-spacing: 0.1px;
}
.evm-tab:hover { color: var(--evm-text-2); }
.evm-tab.evm-tab-active { color: var(--evm-accent); border-bottom-color: var(--evm-accent); }

/* ── BODY ── */
.evm-body {
  padding: 24px 28px;
  max-height: 480px;
  overflow-y: auto;
  font-family: var(--evm-font-b);
}
.evm-body::-webkit-scrollbar { width: 4px; }
.evm-body::-webkit-scrollbar-track { background: transparent; }
.evm-body::-webkit-scrollbar-thumb { background: var(--evm-border-lt); border-radius: 4px; }

/* ── PANELS ── */
.evm-tab-panel { display: none; }
.evm-tab-panel.evm-panel-active { display: block; animation: evmFadeIn 0.2s ease; }
@keyframes evmFadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ── STATS ROW ── */
.evm-stats-row {
  display: grid;
  grid-template-columns: repeat(4,1fr);
  gap: 10px;
  margin-bottom: 20px;
}
.evm-stat-card {
  background: var(--evm-bg-surface);
  border: 1px solid var(--evm-border);
  border-radius: var(--evm-r-md);
  padding: 16px 12px;
  text-align: center;
  transition: border-color 0.15s;
}
.evm-stat-card:hover { border-color: var(--evm-border-lt); }
.evm-stat-icon {
  width: 32px; height: 32px;
  background: var(--evm-accent-dim);
  border-radius: var(--evm-r-sm);
  display: flex; align-items: center; justify-content: center;
  color: var(--evm-accent);
  margin: 0 auto 10px;
}
.evm-stat-num {
  font-family: var(--evm-font-h);
  font-size: 24px;
  font-weight: 700;
  color: var(--evm-text-1);
  line-height: 1;
  margin-bottom: 4px;
  letter-spacing: -0.5px;
}
.evm-stat-lbl {
  font-size: 10.5px;
  color: var(--evm-text-3);
  text-transform: uppercase;
  letter-spacing: 0.7px;
  font-weight: 500;
}

/* ── INFO GRID ── */
.evm-info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  margin-bottom: 20px;
}
.evm-info-box {
  background: var(--evm-bg-surface);
  border: 1px solid var(--evm-border);
  border-radius: var(--evm-r-md);
  padding: 14px 16px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  transition: border-color 0.15s;
}
.evm-info-box:hover { border-color: var(--evm-border-lt); }
.evm-info-box-icon {
  width: 30px; height: 30px;
  background: var(--evm-bg-hover);
  border-radius: var(--evm-r-sm);
  display: flex; align-items: center; justify-content: center;
  color: var(--evm-text-2);
  flex-shrink: 0;
  margin-top: 1px;
}
.evm-info-box-label {
  font-size: 10.5px;
  color: var(--evm-text-3);
  text-transform: uppercase;
  letter-spacing: 0.7px;
  font-weight: 600;
  margin-bottom: 4px;
}
.evm-info-box-value {
  font-size: 13.5px;
  color: var(--evm-text-1);
  font-weight: 500;
}

/* ── SECTION TITLE ── */
.evm-section-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 10.5px;
  font-weight: 700;
  font-family: var(--evm-font-h);
  color: var(--evm-text-3);
  text-transform: uppercase;
  letter-spacing: 1px;
  margin: 0 0 14px;
}
.evm-section-title::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--evm-border);
}

/* ── TEAM ITEMS ── */
.evm-team-item {
  background: var(--evm-bg-surface);
  border: 1px solid var(--evm-border);
  border-radius: var(--evm-r-md);
  padding: 14px 16px;
  margin-bottom: 8px;
  transition: border-color 0.15s;
}
.evm-team-item:hover { border-color: var(--evm-border-lt); }
.evm-team-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}
.evm-team-avatar {
  width: 32px; height: 32px;
  background: var(--evm-bg-hover);
  border: 1px solid var(--evm-border-lt);
  border-radius: var(--evm-r-sm);
  display: flex; align-items: center; justify-content: center;
  font-family: var(--evm-font-h);
  font-size: 12px;
  font-weight: 700;
  color: var(--evm-accent);
  flex-shrink: 0;
}
.evm-team-name {
  font-family: var(--evm-font-h);
  font-size: 13.5px;
  font-weight: 600;
  color: var(--evm-text-1);
}
.evm-team-count {
  margin-left: auto;
  font-size: 11px;
  color: var(--evm-text-3);
  display: flex;
  align-items: center;
  gap: 4px;
}

/* ── PLAYER CHIPS ── */
.evm-player-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.evm-player-chip {
  background: var(--evm-bg-hover);
  border: 1px solid var(--evm-border);
  color: var(--evm-text-2);
  font-size: 11.5px;
  padding: 4px 10px 4px 6px;
  border-radius: 20px;
  display: flex; align-items: center; gap: 6px;
  font-family: var(--evm-font-b);
  font-weight: 400;
  transition: border-color 0.15s;
}
.evm-player-chip:hover { border-color: var(--evm-border-lt); color: var(--evm-text-1); }
.evm-player-avatar {
  width: 18px; height: 18px;
  border-radius: 50%;
  background: var(--evm-accent-dim);
  display: flex; align-items: center; justify-content: center;
  font-size: 9px;
  font-weight: 700;
  color: var(--evm-accent);
  flex-shrink: 0;
}

/* ── ACTIVITIES ── */
.evm-activity-item {
  background: var(--evm-bg-surface);
  border: 1px solid var(--evm-border);
  border-radius: var(--evm-r-md);
  padding: 14px 16px;
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  gap: 14px;
  transition: border-color 0.15s;
}
.evm-activity-item:hover { border-color: var(--evm-border-lt); }
.evm-activity-icon {
  width: 38px; height: 38px;
  background: var(--evm-accent-dim);
  border-radius: var(--evm-r-sm);
  display: flex; align-items: center; justify-content: center;
  color: var(--evm-accent);
  flex-shrink: 0;
}
.evm-activity-name {
  font-family: var(--evm-font-h);
  font-size: 13.5px;
  font-weight: 600;
  color: var(--evm-text-1);
  margin-bottom: 3px;
}
.evm-activity-meta { font-size: 11.5px; color: var(--evm-text-3); }
.evm-activity-score {
  margin-left: auto;
  background: var(--evm-bg-hover);
  border: 1px solid var(--evm-border-lt);
  color: var(--evm-accent);
  font-size: 12px;
  font-weight: 700;
  font-family: var(--evm-font-h);
  padding: 4px 12px;
  border-radius: 20px;
  flex-shrink: 0;
  white-space: nowrap;
}

/* ── GROUPS ── */
.evm-group-block {
  background: var(--evm-bg-surface);
  border: 1px solid var(--evm-border);
  border-radius: var(--evm-r-md);
  overflow: hidden;
  margin-bottom: 10px;
}
.evm-group-header {
  background: var(--evm-bg-raised);
  padding: 12px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--evm-border);
}
.evm-group-name {
  font-family: var(--evm-font-h);
  font-size: 13px;
  font-weight: 700;
  color: var(--evm-accent);
  display: flex;
  align-items: center;
  gap: 7px;
}
.evm-group-count { font-size: 11px; color: var(--evm-text-3); }
.evm-subgroup { padding: 12px 16px; border-top: 1px solid var(--evm-border); }
.evm-subgroup-title {
  font-size: 11px;
  color: var(--evm-text-2);
  font-weight: 600;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* ── TOURNAMENT GRID ── */
.evm-tournament-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}
.evm-tour-item {
  background: var(--evm-bg-surface);
  border: 1px solid var(--evm-border);
  border-radius: var(--evm-r-md);
  padding: 14px 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  transition: border-color 0.15s;
}
.evm-tour-item:hover { border-color: var(--evm-border-lt); }
.evm-tour-icon {
  width: 32px; height: 32px;
  background: var(--evm-bg-hover);
  border-radius: var(--evm-r-sm);
  display: flex; align-items: center; justify-content: center;
  color: var(--evm-text-2);
  flex-shrink: 0;
}
.evm-tour-label {
  font-size: 10.5px;
  color: var(--evm-text-3);
  text-transform: uppercase;
  letter-spacing: 0.6px;
  font-weight: 600;
  margin-bottom: 3px;
}
.evm-tour-value {
  font-size: 13.5px;
  color: var(--evm-text-1);
  font-weight: 500;
}

/* ── EMPTY STATE ── */
.evm-empty {
  text-align: center;
  padding: 48px 20px;
  color: var(--evm-text-3);
}
.evm-empty svg { margin-bottom: 12px; opacity: 0.35; }
.evm-empty-title {
  font-size: 14px;
  font-weight: 600;
  font-family: var(--evm-font-h);
  color: var(--evm-text-2);
  margin-bottom: 4px;
}
.evm-empty-sub { font-size: 12px; }
</style>

<script>
function evmSwitchTab(tab, btn) {
  document.querySelectorAll('.evm-tab').forEach(t => t.classList.remove('evm-tab-active'));
  document.querySelectorAll('.evm-tab-panel').forEach(p => p.classList.remove('evm-panel-active'));
  btn.classList.add('evm-tab-active');
  document.getElementById('evmPanel-' + tab).classList.add('evm-panel-active');
  if (window.lucide) lucide.createIcons();
}

function openEventModal(eventId) {
  const contentDiv = document.getElementById('eventDetailsContent');
  contentDiv.innerHTML = `
    <div style="padding:48px;text-align:center;color:#555f74;font-family:'Inter',sans-serif;">
      <div style="margin-bottom:12px;opacity:0.4;">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
      </div>
      Loading event...
    </div>`;
  new bootstrap.Modal(document.getElementById('eventDetailsModal')).show();

  fetch(`/events/${eventId}`, { headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} })
    .then(r => r.json())
    .then(ev => {
      let typeLabel = ev.type === 'esports' ? 'STEAM ESports' : ev.type === 'xr' ? 'STEAM XR Sports' : 'STEAM ' + (ev.type ?? '');
      let ts = ev.tournament_setting || {};

      /* ── Flatten teams ── */
      let allTeams = [];
      (ev.organizations || []).forEach(org => {
        (org.groups || []).forEach(grp => {
          (grp.teams || []).forEach(t => allTeams.push(t));
          (grp.subgroups || []).forEach(sub => (sub.teams || []).forEach(t => allTeams.push(t)));
        });
      });
      allTeams = allTeams.filter((t,i,s) => i === s.findIndex(x => x.id === t.id));
      let totalStudents = allTeams.flatMap(t => t.students || []).length;
      let totalGroups   = (ev.organizations || []).flatMap(o => o.groups || []).length;

      // helper to format numbers with thousand separators for injected HTML
      const fmt = (n) => {
        if (n === undefined || n === null || n === '') return 'N/A';
        const num = Number(n);
        return isNaN(num) ? n : num.toLocaleString();
      };

      /* ── Status badge ── */
      let statusClass = ev.status === 'live' ? 'evm-badge-live' : ev.status === 'closed' ? 'evm-badge-closed' : ev.status === 'upcoming' ? 'evm-badge-upcoming' : 'evm-badge-draft';
      let statusDot   = ev.status === 'live' ? '<span class="evm-dot"></span>' : '';
      let statusLabel = (ev.status ?? 'draft').charAt(0).toUpperCase() + (ev.status ?? 'draft').slice(1);

      /* ── Teams HTML ── */
      let teamsHtml = allTeams.length ? allTeams.map(team => {
        let init  = (team.name || '?')[0].toUpperCase();
        let count = (team.students || []).length;
        let chips = (team.students || []).map(s => {
          let si = (s.name || '?')[0].toUpperCase();
          return `<div class="evm-player-chip"><div class="evm-player-avatar">${si}</div>${s.name}</div>`;
        }).join('') || '<div class="evm-player-chip">No players</div>';
        return `
          <div class="evm-team-item">
            <div class="evm-team-header">
              <div class="evm-team-avatar">${init}</div>
              <div class="evm-team-name">${team.name}</div>
              <div class="evm-team-count">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                ${fmt(count)} player${count !== 1 ? 's' : ''}
              </div>
            </div>
            <div class="evm-player-chips">${chips}</div>
          </div>`;
      }).join('') : `
        <div class="evm-empty">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          <div class="evm-empty-title">No teams registered yet</div>
          <div class="evm-empty-sub">Teams will appear here once registered.</div>
        </div>`;

      /* ── Groups HTML ── */
      let groupsHtml = '';
      (ev.organizations || []).forEach(org => {
        (org.groups || []).forEach(grp => {
          let subHtml = (grp.subgroups || []).map(sub => {
            let subTeams = (sub.teams || []).map(t => `<div class="evm-player-chip">${t.name}</div>`).join('')
              || '<span style="color:var(--evm-text-3);font-size:12px">No teams</span>';
            return `<div class="evm-subgroup"><div class="evm-subgroup-title">${sub.name || 'Subgroup'}</div><div class="evm-player-chips">${subTeams}</div></div>`;
          }).join('');
          if (!subHtml) {
            let dTeams = (grp.teams || []).map(t => `<div class="evm-player-chip">${t.name}</div>`).join('');
            subHtml = `<div class="evm-subgroup"><div class="evm-player-chips">${dTeams || '<span style="color:var(--evm-text-3);font-size:12px">No teams</span>'}</div></div>`;
          }
          let teamCount = (grp.teams || []).length;
          let subCount  = (grp.subgroups || []).length;
          groupsHtml += `
            <div class="evm-group-block">
              <div class="evm-group-header">
                <span class="evm-group-name">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                  ${grp.group_name || grp.name || 'Group'}
                </span>
                <span class="evm-group-count">${teamCount} teams · ${subCount} subgroups</span>
              </div>
              ${subHtml}
            </div>`;
        });
      });
      if (!groupsHtml) groupsHtml = `
        <div class="evm-empty">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
          <div class="evm-empty-title">No groups configured yet</div>
          <div class="evm-empty-sub">Groups will appear here once added.</div>
        </div>`;

      /* ── Activities HTML ── */
      let actHtml = (ev.activities || []).length ? (ev.activities || []).map(a => {
        let aName      = a.display_name || a.brain_type || a.esports_type || a.egaming_type || a.badge_name || a.name || 'Activity';
        let aType      = a.activity_or_mission === 'mission' ? 'Mission' : 'Activity';
        let aSubtype   = a.activity_type ? ' · ' + a.activity_type.charAt(0).toUpperCase() + a.activity_type.slice(1) : '';
        let aStructure = a.point_structure ? ' · ' + (a.point_structure === 'per_team' ? 'Per Team' : 'Per Player') : '';
        return `
          <div class="evm-activity-item">
            <div class="evm-activity-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            </div>
            <div>
              <div class="evm-activity-name">${aName}</div>
              <div class="evm-activity-meta">${aType}${aSubtype}${aStructure}</div>
            </div>
            <span class="evm-activity-score">${a.max_score ?? 0} pts</span>
          </div>`;
      }).join('') : `
        <div class="evm-empty">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
          <div class="evm-empty-title">No activities added yet</div>
          <div class="evm-empty-sub">Activities will appear here once configured.</div>
        </div>`;

      /* ── Render ── */
      contentDiv.innerHTML = `
        <div class="evm-banner">
          <div class="evm-banner-top">
            <div class="evm-trophy">🏆</div>
            <div class="evm-title-block">
              <div class="evm-event-title">${ev.name ?? 'N/A'}</div>
              <div class="evm-event-sub">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                ${ev.location ?? 'N/A'}
              </div>
              <div class="evm-badge-row">
                <span class="evm-badge ${statusClass}">${statusDot}${statusLabel}</span>
                <span class="evm-badge evm-badge-type">
                  <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="6" y1="3" x2="6" y2="15"/><circle cx="18" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M18 9a9 9 0 0 1-9 9"/></svg>
                  ${typeLabel}
                </span>
              </div>
            </div>
            <button class="evm-close-btn" data-bs-dismiss="modal">
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
          <div class="evm-tabs">
            <button class="evm-tab evm-tab-active" onclick="evmSwitchTab('overview',this)">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
              Overview
            </button>
            <button class="evm-tab" onclick="evmSwitchTab('teams',this)">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              Teams
            </button>
            <button class="evm-tab" onclick="evmSwitchTab('groups',this)">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
              Groups
            </button>
            <button class="evm-tab" onclick="evmSwitchTab('activities',this)">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              Activities
            </button>
            <button class="evm-tab" onclick="evmSwitchTab('settings',this)">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
              Tournament Info
            </button>
          </div>
        </div>

        <div class="evm-body">

          {{-- OVERVIEW --}}
          <div class="evm-tab-panel evm-panel-active" id="evmPanel-overview">
            <div class="evm-stats-row">
              <div class="evm-stat-card">
                <div class="evm-stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                <div class="evm-stat-num">${fmt(allTeams.length)}</div>
                <div class="evm-stat-lbl">Teams</div>
              </div>
              <div class="evm-stat-card">
                <div class="evm-stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
                <div class="evm-stat-num">${fmt(totalStudents)}</div>
                <div class="evm-stat-lbl">Players</div>
              </div>
              <div class="evm-stat-card">
                <div class="evm-stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg></div>
                <div class="evm-stat-num">${fmt(totalGroups)}</div>
                <div class="evm-stat-lbl">Groups</div>
              </div>
              <div class="evm-stat-card">
                <div class="evm-stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div>
                <div class="evm-stat-num">${fmt((ev.activities || []).length)}</div>
                <div class="evm-stat-lbl">Activities</div>
              </div>
            </div>
            <div class="evm-info-grid">
              <div class="evm-info-box">
                <div class="evm-info-box-icon"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                <div><div class="evm-info-box-label">Start Date</div><div class="evm-info-box-value">${ev.start_date ?? 'N/A'}</div></div>
              </div>
              <div class="evm-info-box">
                <div class="evm-info-box-icon"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="9 16 11 18 15 14"/></svg></div>
                <div><div class="evm-info-box-label">End Date</div><div class="evm-info-box-value">${ev.end_date ?? 'N/A'}</div></div>
              </div>
              <div class="evm-info-box">
                <div class="evm-info-box-icon"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg></div>
                <div><div class="evm-info-box-label">Tournament Type</div><div class="evm-info-box-value">${ts.tournament_type ?? 'N/A'}</div></div>
              </div>
              <div class="evm-info-box">
                <div class="evm-info-box-icon"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                <div><div class="evm-info-box-label">Players per Team</div><div class="evm-info-box-value">${fmt(ts.players_per_team)}</div></div>
              </div>
            </div>
          </div>

          {{-- TEAMS --}}
          <div class="evm-tab-panel" id="evmPanel-teams">
            <p class="evm-section-title">
              <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              Registered Teams
            </p>
            ${teamsHtml}
          </div>

          {{-- GROUPS --}}
          <div class="evm-tab-panel" id="evmPanel-groups">
            <p class="evm-section-title">
              <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
              Organizations & Groups
            </p>
            ${groupsHtml}
          </div>

          {{-- ACTIVITIES --}}
          <div class="evm-tab-panel" id="evmPanel-activities">
            <p class="evm-section-title">
              <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              Challenge Activities & Missions
            </p>
            ${actHtml}
          </div>

          {{-- TOURNAMENT INFO --}}
          <div class="evm-tab-panel" id="evmPanel-settings">
            <p class="evm-section-title">
              <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
              Tournament Configuration
            </p>
            <div class="evm-tournament-grid">
              <div class="evm-tour-item">
                <div class="evm-tour-icon"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg></div>
                <div><div class="evm-tour-label">Format</div><div class="evm-tour-value">${ts.tournament_type ?? 'N/A'}</div></div>
              </div>
              <div class="evm-tour-item">
                <div class="evm-tour-icon"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="6" y1="3" x2="6" y2="15"/><circle cx="18" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M18 9a9 9 0 0 1-9 9"/></svg></div>
                <div><div class="evm-tour-label">Game</div><div class="evm-tour-value">${ts.game ?? 'N/A'}</div></div>
              </div>
              <div class="evm-tour-item">
                <div class="evm-tour-icon"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                <div><div class="evm-tour-label">Players per Team</div><div class="evm-tour-value">${ts.players_per_team ?? 'N/A'}</div></div>
              </div>
              <div class="evm-tour-item">
                <div class="evm-tour-icon"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                <div><div class="evm-tour-label">Number of Teams</div><div class="evm-tour-value">${ts.number_of_teams ?? 'N/A'}</div></div>
              </div>
              <div class="evm-tour-item">
                <div class="evm-tour-icon"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
                <div><div class="evm-tour-label">Win Points</div><div class="evm-tour-value">${ts.points_win ?? 'N/A'}</div></div>
              </div>
              <div class="evm-tour-item">
                <div class="evm-tour-icon"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/></svg></div>
                <div><div class="evm-tour-label">Draw Points</div><div class="evm-tour-value">${ts.points_draw ?? 'N/A'}</div></div>
              </div>
              <div class="evm-tour-item">
                <div class="evm-tour-icon"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></div>
                <div><div class="evm-tour-label">Brain Module</div><div class="evm-tour-value">${ts.brain_enabled ? 'Enabled' : 'Disabled'}</div></div>
              </div>
              <div class="evm-tour-item">
                <div class="evm-tour-icon"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg></div>
                <div><div class="evm-tour-label">Match Rule</div><div class="evm-tour-value">${ts.match_rule ?? 'N/A'}</div></div>
              </div>
            </div>
          </div>

        </div>`;
    })
    .catch(err => {
      console.error(err);
      contentDiv.innerHTML = `
        <div style="padding:48px;text-align:center;font-family:'Inter',sans-serif;">
          <div style="color:#f05252;margin-bottom:12px;opacity:0.7;">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          </div>
          <div style="color:#f05252;font-size:14px;font-weight:600;margin-bottom:4px;">Failed to load event data</div>
          <div style="color:#555f74;font-size:12px;">Please try again or contact support.</div>
        </div>`;
    });
}
</script>