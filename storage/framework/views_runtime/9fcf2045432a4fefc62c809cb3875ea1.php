<script>

    function initUserCheckboxes() {
    
        const selectAll = document.getElementById('selectAllUsers');
        const deleteBtn = document.getElementById('deleteSelectedUsersBtn');
    
        if (!selectAll || !deleteBtn) return;
    
        function updateCount() {
            const count = document.querySelectorAll('.user-checkbox:checked').length;
            deleteBtn.innerText = `Delete Selected (${count})`;
        }
    
        selectAll.addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateCount();
        });
    
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('user-checkbox')) {
                updateCount();
            }
        });
    }
    
    
    async function deleteSelectedUsers() {
    
        const ids = [...document.querySelectorAll('.user-checkbox:checked')]
            .map(cb => cb.value);
    
        if (!ids.length) {
            alert('Select at least one user');
            return;
        }
    
        if (!confirm('Delete selected users?')) return;
    
        try {
    
            const res = await fetch('/users/bulk-delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids })
            });
    
            const data = await res.json();
    
            if (data.success) {
                toastr.success("Selected users deleted successfully.");
                resetUserSelectionUI();
                location.reload();
            } else {
                alert('Delete failed');
            }
    
        } catch (e) {
            console.error(e);
        }
    }
    
    
    function resetUserSelectionUI() {
        const selectAll = document.getElementById('selectAllUsers');
        const deleteBtn = document.getElementById('deleteSelectedUsersBtn');
    
        if (selectAll) selectAll.checked = false;
        if (deleteBtn) deleteBtn.innerText = `Delete Selected (0)`;
    }
    
    
    document.addEventListener('DOMContentLoaded', function () {
        initUserCheckboxes();
    });
    
    </script><?php /**PATH C:\Users\PC\Desktop\steam-two\resources\views/settings/scripts/users-bulk.blade.php ENDPATH**/ ?>