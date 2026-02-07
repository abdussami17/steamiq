
<script>
    document.addEventListener('DOMContentLoaded', fetchScores);

async function fetchScores() {
    const tbody = document.getElementById('scoresTableBody');
    tbody.innerHTML = `<tr><td colspan="6">Loading...</td></tr>`;
    
    try {
        const res = await fetch('/scores-data', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const scores = await res.json();

        if (!scores.length) {
            tbody.innerHTML = `<tr><td colspan="6">N/A</td></tr>`;
            return;
        }

        let rows = '';
        scores.forEach(score => {
            rows += `<tr>
                <td>${score.player}</td>
                <td>${score.pillar}</td>
                <td>${score.category}</td>
                <td>${score.points}</td>
                <td>${score.date}</td>
                <td>
                    <div style="display:flex;gap:0.25rem;">
                        <button class="btn btn-icon btn-edit" onclick="openScoreModal('${score.id}')" title="Edit">
                            <i data-lucide="edit-2"></i>
                        </button>
                        <button class="btn btn-icon btn-delete" onclick="deleteScore('${score.id}','${score.category} - ${score.points}pts')" title="Delete">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        });

        tbody.innerHTML = rows;
        if (window.lucide) lucide.createIcons();
    } catch(err) {
        console.error(err);
        tbody.innerHTML = `<tr><td colspan="6">Error loading scores</td></tr>`;
    }
}

function openScoreModal(scoreId){
    const modal = new bootstrap.Modal(document.getElementById('viewEditScoreModal'));
    document.getElementById('editScoreForm').reset();
    document.getElementById('scoreId').value = scoreId;

    fetch(`/scores/view/${scoreId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(data => {
            if(data.error){ alert(data.error); return; }

            // Pre-select player
            Array.from(document.getElementById('scorePlayer').options).forEach(opt => {
                opt.selected = opt.value == data.player_id;
            });

            // Pre-select CAM Pillar
            Array.from(document.getElementById('scorePillar').options).forEach(opt => {
                opt.selected = opt.value == data.pillar_id;
            });

            // Category, Points, Date
            document.getElementById('scoreCategory').value = data.category;
            document.getElementById('scorePoints').value = data.points;
            document.getElementById('scoreDate').value = data.date;

            modal.show();
        })
        .catch(err => console.error(err));
}


// Submit edit score
document.getElementById('editScoreForm').addEventListener('submit', function(e){
    e.preventDefault();
    const id = document.getElementById('scoreId').value;
    const formData = new FormData(this);

    fetch(`/scores/update/${id}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(res => res.json())
      .then(data => {
          if(data.success){
              fetchScores();
              bootstrap.Modal.getInstance(document.getElementById('viewEditScoreModal')).hide();
          } else {
              alert('Failed to update score');
          }
      });
});

// Delete score
function deleteScore(id, name){
    if(confirm(`Delete ${name}?`)){
        fetch(`/scores/delete/${id}`, {
            method:'DELETE',
            headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}
        }).then(res=>res.json()).then(data=>{
            if(data.success) fetchScores();
        });
    }
}

</script>