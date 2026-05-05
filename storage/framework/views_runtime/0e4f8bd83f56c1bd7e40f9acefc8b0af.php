<script>
    document.addEventListener('DOMContentLoaded', function() {
    
        const assignedToSelect = document.getElementById('assignedToSelect');
        const assignableTypeInput = document.getElementById('assignable_type');
        const finalAssignableId = document.getElementById('final_assignable_id');
    
        const firstLevelContainer = document.getElementById('firstLevelContainer');
        const secondLevelContainer = document.getElementById('secondLevelContainer');
        const directContainer = document.getElementById('directContainer');
    
        const firstLevelDropdown = document.getElementById('firstLevelDropdown');
        const secondLevelDropdown = document.getElementById('secondLevelDropdown');
        const directDropdown = document.getElementById('directDropdown');
    
        const firstLevelLabel = document.getElementById('firstLevelLabel');
        const secondLevelLabel = document.getElementById('secondLevelLabel');
        const directLabel = document.getElementById('directLabel');
    
        function hideAllContainers() {
            firstLevelContainer.style.display = 'none';
            secondLevelContainer.style.display = 'none';
            directContainer.style.display = 'none';
        }
    
        function resetDropdowns() {
            firstLevelDropdown.innerHTML = '<option value="">-- Choose --</option>';
            secondLevelDropdown.innerHTML = '<option value="">-- Choose --</option>';
            directDropdown.innerHTML = '<option value="">-- Choose --</option>';
            finalAssignableId.value = '';
        }
    
        assignedToSelect.addEventListener('change', async function() {
            const type = this.value;
            assignableTypeInput.value = type;
            finalAssignableId.value = '';
    
            hideAllContainers();
            resetDropdowns();
    
            if (!type) return;
    
            // ================= ORGANIZATION =================
            if (type === 'organization') {
                directLabel.textContent = 'Select Organization';
                directContainer.style.display = 'block';
                await loadOrganizations();
    
                directDropdown.onchange = function() {
                    finalAssignableId.value = this.value;
                };
            }
    
            // ================= GROUP =================
            else if (type === 'group') {
                firstLevelLabel.textContent = 'Select Organization';
                secondLevelLabel.textContent = 'Select Group';
    
                firstLevelContainer.style.display = 'block';
                await loadOrganizationsInto(firstLevelDropdown);
    
                firstLevelDropdown.onchange = async function() {
                    const orgId = this.value;
                    if (!orgId) return;
    
                    secondLevelContainer.style.display = 'block';
                    await loadGroupsByOrganization(orgId);
    
                    secondLevelDropdown.onchange = function() {
                        finalAssignableId.value = this.value;
                    };
                };
            }
    
            // ================= TEAM =================
            else if (type === 'team') {
                firstLevelLabel.textContent = 'Select Organization';
                secondLevelLabel.textContent = 'Select Group';
    
                firstLevelContainer.style.display = 'block';
                await loadOrganizationsInto(firstLevelDropdown);
    
                firstLevelDropdown.onchange = async function() {
                    const orgId = this.value;
                    if (!orgId) return;
    
                    secondLevelContainer.style.display = 'block';
                    await loadGroupsByOrganization(orgId);
    
                    secondLevelDropdown.onchange = async function() {
                        const groupId = this.value;
                        if (!groupId) return;
    
                        // create third dropdown dynamically
                        createThirdDropdown('Select Team');
    
                        await loadTeamsByGroup(groupId, 'thirdLevelDropdown');
    
                        document.getElementById('thirdLevelDropdown').onchange = function() {
                            finalAssignableId.value = this.value;
                        };
                    };
                };
            }
    
            // ================= STUDENT =================
            else if (type === 'student') {
                firstLevelLabel.textContent = 'Select Organization';
                secondLevelLabel.textContent = 'Select Group';
    
                firstLevelContainer.style.display = 'block';
                await loadOrganizationsInto(firstLevelDropdown);
    
                firstLevelDropdown.onchange = async function() {
                    const orgId = this.value;
                    if (!orgId) return;
    
                    secondLevelContainer.style.display = 'block';
                    await loadGroupsByOrganization(orgId);
    
                    secondLevelDropdown.onchange = async function() {
                        const groupId = this.value;
                        if (!groupId) return;
    
                        createThirdDropdown('Select Team');
                        await loadTeamsByGroup(groupId, 'thirdLevelDropdown');
    
                        document.getElementById('thirdLevelDropdown').onchange = async function() {
                            const teamId = this.value;
                            if (!teamId) return;
    
                            createFourthDropdown('Select Player');
    
                            await loadStudentsByTeam(teamId, 'fourthLevelDropdown');
    
                            document.getElementById('fourthLevelDropdown').onchange = function() {
                                finalAssignableId.value = this.value;
                            };
                        };
                    };
                };
            }
        });
    
        // ================= HELPERS =================
        async function loadOrganizations() {
    directDropdown.innerHTML = '<option>Loading...</option>';

    try {
        const res = await fetch('<?php echo e(route("api.organizations.list")); ?>');
        const data = await res.json();

        let opt = '<option value="">-- Choose Organization --</option>';
        data.forEach(i => {
            opt += `<option value="${i.id}">${i.name}</option>`;
        });

        directDropdown.innerHTML = opt;

    } catch (err) {
        directDropdown.innerHTML = '<option value="">-- Error Loading --</option>';
        console.error(err);
    }
}
        async function loadOrganizationsInto(dropdown) {
            dropdown.innerHTML = '<option>Loading...</option>';
            const res = await fetch('<?php echo e(route("api.organizations.list")); ?>');
            const data = await res.json();
    
            let opt = '<option value="">-- Choose Organization --</option>';
            data.forEach(i => opt += `<option value="${i.id}">${i.name}</option>`);
            dropdown.innerHTML = opt;
        }
    
        async function loadGroupsByOrganization(orgId) {
            const res = await fetch('<?php echo e(route("api.groups.list")); ?>');
            const data = await res.json();
    
            let opt = '<option value="">-- Choose Group --</option>';
            data.filter(g => g.organization_id == orgId)
                .forEach(g => opt += `<option value="${g.id}">${g.group_name}</option>`);
    
            secondLevelDropdown.innerHTML = opt;
        }
    
        async function loadTeamsByGroup(groupId, dropdownId) {
            const res = await fetch('/api/teams/by-group/' + groupId);
            const data = await res.json();
    
            let opt = '<option value="">-- Choose Team --</option>';
            data.forEach(t => opt += `<option value="${t.id}">${t.name}</option>`);
    
            document.getElementById(dropdownId).innerHTML = opt;
        }
    
        async function loadStudentsByTeam(teamId, dropdownId) {
            const res = await fetch('/api/students/by-team/' + teamId);
            const data = await res.json();
    
            let opt = '<option value="">-- Choose Player --</option>';
            data.forEach(s => opt += `<option value="${s.id}">${s.name}</option>`);
    
            document.getElementById(dropdownId).innerHTML = opt;
        }
    
        function createThirdDropdown(labelText) {
            removeExtraDropdowns();
    
            const div = document.createElement('div');
            div.className = 'mb-3 col-md-6';
            div.innerHTML = `
                <label class="form-label">${labelText}</label>
                <select class="form-select" id="thirdLevelDropdown">
                    <option>Loading...</option>
                </select>
            `;
            secondLevelContainer.after(div);
        }
    
        function createFourthDropdown(labelText) {
            const div = document.createElement('div');
            div.className = 'mb-3 col-md-6';
            div.innerHTML = `
                <label class="form-label">${labelText}</label>
                <select class="form-select" id="fourthLevelDropdown">
                    <option>Loading...</option>
                </select>
            `;
            document.getElementById('thirdLevelDropdown').parentElement.after(div);
        }
    
        function removeExtraDropdowns() {
            document.getElementById('thirdLevelDropdown')?.parentElement.remove();
            document.getElementById('fourthLevelDropdown')?.parentElement.remove();
        }
    
    });
    </script><?php /**PATH /home/u236413684/domains/voags.com/public_html/steam-two/resources/views/card/assign-script.blade.php ENDPATH**/ ?>