<script>

    function initGroupCheckboxes() {
    
        const selectAll = document.getElementById('selectAllGroups');
        const deleteBtn = document.getElementById('deleteSelectedGroupsBtn');
    
        if (!selectAll || !deleteBtn) return;
    
        function updateCount() {
            const count = document.querySelectorAll('.group-checkbox:checked').length;
            deleteBtn.innerText = `Delete Selected (${count})`;
        }
    
        selectAll.addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.group-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateCount();
        });
    
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('group-checkbox')) {
                updateCount();
            }
        });
    }
    
    
    async function deleteSelectedGroups() {
    
        const ids = [...document.querySelectorAll('.group-checkbox:checked')]
            .map(cb => cb.value);
    
        if (!ids.length) {
            alert('Select at least one group');
            return;
        }
    
        if (!confirm('Delete selected groups?')) return;
    
        try {
    
            const res = await fetch('/groups/bulk-delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids })
            });
    
            const data = await res.json();
    
            if (data.success) {
                toastr.success("Selected groups deleted successfully.");
                resetGroupSelectionUI();
                location.reload(); 
            } else {
                alert('Delete failed');
            }
    
        } catch (e) {
            console.error(e);
        }
    }
    
    
    function resetGroupSelectionUI() {
        const selectAll = document.getElementById('selectAllGroups');
        const deleteBtn = document.getElementById('deleteSelectedGroupsBtn');
    
        if (selectAll) selectAll.checked = false;
        if (deleteBtn) deleteBtn.innerText = `Delete Selected (0)`;
    }
    
    
    // INIT
    document.addEventListener('DOMContentLoaded', function () {
        initGroupCheckboxes();
    });
    
    </script><?php /**PATH C:\Users\PC\Downloads\steamiq (5)\resources\views/groups/script.blade.php ENDPATH**/ ?>