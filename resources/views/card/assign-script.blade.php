<script>
    const assignables = @json($assignables);
    
    document.querySelectorAll('.assign-card-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            const modal = document.getElementById('assignCardModal');
    
            // Update hidden input
            modal.querySelector('.assignable-type').value = type;
    
            // Update labels
            modal.querySelectorAll('.assignable-title').forEach(el => {
                el.textContent = type.charAt(0).toUpperCase() + type.slice(1);
            });
    
            // Populate select
            const select = modal.querySelector('.assignable-select');
            select.innerHTML = '<option value="">-- Choose ' + type.charAt(0).toUpperCase() + type.slice(1) + ' --</option>';
    
            assignables[type].forEach(item => {
                let displayName;
    
                // Use correct property based on type
                if (type === 'group') {
                    displayName = item.group_name ?? 'N/A';
                } else if (type === 'player') {
                    displayName = item.name ?? item.username ?? 'N/A';
                } else if (type === 'team') {
                    displayName = item.name ?? 'N/A';
                } else {
                    displayName = item.name ?? item.title ?? item.username ?? 'N/A';
                }
    
                select.innerHTML += `<option value="${item.id}">${displayName}</option>`;
            });
    
            // Show modal
            new bootstrap.Modal(modal).show();
        });
    });
    </script>