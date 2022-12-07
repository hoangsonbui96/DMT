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
    <link rel="stylesheet"
        href="{{ asset('themes/adminlte/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('themes/adminlte/iCheck/all.css') }}">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet"
        href="{{ asset('themes/adminlte/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}">
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
    {{-- demo --}}
    <script type="text/javascript">
        let workingsheduleIot = false;
        var confirmMsg = 'Bạn có chắc muốn xóa?';
        var LOGIN_URL = '{{ route('login') }}';
        var FOMAT_DATE = 'DD/MM/YYYY';
        var FOMAT_MOTH = 'MM/YYYY';

        var route_prefix = "{{ asset('') }}" + "/{{ $company }}/laravel-filemanager";
        const URL = `{{ asset('') }}`;
        @if ($currentRouteName == 'login')
            basket.clear();
        @endif

        basket.require(
            // { url: "{{ asset('themes/adminlte/jquery/dist/jquery.min.js') }}" },
            {
                url: "{{ asset('themes/adminlte/bootstrap/dist/js/bootstrap.min.js') }}"
            }, {
                url: "{{ asset('themes/adminlte/moment/min/moment.min.js') }}"
            },
        ).then(function() {

            basket.require({
                url: "{{ asset('js/jquery-ui.min.js') }}"
            }, {
                url: "{{ asset('js/bootstrap-toggle.min.js') }}"
            }, {
                url: "{{ asset('themes/adminlte/fastclick/lib/fastclick.js') }}"
            }, {
                url: "{{ asset('themes/adminlte/dist/js/adminlte.min.js') }}"
            }, {
                url: "{{ asset('themes/adminlte/jquery-sparkline/dist/jquery.sparkline.min.js') }}"
            }, {
                url: "{{ asset('themes/adminlte/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"
            }, {
                url: "{{ asset('themes/adminlte/jvectormap/jquery-jvectormap-world-mill-en.js') }}"
            }, {
                url: "{{ asset('themes/adminlte/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"
            }, {
                url: "{{ asset('themes/adminlte/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"
            }, {
                url: "{{ asset('themes/adminlte/iCheck/icheck.min.js') }}"
            }, {
                url: "{{ asset('themes/adminlte/jquery-slimscroll/jquery.slimscroll.min.js') }}"
            }, {
                url: "{{ asset('themes/adminlte/moment/moment.js') }}"
            }, {
                url: "{{ asset('themes/adminlte/moment/locale/vi.js') }}"
            }, {
                url: "{{ asset('js/bootstrap-datetimepicker.min.js') }}"
            }, {
                url: "{{ asset('js/bootstrap-select.min.js') }}"
            }, {
                url: "{{ asset('js/jquery-confirm.min.js') }}"
            }, {
                url: "{{ asset('js/common.js') }}"
            }, {
                url: "{{ asset('js/admin.js') }}"
            }, {
                url: "{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"
            }, {
                url: "{{ asset('js/amlich-hnd.js') }}"
            }, ).then(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                InitDatePicker();
                $(function() {

                    $('input[type=search]:eq(0)').focus();

                    $('form').submit(function() {
                        $('.loadajax').show();
                    });

                    $('button').click(function(e) {
                        $(this).blur();
                    });

                    $('.sort-link').each(function() {
                        var iconAdd = '<i class="fa fa-caret-down"></i>';
                        var sortValue = $(this).attr('data-sort');
                        var urlCol = $(this).attr('data-link');
                        var urlCurrent = location.href;
                        var urlPage = urlCol + sortValue;
                        $(this).attr('href', urlPage);
                        if (urlCurrent.startsWith(urlCol)) {
                            iconAdd = sortValue.startsWith('asc') ? iconAdd :
                                '<i class="fa fa-caret-up"></i>';
                        }
                        $(this).html($(this).text() + ' ' + iconAdd);
                    });

                    $('.loadajax').hide();
                });
            }, function(error) {
                // There was an error fetching the script
                console.log(error);
            });
        }, function(error) {
            // There was an error fetching the script
            console.log(error);
        });
    </script>
    <style>
        .d-none{
            display: none;
        }
        .height-iframe-accept{
            height: calc(100vh - 110px) !important;
        }
        .height-iframe-refuse{
            height: calc(100vh - 85px) !important;
        }
        .show_form{
            margin-top: 25px;
        }
        .p_lr_none{
            padding: 0 !important;
        }
        .p_left{
            padding-left: 15px;
        }
    </style>
</head>

