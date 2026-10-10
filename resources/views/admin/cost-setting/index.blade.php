@extends('layouts.admin')
@section('title', 'Cost Setting')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Cost Setting List</h4>
                <div>
                    <a href="{{ route('admin.cost-head.index') }}" class="btn btn-outline-secondary mr-50">
                        <i data-feather="list"></i> Cost Heads
                    </a>
                    <a href="{{ route('admin.cost-setting.create') }}" class="btn btn-primary">
                        <i data-feather="plus"></i> Add Cost Setting
                    </a>
                </div>
            </div>
            <div class="card-body pt-2">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped data-table w-100">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">SL</th>
                                <th>Cost Title</th>
                                <th>Cost Heads</th>
                                <th>Total Cost</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 130px;">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.cost-setting.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'cost_title', name: 'cost_title'},
                {data: 'heads_count', name: 'heads_count', orderable: false, searchable: false},
                {data: 'total_cost', name: 'total_cost', orderable: false, searchable: false},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (feather) {
                    feather.replace({
                        width: 14,
                        height: 14
                    });
                }
            }
        });

        // Change Status
        $('body').on('change', '.changeStatus', function() {
            let status = $(this).prop('checked') == true ? 1 : 0;
            let id = $(this).data('id');

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "{{ route('admin.cost-setting.status') }}",
                data: {
                    'status': status,
                    'id': id,
                    '_token': "{{ csrf_token() }}"
                },
                success: function(data) {
                    toastr.success(data.success);
                },
                error: function(xhr) {
                    toastr.error('Failed to update status');
                }
            });
        });

        // Delete Cost Setting
        $('body').on('click', '.deleteBtn', function() {
            let id = $(this).data('id');
            if (confirm('Are you sure you want to delete this cost setting?')) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('admin/cost-setting') }}/" + id,
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        table.draw();
                        toastr.success(response.success);
                    },
                    error: function(xhr) {
                        toastr.error('Failed to delete cost setting.');
                    }
                });
            }
        });
    });
</script>
@endpush
