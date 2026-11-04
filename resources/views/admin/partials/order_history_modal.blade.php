<!-- Order History Modal -->
<div class="modal fade text-left" id="orderHistoryModal" tabindex="-1" role="dialog" aria-labelledby="orderHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white d-flex align-items-center" id="orderHistoryModalLabel">
                    <i data-feather="clock" class="mr-50" style="width: 18px; height: 18px;"></i>
                    <span id="orderHistoryModalTitle">Order Activity History</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.85;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2" id="orderHistoryModalBody" style="min-height: 250px;">
                <!-- Loader Placeholder -->
                <div class="text-center py-5" id="orderHistoryLoader">
                    <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                        <span class="sr-only">Loading history...</span>
                    </div>
                    <div class="mt-1 text-muted font-weight-bold">Fetching complete activity log...</div>
                </div>
            </div>
            <div class="modal-footer py-1">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Universal Order History Trigger
    $(document).on('click', '.view-order-history', function (e) {
        e.preventDefault();
        var btn = $(this);
        var orderId = btn.data('order-id') || btn.data('id');
        var orderType = btn.data('order-type') || btn.data('type') || 'pos_order';
        var orderNumber = btn.data('order-number') || '';

        var modal = $('#orderHistoryModal');
        var titleElem = $('#orderHistoryModalTitle');
        var bodyElem = $('#orderHistoryModalBody');

        titleElem.text('Activity History' + (orderNumber ? ' - ' + orderNumber : ''));
        bodyElem.html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="mt-1 text-muted font-weight-bold">Loading activity history...</div>
            </div>
        `);

        modal.modal('show');

        var url = "{{ route('admin.orders.history', ['type' => ':type', 'id' => ':id']) }}"
            .replace(':type', orderType)
            .replace(':id', orderId);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.success && res.html) {
                    bodyElem.html(res.html);
                    if (res.order_number) {
                        titleElem.text(res.type_label + ' History - ' + res.order_number);
                    }
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                } else {
                    bodyElem.html(`
                        <div class="alert alert-warning m-2">
                            Failed to load activity history: ${res.message || 'Unknown error'}
                        </div>
                    `);
                }
            },
            error: function (xhr) {
                var msg = 'Failed to load order history. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                bodyElem.html(`
                    <div class="alert alert-danger m-2">
                        <i data-feather="alert-circle" class="mr-50"></i> ${msg}
                    </div>
                `);
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
        });
    });
</script>
