<!-- Event Details Modal (Bootstrap 5) -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 800px;">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title" id="eventDetailsModalLabel">Event Details</h2>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="eventDetailsContent">
          Loading...
        </div>
      </div>
    </div>
  </div>
  
  <script>
  function openEventModal(eventId) {
      const modalLabel = document.getElementById('eventDetailsModalLabel');
      const contentDiv = document.getElementById('eventDetailsContent');
      if (!modalLabel || !contentDiv) return;
  
      contentDiv.innerHTML = 'Loading...';
  
      fetch(`/events/${eventId}`, {
          headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
          }
      })
      .then(res => {
          if (!res.ok) throw new Error('Server error');
          return res.json();
      })
      .then(event => {
          modalLabel.textContent = event.name ?? 'N/A';
  
          let html = `
              <div style="margin-bottom: 2rem;">
                  <span class="badge badge-${event.status ?? 'draft'}">${event.status ?? 'N/A'}</span>
                  <span class="badge" style="background: rgba(0, 212, 255, 0.2); color: var(--accent); border: 1px solid var(--accent); margin-left: 0.5rem;">
                      ${event.event_type ?? 'N/A'}
                  </span>
              </div>
  
              <div class="stats-grid" style="margin-bottom: 2rem;">
                  <div class="stat">
                      <div class="stat-label">Total Teams</div>
                      <div class="stat-value" style="font-size: 1.5rem;">${event.teams?.length ?? 'N/A'}</div>
                  </div>
                  <div class="stat">
                      <div class="stat-label">Total Players</div>
                      <div class="stat-value" style="font-size: 1.5rem;">
                          ${event.teams?.reduce((sum, t) => sum + (t.players?.length ?? 0), 0) ?? 'N/A'}
                      </div>
                  </div>
                  <div class="stat">
                      <div class="stat-label">Completed</div>
                      <div class="stat-value" style="font-size: 1.5rem;">
                        ${event.completed_players ?? 0} / ${event.teams?.reduce((sum,t)=>sum+(t.players?.length??0),0) ?? 'N/A'}
                      </div>
                  </div>
              </div>
  
              <div style="background: var(--dark); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;">
                  <h3 style="margin-bottom: 1rem; font-size: 1.1rem;color:var(--text) ; font-weight:700">Event Information</h3>
                  <div style="display: grid; gap: 0.75rem;">
                      <div style="display: flex; justify-content: space-between;">
                          <span style="color: var(--text-dim);">Start Date:</span>
                          <span style="font-weight: 600;color:var(--text) ;">${event.start_date ?? 'N/A'}</span>
                      </div>
                      <div style="display: flex; justify-content: space-between;">
                          <span style="color: var(--text-dim);">End Date:</span>
                          <span style="font-weight: 600;color:var(--text) ;">${event.end_date ?? 'N/A'}</span>
                      </div>
                      <div style="display: flex; justify-content: space-between;">
                          <span style="color: var(--text-dim);">Location:</span>
                          <span style="font-weight: 600;color:var(--text) ;">${event.location ?? 'N/A'}</span>
                      </div>
                      <div style="display: flex; justify-content: space-between;">
                          <span style="color: var(--text-dim);">Notes:</span>
                          <span style="font-weight: 600;color:var(--text) ;">${event.notes ?? '-'}</span>
                      </div>
                  </div>
              </div>
  
              <div  style="margin-top: 1rem;background: var(--dark); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;">
                  <h4 style="margin-bottom: 1rem; font-size: 1.1rem;color:var(--text) ; font-weight:700">Teams & Players</h4>
                  <ul style="max-height: 200px; overflow-y: auto; list-style: none; padding-left: 0;">
                      ${event.teams?.map(t => `<li style='color:var(--text)'><strong>${t.team_name ?? 'N/A'}</strong>: ${t.players?.map(p => p.name ?? 'N/A').join(', ') ?? 'N/A'}</li>`).join('') ?? '<li>N/A</li>'}
                  </ul>
              </div>
  
              <div style="margin-top: 1rem;background: var(--dark); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem;margin-top:1rem">
                  <h4 style="margin-bottom: 1rem; font-size: 1.1rem;color:var(--text) ; font-weight:700">Challenges</h4>
                  <ul style="list-style: none; padding-left: 0;">
                    ${event.challenges?.map(c => `<li style='color:var(--text)'>${c.name} (${c.pillar_type}${c.sub_category ? ' - ' + c.sub_category : ''})</li>`).join('') ?? '<li>N/A</li>'}

                  </ul>
              </div>
  
          `;
  
          contentDiv.innerHTML = html;
          new bootstrap.Modal(document.getElementById('eventDetailsModal')).show();
      })
      .catch(err => {
          if (contentDiv) contentDiv.innerHTML = '<p>Error loading event data.</p>';
          console.error(err);
      });
  }
  </script>
  