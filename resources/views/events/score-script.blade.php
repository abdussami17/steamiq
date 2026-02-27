
@push('styles')
<style>
    /* Spreadsheet styles */
    .score-cell {
        cursor: pointer;
        transition: background-color 0.2s;
        text-align: center;
        position: relative;
    }

    .score-cell:hover {
        background-color: #f0f0f0;
    }

    .score-cell.selectable {
        cursor: crosshair;
    }

    .score-cell.selected {
        background-color: #cce5ff !important;
        border: 2px solid #007bff !important;
        box-shadow: inset 0 0 0 1px #007bff;
        color: #000
    }

    .team-score {
        background-color: #f8f9fa;
        font-weight: 500;
    }

    .student-score {
        background-color: #ffffff;
    }




    .cell-editor {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 2px solid #007bff !important;
        border-radius: 0;
        padding: 8px;
        font-size: inherit;
        font-family: inherit;
        text-align: center;
        background-color: white;
        z-index: 1000;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
    }

    .cell-editor:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(0,123,255,0.5);
    }

    .spreadsheet-toolbar {
        margin-bottom: 15px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }




   


    .total-cell {
        background-color: #e9ecef;
        font-weight: bold;
        color: #495057;
    }

    .total-cell strong {
        font-size: 1.1em;
    }

    /* Loading state */
    #scoreBody tr td[colspan] {
        text-align: center;
        padding: 40px;
        color: #6c757d;
        font-style: italic;
    }

 

    /* Score badges */
    .score-cell[data-points="0"] {
        color: #999;
        font-style: italic;
    }

    .score-cell[data-points]:not([data-points="0"]) {
        font-weight: 500;
    }

    /* Animation for updates */
    @keyframes highlight {
        0% { background-color: #fff3cd; }
        100% { background-color: transparent; }
    }

    .score-cell.updated {
        animation: highlight 1s ease;
    }

    /* Bulk edit mode indicator */
    .bulk-mode-active .score-cell {
        cursor: crosshair;
    }

    .bulk-mode-active .score-cell:hover {
        background-color: #e2e6ea;
    }



    /* Tooltip for instructions */
    .score-cell[title] {
        cursor: help;
    }

    /* Selection counter */
    .selection-counter {
        background-color: #007bff;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        margin-left: 5px;
    }


</style>
@endpush
<script>
    // Make sure all functions are globally available
    window.editingCell = null;
    window.bulkEditMode = false;
    window.selectedCells = new Set();
    
    document.addEventListener('DOMContentLoaded', () => {
        console.log('Score script loaded');
        const eventSelect = document.getElementById('eventSelect');
        if (eventSelect) {
            eventSelect.addEventListener('change', fetchScores);
            
            // Auto-load if there's a selected event
            if (eventSelect.value) {
                setTimeout(fetchScores, 100);
            }
        }
    
        // Add keyboard shortcuts
        document.addEventListener('keydown', handleKeyboardShortcuts);
    });
    
    async function fetchScores() {
        console.log('Fetching scores...');
        const eventSelect = document.getElementById('eventSelect');
        const eventId = eventSelect.value;
        const tbody = document.getElementById('scoreBody');
        const thead = document.getElementById('scoreHead');
    
        if (!eventId) {
            tbody.innerHTML = `<tr><td colspan="9">Please select an event</td></tr>`;
            return;
        }
    
        tbody.innerHTML = `<tr><td colspan="9">Loading...</td></tr>`;
    
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            
            if (!token) {
                throw new Error('CSRF token not found');
            }
    
            const res = await fetch(`/scores/fetch`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ event_id: eventId })
            });
    
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
    
            const data = await res.json();
            
            if (!data.table || !data.categories) {
                throw new Error('Invalid response structure');
            }
            
            window.currentTableData = data.table;
            window.currentCategories = data.categories;
            window.currentEventId = eventId;
            
            renderScoreTable(data.table, data.categories);
            
            // Add bulk edit toolbar after table is rendered
            setTimeout(addBulkEditToolbar, 200);
    
        } catch (error) {
            console.error('Error fetching scores:', error);
            tbody.innerHTML = `<tr><td colspan="9">Error loading scores: ${error.message}</td></tr>`;
        }
    }
    
    function renderScoreTable(tableData, categories) {
        console.log('Rendering table with', tableData.length, 'rows');
        const tbody = document.getElementById('scoreBody');
        const thead = document.getElementById('scoreHead');
    
        // Build dynamic table head
        let headHtml = '<tr>';
        headHtml += '<th style="width: 80px">Type</th>';
        headHtml += '<th style="width: 150px">Team</th>';
        headHtml += '<th style="width: 150px">Name</th>';
        
        categories.forEach(cat => {
            headHtml += `<th style="width: 100px">${cat.name}</th>`;
        });
        
        headHtml += '<th style="width: 80px">Total</th>';
        headHtml += '</tr>';
        
        thead.innerHTML = headHtml;
    
        if (!tableData || tableData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${categories.length + 4}">No scores available for this event</td></tr>`;
            return;
        }
    
        let bodyHtml = '';
        
        tableData.forEach((row, rowIndex) => {
            let total = 0;
            let scoresHtml = '';
            
            categories.forEach((cat, colIndex) => {
                const points = row.scores && row.scores[cat.id] ? row.scores[cat.id] : 0;
                total += points;
                scoresHtml += `<td class="score-cell ${row.type === 'team' ? 'team-score' : 'student-score'}" 
                                  data-row-type="${row.type}"
                                  data-row-id="${row.id}"
                                  data-team-id="${row.team_id || ''}"
                                  data-category-id="${cat.id}"
                                  data-points="${points}"
                                  data-row-index="${rowIndex}"
                                  data-col-index="${colIndex + 3}"
                                  ondblclick="startEditing(this)"
                                  onclick="handleCellClick(this, event)">${points}</td>`;
            });
    
            const typeDisplay = row.type === 'team' ? 'Team' : 'Student';
            const nameDisplay = row.name;
            const teamDisplay = row.team_name || '-';
            const rowClass = row.type === 'team' ? 'team-row fw-bold' : 'student-row';
            
            bodyHtml += `
                <tr class="${rowClass}" data-row-id="${row.id}" data-row-type="${row.type}">
                    <td>${typeDisplay}</td>
                    <td>${teamDisplay}</td>
                    <td>${nameDisplay}</td>
                    ${scoresHtml}
                    <td class="total-cell" data-row-id="${row.id}" data-row-type="${row.type}"><strong>${total}</strong></td>
                </tr>
            `;
        });
    
        tbody.innerHTML = bodyHtml;
        
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    }
    
    function addBulkEditToolbar() {
        console.log('Adding bulk edit toolbar');
        const toolbar = document.getElementById('scoreToolbar');
        if (!toolbar) {
            console.error('Toolbar not found');
            return;
        }
    
        // Remove existing bulk edit buttons if any
        const existingBulkBtn = document.getElementById('bulkEditBtn');
        if (existingBulkBtn) {
            existingBulkBtn.remove();
        }
        
        const existingApplyBtn = document.getElementById('applyBulkBtn');
        if (existingApplyBtn) {
            existingApplyBtn.remove();
        }
        
        const existingCancelBtn = document.getElementById('cancelBulkBtn');
        if (existingCancelBtn) {
            existingCancelBtn.remove();
        }
    
        // Create Bulk Edit button
        const bulkEditBtn = document.createElement('button');
        bulkEditBtn.id = 'bulkEditBtn';
        bulkEditBtn.className = 'btn btn-primary';
        bulkEditBtn.innerHTML = '<i data-lucide="edit-3"></i> Bulk Edit Mode';
        bulkEditBtn.onclick = toggleBulkEditMode;
        bulkEditBtn.style.marginLeft = '10px';
        bulkEditBtn.setAttribute('type', 'button');
    
        // Create Apply button (hidden initially)
        const applyBulkBtn = document.createElement('button');
        applyBulkBtn.id = 'applyBulkBtn';
        applyBulkBtn.className = 'btn btn-success';
        applyBulkBtn.innerHTML = '<i data-lucide="check"></i> Apply to Selected';
        applyBulkBtn.style.display = 'none';
        applyBulkBtn.onclick = applyBulkEdit;
        applyBulkBtn.style.marginLeft = '10px';
        applyBulkBtn.setAttribute('type', 'button');
    
        // Create Cancel button (hidden initially)
        const cancelBulkBtn = document.createElement('button');
        cancelBulkBtn.id = 'cancelBulkBtn';
        cancelBulkBtn.className = 'btn btn-secondary';
        cancelBulkBtn.innerHTML = '<i data-lucide="x"></i> Cancel';
        cancelBulkBtn.style.display = 'none';
        cancelBulkBtn.onclick = cancelBulkEdit;
        cancelBulkBtn.style.marginLeft = '10px';
        cancelBulkBtn.setAttribute('type', 'button');
    
        // Add buttons to toolbar
        toolbar.appendChild(bulkEditBtn);
        toolbar.appendChild(applyBulkBtn);
        toolbar.appendChild(cancelBulkBtn);
        
        // Reinitialize Lucide icons
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
        
        console.log('Bulk edit buttons added');
    }
    
    function toggleBulkEditMode() {
        console.log('Toggling bulk edit mode. Current:', bulkEditMode);
        bulkEditMode = !bulkEditMode;
        
        const bulkEditBtn = document.getElementById('bulkEditBtn');
        const applyBulkBtn = document.getElementById('applyBulkBtn');
        const cancelBulkBtn = document.getElementById('cancelBulkBtn');
        
        if (!bulkEditBtn || !applyBulkBtn || !cancelBulkBtn) {
            console.error('Bulk edit buttons not found');
            return;
        }
        
        if (bulkEditMode) {
            // Switching to bulk edit mode
            bulkEditBtn.classList.remove('btn-warning');
            bulkEditBtn.classList.add('btn-danger');
            bulkEditBtn.innerHTML = '<i data-lucide="edit-3"></i> Exit Bulk Edit';
            applyBulkBtn.style.display = 'inline-flex';
            cancelBulkBtn.style.display = 'inline-flex';
            
            // Clear any existing selection
            clearSelection();
            
            // Add selection class to all score cells
            document.querySelectorAll('.score-cell').forEach(cell => {
                cell.classList.add('selectable');
            });
            
            console.log('Bulk edit mode activated');
        } else {
            // Switching back to normal mode
            bulkEditBtn.classList.remove('btn-danger');
            bulkEditBtn.classList.add('btn-warning');
            bulkEditBtn.innerHTML = '<i data-lucide="edit-3"></i> Bulk Edit Mode';
            applyBulkBtn.style.display = 'none';
            cancelBulkBtn.style.display = 'none';
            
            // Clear selection
            clearSelection();
            
            // Remove selection class
            document.querySelectorAll('.score-cell').forEach(cell => {
                cell.classList.remove('selectable', 'selected');
            });
            
            console.log('Bulk edit mode deactivated');
        }
        
        // Reinitialize Lucide icons
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    }
    
    function handleCellClick(cell, event) {
        if (bulkEditMode) {
            event.preventDefault();
            event.stopPropagation();
            
            if (selectedCells.has(cell)) {
                selectedCells.delete(cell);
                cell.classList.remove('selected');
                console.log('Cell deselected, total selected:', selectedCells.size);
            } else {
                selectedCells.add(cell);
                cell.classList.add('selected');
                console.log('Cell selected, total selected:', selectedCells.size);
            }
        }
    }
    
    function clearSelection() {
        selectedCells.forEach(cell => {
            if (cell) cell.classList.remove('selected');
        });
        selectedCells.clear();
        console.log('Selection cleared');
    }
    
    async function applyBulkEdit() {
        if (selectedCells.size === 0) {
            alert('Please select at least one cell to edit');
            return;
        }
    
        const value = prompt(`Enter new score value for ${selectedCells.size} selected cell(s) (0-1200):`);
        if (value === null) return;
    
        const points = parseFloat(value);
        if (isNaN(points) || points < 0 || points > 1200) {
            alert('Please enter a valid number between 0 and 1200');
            return;
        }
    
        const updates = [];
        const updatedRows = new Set();
    
        selectedCells.forEach(cell => {
            if (!cell) return;
            
            updates.push({
                team_id: cell.dataset.rowType === 'team' ? cell.dataset.rowId : null,
                student_id: cell.dataset.rowType === 'student' ? cell.dataset.rowId : null,
                category_id: cell.dataset.categoryId,
                points: points
            });
    
            // Track rows that need total recalculation
            updatedRows.add(`${cell.dataset.rowType}_${cell.dataset.rowId}`);
        });
    
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            
            const res = await fetch('/scores/bulk-update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    event_id: window.currentEventId,
                    updates: updates
                })
            });
    
            const data = await res.json();
    
            if (data.success) {
                // Update cell values
                selectedCells.forEach(cell => {
                    if (cell) {
                        cell.textContent = points;
                        cell.dataset.points = points;
                    }
                });
    
                // Update totals for affected rows
                updatedRows.forEach(key => {
                    const [type, id] = key.split('_');
                    const totalCell = document.querySelector(`.total-cell[data-row-id="${id}"][data-row-type="${type}"]`);
                    if (totalCell) {
                        const rowCells = document.querySelectorAll(`.score-cell[data-row-id="${id}"][data-row-type="${type}"]`);
                        let total = 0;
                        rowCells.forEach(cell => {
                            total += parseFloat(cell.dataset.points) || 0;
                        });
                        totalCell.innerHTML = `<strong>${total}</strong>`;
                    }
                });
    
                // Clear selection
                clearSelection();
                
                alert(`Successfully updated ${selectedCells.size} cell(s)!`);
            } else {
                alert('Error: ' + data.message);
            }
    
        } catch (error) {
            console.error('Error in bulk update:', error);
            alert('Error performing bulk update: ' + error.message);
        }
    }
    
    function cancelBulkEdit() {
        if (bulkEditMode) {
            toggleBulkEditMode();
        }
    }
    
    function startEditing(cell) {
        if (bulkEditMode) {
            alert('Please exit bulk edit mode first to edit individual cells');
            return;
        }
        
        if (editingCell === cell) return;
        
        // Remove any existing editor
        if (editingCell) {
            cancelEditing();
        }
    
        const currentValue = cell.textContent;
        const input = document.createElement('input');
        input.type = 'number';
        input.value = currentValue;
        input.min = 0;
        input.max = 1200;
        input.step = 1;
        input.className = 'cell-editor';
        input.style.width = '100%';
        input.style.height = '100%';
        input.style.border = 'none';
        input.style.padding = '8px';
        input.style.textAlign = 'center';
        
        // Clear cell content and append input
        cell.textContent = '';
        cell.appendChild(input);
        input.focus();
        input.select();
        
        editingCell = cell;
    
        // Handle input events
        input.addEventListener('blur', () => {
            saveCellEdit(cell, input.value);
        });
    
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveCellEdit(cell, input.value);
            }
        });
    
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                e.preventDefault();
                cancelEditing();
            }
        });
    }
    
    async function saveCellEdit(cell, newValue) {
        if (!editingCell) return;
    
        const points = parseFloat(newValue);
        
        // Validate
        if (isNaN(points) || points < 0 || points > 1200) {
            alert('Please enter a valid number between 0 and 1200');
            cancelEditing();
            return;
        }
    
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            
            const res = await fetch('/scores/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    event_id: window.currentEventId,
                    team_id: cell.dataset.rowType === 'team' ? cell.dataset.rowId : null,
                    student_id: cell.dataset.rowType === 'student' ? cell.dataset.rowId : null,
                    category_id: cell.dataset.categoryId,
                    points: points
                })
            });
    
            const data = await res.json();
    
            if (data.success) {
                // Update cell
                cell.textContent = points;
                cell.dataset.points = points;
                
                // Update total for this row
                updateRowTotal(cell.dataset.rowId, cell.dataset.rowType);
                
                editingCell = null;
                
                // Highlight the updated cell
                cell.classList.add('updated');
                setTimeout(() => cell.classList.remove('updated'), 1000);
            } else {
                alert('Error: ' + data.message);
                cancelEditing();
            }
    
        } catch (error) {
            console.error('Error updating score:', error);
            alert('Error updating score: ' + error.message);
            cancelEditing();
        }
    }
    
    function updateRowTotal(rowId, rowType) {
        const rowCells = document.querySelectorAll(`.score-cell[data-row-id="${rowId}"][data-row-type="${rowType}"]`);
        const totalCell = document.querySelector(`.total-cell[data-row-id="${rowId}"][data-row-type="${rowType}"]`);
        
        if (totalCell && rowCells.length > 0) {
            let total = 0;
            rowCells.forEach(cell => {
                total += parseFloat(cell.dataset.points) || 0;
            });
            totalCell.innerHTML = `<strong>${total}</strong>`;
        }
    }
    
    function cancelEditing() {
        if (editingCell) {
            const originalValue = editingCell.dataset.points;
            editingCell.textContent = originalValue;
            editingCell = null;
        }
    }
    
    function handleKeyboardShortcuts(e) {
        // Ctrl+A for select all in bulk edit mode
        if (e.ctrlKey && e.key === 'a' && bulkEditMode) {
            e.preventDefault();
            document.querySelectorAll('.score-cell').forEach(cell => {
                selectedCells.add(cell);
                cell.classList.add('selected');
            });
            console.log('Selected all cells:', selectedCells.size);
        }
        
        // Escape to cancel bulk edit
        if (e.key === 'Escape' && bulkEditMode) {
            e.preventDefault();
            cancelBulkEdit();
        }
    }
    
    function refreshScores() {
        fetchScores();
    }
    </script>