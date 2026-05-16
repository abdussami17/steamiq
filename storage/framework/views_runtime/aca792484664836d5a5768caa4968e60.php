<script>

    function initSubGroupCheckboxes() {
    
        const selectAll = document.getElementById('selectAllSubGroups');
        const deleteBtn = document.getElementById('deleteSelectedSubGroupsBtn');
    
        if (!selectAll || !deleteBtn) return;
    
        function updateCount() {
            const count = document.querySelectorAll('.subgroup-checkbox:checked').length;
            deleteBtn.innerText = `Delete Selected (${count})`;
        }
    
        selectAll.addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.subgroup-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateCount();
        });
    
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('subgroup-checkbox')) {
                updateCount();
            }
        });
    }
    
    
    async function deleteSelectedSubGroups() {
    
        const ids = [...document.querySelectorAll('.subgroup-checkbox:checked')]
            .map(cb => cb.value);
    
        if (!ids.length) {
            alert('Select at least one sub group');
            return;
        }
    
        if (!confirm('Delete selected sub groups?')) return;
    
        try {
    
            const res = await fetch('/subgroups/bulk-delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids })
            });
    
            const data = await res.json();
    
            if (data.success) {
                toastr.success("Selected subgroups deleted successfully.");
                resetSubGroupSelectionUI();
                location.reload();
            } else {
                alert('Delete failed');
            }
    
        } catch (e) {
            console.error(e);
        }
    }
    
    
    function resetSubGroupSelectionUI() {
        const selectAll = document.getElementById('selectAllSubGroups');
        const deleteBtn = document.getElementById('deleteSelectedSubGroupsBtn');
    
        if (selectAll) selectAll.checked = false;
        if (deleteBtn) deleteBtn.innerText = `Delete Selected (0)`;
    }
    
    
    document.addEventListener('DOMContentLoaded', function () {
        initSubGroupCheckboxes();
    });
    
    </script><?php /**PATH C:\Users\PC\Desktop\steam-two\resources\views/subgroups/scripts/bulk-edit.blade.php ENDPATH**/ ?>