<!DOCTYPE html>
<html class="loading" lang="en" id="mainHtml" data-textdirection="ltr">
<!-- BEGIN: Head-->
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <title>@yield('page-title','Amer Code') | {{config('app.name')}}</title>
    <link rel="apple-touch-icon" href="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <!-- BEGIN:CSS-->
     <link rel="stylesheet" type="text/css" href="{{ asset(mix('css/admin.css')) }}">
     <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/css/fieldora-theme.css') }}?v={{ time() }}">
    <!-- END: CSS-->
    @stack('styles')
    <script>
        document.getElementById('mainHtml').classList.add(localStorage.getItem('light-layout-current-skin'))
    </script>
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="">

<!-- BEGIN: Header-->
@include('dealer.inc.header')
<!-- END: Header-->
<!-- BEGIN: Main Menu-->
@include('dealer.inc.sidebar')
<!-- END: Main Menu-->
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        @yield('breadcrumb')
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Dashboard Ecommerce Starts -->
            @yield('content')
            <!-- Dashboard Ecommerce ends -->
        </div>
    </div>
</div>
<!-- END: Content-->

<div class="sidenav-overlay"></div>
<div class="drag-target"></div>
<!-- BEGIN: Footer-->
@include('dealer.inc.footer')

<!-- BEGIN: Page JS-->
<script src="{{ asset(mix('js/admin.js')) }}"></script>
<!-- END: Page JS-->
{!! Toastr::message() !!}
@stack('scripts')
<script>
    function applyFieldoraIcons() {
        // 1. Set Card Title Icons across ALL blade pages
        $('.card-title').each(function() {
            var $title = $(this);
            if ($title.find('.fieldora-title-badge').length === 0 && !$title.closest('.fieldora-stat-card').length) {
                var text = $title.text().trim().toLowerCase();
                var iconName = 'grid'; // default FieldOra SaaS icon
                
                if (text.includes('product') || text.includes('item')) iconName = 'box';
                else if (text.includes('web order')) iconName = 'globe';
                else if (text.includes('pos order') || text.includes('pos')) iconName = 'shopping-cart';
                else if (text.includes('dealer order') || text.includes('dealer')) iconName = 'truck';
                else if (text.includes('order')) iconName = 'shopping-bag';
                else if (text.includes('customer') || text.includes('party')) iconName = 'users';
                else if (text.includes('user')) iconName = 'user';
                else if (text.includes('worker') || text.includes('employee')) iconName = 'user-check';
                else if (text.includes('stock') || text.includes('inventory') || text.includes('adjustment')) iconName = 'layers';
                else if (text.includes('collection') || text.includes('payment') || text.includes('wallet') || text.includes('finance') || text.includes('due') || text.includes('balance') || text.includes('revenue')) iconName = 'dollar-sign';
                else if (text.includes('report') || text.includes('analytics') || text.includes('stat')) iconName = 'bar-chart-2';
                else if (text.includes('manufacture') || text.includes('fabric')) iconName = 'tool';
                else if (text.includes('role') || text.includes('permission') || text.includes('access')) iconName = 'shield';
                else if (text.includes('setting') || text.includes('setup')) iconName = 'settings';
                else if (text.includes('category') || text.includes('sub category')) iconName = 'tag';
                else if (text.includes('filter')) iconName = 'filter';
                else if (text.includes('history') || text.includes('statement')) iconName = 'clock';

                $title.prepend('<span class="fieldora-title-badge mr-1"><i data-feather="' + iconName + '"></i></span>');
            }
        });

        // 2. Set Button Icons across ALL blade pages
        $('button, a.btn, input[type="submit"]').each(function() {
            var $btn = $(this);
            if ($btn.find('i, svg').length === 0) {
                var text = $btn.text().trim().toLowerCase();
                var id = ($btn.attr('id') || '').toLowerCase();
                var href = ($btn.attr('href') || '').toLowerCase();
                var icon = '';

                if (text.includes('add') || text.includes('create') || text.includes('new') || href.includes('create')) {
                    icon = 'plus';
                } else if (text === 'filter' || id.includes('filter')) {
                    icon = 'filter';
                } else if (text === 'reset' || id.includes('reset')) {
                    icon = 'rotate-ccw';
                } else if (text.includes('export') || text.includes('download')) {
                    icon = 'download';
                } else if (text.includes('excel')) {
                    icon = 'file-text';
                } else if (text.includes('pdf')) {
                    icon = 'file';
                } else if (text.includes('save') || text.includes('submit')) {
                    icon = 'check';
                } else if (text.includes('cancel') || text.includes('close')) {
                    icon = 'x';
                } else if (text.includes('print')) {
                    icon = 'printer';
                } else if (text.includes('search')) {
                    icon = 'search';
                }

                if (icon) {
                    $btn.prepend('<i data-feather="' + icon + '" class="mr-50"></i>');
                }
            }
        });

        if (typeof feather !== 'undefined') {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    }

    $(document).ready(function() {
        applyFieldoraIcons();
    });

    $(window).on('load', function() {
        applyFieldoraIcons();
    });

    $(document).ajaxComplete(function() {
        applyFieldoraIcons();
    });
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
