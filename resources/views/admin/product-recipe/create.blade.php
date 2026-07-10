@extends('layouts.admin')
@section('title', 'Add Product Recipe')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Add Product Recipe</h4>
                <a href="{{ route('admin.product-recipe.index') }}" class="btn btn-secondary"><i data-feather="arrow-left"></i> Back to List</a>
            </div>
            <div class="card-body pt-2">
                <form action="{{ route('admin.product-recipe.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Select Product <span class="text-danger">*</span></label>
                            <select name="product_id" class="form-control select2" required>
                                <option value="">Select a Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Select Raw Materials</label>
                            <select name="raw_materials[]" id="raw_materials_select" class="form-control select2" multiple>
                                @foreach($raw_material as $material)
                                    <option value="{{ $material->id }}" data-type="{{ $material->materialType ? strtolower($material->materialType->name) : '' }}" data-grade="{{ $material->grade_value }}" data-price="{{ $material->price_per_unit }}">{{ $material->name }} ({{ $material->unit ? $material->unit->name : '' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Body Charge</label>
                            <input type="number" step="0.01" name="body_charge" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Finishing Charge</label>
                            <input type="number" step="0.01" name="finishing_charge" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Bearing</label>
                            <input type="number" step="0.01" name="bearing" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Stone</label>
                            <input type="number" step="0.01" name="stone" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Electric Bill</label>
                            <input type="number" step="0.01" name="electric_bill" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Gas Bill</label>
                            <input type="number" step="0.01" name="gas_bill" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Box Price</label>
                            <input type="number" step="0.01" name="box_price" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Carrying Charge</label>
                            <input type="number" step="0.01" name="carrying_charge" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Wire Price</label>
                            <input type="number" step="0.01" name="wire_price" class="form-control" value="0">
                        </div>
                        <div class="col-md-12 form-group">
                            <label>Manufacture Images (Multiple)</label>
                            <input type="file" name="manufacture_images[]" id="manufacture_images" class="form-control" multiple accept="image/*">
                            <div id="image_preview_container" class="mt-2 d-flex flex-wrap" style="gap: 10px;"></div>
                        </div>
                        <div class="col-md-12 mt-3 mb-3">
                            <div class="card border">
                                <div class="card-header bg-light p-2 border-bottom">
                                    <h4 class="card-title mb-0">Items Details</h4>
                                </div>
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover text-center align-middle mb-0">
                                            <thead class="table-secondary">
                                                <tr>
                                                    <th class="text-nowrap" style="min-width: 150px;">Material</th>
                                                    <th class="text-nowrap" style="min-width: 120px;">Thickness (T)</th>
                                                    <th class="text-nowrap" style="min-width: 100px;">Width (X)</th>
                                                    <th class="text-nowrap" style="min-width: 100px;">Height (Y)</th>
                                                    <th class="text-nowrap" style="min-width: 100px;">Area</th>
                                                    <th class="text-nowrap" style="min-width: 120px;">Grand Value</th>
                                                    <th class="text-nowrap" style="min-width: 150px;">Qty(Carbide/Iron)</th>
                                                </tr>
                                            </thead>
                                            <tbody id="items_table_body">
                                                <!-- Dynamic rows will be added here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Save Recipe</button>
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
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select options"
        });

        $('#raw_materials_select').on('change', function() {
            let selectedOptions = $(this).find('option:selected');
            let tbody = $('#items_table_body');
            
            let currentIds = [];
            tbody.find('tr').each(function() {
                let id = $(this).find('input[name="raw_material_id[]"]').val();
                if(id) currentIds.push(id.toString());
            });

            let selectedIds = [];
            selectedOptions.each(function() {
                selectedIds.push($(this).val().toString());
            });

            tbody.find('tr').each(function() {
                let id = $(this).find('input[name="raw_material_id[]"]').val();
                if (id && !selectedIds.includes(id.toString())) {
                    $(this).remove();
                }
            });

            selectedOptions.each(function() {
                let option = $(this);
                let id = option.val().toString();
                if (!currentIds.includes(id)) {
                    let name = option.text();
                    let cleanName = name.replace(/\s*\([^)]*\)$/, '');
                    let type = (option.data('type') || '').toLowerCase();
                    let grade = option.data('grade') || 0;
                    
                    let tr = `
                        <tr data-type="${type}" data-grade="${grade}">
                            <td>${cleanName}<input type="hidden" name="raw_material_id[]" value="${id}"></td>
                            <td><input type="number" step="0.01" name="thickness[]" class="form-control form-control-sm text-center row-thickness" value="2" readonly></td>
                            <td><input type="number" step="0.01" name="width[]" placeholder="0" class="form-control form-control-sm text-center row-width" value=""></td>
                            <td><input type="number" step="0.01" name="height[]" placeholder="0" class="form-control form-control-sm text-center row-height" value=""></td>
                            <td><input type="number" step="0.01" name="area[]" class="form-control form-control-sm text-center row-area" value="0" readonly></td>
                            <td><input type="number" step="any" name="grade_value[]" class="form-control form-control-sm text-center" value="${grade}" readonly></td>
                            <td><input type="number" step="0.01" name="qty[]" class="form-control form-control-sm text-center row-qty" value="0" readonly></td>
                        </tr>
                    `;
                    tbody.append(tr);
                }
            });
        });

        $(document).on('input', '.row-width, .row-height, .row-thickness', function() {
            let tr = $(this).closest('tr');
            let type = tr.data('type');
            let grade = parseFloat(tr.data('grade')) || 0;
            
            let t = parseFloat(tr.find('.row-thickness').val()) || 0;
            let w = parseFloat(tr.find('.row-width').val()) || 0;
            let h = parseFloat(tr.find('.row-height').val()) || 0;
            
            let area = 0;
            if (type.includes('carbait') || type.includes('carbaid') || type.includes('carbide')) {
                area = t * w * h;
            } else if (type.includes('iron')) {
                area = w * w * h;
            } else {
                area = t * w * h; 
            }
            
            tr.find('.row-area').val(area.toFixed(2));
            
            let qty = Math.round(area * grade);
            tr.find('.row-qty').val(qty);
        });

        let selectedFiles = new DataTransfer();

        $('#manufacture_images').on('change', function(e) {
            let files = e.target.files;
            if (files && files.length > 0) {
                $.each(files, function(i, file) {
                    selectedFiles.items.add(file);
                });
                
                this.files = selectedFiles.files;
                renderNewImagePreviews();
            }
        });

        function renderNewImagePreviews() {
            let container = $('#image_preview_container');
            container.empty();
            
            $.each(selectedFiles.files, function(i, file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    let img = $('<img>').attr('src', e.target.result)
                                        .addClass('rounded border')
                                        .css({ width: '80px', height: '80px', objectFit: 'cover' });
                    
                    let removeBtn = $('<button>')
                        .attr('type', 'button')
                        .addClass('btn btn-sm btn-danger position-absolute remove-new-image')
                        .css({ top: '-5px', right: '-5px', padding: '2px 5px', fontSize: '10px' })
                        .html('&times;')
                        .data('index', i);

                    let wrapper = $('<div>').addClass('position-relative').append(img).append(removeBtn);
                    container.append(wrapper);
                };
                reader.readAsDataURL(file);
            });
        }

        $(document).on('click', '.remove-new-image', function() {
            let index = $(this).data('index');
            let newFiles = new DataTransfer();
            
            $.each(selectedFiles.files, function(i, file) {
                if (i !== index) {
                    newFiles.items.add(file);
                }
            });
            
            selectedFiles = newFiles;
            $('#manufacture_images')[0].files = selectedFiles.files;
            renderNewImagePreviews();
        });
    });
</script>
@endpush
