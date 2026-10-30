<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-light py-1">
                <h5 class="modal-title font-weight-bolder text-primary mb-0" id="imagePreviewModalTitle">Image Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-2" style="background: #f8fafc; max-height: 75vh; overflow-y: auto;">
                <div class="d-inline-block p-1 bg-white shadow-sm rounded" style="border: 1px solid #e2e8f0; max-width: 100%;">
                    <img id="imagePreviewModalImg" src="" alt="Preview" style="max-width: 100%; max-height: 60vh; object-fit: contain; border-radius: 4px; display: block; margin: 0 auto;">
                </div>
            </div>
            <div class="modal-footer py-1 d-flex justify-content-between align-items-center">
                <span class="small text-muted"><i data-feather="zoom-in" style="width: 13px; height: 13px;"></i> Click outside or Close to exit</span>
                <div>
                    <a id="imagePreviewModalDownloadBtn" href="#" download class="btn btn-sm btn-primary mr-50" target="_blank">
                        <i data-feather="external-link" style="width: 13px; height: 13px;" class="mr-25"></i> Open Full Image
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.img-preview-trigger {
    cursor: pointer !important;
    transition: transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}
.img-preview-trigger:hover {
    transform: scale(1.08);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18) !important;
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '.img-preview-trigger', function(e) {
        e.preventDefault();
        let src = $(this).attr('data-preview-url') || $(this).attr('src');
        let title = $(this).attr('data-preview-title') || 'Image Preview';
        if (!src) return;
        $('#imagePreviewModalImg').attr('src', src);
        $('#imagePreviewModalTitle').text(title);
        $('#imagePreviewModalDownloadBtn').attr('href', src);
        $('#imagePreviewModal').modal('show');
        if (typeof feather !== 'undefined') {
            feather.replace({ width: 14, height: 14 });
        }
    });
});
</script>
@endpush
