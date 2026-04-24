<div class="modal fade text-left" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="paymentModalLabel">Add Payment</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="paymentForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="payable_id" id="modal_payable_id">
                    <input type="hidden" name="payable_type" id="modal_payable_type">
                    <input type="hidden" name="customer_id" id="modal_customer_id">

                    <div class="form-group">
                        <label>Customer:</label>
                        <input type="text" class="form-control" id="modal_customer_name" readonly>
                    </div>
                    <div class="form-group">
                        <label>Order Number:</label>
                        <input type="text" class="form-control" id="modal_order_number" readonly>
                    </div>
                    <div class="form-group">
                        <label>Due Amount:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">৳</span>
                            </div>
                            <input type="text" class="form-control" id="modal_due_amount" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Payment Amount: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">৳</span>
                            </div>
                            <input type="number" step="0.01" name="amount" class="form-control" id="modal_payment_amount" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Payment Date: <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Payment Method: <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-control" required>
                            <option value="Cash">Cash</option>
                            <option value="bKash">bKash</option>
                            <option value="Nagad">Nagad</option>
                            <option value="Bank">Bank Transfer</option>
                            <option value="Card">Card</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Note:</label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).on('click', '.payBtn', function() {
        let id = $(this).data('id');
        let customerId = $(this).data('customer-id');
        let customerName = $(this).data('customer-name');
        let orderNumber = $(this).data('order-number');
        let due = $(this).data('due');
        let type = $(this).data('type');

        $('#modal_payable_id').val(id);
        $('#modal_payable_type').val(type);
        $('#modal_customer_id').val(customerId);
        $('#modal_customer_name').val(customerName);
        $('#modal_order_number').val(orderNumber);
        $('#modal_due_amount').val(due);
        $('#modal_payment_amount').val(due).attr('max', due);
        
        $('#paymentModal').modal('show');
    });

    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serialize();
        let submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Processing...');

        $.ajax({
            url: "{{ route('admin.due-collection.store') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#paymentModal').modal('hide');
                    // Reload the table if it exists
                    if ($.fn.DataTable.isDataTable('#customDueTable')) {
                        $('#customDueTable').DataTable().ajax.reload();
                    }
                    if ($.fn.DataTable.isDataTable('#posDueTable')) {
                        $('#posDueTable').DataTable().ajax.reload();
                    }
                } else {
                    toastr.error(response.message);
                }
                submitBtn.prop('disabled', false).text('Save Payment');
            },
            error: function(xhr) {
                let msg = 'Something went wrong';
                if (xhr.status === 422) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
                submitBtn.prop('disabled', false).text('Save Payment');
            }
        });
    });
</script>
@endpush
