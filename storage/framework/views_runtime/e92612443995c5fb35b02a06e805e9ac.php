<script>

    function initEventCheckboxes() {
    
        const selectAll = document.getElementById('selectAllEvents');
        const deleteBtn = document.getElementById('deleteSelectedEventsBtn');
    
        if (!selectAll || !deleteBtn) return;
    
        function updateCount() {
            const count = document.querySelectorAll('.event-checkbox:checked').length;
            deleteBtn.innerText = `Delete Selected (${count})`;
        }
    
        selectAll.addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.event-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateCount();
        });
    
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('event-checkbox')) {
                updateCount();
            }
        });
    }
    
    
    async function deleteSelectedEvents() {
    
        const ids = [...document.querySelectorAll('.event-checkbox:checked')]
            .map(cb => cb.value);
    
        if (!ids.length) {
            alert('Select at least one event');
            return;
        }
    
        if (!confirm('Delete selected events?')) return;
    
        try {
    
            const res = await fetch('/events/bulk-delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids })
            });
    
            const data = await res.json();
    
            if (data.success) {
                toastr.success("Selected events deleted successfully.");
                resetEventSelectionUI();
                location.reload();
            } else {
                alert('Delete failed');
            }
    
        } catch (e) {
            console.error(e);
        }
    }
    
    
    function resetEventSelectionUI() {
        const selectAll = document.getElementById('selectAllEvents');
        const deleteBtn = document.getElementById('deleteSelectedEventsBtn');
    
        if (selectAll) selectAll.checked = false;
        if (deleteBtn) deleteBtn.innerText = `Delete Selected (0)`;
    }
    
    
    document.addEventListener('DOMContentLoaded', function () {
        initEventCheckboxes();
    });
    
    </script><?php /**PATH C:\Users\PC\Downloads\steamiq (5)\resources\views/events/scripts/bulk-delete.blade.php ENDPATH**/ ?>