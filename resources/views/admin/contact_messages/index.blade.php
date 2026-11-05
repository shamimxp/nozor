@extends('layouts.admin')
@section('title', 'Contact Messages')
@section('content')
<section class="content-main">
    <div class="card mb-4">
        <div class="card-header border-bottom p-1 d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Contact Messages</h4>
            @include('admin.partials.show_deleted_button', ['route' => route('admin.contact-messages.trash'), 'permission' => 'contact_message.deleted-list'])
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="example">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        var table = $('#example').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.contact-messages.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'first_name', name: 'first_name'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {data: 'subject', name: 'subject'},
                {data: 'message', name: 'message'},
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString() : '';
                    }
                },
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end'}
            ]
        });

        $('body').on('click', '.deleteContactMessage', function () {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Move to trash?',
                text: "You can restore this later from Show Deleted Data.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/contact-messages') }}/" + id,
                        success: function (data) {
                            table.draw();
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message || data.success,
                                customClass: { confirmButton: 'btn btn-success' }
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Cannot delete',
                                text: xhr.responseJSON?.message || 'Something went wrong!',
                                customClass: { confirmButton: 'btn btn-danger' }
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
