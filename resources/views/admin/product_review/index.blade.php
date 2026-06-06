@extends('layouts.admin')
@section('title', 'Product Reviews')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Product Reviews</h4>
            </div>
            <div class="card-body table-responsive pt-2">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <table id="reviewTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Customer Name</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Reply</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $key => $review)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $review->product->name ?? 'N/A' }}</td>
                            <td>{{ $review->name }}</td>
                            <td>{{ $review->rating }} Stars</td>
                            <td>{{ Str::limit($review->comment, 50) }}</td>
                            <td>{{ Str::limit($review->reply, 50) }}</td>
                            <td>
                                <div class="custom-control custom-switch custom-control-inline">
                                    <input type="checkbox" class="custom-control-input changeStatus" id="customSwitch{{$review->id}}" data-id="{{$review->id}}" {{ $review->status == 1 ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="customSwitch{{$review->id}}"></label>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#replyModal{{ $review->id }}">
                                    Reply
                                </button>
                                <form action="{{ route('admin.product_reviews.destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Reply Modal -->
                        <div class="modal fade" id="replyModal{{ $review->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form action="{{ route('admin.product_reviews.reply', $review->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reply to Review</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Customer Comment:</strong><br>{{ $review->comment }}</p>
                                            <div class="form-group">
                                                <label for="reply">Admin Reply</label>
                                                <textarea name="reply" class="form-control" rows="4">{{ $review->reply }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save Reply</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#reviewTable').DataTable();

        $('.changeStatus').on('change', function() {
            var id = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;
            
            $.ajax({
                type: "POST",
                url: "{{ route('admin.product_reviews.status') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    status: status
                },
                success: function(response) {
                    toastr.success(response.message);
                },
                error: function() {
                    toastr.error('Something went wrong!');
                }
            });
        });
    });
</script>
@endpush
