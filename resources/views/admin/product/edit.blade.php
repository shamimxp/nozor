@extends('layouts.admin')
@section('title', 'Edit Product')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Edit Product: {{ $product->name }}</h4>
            </div>
            <div class="card-body pt-2">
                <form id="productEditForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $product->name }}" required>
                                <span class="text-danger error-text name_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="category_id">Category <span class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" class="form-control select2" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text category_id_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sub_category_id">Sub Category</label>
                                <select name="sub_category_id" id="sub_category_id" class="form-control select2">
                                    <option value="">Select Sub Category</option>
                                    @foreach($subcategories as $sub)
                                        <option value="{{ $sub->id }}" {{ $product->sub_category_id == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text sub_category_id_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="unit_id">Unit</label>
                                <select name="unit_id" id="unit_id" class="form-control select2">
                                    <option value="">Select Unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ $product->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="stock">Product Stock</label>
                                <input type="number" name="stock" id="stock" class="form-control" value="{{ $product->stock }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="selling_price">Selling Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="selling_price" id="selling_price" class="form-control" value="{{ $product->selling_price }}" required>
                                <span class="text-danger error-text selling_price_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cost_price">Cost Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="cost_price" id="cost_price" class="form-control" value="{{ $product->cost_price }}" required>
                                <span class="text-danger error-text cost_price_error"></span>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="discount_type">Discount Type</label>
                                <select name="discount_type" id="discount_type" class="form-control">
                                    <option value="" {{ $product->discount_type == '' ? 'selected' : '' }}>No Discount</option>
                                    <option value="percent" {{ $product->discount_type == 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                                    <option value="amount" {{ $product->discount_type == 'amount' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="discount_amount">Discount</label>
                                <input type="number" step="0.01" name="discount_amount" id="discount_amount" class="form-control" value="{{ $product->discount_amount }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="max_order_qty">Max Order Quantity</label>
                                <input type="number" name="max_order_qty" id="max_order_qty" class="form-control" value="{{ $product->max_order_qty }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mt-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="is_featured" id="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_featured">Is Featured?</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label for="short_description">Short Description</label>
                                <textarea name="short_description" id="short_description" class="form-control editor" rows="3">{{ $product->short_description }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="featured_image">Featured Image (One)</label>
                                <input type="file" name="featured_image" id="featured_image" class="form-control imageInput" accept="image/*">
                                <div class="mt-1">
                                    <img src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" width="100" class="img-thumbnail imagePreview" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gallery">Add More Images</label>
                                <input type="file" name="gallery[]" id="gallery" class="form-control" accept="image/*" multiple>
                                <div id="galleryPreview" class="mt-1 d-flex flex-wrap">
                                    @foreach($product->gallery as $img)
                                        <img src="{{ asset(config('imagepath.product') . $img->image) }}" width="80" class="img-thumbnail m-1">
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            <h5>Product Attributes</h5>
                            <hr>
                            <div id="attributeSection">
                                @foreach($attributes as $attribute)
                                @php
                                    $attrValue = $product->attributes->where('product_attribute_id', $attribute->id)->first();
                                @endphp
                                <div class="row align-items-center mb-1">
                                    <div class="col-md-3">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input attr-check" name="attributes_id[]" id="attr_{{ $attribute->id }}" value="{{ $attribute->id }}" {{ $attrValue ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="attr_{{ $attribute->id }}">{{ $attribute->name }}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" name="attribute_values[]" class="form-control attr-val" value="{{ $attrValue ? $attrValue->attribute_value : '' }}" placeholder="{{ $attribute->name }} Value" {{ $attrValue ? '' : 'disabled' }}>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary" id="saveBtn">Update Product</button>
                            <a href="{{ route('admin.product.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    $(document).ready(function() {
        CKEDITOR.config.versionCheck = false;
        CKEDITOR.replace('short_description');
        $('.select2').select2();

        $('#category_id').on('change', function() {
            let category_id = $(this).val();
            if (category_id) {
                $.ajax({
                    url: "{{ url('admin/get-subcategory') }}/" + category_id,
                    type: "GET",
                    success: function(data) {
                        $('#sub_category_id').empty();
                        $('#sub_category_id').append('<option value="">Select Sub Category</option>');
                        $.each(data, function(key, value) {
                            $('#sub_category_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            } else {
                $('#sub_category_id').empty();
            }
        });

        $('.attr-check').on('change', function() {
            let valInput = $(this).closest('.row').find('.attr-val');
            if ($(this).is(':checked')) {
                valInput.prop('disabled', false).attr('required', true);
            } else {
                valInput.prop('disabled', true).attr('required', false).val('');
            }
        });

        $('.imageInput').on('change', function(){
            let reader = new FileReader();
            let preview = $(this).closest('.form-group').find('.imagePreview');
            reader.onload = (e) => {
                preview.attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });

        $('#productEditForm').on('submit', function(e) {
            e.preventDefault();
            // Sync CKEditor data
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            $('#saveBtn').text('Updating...').attr('disabled', true);
            $('.error-text').text('');

            let id = "{{ $product->id }}";
            let formData = new FormData(this);

            $.ajax({
                url: "{{ url('admin/product') }}/" + id,
                type: 'POST', // Use POST with _method PUT for FormData
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    toastr.success(data.success);
                    setTimeout(() => {
                        window.location.href = "{{ route('admin.product.index') }}";
                    }, 1000);
                },
                error: function(data) {
                    $('#saveBtn').text('Update Product').attr('disabled', false);
                    if (data.status === 422) {
                        let errors = data.responseJSON.errors;
                        $.each(errors, function(prefix, val) {
                            $('span.' + prefix + '_error').text(val[0]);
                        });
                    } else {
                        toastr.error('Something went wrong!');
                    }
                }
            });
        });
    });
</script>
@endpush
