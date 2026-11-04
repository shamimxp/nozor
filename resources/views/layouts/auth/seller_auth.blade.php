<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="WOOD Machinery secure portal login">
    <meta name="keywords" content="WOOD Machinery, dealer, worker, login">
    <meta name="author" content="WOOD Machinery">
    <title>@yield('page-title', 'Login') — WOOD Machinery</title>
    <link rel="apple-touch-icon" href="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}">
    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin')}}/app-assets/vendors/css/vendors.min.css">
    <!-- END: Vendor CSS-->
    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin')}}/app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="{{asset('admin')}}/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="{{asset('admin')}}/app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="{{asset('admin')}}/app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="{{asset('admin')}}/app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="{{asset('admin')}}/app-assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" type="text/css" href="{{asset('admin')}}/app-assets/css/themes/semi-dark-layout.css">
    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin')}}/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="{{asset('admin')}}/app-assets/css/plugins/forms/form-validation.css">
    <link rel="stylesheet" type="text/css" href="{{asset('admin')}}/app-assets/css/pages/page-auth.css">
    <!-- END: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin')}}/app-assets/vendors/css/extensions/toastr.min.css">
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('admin')}}/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/css/wm-auth.css') }}?v={{ filemtime(public_path('admin/assets/css/wm-auth.css')) }}">
    <!-- END: Custom CSS-->
</head>
<!-- END: Head-->
<!-- BEGIN: Body-->
<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
<!-- BEGIN: Content-->
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            @yield('content')
        </div>
    </div>
</div>
<!-- END: Content-->


<!-- BEGIN: Vendor JS-->
<script src="{{asset('admin')}}/app-assets/vendors/js/vendors.min.js"></script>
<!-- BEGIN Vendor JS-->
<script src="{{asset('admin')}}/app-assets/vendors/js/extensions/toastr.min.js"></script>

<!-- BEGIN: Page Vendor JS-->
<script src="{{asset('admin')}}/app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{asset('admin')}}/app-assets/js/core/app-menu.js"></script>
<script src="{{asset('admin')}}/app-assets/js/core/app.js"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
<script src="{{asset('admin')}}/app-assets/js/scripts/pages/page-auth-login.js"></script>
<!-- END: Page JS-->

{!! Toastr::message() !!}
@stack('scripts')
<script>
    $(window).on('load', function() {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    })
    @if ($errors->any())
    @foreach ($errors->all() as $error)
    toastr.error('{{$error}}', 'Error', {
        closeButton: true,
        progressBar: true,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut',
        timeOut: 5000,
    });
    @endforeach
    @endif
</script>
</body>
<!-- END: Body-->
</html>
