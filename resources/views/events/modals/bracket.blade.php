
<!-- ================= BRACKET MODAL ================= -->
<div class="modal fade" id="bracketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0" style="background:#0a0e1a;border-radius:16px;overflow:hidden;">

            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div>
                    <div id="bm-type-badge" class="mb-2"></div>
                    <h4 id="bm-title" class="fw-800 mb-1" style="color:#f1f5f9;font-weight:800;"></h4>
                    <div id="bm-meta" class="d-flex flex-wrap gap-2 mt-2"></div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">

                <!-- Loader -->
                <div id="bm-loader" class="text-center py-5">
                    <div class="spinner-border text-info" role="status"></div>
                    <p class="text-muted mt-2 small">Loading bracket...</p>
                </div>

                <!-- Error -->
                <div id="bm-error" class="alert alert-danger d-none"></div>

                <!-- XR Activities -->
                <div id="bm-activities" class="d-none mb-4"></div>

                <!-- Bracket Content -->
                <div id="bm-bracket" class="d-none"></div>

            </div>
        </div>
    </div>
</div>
