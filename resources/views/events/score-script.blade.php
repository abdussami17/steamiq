<script>
    document.addEventListener('DOMContentLoaded', () => {
        fetchScores();
    
        document.getElementById('editScoreForm')
            .addEventListener('submit', submitUpdate);
    });
    
    
    
    /* =============================
       LOAD TABLE
    ============================= */
    async function fetchScores() {
        const tbody = document.getElementById('scoresTableBody');
        tbody.innerHTML = `<tr><td colspan="6">Loading...</td></tr>`;
    
        try {
            const res = await fetch('/scores-data', {
                headers: { 'Accept':'application/json' }
            });
    
            const scores = await res.json();
    
            if (!scores.length) {
                tbody.innerHTML = `<tr><td colspan="6">N/A</td></tr>`;
                return;
            }
    
            tbody.innerHTML = scores.map(s => `
                <tr>
                    <td>${s.player}</td>
                    <td>${s.pillar}</td>
                    <td>${s.category}</td>
                    <td>${s.points}</td>
                    <td>${s.date}</td>
                    <td>
                     <div class="d-flex gap-2">
                        <button class="btn btn-icon btn-edit" onclick="openScoreModal('${s.id}')" title="Edit">
                            <i data-lucide="edit-2"></i>
                        </button>
                        <button class="btn btn-icon btn-delete" onclick="deleteScore('${s.id}')" title="Delete">
                            <i data-lucide="trash-2"></i>
                        </button>
                        </div>
                    </td>
                </tr>
            `).join('');
            try {
    if (typeof lucide !== 'undefined') lucide.createIcons();
} catch (e) {
    console.warn('Lucide icon replacement failed:', e);
}
    
        } catch (e) {
            console.error(e);
            tbody.innerHTML = `<tr><td colspan="6">Error</td></tr>`;
        }
    }
    
    
    
    /* =============================
       OPEN MODAL
    ============================= */
    async function openScoreModal(id) {
    
        const modal = new bootstrap.Modal(
            document.getElementById('editScoreModal')
        );
    
        document.getElementById('scoreId').value = id;
    
        const res = await fetch(`/scores/view/${id}`, {
            headers:{'Accept':'application/json'}
        });
    
        const data = await res.json();
    
       
        document.getElementById('scorePoints').value = data.points;
    
        modal.show();
    }
    
    
    
    /* =============================
       UPDATE
    ============================= */
    async function submitUpdate(e) {
        e.preventDefault();
    
        const id = document.getElementById('scoreId').value;
        const form = new FormData(e.target);
    
        const res = await fetch(`/scores/update/${id}`, {
    method:'POST',
    body:form,
    headers:{
        'Accept':'application/json',
        'X-CSRF-TOKEN':'{{ csrf_token() }}'
    }
});

if(!res.ok){
    const err = await res.json().catch(()=>({error:'Server error'}));
    alert('Update failed: '+ (err.error || 'Unknown error'));
    return;
}

const data = await res.json();

    
        if(data.success){
            bootstrap.Modal.getInstance(
                document.getElementById('editScoreModal')
            ).hide();
    
            fetchScores();
        } else {
            alert('Update failed');
        }
    }
    
    
    
    /* =============================
       DELETE
    ============================= */
    async function deleteScore(id){
    
        if(!confirm('Delete this score?')) return;
    
        await fetch(`/scores/delete/${id}`,{
            method:'DELETE',
            headers:{
                'Accept':'application/json',
                'X-CSRF-TOKEN':'{{ csrf_token() }}'
            }
        });
    
        fetchScores();
    }
    </script>
    