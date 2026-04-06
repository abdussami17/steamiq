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

            // ✅ ORGANIZATION
            if (type === 'organization') {
                directContainer.style.display = 'block';
                directLabel.textContent = 'Select Organization';

                const res = await fetch('{{ route('api.organizations.list') }}');
                const data = await res.json();

                let options = '<option value="">-- Select --</option>';
                data.forEach(i => {
                    options += `<option value="${i.id}">${i.name}</option>`;
                });

                directDropdown.innerHTML = options;

                directDropdown.onchange = function() {
                    finalId.value = this.value;
                };
            }

            // ✅ GROUP
            else if (type === 'group') {
                directContainer.style.display = 'block';
                directLabel.textContent = 'Select Group';

                const res = await fetch('{{ route('api.groups.list') }}');
                const data = await res.json();

                let options = '<option value="">-- Select --</option>';
                data.forEach(i => {
                    options += `<option value="${i.id}">${i.group_name}</option>`;
                });

                directDropdown.innerHTML = options;

                directDropdown.onchange = function() {
                    finalId.value = this.value;
                };
            }

            // ✅ TEAM (Group → Team)
            else if (type === 'team') {

                firstContainer.style.display = 'block';
                firstLabel.textContent = 'Select Group';

                const res = await fetch('{{ route('api.groups.list') }}');
                const groups = await res.json();

                let options = '<option value="">-- Select Group --</option>';
                groups.forEach(g => {
                    options += `<option value="${g.id}">${g.group_name}</option>`;
                });

                firstDropdown.innerHTML = options;

                firstDropdown.onchange = async function() {

                    secondContainer.style.display = 'block';
                    secondLabel.textContent = 'Select Team';

                    const res = await fetch('/api/teams/by-group/' + this.value);
                    const teams = await res.json();

                    let options = '<option value="">-- Select Team --</option>';
                    teams.forEach(t => {
                        options += `<option value="${t.id}">${t.name}</option>`;
                    });

                    secondDropdown.innerHTML = options;

                    secondDropdown.onchange = function() {
                        finalId.value = this.value;
                    };
                };
            }

            // ✅ STUDENT (Team → Student)
            else if (type === 'student') {

                firstContainer.style.display = 'block';
                firstLabel.textContent = 'Select Team';

                const res = await fetch('{{ route('api.teams.list') }}');
                const teams = await res.json();

                let options = '<option value="">-- Select Team --</option>';
                teams.forEach(t => {
                    options += `<option value="${t.id}">${t.name}</option>`;
                });

                firstDropdown.innerHTML = options;

                firstDropdown.onchange = async function() {

                    secondContainer.style.display = 'block';
                    secondLabel.textContent = 'Select Player';

                    const res = await fetch('/api/students/by-team/' + this.value);
                    const students = await res.json();

                    let options = '<option value="">-- Select Player --</option>';
                    students.forEach(s => {
                        options += `<option value="${s.id}">${s.name}</option>`;
                    });

                    secondDropdown.innerHTML = options;

                    secondDropdown.onchange = function() {
                        finalId.value = this.value;
                    };
                };
            }

        });

    });
</script>