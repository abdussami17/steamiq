<script>
    document.addEventListener('DOMContentLoaded', function() {
    
        const assignTo = document.getElementById('assignToSelectUnique98765');
        const assignableType = document.getElementById('assignableTypeInputUnique98765');
        const finalId = document.getElementById('assignableIdFinalUnique98765');
    
        const firstContainer = document.getElementById('firstLevelContainerUnique98765');
        const secondContainer = document.getElementById('secondLevelContainerUnique98765');
        const directContainer = document.getElementById('directContainerUnique98765');
    
        const firstDropdown = document.getElementById('firstLevelDropdownUnique98765');
        const secondDropdown = document.getElementById('secondLevelDropdownUnique98765');
        const directDropdown = document.getElementById('directDropdownUnique98765');
    
        const firstLabel = document.getElementById('firstLevelLabelUnique98765');
        const secondLabel = document.getElementById('secondLevelLabelUnique98765');
        const directLabel = document.getElementById('directLabelUnique98765');
    
        function resetAll() {
            firstContainer.style.display = 'none';
            secondContainer.style.display = 'none';
            directContainer.style.display = 'none';
    
            firstDropdown.innerHTML = '';
            secondDropdown.innerHTML = '';
            directDropdown.innerHTML = '';
    
            finalId.value = '';
        }
    
        assignTo.addEventListener('change', async function() {
    
            const type = this.value;
            assignableType.value = type;
            resetAll();
    
            if (!type) return;
    
            // ================= ORGANIZATION =================
            if (type === 'organization') {
                directContainer.style.display = 'block';
                directLabel.textContent = 'Select Organization';
    
                await loadOrganizations(directDropdown);
    
                directDropdown.onchange = function() {
                    finalId.value = this.value;
                };
            }
    
            // ================= GROUP (Org → Group) =================
            else if (type === 'group') {
    
                firstContainer.style.display = 'block';
                secondContainer.style.display = 'block';
    
                firstLabel.textContent = 'Select Organization';
                secondLabel.textContent = 'Select Group';
    
                await loadOrganizations(firstDropdown);
    
                firstDropdown.onchange = async function() {
    
                    const orgId = this.value;
                    if (!orgId) return;
    
                    await loadGroupsByOrg(orgId, secondDropdown);
    
                    secondDropdown.onchange = function() {
                        finalId.value = this.value;
                    };
                };
            }
    
            // ================= TEAM (Org → Group → Team) =================
            else if (type === 'team') {
    
                firstContainer.style.display = 'block';
                secondContainer.style.display = 'block';
    
                firstLabel.textContent = 'Select Organization';
                secondLabel.textContent = 'Select Group';
    
                await loadOrganizations(firstDropdown);
    
                firstDropdown.onchange = async function() {
    
                    const orgId = this.value;
                    if (!orgId) return;
    
                    await loadGroupsByOrg(orgId, secondDropdown);
    
                    secondDropdown.onchange = async function() {
    
                        const groupId = this.value;
                        if (!groupId) return;
    
                        createThirdDropdown('Select Team');
    
                        await loadTeamsByGroup(groupId, 'thirdDropdownBonus');
    
                        document.getElementById('thirdDropdownBonus').onchange = function() {
                            finalId.value = this.value;
                        };
                    };
                };
            }
    
            // ================= PLAYER (Org → Group → Team → Player) =================
            else if (type === 'student') {
    
                firstContainer.style.display = 'block';
                secondContainer.style.display = 'block';
    
                firstLabel.textContent = 'Select Organization';
                secondLabel.textContent = 'Select Group';
    
                await loadOrganizations(firstDropdown);
    
                firstDropdown.onchange = async function() {
    
                    const orgId = this.value;
                    if (!orgId) return;
    
                    await loadGroupsByOrg(orgId, secondDropdown);
    
                    secondDropdown.onchange = async function() {
    
                        const groupId = this.value;
                        if (!groupId) return;
    
                        createThirdDropdown('Select Team');
    
                        await loadTeamsByGroup(groupId, 'thirdDropdownBonus');
    
                        document.getElementById('thirdDropdownBonus').onchange = async function() {
    
                            const teamId = this.value;
                            if (!teamId) return;
    
                            createFourthDropdown('Select Player');
    
                            await loadStudentsByTeam(teamId, 'fourthDropdownBonus');
    
                            document.getElementById('fourthDropdownBonus').onchange = function() {
                                finalId.value = this.value;
                            };
                        };
                    };
                };
            }
    
        });
    
        // ================= HELPERS =================
    
        async function loadOrganizations(dropdown) {
            const res = await fetch('<?php echo e(route("api.organizations.list")); ?>');
            const data = await res.json();
    
            let opt = '<option value="">-- Select Organization --</option>';
            data.forEach(i => opt += `<option value="${i.id}">${i.name}</option>`);
    
            dropdown.innerHTML = opt;
        }
    
        async function loadGroupsByOrg(orgId, dropdown) {
            const res = await fetch('<?php echo e(route("api.groups.list")); ?>');
            const data = await res.json();
    
            let opt = '<option value="">-- Select Group --</option>';
    
            data.filter(g => g.organization_id == orgId)
                .forEach(g => opt += `<option value="${g.id}">${g.group_name}</option>`);
    
            dropdown.innerHTML = opt;
        }
    
        async function loadTeamsByGroup(groupId, dropdownId) {
            const res = await fetch('/api/teams/by-group/' + groupId);
            const data = await res.json();
    
            let opt = '<option value="">-- Select Team --</option>';
            data.forEach(t => opt += `<option value="${t.id}">${t.name}</option>`);
    
            document.getElementById(dropdownId).innerHTML = opt;
        }
    
        async function loadStudentsByTeam(teamId, dropdownId) {
            const res = await fetch('/api/students/by-team/' + teamId);
            const data = await res.json();
    
            let opt = '<option value="">-- Select Player --</option>';
            data.forEach(s => opt += `<option value="${s.id}">${s.name}</option>`);
    
            document.getElementById(dropdownId).innerHTML = opt;
        }
    
        function createThirdDropdown(label) {
            removeExtra();
    
            const div = document.createElement('div');
            div.className = 'mb-3 col-md-6';
            div.innerHTML = `
                <label class="form-label">${label}</label>
                <select class="form-select" id="thirdDropdownBonus"></select>
            `;
            secondContainer.after(div);
        }
    
        function createFourthDropdown(label) {
            const div = document.createElement('div');
            div.className = 'mb-3 col-md-6';
            div.innerHTML = `
                <label class="form-label">${label}</label>
                <select class="form-select" id="fourthDropdownBonus"></select>
            `;
            document.getElementById('thirdDropdownBonus').parentElement.after(div);
        }
    
        function removeExtra() {
            document.getElementById('thirdDropdownBonus')?.parentElement.remove();
            document.getElementById('fourthDropdownBonus')?.parentElement.remove();
        }
    
    });
    </script><?php /**PATH C:\Users\PC\Desktop\steam-two\resources\views/bonus/script.blade.php ENDPATH**/ ?>