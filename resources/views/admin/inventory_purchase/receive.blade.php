@extends('layouts.admin')
@section('title', 'Receive Inventory')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title">Receive Inventory - {{ $purchase->purchase_number }}</h4>
            </div>
            <div class="card-body pt-2">
                <form id="receiveForm">
                    @csrf
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Vendor:</strong> {{ $purchase->vendor->name }}</div>
                        <div class="col-md-4"><strong>Date:</strong> {{ $purchase->purchase_date->format('d M, Y') }}</div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Ordered Quantity</th>
                                    <th>Receive Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>
                                        <input type="number" name="items[{{ $item->id }}][receive_qty]" 
                                               class="form-control" step="0.01" max="{{ $item->quantity }}" 
                                               value="{{ $item->quantity }}" required>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-success" id="receiveBtn">Confirm Reception</button>
                            <a href="{{ route('admin.inventory-purchase.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#receiveForm').on('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Confirm reception?',
                text: 'This will finalize the purchase and increase product stock.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, receive',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-outline-secondary ml-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                $('#receiveBtn').text('Processing...').attr('disabled', true);
                $.ajax({
                    url: "{{ route('admin.inventory-purchase.receive.store', $purchase->id) }}",
                    type: "POST",
                    data: $('#receiveForm').serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Received',
                            text: response.success,
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        }).then(function() {
                            window.location.href = "{{ route('admin.inventory-purchase.index') }}";
                        });
                    },
                    error: function(xhr) {
                        $('#receiveBtn').text('Confirm Reception').attr('disabled', false);
                        toastr.error(xhr.responseJSON.error || 'Something went wrong!');
                    }
                });
            });
        });
    });
</script>
@endpush