<body>
    <div style="margin: 15px">
        @if (isset($interviewJob->Approve))
            <div class="row form-group">
                <div class="col-sm-2">
                    <label class="control-label">Kết quả phỏng vấn:</label>
                    <input class="form-control" type="text" readonly value="@if($interviewJob->Approve == 1) Đạt. @else Trượt @endif"
                    style="color: @if($interviewJob->Approve == 1) green; @else red; @endif">
                </div>
                <div class="col-sm-10" style="padding-left: 0px;">
                    <label class="control-label">Đánh giá buổi phỏng vấn:</label>
                    <textarea class="form-control" style="width: 100%;" rows="1" readonly>{{ $interviewJob->Evaluate }}</textarea>
                </div>
            </div>
        @else
            <div id="form">
                <div id="interview-shedule" style="maxheight: 150px;">
                    <div>
                        <form method="POST" id="interviewShedule-form">
                            <div class="row" style=" margin-bottom:15px; height: 30px;">
                                <div class="col-sm-3 col-xs-6 m-btn @if($candidate->Status == 1) show_form @endif" style=" max-width: 190px !important;">
                                    <div class="input-group" style="margin-bottom: 15px;">
                                        <span class="input-group-addon" style="padding: 0px 2px !important;">
                                            <input type="radio" name="status" id="refuse" value="2" style="width:26px;height:20px; accent-color: red;" @if ($candidate->Status == 2) checked @endif>
                                        </span>
                                        <input type="text" class="form-control" value="Không phỏng vấn" style="color:red;" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2 col-xs-6 m-btn p_lr_none @if($candidate->Status == 1) show_form @endif" style=" max-width: 150px !important;">
                                    <div class="input-group" style="margin-bottom: 15px;">
                                        <span class="input-group-addon" style="padding: 0px 2px !important;">
                                            <input type="radio" name="status" id="accept" value="1"  style="width:26px;height:20px; accent-color: green;" @if ($candidate->Status == 1) checked @endif>
                                        </span>
                                        <input type="text" class="form-control" value="Phỏng vấn" style="color: green;" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-8 row">
                                    <div class="col-sm-9 row @if($candidate->Status != 1) d-none @endif" id="formInterview">
                                        <input type="hidden" class="form-control" name="CandidateID" value="{{ $candidate->id }}">
                                        @if (blank($interviewJob))
                                            <input type="hidden" class="form-control" name="JobID" value="{{ $candidate->JobID }}">
                                        @else
                                            <input type="hidden" class="form-control" name="interview_id" value="{{ $interviewJob->id }}">
                                        @endif
                                        <div class="form-group col-sm-5 col-xs-5">
                                            <label class="control-label" for="">@lang('admin.interview.date')<sup
                                                    class="text-red">*</sup>:</label>
                                            <div class="select-abreason">
                                                <div class="form-row">
                                                    <div class="input-group date" id="sdate" style="padding: 0; max-width: 200px">
                                                        <input type="text" class="form-control datepicker"
                                                            placeholder="@lang('admin.interview.date')" name="InterviewDate" autocomplete="off"
                                                            value="@if (!blank($interviewJob)) {{ $interviewJob->InterviewDate }} @endif">
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-sm-7 col-xs-7 p_lr_none">
                                            <label for="text" class="control-label">@lang('admin.absence.remark'):</label>
                                            <div>
                                                <textarea type="text" class="form-control" id="note" rows="1" placeholder="@lang('admin.absence.remark')"
                                                    name="Note" value="">@if (!blank($interviewJob)){{ $interviewJob->Note }}@endif</textarea>
                                                @if (!blank($interviewJob))
                                                    <input type="hidden" name="evaluation" value="{{ $interviewJob->Evaluate }}">
                                                @endif
                                            </div>
                                        </div>
                                        {{-- <div class="col-sm-3 col-xs-3 row">
                                            <div class="form-group">
                                                <label for="sendMail" class="control-label">@lang('admin.interview.send-mail'):</label>
                                                <div>
                                                    <input style="margin-top: 5px;width:26px;height:20px" type="checkbox" name="sendMail"
                                                        id="sendMail" value="1" @if(empty($interviewJob)) checked @endif >
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>
                                    <div class="col-sm-2 col-xs-2 m-btn row @if($candidate->Status == 1) show_form @endif" style="width: 130px;">
                                        <div class="input-group p_left">
                                            <div>
                                                <label for="sendMail">@lang('admin.interview.send-mail'):</label>
                                                <input style="margin-top: 7px;width:26px;height:20px" type="checkbox" name="sendMail"
                                                    id="sendMail" value="1" @if(empty($interviewJob)) checked @endif >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1 m-btn @if($candidate->Status == 1) show_form @endif">
                                        <a class="btn btn-primary" style=""
                                            id="save_interviewShedule">@lang('admin.btnSave')</a>
                                    </div>
                                </div>
                            </div>
                            
                        </form>
                        <a href="@if (!blank($interviewJob)) {{ $interviewJob->id }} @endif" id="interviewJob_Id"></a>
                    </div>
                </div>
            </div>
        @endif
        
        <section style="padding-top: 0; padding-bottom: 0;">
            <div class="tab-content">
                <div class="tab-pane active" id="classIframe">
                    <h3 style="margin: 0 !important;"><iframe src="" width="100%" 
                        @if (empty($interviewJob)) style="height: calc(100vh - 85px);" @else style="height: calc(100vh - 110px);" @endif id="iframe"></iframe></h3>
                </div>
            </div>
        </section>
        <div class="loadajax">
            <i class="fa fa-spinner fa-spin"></i>
        </div>
    </div>

    <script !src="">
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        }, false);

        $(function() {
            funcViewIframe();
        });

        function funcViewIframe() {
            let link_file = "{{ route('admin.candidates.get_cv', $cv_path) }}";
                $('#iframe').show();
                $('#classIframe p').empty();
                $('#iframe').attr('src', link_file);
        }
    </script>
    <script>
        SetDateTimePicker($('.date'), {
            format: 'DD/MM/YYYY HH:mm',
            stepping: 5,
        });

        $(document).on('change', '#sendMail', function() {
            let active = $(this).is(':checked');
            if (active === true) {
                $(this).val('1');
            } else {
                $(this).val('0');
            }
        });
    </script>

