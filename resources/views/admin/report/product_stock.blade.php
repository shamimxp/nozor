@extends('layouts.admin')
@section('title', 'Product Stock Report')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <h2 class="content-header-title float-left mb-0">Product Stock Report</h2>
        </div>
    </div>
    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <form id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Search by name...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category_id" id="category_id" class="form-control">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sub Category</label>
                                <select name="sub_category_id" id="sub_category_id" class="form-control">
                                    <option value="">All Sub Categories</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mt-2">
                                <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                                <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Export
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" id="exportExcel">Excel</a>
                                        <a class="dropdown-item" href="#" id="exportPdf">PDF</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-datatable p-2">
                <table class="table" id="reportTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Buy Price</th>
                            <th>Sale Price</th>
                            <th>Current Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        var table = $('#reportTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.report.product-stock') }}",
                data: function (d) {
                    d.name = $('#name').val();
                    d.category_id = $('#category_id').val();
                    d.sub_category_id = $('#sub_category_id').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name' },
                { data: 'category_name' },
                { data: 'sub_category_name' },
                { data: 'cost_price', render: $.fn.dataTable.render.number(',', '.', 2, 'TK ') },
                { data: 'selling_price', render: $.fn.dataTable.render.number(',', '.', 2, 'TK ') },
                { data: 'stock' },
                { data: 'stock_status' }
            ]
        });

        $('#filterBtn').click(function() {
            table.draw();
        });

        $('#resetBtn').click(function() {
            $('#filterForm')[0].reset();
            $('#sub_category_id').html('<option value="">All Sub Categories</option>');
            table.draw();
        });

        // Dependent Dropdown
        $('#category_id').change(function() {
            var category_id = $(this).val();
            if (category_id) {
                $.ajax({
                    url: "{{ url('/admin/get-subcategory') }}/" + category_id,
                    type: "GET",
                    success: function(data) {
                        $('#sub_category_id').empty();
                        $('#sub_category_id').append('<option value="">All Sub Categories</option>');
                        $.each(data, function(key, value) {
                            $('#sub_category_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            } else {
                $('#sub_category_id').html('<option value="">All Sub Categories</option>');
            }
        });

        $('#exportExcel').click(function(e) {
            e.preventDefault();
            var name = $('#name').val();
            var cat = $('#category_id').val();
            var sub = $('#sub_category_id').val();
            window.location.href = "{{ route('admin.report.export-product-stock-excel') }}?name=" + name + "&category_id=" + cat + "&sub_category_id=" + sub;
        });

        $('#exportPdf').click(function(e) {
            e.preventDefault();
            var name = $('#name').val();
            var cat = $('#category_id').val();
            var sub = $('#sub_category_id').val();
            window.location.href = "{{ route('admin.report.export-product-stock-pdf') }}?name=" + name + "&category_id=" + cat + "&sub_category_id=" + sub;
        });
    });
</script>
@endpush
