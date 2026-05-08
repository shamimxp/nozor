@extends('layouts.admin')
@section('title', 'Product Variations')
@section('content')
<div class="row" id="table-striped">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Variations</h4>
                <button type="button" class="btn btn-primary" id="createNewVariation">
                    <i data-feather="plus"></i> Add Variation
                </button>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="variationTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Variations</th>
                            <th>Values</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="variationForm" name="variationForm" class="form-horizontal">
                    @csrf
                    <input type="hidden" name="variation_id" id="variation_id">
                    
                    <div class="form-group row">
                        <label for="name" class="col-sm-4 col-form-label font-weight-bold">Variation Name:*</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Variation Name" value="" required="">
                            <span class="text-danger error-text name_error"></span>
                        </div>
                    </div>

                    <div class="form-group row align-items-start">
                        <label class="col-sm-4 col-form-label font-weight-bold">Add variation values:*</label>
                        <div class="col-sm-8" id="variationValuesWrapper">
                            <div class="d-flex mb-1 value-row">
                                <input type="text" class="form-control mr-1" name="values[]" required="">
                                <button type="button" class="btn btn-primary px-1 add-value-btn"><i data-feather="plus"></i></button>
                            </div>
                            <span class="text-danger error-text values_error"></span>
                        </div>
                    </div>

                    <div class="mt-2 text-right">
                        <button type="submit" class="btn btn-primary mr-1" id="saveBtn" value="create">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#variationTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.variations.index') }}",
            columns: [
                {data: 'name', name: 'name'},
                {data: 'values', name: 'values', orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (feather) feather.replace({ width: 14, height: 14 });
            }
        });

        $('#createNewVariation').click(function () {
            $('#saveBtn').val("create-variation");
            $('#saveBtn').text("Save");
            $('#variation_id').val('');
            $('#variationForm').trigger("reset");
            $('#modelHeading').html("Add Variation");
            
            // Reset values wrapper to 1 input
            $('#variationValuesWrapper').find('.value-row:not(:first)').remove();
            $('#variationValuesWrapper').find('.value-row:first input').val('');
            $('#variationValuesWrapper').find('.value-row:first button').removeClass('btn-danger remove-value-btn').addClass('btn-primary add-value-btn').html('<i data-feather="plus"></i>');
            
            $('.error-text').text('');
            $('#ajaxModel').modal('show');
            if(feather) feather.replace();
        });

        $('body').on('click', '.editVariation', function () {
            var id = $(this).data('id');
            $.get("{{ url('admin/variations') }}" +'/' + id + '/edit', function (data) {
                $('#modelHeading').html("Edit Variation");
                $('#saveBtn').val("update-variation");
                $('#saveBtn').text("Update");
                $('#variation_id').val(data.id);
                $('#name').val(data.name);
                
                // Populate values
                $('#variationValuesWrapper .value-row').remove();
                if(data.variation_values && data.variation_values.length > 0) {
                    $.each(data.variation_values, function(i, valObj) {
                        let btnClass = i === 0 ? 'btn-primary add-value-btn' : 'btn-danger remove-value-btn';
                        let icon = i === 0 ? 'plus' : 'minus';
                        let row = `
                            <div class="d-flex mb-1 value-row">
                                <input type="text" class="form-control mr-1" name="values[]" value="${valObj.value}" required="">
                                <button type="button" class="btn ${btnClass} px-1"><i data-feather="${icon}"></i></button>
                            </div>
                        `;
                        $('#variationValuesWrapper').prepend(row); // prepend to keep + at top if preferred, or append
                    });
                }
                
                // Re-init feather icons
                if(feather) feather.replace();
                
                $('.error-text').text('');
                $('#ajaxModel').modal('show');
            });
        });

        // Add/Remove value fields
        $('body').on('click', '.add-value-btn', function(){
            let row = `
                <div class="d-flex mb-1 value-row">
                    <input type="text" class="form-control mr-1" name="values[]" required="">
                    <button type="button" class="btn btn-danger px-1 remove-value-btn"><i data-feather="minus"></i></button>
                </div>
            `;
            $('#variationValuesWrapper').append(row);
            if(feather) feather.replace();
        });

        $('body').on('click', '.remove-value-btn', function(){
            $(this).closest('.value-row').remove();
        });

        $('#variationForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Sending...').attr('disabled', true);
            $('.error-text').text('');

            let id = $('#variation_id').val();
            let url = id ? "{{ url('admin/variations') }}" + "/" + id : "{{ route('admin.variations.store') }}";
            
            var formData = $(this).serialize();
            if(id) formData += "&_method=PATCH";

            $.ajax({
                data: formData,
                url: url,
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#variationForm').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    $('#saveBtn').text('Save').attr('disabled', false);
                    table.draw();
                    toastr.success(data.success);
                },
                error: function (data) {
                    $('#saveBtn').text($('#saveBtn').val() === 'create-variation' ? 'Save' : 'Update').attr('disabled', false);
                    if(data.status === 422){
                        let errors = data.responseJSON.errors;
                        $.each(errors, function(prefix, val){
                            $('span.'+prefix+'_error').text(val[0]);
                        });
                    }
                }
            });
        });

        $('body').on('click', '.deleteVariation', function () {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/variations') }}"+'/'+id,
                        success: function (data) {
                            table.draw();
                            toastr.success(data.success);
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
