<script>

    function initRoleCheckboxes() {
    
        const selectAll = document.getElementById('selectAllRoles');
        const deleteBtn = document.getElementById('deleteSelectedRolesBtn');
    
        if (!selectAll || !deleteBtn) return;
    
        function updateCount() {
            const count = document.querySelectorAll('.role-checkbox:checked').length;
            deleteBtn.innerText = `Delete Selected (${count})`;
        }
    
        selectAll.addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.role-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateCount();
        });
    
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('role-checkbox')) {
                updateCount();
            }
        });
    }
    
    
    async function deleteSelectedRoles() {
    
        const ids = [...document.querySelectorAll('.role-checkbox:checked')]
            .map(cb => cb.value);
    
        if (!ids.length) {
            alert('Select at least one role');
            return;
        }
    
        if (!confirm('Delete selected roles?')) return;
    
        try {
    
            const res = await fetch('/roles/bulk-delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids })
            });
    
            const data = await res.json();
    
            if (data.success) {
                toastr.success("Selected roles deleted successfully.");
                resetRoleSelectionUI();
                location.reload();
            } else {
                alert('Delete failed');
            }
    
        } catch (e) {
            console.error(e);
        }
    }
    
    
    function resetRoleSelectionUI() {
        const selectAll = document.getElementById('selectAllRoles');
        const deleteBtn = document.getElementById('deleteSelectedRolesBtn');
    
        if (selectAll) selectAll.checked = false;
        if (deleteBtn) deleteBtn.innerText = `Delete Selected (0)`;
    }
    
    
    document.addEventListener('DOMContentLoaded', function () {
        initRoleCheckboxes();
    });
    
    </script><?php /**PATH C:\Users\PC\Downloads\steamiq (8)\resources\views/settings/scripts/roles-bulk.blade.php ENDPATH**/ ?>