<script>
    $(function() {
        var interview_id = $('#interviewJob_Id').attr('href');
        $(document).off('click', '#save_interviewShedule').on('click', '#save_interviewShedule', function() {
            var check_sendMail = $('#sendMail').is(':checked');
            var check_accept = $('#accept').is(':checked');
            var check_refuse = $('#refuse').is(':checked');
            if(interview_id  && check_refuse){
                var urlSaveInterviewShedule = "{{ route('admin.candidates.decide_cv') }}";
                var content = "Bạn muốn từ chối phỏng vấn ứng viên này?";
            }
            else if (interview_id) {
                var urlSaveInterviewShedule = "{{ Route('admin.interviewShedule.update') }}";
                if (check_sendMail == true) {
                    var content = "Bạn muốn cập nhập lại thông tin buổi phỏng vấn và gửi lại mail hẹn phỏng vấn cho ứng viên này?";
                } else {
                    var content = "Bạn muốn cập nhập lại thông tin buổi phỏng vấn cho ứng viên này?";
                }
            } else {  
                if(check_accept == true){
                    var urlSaveInterviewShedule = "{{ route('admin.interviewShedule.store') }}";
                    if (check_sendMail == true) {
                        var content = "Bạn muốn tạo lịch và gửi mail cho ứng viên này?";
                    } else {
                        var content = "Bạn muốn tạo lịch cho ứng viên này?";
                    } 
                }
                if(check_refuse == true){
                    var urlSaveInterviewShedule = "{{ route('admin.candidates.decide_cv') }}";
                    var content = "Bạn muốn từ chối phỏng vấn ứng viên này?";
                }         
            }
            showConfirm(content, function() {
                $('.loadajax').show();
                let saveData = $('#interviewShedule-form').serializeArray();
                console.log(saveData);
                ajaxGetServerWithLoader(urlSaveInterviewShedule, "POST", saveData, function(rst) {
                    $('.loadajax').hide();
                        if ($.isEmptyObject(rst.errors)) {
                            showSuccess(rst.success);
                            localStorage.setItem('load', 1);
                        } else {
                            showErrors(rst.errors);
                        }
                    }, function() {
                        alert('lỗi');
                });
            });
            
        });
    })
</script>
<script>
    $(function(){
        $(document).on('click', '#accept', function(){
            $("#formInterview").slideDown();
            $(".m-btn").addClass('show_form');
            $("#iframe").removeClass('height-iframe-refuse');
            $("#iframe").addClass('height-iframe-accept');
        });
        $(document).on('click', '#refuse', function(){
            $("#formInterview").slideUp();
            $(".m-btn").removeClass('show_form');
            $("#iframe").removeClass('height-iframe-accept');
            $("#iframe").addClass('height-iframe-refuse');
        });
    })
</script>
</body>

</html>
