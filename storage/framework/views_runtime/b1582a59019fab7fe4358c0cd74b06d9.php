<script>

    function initOrgCheckboxes() {
    
        const selectAll = document.getElementById('selectAllOrgs');
        const deleteBtn = document.getElementById('deleteSelectedOrgsBtn');
    
        if (!selectAll || !deleteBtn) return;
    
        function updateCount() {
            const count = document.querySelectorAll('.org-checkbox:checked').length;
            deleteBtn.innerText = `Delete Selected (${count})`;
        }
    
        selectAll.addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.org-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateCount();
        });
    
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('org-checkbox')) {
                updateCount();
            }
        });
    }
    
    
    async function deleteSelectedOrganizations() {
    
        const ids = [...document.querySelectorAll('.org-checkbox:checked')]
            .map(cb => cb.value);
    
        if (!ids.length) {
            alert('Select at least one organization');
            return;
        }
    
        if (!confirm('Delete selected organizations?')) return;
    
        try {
    
            const res = await fetch('/organizations/bulk-delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids })
            });
    
            const data = await res.json();
    
            if (data.success) {
                toastr.success("Selected organizations deleted successfully.");
                resetOrgSelectionUI();
                location.reload(); // ya ajax reload
            } else {
                alert('Delete failed');
            }
    
        } catch (e) {
            console.error(e);
        }
    }
    
    
    function resetOrgSelectionUI() {
        const selectAll = document.getElementById('selectAllOrgs');
        const deleteBtn = document.getElementById('deleteSelectedOrgsBtn');
    
        if (selectAll) selectAll.checked = false;
        if (deleteBtn) deleteBtn.innerText = `Delete Selected (0)`;
    }
    
    
    document.addEventListener('DOMContentLoaded', function () {
        initOrgCheckboxes();
    });
    
    </script><?php /**PATH C:\Users\PC\Downloads\steamiq (5)\resources\views/organization/script.blade.php ENDPATH**/ ?>