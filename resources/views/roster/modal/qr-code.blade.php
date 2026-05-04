{{-- ═══════════════════════════════════════════════════════════════════════════
     QR CODE MODAL (Phase 2)
══════════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-qr-code me-2"></i>Roster QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4" id="qrModalBody">
                <div class="spinner-border text-primary"></div>
            </div>
            <div class="modal-footer justify-content-between">
                <small class="text-muted" id="qrModalMeta"></small>
                <a href="#" class="btn btn-primary d-none" id="qrDownloadBtn" download>
                   Download
                </a>
            </div>
        </div>
    </div>
</div>