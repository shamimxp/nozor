@extends('layouts.admin')

@section('title', 'Due Collection History')

@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Due Collection</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Payment History</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <section id="basic-datatable">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">Due Collection History</h4>
                        </div>
                        <div class="card-datatable p-2">
                            <table class="table" id="due-collection-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Payment For</th>
                                        <th>Method</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        $('#due-collection-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('admin.due-collection.index') }}",
                type: "GET",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'date', name: 'payment_date' },
                { data: 'customer_info', name: 'customer_info', orderable: false },
                { data: 'payment_for', name: 'payment_for', orderable: false },
                { data: 'method', name: 'payment_method' },
                { data: 'amount', name: 'amount' },
            ],
            order: [[1, 'desc']],
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            language: {
                paginate: {
                    previous: '&nbsp;',
                    next: '&nbsp;'
                }
            }
        });
    });
</script>
@endpush
