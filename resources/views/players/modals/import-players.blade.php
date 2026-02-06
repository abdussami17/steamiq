<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Import Players</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Select Event</label>
                    <select id="importEvent" class="form-select">
                        <option hidden>Select Event</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload CSV / XLSX</label>
                    <input type="file" id="importFile" class="form-control" accept=".csv,.xlsx,.xls">
                </div>

                <div id="importLoading" class="text-center d-none">
                    <div class="spinner-border"></div>
                    <p class="mt-2">Importing...</p>
                </div>

                <div id="importResult" class="alert alert-success d-none"></div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="startImport()">Import</button>
            </div>

        </div>
    </div>
</div>
