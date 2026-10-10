@extends('layouts.admin')
@section('title', 'Edit Cost Setting')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Edit Cost Setting: <span class="text-primary">{{ $costSetting->cost_title }}</span></h4>
                <a href="{{ route('admin.cost-setting.index') }}" class="btn btn-secondary">
                    <i data-feather="arrow-left"></i> Back to List
                </a>
            </div>
            <div class="card-body pt-2">
                <form id="costSettingForm" action="{{ route('admin.cost-setting.update', $costSetting->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="cost_setting_id" value="{{ $costSetting->id }}">
                    
                    {{-- Header Inputs: Cost Title & Status --}}
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="cost_title" class="font-weight-bold">
                                    Cost Title <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           name="cost_title" 
                                           id="cost_title" 
                                           placeholder="Ex: 29 mm, 34 mm, 50 mm" 
                                           value="{{ old('cost_title', $costSetting->cost_title) }}" 
                                           autocomplete="off"
                                           required>
                                    <div class="input-group-append" id="title_spinner_wrap" style="display: none;">
                                        <span class="input-group-text bg-white">
                                            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                                        </span>
                                    </div>
                                </div>
                                <span class="font-small-2 d-block mt-25" id="cost_title_feedback"></span>
                                <span class="text-danger error-text cost_title_error font-small-2"></span>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label class="font-weight-bold d-block">Status</label>
                                <div class="custom-control custom-switch custom-switch-success mt-50">
                                    <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" {{ $costSetting->status == 1 ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="status">
                                        <span class="switch-icon-left"><i data-feather="check"></i></span>
                                        <span class="switch-icon-right"><i data-feather="x"></i></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 text-right d-flex flex-column justify-content-center">
                            <div class="p-1 rounded bg-light border text-center">
                                <span class="text-muted font-small-3 d-block">Estimated Total Cost</span>
                                <h3 class="font-weight-bolder text-primary mb-0 mt-25">
                                    ৳ <span id="total_cost_display">0.00</span>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <hr class="my-2">

                    {{-- Cost Heads List with Price Inputs --}}
                    <div style="max-width: 720px;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div>
                                <h5 class="mb-0 font-weight-bolder text-dark">
                                    <i data-feather="dollar-sign" class="mr-50"></i> Declare Price for Cost Heads
                                </h5>
                                <small class="text-muted">Modify unit cost/price against each cost head</small>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="fillZeroBtn">
                                    Clear All
                                </button>
                            </div>
                        </div>

                        @if($costHeads->isEmpty())
                            <div class="alert alert-warning p-2">
                                <h6 class="alert-heading font-weight-bold mb-50">No Cost Heads Found!</h6>
                                <span>Please create Cost Heads first from the <a href="{{ route('admin.cost-head.index') }}" class="font-weight-bold alert-link">Cost Head</a> menu.</span>
                            </div>
                        @else
                            <div class="table-responsive border rounded">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;" class="text-center">#</th>
                                            <th>Cost Head Title</th>
                                            <th style="width: 220px;">Cost Price (৳) <span class="text-danger">*</span></th>
                                            <th style="width: 100px;" class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($costHeads as $index => $head)
                                            @php
                                                $price = isset($itemPrices[$head->id]) ? $itemPrices[$head->id] : '0.00';
                                            @endphp
                                            <tr>
                                                <td class="text-center align-middle font-weight-bold">{{ $index + 1 }}</td>
                                                <td class="align-middle">
                                                    <input type="hidden" name="heads[{{ $index }}][cost_head_id]" value="{{ $head->id }}">
                                                    <span class="font-weight-bold text-dark">{{ $head->title }}</span>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text bg-light">৳</span>
                                                        </div>
                                                        <input type="number" 
                                                               step="0.01" 
                                                               min="0" 
                                                               name="heads[{{ $index }}][cost_price]" 
                                                               class="form-control cost-price-input" 
                                                               placeholder="0.00" 
                                                               value="{{ old('heads.'.$index.'.cost_price', ($price > 0 ? $price : '')) }}">
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    @if($head->status == 1)
                                                        <span class="badge badge-light-success">Active</span>
                                                    @else
                                                        <span class="badge badge-light-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light font-weight-bold">
                                        <tr>
                                            <td colspan="2" class="text-right">Total Declared Cost:</td>
                                            <td>
                                                <span class="text-primary font-weight-bolder">৳ <span id="table_total_display">0.00</span></span>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif

                        <div class="d-flex justify-content-end mt-2" style="gap: 10px;">
                            <a href="{{ route('admin.cost-setting.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i data-feather="save" class="mr-50"></i> Update Cost Setting
                            </button>
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let titleTimeout = null;
        let initialTitle = $('#cost_title').val().trim();
        let currentId = $('#cost_setting_id').val();

        // Recalculate total cost
        function calculateTotal() {
            let total = 0;
            $('.cost-price-input').each(function() {
                let val = parseFloat($(this).val());
                if (!isNaN(val) && val > 0) {
                    total += val;
                }
            });
            let formatted = total.toFixed(2);
            $('#total_cost_display').text(formatted);
            $('#table_total_display').text(formatted);
        }

        $(document).on('input', '.cost-price-input', function() {
            calculateTotal();
        });

        $('#fillZeroBtn').on('click', function() {
            $('.cost-price-input').val('');
            calculateTotal();
        });

        calculateTotal();

        // Real-time title validation with debounce
        $('#cost_title').on('input paste keyup', function() {
            let title = $(this).val().trim();
            clearTimeout(titleTimeout);
            $('.cost_title_error').text('');

            if (title.length === 0) {
                $('#cost_title').removeClass('is-invalid is-valid');
                $('#cost_title_feedback').text('').removeClass('text-success text-danger');
                $('#title_spinner_wrap').hide();
                return;
            }

            if (title === initialTitle) {
                $('#cost_title').removeClass('is-invalid').addClass('is-valid');
                $('#cost_title_feedback').text('').removeClass('text-danger');
                $('#title_spinner_wrap').hide();
                $('#submitBtn').attr('disabled', false);
                return;
            }

            $('#title_spinner_wrap').show();

            titleTimeout = setTimeout(function() {
                $.ajax({
                    url: "{{ route('admin.cost-setting.check-title') }}",
                    type: "GET",
                    data: { cost_title: title, id: currentId },
                    success: function(res) {
                        $('#title_spinner_wrap').hide();
                        if (res.exists) {
                            $('#cost_title').addClass('is-invalid').removeClass('is-valid');
                            $('#cost_title_feedback')
                                .removeClass('text-success')
                                .addClass('text-danger font-weight-bold')
                                .html('<i data-feather="x-circle" class="mr-25"></i> ' + res.message);
                            $('#submitBtn').attr('disabled', true);
                        } else {
                            $('#cost_title').addClass('is-valid').removeClass('is-invalid');
                            $('#cost_title_feedback')
                                .removeClass('text-danger')
                                .addClass('text-success font-weight-bold')
                                .html('<i data-feather="check-circle" class="mr-25"></i> ' + res.message);
                            $('#submitBtn').attr('disabled', false);
                        }
                        if (feather) { feather.replace({ width: 14, height: 14 }); }
                    },
                    error: function() {
                        $('#title_spinner_wrap').hide();
                    }
                });
            }, 350);
        });

        // Form Submit
        $('#costSettingForm').on('submit', function(e) {
            e.preventDefault();
            let $btn = $('#submitBtn');
            $btn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm mr-50"></span> Updating...');
            $('.error-text').text('');

            let formData = $(this).serialize();

            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.status) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 800);
                    } else {
                        toastr.error(response.message || 'Something went wrong.');
                        $btn.attr('disabled', false).html('<i data-feather="save" class="mr-50"></i> Update Cost Setting');
                        if (feather) feather.replace({ width: 14, height: 14 });
                    }
                },
                error: function(xhr) {
                    $btn.attr('disabled', false).html('<i data-feather="save" class="mr-50"></i> Update Cost Setting');
                    if (feather) feather.replace({ width: 14, height: 14 });

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            if (key === 'cost_title') {
                                $('#cost_title').addClass('is-invalid');
                                $('.cost_title_error').text(val[0]);
                            } else {
                                toastr.error(val[0]);
                            }
                        });
                    } else {
                        toastr.error(xhr.responseJSON?.message || 'Server error occurred.');
                    }
                }
            });
        });
    });
</script>
@endpush
