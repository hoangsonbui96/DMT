<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="{{ asset('imgs/compary-icon.ico') }}">

        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="{{ asset('themes/adminlte/bootstrap/dist/css/bootstrap.min.css') }}">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('themes/adminlte/font-awesome/css/font-awesome.min.css') }}">

        <!-- Ionicons -->
        <link rel="stylesheet" href="{{ asset('themes/adminlte/Ionicons/css/ionicons.min.css') }}">
        <!-- jvectormap -->
        <link rel="stylesheet" href="{{ asset('themes/adminlte/jvectormap/jquery-jvectormap.css') }}">
        <!-- bootstrap datepicker -->
        <link rel="stylesheet" href="{{ asset('themes/adminlte/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
        <!-- iCheck for checkboxes and radio inputs -->
        <link rel="stylesheet" href="{{ asset('themes/adminlte/iCheck/all.css') }}">
        <!-- Bootstrap Color Picker -->
        <link rel="stylesheet" href="{{ asset('themes/adminlte/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
        <!-- Bootstrap time Picker -->
        <link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
        <!-- Bootstrap select -->
        <link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap-toggle.min.css') }}">

        <!-- Jquery Comfirm -->
        <link rel="stylesheet" href="{{ asset('css/jquery-confirm.min.css') }}">

        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('themes/adminlte/dist/css/AdminLTE.min.css') }}">
        <!-- AdminLTE Skins. Choose a skin from the css/skins
             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="{{ asset('themes/adminlte/dist/css/skins/_all-skins.min.css') }}">
        <script src="https://kit.fontawesome.com/135b549aa7.js" crossorigin="anonymous"></script>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Google Font -->
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <!-- Custom Css -->
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <script type="text/javascript" src="{{ asset('js/basket.full.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('themes/adminlte/jquery/dist/jquery.min.js') }}"></script>
        {{--demo--}}
        <script type="text/javascript">
            let workingsheduleIot = false;
            var confirmMsg = 'Bạn có chắc muốn xóa?';
            var LOGIN_URL = '{{ route('login') }}';
            var FOMAT_DATE = 'DD/MM/YYYY';
            var FOMAT_MOTH = 'MM/YYYY';

            var route_prefix = "{{ asset('') }}" + "/{{ $company }}/laravel-filemanager";
            const URL = `{{ asset("") }}`;
            @if ($currentRouteName == 'login')
                basket.clear();
            @endif

            basket.require(
                // { url: "{{ asset('themes/adminlte/jquery/dist/jquery.min.js') }}" },
                { url: "{{ asset('themes/adminlte/bootstrap/dist/js/bootstrap.min.js') }}" },
                { url: "{{ asset('themes/adminlte/moment/min/moment.min.js') }}" },
            ).then(function() {

                basket.require(
                    { url: "{{ asset('js/jquery-ui.min.js') }}" },
                    { url: "{{ asset('js/bootstrap-toggle.min.js') }}" },
                    { url: "{{ asset('themes/adminlte/fastclick/lib/fastclick.js') }}" },
                    { url: "{{ asset('themes/adminlte/dist/js/adminlte.min.js') }}" },
                    { url: "{{ asset('themes/adminlte/jquery-sparkline/dist/jquery.sparkline.min.js') }}" },
                    { url: "{{ asset('themes/adminlte/jvectormap/jquery-jvectormap-1.2.2.min.js') }}" },
                    { url: "{{ asset('themes/adminlte/jvectormap/jquery-jvectormap-world-mill-en.js') }}" },
                    { url: "{{ asset('themes/adminlte/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}" },
                    { url: "{{ asset('themes/adminlte/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}" },
                    { url: "{{ asset('themes/adminlte/iCheck/icheck.min.js') }}" },
                    { url: "{{ asset('themes/adminlte/jquery-slimscroll/jquery.slimscroll.min.js') }}" },
                    { url: "{{ asset('themes/adminlte/moment/moment.js') }}" },
                    { url: "{{ asset('themes/adminlte/moment/locale/vi.js') }}" },
                    { url: "{{ asset('js/bootstrap-datetimepicker.min.js') }}" },
                    { url: "{{ asset('js/bootstrap-select.min.js') }}" },
                    { url: "{{ asset('js/jquery-confirm.min.js') }}" },
                    { url: "{{ asset('js/common.js') }}" },
                    { url: "{{ asset('js/admin.js') }}" },
                    { url: "{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}" },
                    { url: "{{ asset('js/amlich-hnd.js') }}"},
                ).then(function() {

                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    InitDatePicker();

                    $(function () {

                        $('input[type=search]:eq(0)').focus();

                        $('form').submit(function () {
                            $('.loadajax').show();
                        });

                        $('button').click(function (e) {
                            $(this).blur();
                        });

                        $('.sort-link').each(function () {
                            var iconAdd = '<i class="fa fa-caret-down"></i>';
                            var sortValue = $(this).attr('data-sort');
                            var urlCol = $(this).attr('data-link');
                            var urlCurrent = location.href;
                            var urlPage = urlCol + sortValue;
                            $(this).attr('href', urlPage);
                            if (urlCurrent.startsWith(urlCol)) {
                                iconAdd = sortValue.startsWith('asc') ? iconAdd : '<i class="fa fa-caret-up"></i>';
                            }
                            $(this).html($(this).text() + ' ' + iconAdd);
                        });

                        $('.loadajax').hide();
                    });
                }, function (error) {
                // There was an error fetching the script
                console.log(error);
                });
            }, function (error) {
                // There was an error fetching the script
                console.log(error);
            });
        </script>
        @stack('pageCss')
        @stack('pageJs')
        <script>
            $(function() {
                var currentwidth = $(window).width();
                if(currentwidth <= 1366) {
                    $('body').addClass('sidebar-collapse');
                } else {
                    $('body').removeClass('sidebar-collapse');
                }

                $(window).resize(function(){
                    var currentwidth = $(window).width();
                    if(currentwidth <= 1366) {
                        $('body').addClass('sidebar-collapse');
                    } else {
                        $('body').removeClass('sidebar-collapse');
                    }
                });
                window.setTimeout(function() {
                    $(".custom-alert").slideUp(500, function() {
                        $(this).remove();
                    });
                }, 2500);
            });
        </script>
    </head>
    @if($currentRouteName != 'login' && $currentRouteName != 'qrCode')
    <body class="hold-transition skin-blue sidebar-mini fixed">
        <div id="wrapper">
            @if($currentRouteName != 'login' && $currentRouteName != 'qrCode')
                @include('admin.layouts.'.config('settings.template').'.header')
            @endif
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <div id="noti-personal">
{{--                    @include('admin.includes.notification')--}}
                </div>
                @yield('content')
            </div>
            @if(Session::has('error_ip'))
                <script>
                    showErrors("{{Session::get('error_ip')}}");
                </script>

                {{ Session::forget('error_ip') }}
                {{ Session::save() }}
             @endif
            {{-- @endif --}}
            <!-- /.content-wrapper -->
        </div>
    @else
    <body class="hold-transition login-page">
        @yield('content')
    @endif
        @if (session('alert'))
            <div class="toast custom-alert" style="position: absolute; bottom: 1px; right: 2px; width: 40rem; padding: 10px; background-color: #6c757d!important; color: white">
                <div class="toast-header">
                    <strong class="mr-auto">AKB Office</strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    {{ \Illuminate\Support\Facades\Session::get('alert') }}
                </div>
            </div>
        @endif
    <div id="popupModal"></div>
    <div class="loadajax">
        <i class="fa fa-spinner fa-spin"></i>
    </div>
    @yield('js')
</body>
</html>
