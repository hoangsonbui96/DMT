@extends('admin.layouts.default.app')
<style type="text/css">
    body{
        background-color: #ecf0f5 !important;
    }
</style>
@section('content')
<section class="content-header">
    <h1 class="page-header">
    {{ !Session::has("checkPass") ? 'Đổi mật khẩu' : 'Mật khẩu yếu vui lòng đổi lại mật khẩu' }}
</h1>
</section>
    <section class="content">
        <div class="row">
            <div class="col-sm-9 col-md-10 col-xs-12 main">
                <div class="row">
                    @if(Session::has("checkPass"))
                    <div class="col-xs-12 col-md-10 col-md-push-2 col-lg-8 col-lg-push-2 well">
                    @else
                    <div class="col-xs-12 col-md-10 col-md-push-2 col-lg-8 col-lg-push-3 well">
                    @endif
                        <form class="form-horizontal" id="form-change-password">
                            <div class="form-inputs">
                                @if(isset($user))
                                    <input type="text" class="form-control hidden" name="id" value="{{$user}}">
                                @endif
                                <div class="form-group"> 
                                    <label class="control-label col-sm-3 col-md-4" for="new_password">@lang('admin.user-detail.current_password') <sup>*</sup>:</label>
                                    <div class="col-sm-9 col-md-7" id="show_hide_password">
                                        <input class="form-control" id="current_password" name="oldPassword" type="password" autocomplete="off">
                                        <i class="fa fa-eye-slash" onclick="ShowPassword()" aria-hidden="true" style="position: absolute;right: 6%;top: 30%;"></i>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3 col-md-4" for="new_password">@lang('admin.user-detail.newPassword') <sup>*</sup>:</label>
                                    <div class="col-sm-9 col-md-7" id="show_hide_password">
                                        <input class="form-control" id="new_password" name="new_password" type="password" autocomplete="off" placeholder="t@123abc">
                                        <i class="fa fa-eye-slash" onclick="ShowPassword()" aria-hidden="true" style="position: absolute;right: 6%;top: 30%;"></i>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3 col-md-4" for="password_confirmation">@lang('admin.user-detail.confirmPassword') <sup>*</sup>:</label>
                                    <div class="col-sm-9 col-md-7" id="show_hide_password">
                                        <input class="form-control" id="confirm_password" name="new_password_confirmation" type="password" autocomplete="off" placeholder="t@123abc">
                                        <i class="fa fa-eye-slash" onclick="ShowPassword()" aria-hidden="true" style="position: absolute;right: 6%;top: 30%;"></i>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3 col-md-4"></label>
                                    <div class="col-sm-9 col-md-7"><i class="text-danger">(*) Mật khẩu gồm 8 kí tự (kí tự đặc biệt, chữ số và chữ cái).</i></div>
                                </div>
                            </div>
                        </form>
                        <div class="text-center">
                            <button class="btn btn-primary" id="password_modal_save">@lang('admin.user-detail.changePassword')</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script type="text/javascript" async>
        $( document ).ready(function() {
            var boolcheck = false;
            var boolchecks = false;
            var check = <?php echo CHECK_PASSWORD?>;
            $("#current_password").on('keyup',function(){
                if($("#current_password").val()!='')
                    boolchecks = true;
                else
                    boolchecks = false;
            });
            $("#new_password").on('keyup',function(){
                if(!check.test($("#new_password").val())){
                    $('#new_password').css({
                        "border-color": "#ea0b0b"
                    });
                    boolcheck = false;
                }else{
                     $('#new_password').css({
                        "border-color": "#d2d6de"
                    });
                    boolcheck = true;
                }
            });
            
            $("#confirm_password").on('keyup',function(){
                var newpass= $("#new_password").val();
                if(newpass!=$("#confirm_password").val()){
                    $('#confirm_password').css({
                        "border-color": "#ea0b0b"
                    });
                }else{
                     $('#confirm_password').css({
                        "border-color": "#d2d6de"
                    })
                }
            });
            $('#password_modal_save').click(function () {
                if(!boolcheck||!boolchecks){
                    showErrors("Vui lòng kiểm tra lại mật khẩu!");
                }else{
                    ajaxServer("{{ route('admin.changePassword') }}", 'POST', $('#form-change-password').serializeArray(), function (data) {
                        if (typeof data.errors !== 'undefined') {
                            showErrors(data.errors);
                        } else {
                            $(function () {
                                $('#current_password,#new_password,#confirm_password').val('');
                            });
                            $.confirm({
                                title: 'Thông báo!',
                                content: data.success,
                                buttons: {
                                    ok: function () {
                                        window.location.href = '{{ route('admin.home') }}';
                                    },
                                }
                            });
                        }
                    });
                }
            });
        });
        function ShowPassword() {
            if($('#new_password').attr("type") == "text"){
                $('#show_hide_password input').attr('type', 'password');
                $("#show_hide_password i").removeClass( "fa-eye" )
                $('#show_hide_password i').addClass( "fa-eye-slash" );
            }else if($('#new_password').attr("type") == "password"){
                $('#show_hide_password input').attr('type', 'text');
                $("#show_hide_password i").removeClass( "fa-eye-slash" )
                $('#show_hide_password i').addClass( "fa-eye" );
            }
        };
    </script>
@endsection
