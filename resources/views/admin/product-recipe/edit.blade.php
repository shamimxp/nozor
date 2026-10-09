@extends('layouts.admin')
@section('title', 'Edit Product Recipe')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Edit Product Recipe</h4>
                <a href="{{ route('admin.product-recipe.index') }}" class="btn btn-secondary"><i data-feather="arrow-left"></i> Back to List</a>
            </div>
            <div class="card-body pt-2">
                <form action="{{ route('admin.product-recipe.update', $recipe->id) }}" method="POST" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="row">
                        @include('admin.partials.product-recipe-fields', [
                            'showProductSelect' => true,
                            'products' => $products,
                            'raw_material' => $raw_material,
                            'recipe' => $recipe,
                        ])
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
    $(document).ready(function () {
        $('.select2').select2({
            placeholder: 'Select options',
            width: '100%'
        });
        if (window.ensureRawMaterialSelect) {
            window.ensureRawMaterialSelect();
        }
    });
</script>
@endpush
