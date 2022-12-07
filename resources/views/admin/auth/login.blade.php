{{-- @push('pageJs')
    <script src="https://www.google.com/recaptcha/api.js" defer></script>
@endpush --}}
@extends('admin.layouts.default.app')
@php
    if (isset($_COOKIE['showNoti'])){
        unset($_COOKIE['showNoti']);
        setcookie('showNoti', '', time() - 3600, '/');
    }
@endphp
@section('content')
<div class="login-box">
    <div class="login-logo">
        <a href=""><img src="{{ asset("imgs/logo-akb-edit.png") }}" width="200px" height="91.27px" alt="Công ty TNHH Liên doanh phần mềm AKB Software" id="logo-akb" ></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <form method="POST" action="{{ route('login') }}" id = "login-form">
            @csrf
            <div class="form-group has-feedback @error('username') has-error @enderror">
                <input type="username" class="form-control" name="username" autofocus="true" value="{{ old('username') }}" placeholder="{{ __('Username') }}" />
                <span class="fa fa-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback @error('password') has-error @enderror">
                <input type="password" class="form-control" name="password" placeholder="{{ __('Password') }}">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            {{-- <div class="form-group" style="overflow: hidden;">
                <div class="g-recaptcha" data-sitekey="6LecZ8kiAAAAAK3LMZRthI3fZgq6ZPoh7az8XQoH" data-callback="callback"></div>
            </div> --}}
            <div class="row">
                <div class="col-xs-12">
                    <button type="button" id="btnLogin" class="btn btn-primary btn-block btn-flat">{{ __('Login') }}</button>
                    @if($qrCode)
                    <a href="{{ route('qrCode') }}" type="button" class="btn btn-primary btn-block btn-flat">{{ __('QR Code') }}</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
    <!-- /.login-box-body -->
    @if($errors->any())
    <div class="login-box-body bg-red">
        @error('username')<div>{{ $message }}</div>@enderror
        @error('password')<div>{{ $message }}</div>@enderror
        @error('TheMessage')<div>{{ $message }}</div>@enderror

          <!-- <div class="alert alert-success"> -->

            <!-- </div> -->
    </div>
    @endif
    {{-- <div class="login-box-body bg-red hidden recapt">Không chấp nhận robot</div> --}}
</div>
@endsection
<style type="text/css">
    .login-box-body,
    .register-box-body {
        padding: 29px !important;
    }
</style>
@section('js')
    <script>
        $(function () {
            $('#btnLogin').click(function (e) {
                e.preventDefault();
                $('#login-form').submit();
            });

        });
    </script>
@endsection
