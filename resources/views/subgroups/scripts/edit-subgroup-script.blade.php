<script>
    // Store the original subgroup data when editing
    let currentSubgroupData = null;
    
    function openSubGroupEditModal(subgroupId) {
        fetch(`/subgroup/fetch/${subgroupId}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            })
            .then(data => {
                console.log('Fetched data:', data); // Check what data is coming
                currentSubgroupData = data; // Store for later use
                
                const form = document.getElementById('editSubGroupForm');
                form.action = `/subgroup/update/${subgroupId}`;
    
                // Set the subgroup name - try different possible field names
                const nameField = document.getElementById('subgroupname');
                nameField.value = data.name || data.subgroup_name || '';
    
                // Set the group
                const groupSelect = document.getElementById('edit_group');
                groupSelect.value = data.group_id || '';
    
                // Update event based on selected group
                updateEventFromGroupSelect(groupSelect);
    
                const modalEl = document.getElementById('editSubGroupModal');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            })
            .catch(err => {
                console.error('Error fetching subgroup:', err);
                alert('Error loading subgroup data');
            });
    }
    
    // Function to update event display based on selected group
    function updateEventFromGroupSelect(selectElement) {
        const selected = selectElement.options[selectElement.selectedIndex];
        
        // If we have stored data and this is the initial load, use that
        if (currentSubgroupData && selectElement.value == currentSubgroupData.group_id) {
            const eventWrapper = document.getElementById('editEventWrapper');
            const eventDisplay = document.getElementById('edit_eventDisplay');
            const eventHidden = document.getElementById('edit_eventHidden');
    
            if (currentSubgroupData.event_id) {
                eventWrapper.classList.remove('d-none');
                eventDisplay.value = currentSubgroupData.event_name || 'N/A';
                eventHidden.value = currentSubgroupData.event_id;
            } else {
                eventWrapper.classList.add('d-none');
                eventDisplay.value = '';
                eventHidden.value = '';
            }
        } else {
            // Normal case - get from selected option
            const eventId = selected ? selected.dataset.eventId : null;
            const eventName = selected ? selected.dataset.eventName : null;
    
            const eventWrapper = document.getElementById('editEventWrapper');
            const eventDisplay = document.getElementById('edit_eventDisplay');
            const eventHidden = document.getElementById('edit_eventHidden');
    
            if (eventId) {
                eventWrapper.classList.remove('d-none');
                eventDisplay.value = eventName || 'N/A';
                eventHidden.value = eventId;
            } else {
                eventWrapper.classList.add('d-none');
                eventDisplay.value = '';
                eventHidden.value = '';
            }
        }
    }
    
    // Add event listener for group change
    document.addEventListener('DOMContentLoaded', function() {
        const groupSelect = document.getElementById('edit_group');
        
        // Remove any existing listeners to prevent duplicates
        groupSelect.removeEventListener('change', handleGroupChange);
        groupSelect.addEventListener('change', handleGroupChange);
    });
    
    function handleGroupChange() {
        updateEventFromGroupSelect(this);
    }
    
    // Optional: Reset currentSubgroupData when modal is closed
    document.getElementById('editSubGroupModal').addEventListener('hidden.bs.modal', function () {
        currentSubgroupData = null;
    });
    </script>