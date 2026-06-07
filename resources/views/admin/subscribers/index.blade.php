@extends('layouts.admin')
@section('title', 'Subscribers')
@section('content')
<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Subscribers</h2>
            <p>List of all newsletter subscribers.</p>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="example">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Date Subscribed</th>
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
        $('#example').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.subscribers.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'email', name: 'email'},
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
    });
</script>
@endpush
