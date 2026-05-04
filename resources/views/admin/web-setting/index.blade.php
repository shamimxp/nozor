@extends('layouts.admin')
@section('title', 'Web Setting')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Update Web Settings</h4>
            </div>
            <div class="card-body pt-2">
                <form id="settingForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="slogan">Slogan</label>
                                <input type="text" name="slogan" id="slogan" class="form-control" value="{{ $setting->slogan }}" placeholder="Site Slogan">
                                <span class="text-danger error-text slogan_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="currency_symbol">Currency Symbol</label>
                                <input type="text" name="currency_symbol" id="currency_symbol" class="form-control" value="{{ $setting->currency_symbol }}" placeholder="৳">
                                <span class="text-danger error-text currency_symbol_error"></span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="contact_number_1">Contact Number 1</label>
                                <input type="text" name="contact_number_1" id="contact_number_1" class="form-control" value="{{ $setting->contact_number_1 }}" placeholder="Primary Phone Number">
                                <span class="text-danger error-text contact_number_1_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="contact_number_2">Contact Number 2</label>
                                <input type="text" name="contact_number_2" id="contact_number_2" class="form-control" value="{{ $setting->contact_number_2 }}" placeholder="Secondary Phone Number">
                                <span class="text-danger error-text contact_number_2_error"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="email">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ $setting->email }}" placeholder="Contact Email">
                                <span class="text-danger error-text email_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="office_hour">Office Hour</label>
                                <input type="text" name="office_hour" id="office_hour" class="form-control" value="{{ $setting->office_hour }}" placeholder="E.g. Sunday to Thursday: 9 am - 5 pm">
                                <span class="text-danger error-text office_hour_error"></span>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group mb-1">
                                <label for="address">Address</label>
                                <textarea name="address" id="address" class="form-control" rows="2" placeholder="Street Address">{{ $setting->address }}</textarea>
                                <span class="text-danger error-text address_error"></span>
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <h5 class="border-bottom pb-1">Social Links</h5>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="facebook">Facebook</label>
                                <input type="url" name="facebook" id="facebook" class="form-control" value="{{ $setting->facebook }}" placeholder="https://facebook.com/yourpage">
                                <span class="text-danger error-text facebook_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="twitter">Twitter</label>
                                <input type="url" name="twitter" id="twitter" class="form-control" value="{{ $setting->twitter }}" placeholder="https://twitter.com/yourhandle">
                                <span class="text-danger error-text twitter_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="instagram">Instagram</label>
                                <input type="url" name="instagram" id="instagram" class="form-control" value="{{ $setting->instagram }}" placeholder="https://instagram.com/yourhandle">
                                <span class="text-danger error-text instagram_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="linkedin">LinkedIn</label>
                                <input type="url" name="linkedin" id="linkedin" class="form-control" value="{{ $setting->linkedin }}" placeholder="https://linkedin.com/yourcompany">
                                <span class="text-danger error-text linkedin_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="youtube">YouTube</label>
                                <input type="url" name="youtube" id="youtube" class="form-control" value="{{ $setting->youtube }}" placeholder="https://youtube.com/yourchannel">
                                <span class="text-danger error-text youtube_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="whatsapp">WhatsApp (URL/Number)</label>
                                <input type="text" name="whatsapp" id="whatsapp" class="form-control" value="{{ $setting->whatsapp }}" placeholder="WhatsApp Number or Link">
                                <span class="text-danger error-text whatsapp_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="tiktok">TikTok</label>
                                <input type="url" name="tiktok" id="tiktok" class="form-control" value="{{ $setting->tiktok }}" placeholder="https://tiktok.com/@yourhandle">
                                <span class="text-danger error-text tiktok_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="pinterest">Pinterest</label>
                                <input type="url" name="pinterest" id="pinterest" class="form-control" value="{{ $setting->pinterest }}" placeholder="https://pinterest.com/yourhandle">
                                <span class="text-danger error-text pinterest_error"></span>
                            </div>
                        </div>

                    </div>

                    <div class="mt-2 text-right">
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save Settings</button>
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

        $('#settingForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Saving...').attr('disabled', true);
            $('.error-text').text('');

            $.ajax({
                url: "{{ route('admin.web-setting.update') }}",
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#saveBtn').text('Save Settings').attr('disabled', false);
                    toastr.success(data.success);
                },
                error: function (data) {
                    $('#saveBtn').text('Save Settings').attr('disabled', false);
                    if (data.status === 422) {
                        let errors = data.responseJSON.errors;
                        $.each(errors, function(prefix, val) {
                            $('span.' + prefix + '_error').text(val[0]);
                        });
                        toastr.error('Validation error. Please check fields.');
                    } else {
                        toastr.error(data.responseJSON.error || 'Something went wrong.');
                    }
                }
            });
        });
    });
</script>
@endpush
