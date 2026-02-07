<script>
    document.addEventListener('DOMContentLoaded', () => {
        fetchChallenges();
    
        document.getElementById('editChallengeForm').addEventListener('submit', async function(e){
            e.preventDefault();
            const id = document.getElementById('challengeId').value;
            const formData = new FormData(this);
    
            try {
                const res = await fetch(`/challenges/update/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });
    
                const data = await res.json();
    
                if(data.success){
                    bootstrap.Modal.getInstance(document.getElementById('editChallengeModal')).hide();
                    fetchChallenges();
                    toastr.success(data.message);
                } else if(data.errors){
                    data.errors.forEach(err => toastr.error(err));
                }
            } catch(err) {
                console.error(err);
                toastr.error('Something went wrong.');
            }
        });
    });
    
    async function fetchChallenges(){
        const tbody = document.querySelector('#challenges-tab tbody');
        tbody.innerHTML = '<tr><td colspan="5">Loading...</td></tr>';
    
        const res = await fetch('/challenges/fetch', { headers:{'Accept':'application/json'} });
        const challenges = await res.json();
    
        tbody.innerHTML = challenges.map(c => `
            <tr>
                <td>${c.id}</td>
                <td>${c.pillar_type}</td>
                <td>${c.name}</td>
                <td>${c.max_points}</td>
                <td>
                    <div class="d-flex gap-2">
                        <button class="btn btn-icon btn-edit" onclick="openEditModal(${c.id})" title="Edit">
                            <i data-lucide="edit-2"></i>
                        </button>
                        <button class="btn btn-icon btn-delete" onclick="deleteChallenge(${c.id})" title="Delete">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    
        if(typeof lucide !== 'undefined') lucide.createIcons();
    }
    
    async function openEditModal(id){
        const modalEl = document.getElementById('editChallengeModal');
        const modal = new bootstrap.Modal(modalEl);
    
        const res = await fetch(`/challenges/edit/${id}`, { headers:{'Accept':'application/json'} });
        const data = await res.json();
    
        document.getElementById('challengeId').value = data.id;
        document.getElementById('challengeName').value = data.name;
        document.getElementById('challengeMaxPoints').value = data.max_points;
    
        modal.show();
    }
    
    async function deleteChallenge(id){
        if(!confirm('Delete this challenge?')) return;
    
        try {
            const res = await fetch(`/challenges/delete/${id}`, {
                method:'DELETE',
                headers:{
                    'Accept':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                }
            });
            const data = await res.json();
    
            if(data.success){
                fetchChallenges();
                toastr.success(data.message);
            } else {
                toastr.error('Failed to delete challenge');
            }
        } catch(err){
            console.error(err);
            toastr.error('Something went wrong.');
        }
    }
    </script>
    