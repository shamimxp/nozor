@extends('layouts.admin')
@section('title', 'Add Cost Setting')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Add Cost Setting</h4>
                <a href="{{ route('admin.cost-setting.index') }}" class="btn btn-secondary">
                    <i data-feather="arrow-left"></i> Back to List
                </a>
            </div>
            <div class="card-body pt-2">
                <form id="costSettingForm" action="{{ route('admin.cost-setting.store') }}" method="POST">
                    @csrf
                    
                    {{-- Header Inputs: Cost Title & Status --}}
                    <div class="row mb-2 align-items-center">
                        <div class="col-lg-6 col-md-5">
                            <div class="form-group mb-1 mb-md-0">
                                <label for="cost_title" class="font-weight-bold">
                                    Cost Title <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           name="cost_title" 
                                           id="cost_title" 
                                           placeholder="Ex: 29 mm, 34 mm, 50 mm" 
                                           value="{{ old('cost_title') }}" 
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

                        <div class="col-lg-2 col-md-3 text-md-center">
                            <div class="form-group mb-1 mb-md-0">
                                <label class="font-weight-bold d-block mb-50">Status</label>
                                <div class="custom-control custom-switch custom-switch-success d-inline-block">
                                    <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" checked>
                                    <label class="custom-control-label" for="status">
                                        <span class="switch-icon-left"><i data-feather="check"></i></span>
                                        <span class="switch-icon-right"><i data-feather="x"></i></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4">
                            <div class="p-1 rounded bg-light border d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="text-muted font-small-2 text-uppercase font-weight-bold d-block">Estimated Total Cost</span>
                                    <span class="font-small-1 text-muted" id="declared_heads_count_badge">0 of {{ $costHeads->count() }} heads declared</span>
                                </div>
                                <h3 class="font-weight-bolder text-primary mb-0">
                                    ৳ <span id="total_cost_display">0.00</span>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <hr class="my-2">

                    {{-- Cost Heads List with Price Inputs --}}
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <h5 class="mb-0 font-weight-bolder text-dark">
                                <i data-feather="dollar-sign" class="mr-50 text-primary"></i> Declare Price for Cost Heads
                            </h5>
                            <small class="text-muted">Enter unit cost/price against each cost head under this cost title</small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="fillZeroBtn">
                                <i data-feather="rotate-ccw" class="mr-25"></i> Clear All
                            </button>
                        </div>
                    </div>

                    @if($costHeads->isEmpty())
                        <div class="alert alert-warning p-2">
                            <h6 class="alert-heading font-weight-bold mb-50">No Cost Heads Found!</h6>
                            <span>Please create active Cost Heads first from the <a href="{{ route('admin.cost-head.index') }}" class="font-weight-bold alert-link">Cost Head</a> menu.</span>
                        </div>
                    @else
                        @php
                            $chunkCount = $costHeads->count() >= 4 ? 2 : 1;
                            $splitHeads = $costHeads->split($chunkCount);
                            $runningIndex = 0;
                        @endphp
                        <div class="row">
                            @foreach($splitHeads as $chunk)
                                <div class="col-lg-6 col-12 mb-1">
                                    <div class="table-responsive border rounded bg-white shadow-none">
                                        <table class="table table-hover table-striped mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 45px;" class="text-center">#</th>
                                                    <th>Cost Head Title</th>
                                                    <th style="width: 175px;">Cost Price (৳) <span class="text-danger">*</span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($chunk as $head)
                                                    @php
                                                        $index = $runningIndex++;
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center align-middle font-weight-bold text-muted">{{ $index + 1 }}</td>
                                                        <td class="align-middle">
                                                            <input type="hidden" name="heads[{{ $index }}][cost_head_id]" value="{{ $head->id }}">
                                                            <span class="font-weight-bold text-dark">{{ $head->title }}</span>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text bg-light font-weight-bold">৳</span>
                                                                </div>
                                                                <input type="number" 
                                                                       step="0.01" 
                                                                       min="0" 
                                                                       name="heads[{{ $index }}][cost_price]" 
                                                                       class="form-control cost-price-input" 
                                                                       placeholder="0.00" 
                                                                       value="{{ old('heads.'.$index.'.cost_price') }}">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Bottom Total & Actions Bar --}}
                        <div class="card border bg-light shadow-none mt-1 mb-1">
                            <div class="card-body p-1 d-flex flex-wrap justify-content-between align-items-center" style="gap: 15px;">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-light-primary p-50 mr-1 rounded">
                                        <div class="avatar-content"><i data-feather="check-circle" class="font-medium-3 text-primary"></i></div>
                                    </div>
                                    <div>
                                        <span class="text-muted font-small-2 text-uppercase font-weight-bold d-block">Grand Total Declared Cost</span>
                                        <h4 class="font-weight-bolder text-primary mb-0">
                                            ৳ <span id="table_total_display">0.00</span>
                                        </h4>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center" style="gap: 10px;">
                                    <a href="{{ route('admin.cost-setting.index') }}" class="btn btn-outline-secondary">
                                        <i data-feather="x" class="mr-25"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i data-feather="save" class="mr-25"></i> Save Cost Setting
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
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
        let isTitleValid = false;

        // Recalculate total cost
        function calculateTotal() {
            let total = 0;
            let declaredCount = 0;
            let totalCount = $('.cost-price-input').length;
            $('.cost-price-input').each(function() {
                let val = parseFloat($(this).val());
                if (!isNaN(val) && val > 0) {
                    total += val;
                    declaredCount++;
                }
            });
            let formatted = total.toFixed(2);
            $('#total_cost_display').text(formatted);
            $('#table_total_display').text(formatted);
            $('#declared_heads_count_badge').text(declaredCount + ' of ' + totalCount + ' heads declared');
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
                isTitleValid = false;
                return;
            }

            $('#title_spinner_wrap').show();

            titleTimeout = setTimeout(function() {
                $.ajax({
                    url: "{{ route('admin.cost-setting.check-title') }}",
                    type: "GET",
                    data: { cost_title: title },
                    success: function(res) {
                        $('#title_spinner_wrap').hide();
                        if (res.exists) {
                            $('#cost_title').addClass('is-invalid').removeClass('is-valid');
                            $('#cost_title_feedback')
                                .removeClass('text-success')
                                .addClass('text-danger font-weight-bold')
                                .html('<i data-feather="x-circle" class="mr-25"></i> ' + res.message);
                            isTitleValid = false;
                            $('#submitBtn').attr('disabled', true);
                        } else {
                            $('#cost_title').addClass('is-valid').removeClass('is-invalid');
                            $('#cost_title_feedback')
                                .removeClass('text-danger')
                                .addClass('text-success font-weight-bold')
                                .html('<i data-feather="check-circle" class="mr-25"></i> ' + res.message);
                            isTitleValid = true;
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
            $btn.attr('disabled', true).html('<span class="spinner-border spinner-border-sm mr-50"></span> Saving...');
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
                        $btn.attr('disabled', false).html('<i data-feather="save" class="mr-50"></i> Save Cost Setting');
                        if (feather) feather.replace({ width: 14, height: 14 });
                    }
                },
                error: function(xhr) {
                    $btn.attr('disabled', false).html('<i data-feather="save" class="mr-50"></i> Save Cost Setting');
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
