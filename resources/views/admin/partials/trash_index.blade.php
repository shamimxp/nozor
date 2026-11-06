@extends('layouts.admin')
@section('title', $title)
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center flex-wrap w-100">
                    <h4 class="card-title mb-0">{{ $title }}</h4>
                    <div class="d-flex flex-wrap" style="gap: 0.4rem;">
                        <button type="button" class="btn btn-success btn-sm" id="bulkRestoreBtn" disabled>Restore Selected</button>
                        <button type="button" class="btn btn-danger btn-sm" id="bulkForceDeleteBtn" disabled>Delete Forever Selected</button>
                        <a href="{{ $backRoute }}" class="btn btn-outline-secondary btn-sm">Back to List</a>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="trashTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="trashCheckAll"></th>
                            <th>#</th>
                            <th>Record</th>
                            <th>Deleted By</th>
                            <th>Deleted At</th>
                            <th width="140">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        var table = $('#trashTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: @json($trashRoute),
            columns: [
                {data: 'checkbox', orderable: false, searchable: false},
                {data: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'record_label', name: 'id'},
                {data: 'deleted_by_name', orderable: false, searchable: false},
                {data: 'deleted_at_formatted', name: 'deleted_at'},
                {data: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function () {
                if (window.feather) feather.replace({ width: 14, height: 14 });
                syncBulkButtons();
            }
        });

        function selectedIds() {
            return $('.trash-row-check:checked').map(function () { return $(this).val(); }).get();
        }

        function syncBulkButtons() {
            var count = selectedIds().length;
            $('#bulkRestoreBtn, #bulkForceDeleteBtn').prop('disabled', count === 0);
            $('#trashCheckAll').prop('checked', $('.trash-row-check').length > 0 && $('.trash-row-check:checked').length === $('.trash-row-check').length);
        }

        $('#trashCheckAll').on('change', function () {
            $('.trash-row-check').prop('checked', $(this).is(':checked'));
            syncBulkButtons();
        });

        $('body').on('change', '.trash-row-check', syncBulkButtons);

        function restoreUrl(id) {
            return @json($restoreRoute).replace('__ID__', id);
        }
        function forceUrl(id) {
            return @json($forceRoute).replace('__ID__', id);
        }

        $('body').on('click', '.trash-restore-btn', function () {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Restore this record?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, restore',
                customClass: { confirmButton: 'btn btn-success', cancelButton: 'btn btn-outline-secondary ml-1' },
                buttonsStyling: false
            }).then(function (result) {
                if (!result.isConfirmed) return;
                $.post(restoreUrl(id)).done(function (data) {
                    table.draw(false);
                    toastr.success(data.message);
                }).fail(function (xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Restore failed');
                });
            });
        });

        $('body').on('click', '.trash-force-btn', function () {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Delete permanently?',
                text: 'If related records still exist, they will be soft-deleted together instead of permanent cascading delete.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, continue',
                customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-outline-secondary ml-1' },
                buttonsStyling: false
            }).then(function (result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: forceUrl(id),
                    type: 'DELETE'
                }).done(function (data) {
                    table.draw(false);
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message || 'Record permanently deleted.',
                        customClass: { confirmButton: 'btn btn-success' }
                    });
                }).fail(function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cannot delete',
                        text: xhr.responseJSON?.message || 'Delete failed',
                        customClass: { confirmButton: 'btn btn-danger' }
                    });
                });
            });
        });

        $('#bulkRestoreBtn').on('click', function () {
            var ids = selectedIds();
            if (!ids.length) return;
            Swal.fire({
                title: 'Restore selected records?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Restore',
                customClass: { confirmButton: 'btn btn-success', cancelButton: 'btn btn-outline-secondary ml-1' },
                buttonsStyling: false
            }).then(function (result) {
                if (!result.isConfirmed) return;
                $.post(@json($bulkRestoreRoute), { ids: ids }).done(function (data) {
                    table.draw(false);
                    toastr.success(data.message);
                }).fail(function (xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Bulk restore failed');
                });
            });
        });

        $('#bulkForceDeleteBtn').on('click', function () {
            var ids = selectedIds();
            if (!ids.length) return;
            Swal.fire({
                title: 'Permanently delete selected?',
                text: 'Related active records will be soft-deleted together when permanent delete is unsafe.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete Forever',
                customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-outline-secondary ml-1' },
                buttonsStyling: false
            }).then(function (result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: @json($bulkForceRoute),
                    type: 'POST',
                    data: { ids: ids, _method: 'DELETE' }
                }).done(function (data) {
                    table.draw(false);
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message || 'Selected records permanently deleted.',
                        customClass: { confirmButton: 'btn btn-success' }
                    });
                }).fail(function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cannot delete',
                        text: xhr.responseJSON?.message || 'Bulk delete failed',
                        customClass: { confirmButton: 'btn btn-danger' }
                    });
                });
            });
        });
    });
</script>
@endpush
