<script>
    document.addEventListener('DOMContentLoaded', function() {
        const assignedToSelect = document.getElementById('assignedToSelect');
        const assignableTypeInput = document.getElementById('assignable_type');
        const finalAssignableId = document.getElementById('final_assignable_id');
        
        // Containers
        const firstLevelContainer = document.getElementById('firstLevelContainer');
        const secondLevelContainer = document.getElementById('secondLevelContainer');
        const directContainer = document.getElementById('directContainer');
        
        // Dropdowns
        const firstLevelDropdown = document.getElementById('firstLevelDropdown');
        const secondLevelDropdown = document.getElementById('secondLevelDropdown');
        const directDropdown = document.getElementById('directDropdown');
        
        // Labels
        const firstLevelLabel = document.getElementById('firstLevelLabel');
        const secondLevelLabel = document.getElementById('secondLevelLabel');
        const directLabel = document.getElementById('directLabel');

        // Hide all containers initially
        function hideAllContainers() {
            firstLevelContainer.style.display = 'none';
            secondLevelContainer.style.display = 'none';
            directContainer.style.display = 'none';
        }

        // Reset all dropdowns
        function resetDropdowns() {
            firstLevelDropdown.innerHTML = '<option value="">-- Choose --</option>';
            secondLevelDropdown.innerHTML = '<option value="">-- Choose --</option>';
            directDropdown.innerHTML = '<option value="">-- Choose --</option>';
            finalAssignableId.value = '';
        }

        // Handle main Assign To change
        assignedToSelect.addEventListener('change', async function() {
            const type = this.value;
            assignableTypeInput.value = type;
            finalAssignableId.value = '';
            
            hideAllContainers();
            resetDropdowns();

            if (!type) return;

            if (type === 'organization') {
                // Direct selection for organization
                directLabel.textContent = 'Select Organization';
                directContainer.style.display = 'block';
                await loadOrganizations();
                
                // When organization is selected, set final value
                directDropdown.onchange = function() {
                    finalAssignableId.value = this.value;
                };
            } 
            else if (type === 'group') {
                // Direct selection for group
                directLabel.textContent = 'Select Group';
                directContainer.style.display = 'block';
                await loadGroups();
                
                directDropdown.onchange = function() {
                    finalAssignableId.value = this.value;
                };
            } 
            else if (type === 'team') {
                // For Team: First show Groups, then Teams
                firstLevelLabel.textContent = 'Select Group';
                secondLevelLabel.textContent = 'Select Team';
                
                firstLevelContainer.style.display = 'block';
                await loadGroupsIntoFirstLevel();
                
                // When group is selected, load teams
                firstLevelDropdown.onchange = async function() {
                    const groupId = this.value;
                    if (!groupId) {
                        secondLevelContainer.style.display = 'none';
                        finalAssignableId.value = '';
                        return;
                    }
                    
                    secondLevelContainer.style.display = 'block';
                    secondLevelDropdown.innerHTML = '<option value="">-- Loading Teams --</option>';
                    
                    try {
                        const res = await fetch('/api/teams/by-group/' + groupId);
                        const teams = await res.json();
                        
                        let options = '<option value="">-- Choose Team --</option>';
                        teams.forEach(team => {
                            options += `<option value="${team.id}">${team.name}</option>`;
                        });
                        secondLevelDropdown.innerHTML = options;
                        
                        // When team is selected, set final value
                        secondLevelDropdown.onchange = function() {
                            finalAssignableId.value = this.value;
                        };
                    } catch(err) {
                        secondLevelDropdown.innerHTML = '<option value="">-- Error Loading Teams --</option>';
                        console.error('Error:', err);
                    }
                };
            } 
            else if (type === 'student') {
                // For Student: First show Teams, then Students
                firstLevelLabel.textContent = 'Select Team';
                secondLevelLabel.textContent = 'Select Player';
                
                firstLevelContainer.style.display = 'block';
                await loadTeamsIntoFirstLevel();
                
                // When team is selected, load students
                firstLevelDropdown.onchange = async function() {
                    const teamId = this.value;
                    if (!teamId) {
                        secondLevelContainer.style.display = 'none';
                        finalAssignableId.value = '';
                        return;
                    }
                    
                    secondLevelContainer.style.display = 'block';
                    secondLevelDropdown.innerHTML = '<option value="">-- Loading Players --</option>';
                    
                    try {
                        const res = await fetch('/api/students/by-team/' + teamId);
                        const students = await res.json();
                        
                        let options = '<option value="">-- Choose Player --</option>';
                        students.forEach(student => {
                            options += `<option value="${student.id}">${student.name}</option>`;
                        });
                        secondLevelDropdown.innerHTML = options;
                        
                        // When student is selected, set final value
                        secondLevelDropdown.onchange = function() {
                            finalAssignableId.value = this.value;
                        };
                    } catch(err) {
                        secondLevelDropdown.innerHTML = '<option value="">-- Error Loading Players --</option>';
                        console.error('Error:', err);
                    }
                };
            }
        });

        // Helper functions
        async function loadOrganizations() {
            directDropdown.innerHTML = '<option value="">-- Loading Organizations --</option>';
            try {
                const res = await fetch('{{ route("api.organizations.list") }}');
                const organizations = await res.json();
                
                let options = '<option value="">-- Choose Organization --</option>';
                organizations.forEach(org => {
                    options += `<option value="${org.id}">${org.name}</option>`;
                });
                directDropdown.innerHTML = options;
            } catch(err) {
                directDropdown.innerHTML = '<option value="">-- Error Loading Organizations --</option>';
                console.error('Error:', err);
            }
        }

        async function loadGroups() {
            directDropdown.innerHTML = '<option value="">-- Loading Groups --</option>';
            try {
                const res = await fetch('{{ route("api.groups.list") }}');
                const groups = await res.json();
                
                let options = '<option value="">-- Choose Group --</option>';
                groups.forEach(group => {
                    options += `<option value="${group.id}">${group.group_name}</option>`;
                });
                directDropdown.innerHTML = options;
            } catch(err) {
                directDropdown.innerHTML = '<option value="">-- Error Loading Groups --</option>';
                console.error('Error:', err);
            }
        }

        async function loadGroupsIntoFirstLevel() {
            firstLevelDropdown.innerHTML = '<option value="">-- Loading Groups --</option>';
            try {
                const res = await fetch('{{ route("api.groups.list") }}');
                const groups = await res.json();
                
                let options = '<option value="">-- Choose Group --</option>';
                groups.forEach(group => {
                    options += `<option value="${group.id}">${group.group_name}</option>`;
                });
                firstLevelDropdown.innerHTML = options;
            } catch(err) {
                firstLevelDropdown.innerHTML = '<option value="">-- Error Loading Groups --</option>';
                console.error('Error:', err);
            }
        }

        async function loadTeamsIntoFirstLevel() {
            firstLevelDropdown.innerHTML = '<option value="">-- Loading Teams --</option>';
            try {
                const res = await fetch('{{ route("api.teams.list") }}');
                const teams = await res.json();
                
                let options = '<option value="">-- Choose Team --</option>';
                teams.forEach(team => {
                    options += `<option value="${team.id}">${team.name}</option>`;
                });
                firstLevelDropdown.innerHTML = options;
            } catch(err) {
                firstLevelDropdown.innerHTML = '<option value="">-- Error Loading Teams --</option>';
                console.error('Error:', err);
            }
        }
    });
</script>