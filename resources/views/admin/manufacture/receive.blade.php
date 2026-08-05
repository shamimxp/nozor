@extends('layouts.admin')
@section('title', 'Receive Manufacture Orders')
@section('content')
<div class="row">
    <style>
        .custom-dark-btn {
            background-color: #283046 !important;
            border-color: #283046 !important;
            color: #fff !important;
        }
        .custom-dark-btn:hover {
            background-color: #1a233a !important;
        }
        .select-container .select2-container {
            width: 100% !important;
        }
        .select-container .select2-container .select2-selection--single {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            height: 38px !important; 
        }
        .select-container .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
        }
        .select-container .select2-container .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
    </style>
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Receive Manufacture Orders</h4>
            </div>
            <div class="card-body mt-2">
                
                <div class="row mb-2 align-items-end">
                    <div class="col-md-5">
                        <label>Scan Barcode (Manufacture ID)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i data-feather="maximize"></i></span>
                            </div>
                            <input type="text" id="barcode_input" class="form-control" placeholder="Scan Barcode Here..." autofocus>
                            <div class="input-group-append">
                                <button class="btn custom-dark-btn" type="button" id="btn-add-barcode">Add</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2 text-center">
                        <h4 class="mb-0">OR</h4>
                    </div>

                    <div class="col-md-5">
                        <label>Manual Selection</label>
                        <div class="d-flex">
                            <div class="select-container" style="flex-grow: 1;">
                                <select id="manual_select" class="form-control select2">
                                    <option value="">Select a Completed Order...</option>
                                    @foreach($pending_orders as $order)
                                        <option value="{{ $order->id }}" 
                                            data-invoice="{{ $order->invoice_no }}" 
                                            data-product="{{ $order->product->name ?? 'N/A' }}" 
                                            data-worker="{{ $order->worker->name ?? 'N/A' }}"
                                            data-qty="{{ $order->manufacture_qty }}"
                                            data-total="{{ number_format($order->grand_total, 2) }}">
                                            {{ $order->invoice_no }} - {{ $order->product->name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn custom-dark-btn" type="button" id="btn-add-manual" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">Add</button>
                        </div>
                    </div>
                </div>

                <hr>

                <h5 class="mb-1">Items to Receive</h5>
                <div class="table-responsive">
                    <table class="table table-bordered" id="receive-table">
                        <thead class="thead-light">
                            <tr>
                                <th>Invoice No</th>
                                <th>Product</th>
                                <th>Worker</th>
                                <th>Quantity</th>
                                <th>Total Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Items will be appended here -->
                        </tbody>
                    </table>
                </div>

                <div class="text-right mt-2">
                    <button type="button" class="btn btn-success" id="btn-process-receive" disabled>
                        <i data-feather="check-circle"></i> Process Receiving
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('.select2').length > 0) {
            $('.select2').select2();
        }

        let addedIds = [];

        function renderFeather() {
            if (typeof feather !== 'undefined') {
                feather.replace({ width: 14, height: 14 });
            }
        }

        function addOrderToTable(id, invoice, product, worker, qty, total) {
            if (addedIds.includes(id)) {
                toastr.warning('This order is already added to the list.');
                return;
            }

            addedIds.push(id);
            
            let tr = `
                <tr id="row-${id}">
                    <td>${invoice}</td>
                    <td>${product}</td>
                    <td>${worker}</td>
                    <td>${qty}</td>
                    <td>${total}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger btn-remove" data-id="${id}">
                            <i data-feather="trash-2"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#receive-table tbody').append(tr);
            renderFeather();
            updateProcessButton();
        }

        function updateProcessButton() {
            if (addedIds.length > 0) {
                $('#btn-process-receive').prop('disabled', false);
            } else {
                $('#btn-process-receive').prop('disabled', true);
            }
        }

        // Add from manual selection
        $('#btn-add-manual').click(function() {
            let selectedOption = $('#manual_select').find(':selected');
            let id = selectedOption.val();
            
            if (!id) {
                toastr.error('Please select an order first.');
                return;
            }

            let invoice = selectedOption.data('invoice');
            let product = selectedOption.data('product');
            let worker = selectedOption.data('worker');
            let qty = selectedOption.data('qty');
            let total = selectedOption.data('total');

            addOrderToTable(id, invoice, product, worker, qty, total);
            
            // Reset dropdown
            $('#manual_select').val('').trigger('change');
        });

        // Add from barcode scanner
        $('#barcode_input').on('keypress', function(e) {
            if (e.which == 13) { // Enter key
                e.preventDefault();
                $('#btn-add-barcode').click();
            }
        });

        $('#btn-add-barcode').click(function() {
            let id = $('#barcode_input').val().trim();
            if (!id) {
                return;
            }

            // Find if this ID exists in our pre-loaded select options to grab the details quickly
            let option = $('#manual_select option[value="'+id+'"]');
            
            if (option.length > 0) {
                let invoice = option.data('invoice');
                let product = option.data('product');
                let worker = option.data('worker');
                let qty = option.data('qty');
                let total = option.data('total');

                addOrderToTable(id, invoice, product, worker, qty, total);
                $('#barcode_input').val('').focus();
            } else {
                toastr.error('Order ID ' + id + ' not found or it is not completed yet.');
                $('#barcode_input').val('').focus();
            }
        });

        // Remove from table
        $(document).on('click', '.btn-remove', function() {
            let id = $(this).data('id').toString();
            addedIds = addedIds.filter(item => item !== id);
            $('#row-' + id).remove();
            updateProcessButton();
        });

        // Process Receiving
        $('#btn-process-receive').click(function() {
            if (addedIds.length === 0) return;

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to receive " + addedIds.length + " order(s).",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28c76f',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, process it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    // Show loading
                    let btn = $(this);
                    let originalHtml = btn.html();
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

                    $.ajax({
                        url: "{{ route('admin.manufacture.receive.process') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            manufacture_ids: addedIds
                        },
                        success: function(response) {
                            toastr.success(response.success);
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).html(originalHtml);
                            toastr.error(xhr.responseJSON.error || 'Something went wrong.');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
