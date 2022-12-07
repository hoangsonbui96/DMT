<?php
    if (isset($_COOKIE['showNoti'])){
        unset($_COOKIE['showNoti']);
        setcookie('showNoti', '', time() - 3600, '/');
    }
?>
<?php $__env->startSection('content'); ?>
<div class="login-box">
    <div class="login-logo">
        <a href=""><img src="<?php echo e(asset("imgs/logo-akb-edit.png")); ?>" width="200px" height="91.27px" alt="Công ty TNHH Liên doanh phần mềm AKB Software" id="logo-akb" ></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <form method="POST" action="<?php echo e(route('login')); ?>" id = "login-form">
            <?php echo csrf_field(); ?>
            <div class="form-group has-feedback <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> has-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <input type="username" class="form-control" name="username" autofocus="true" value="<?php echo e(old('username')); ?>" placeholder="<?php echo e(__('Username')); ?>" />
                <span class="fa fa-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> has-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <input type="password" class="form-control" name="password" placeholder="<?php echo e(__('Password')); ?>">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            
            <div class="row">
                <div class="col-xs-12">
                    <button type="button" id="btnLogin" class="btn btn-primary btn-block btn-flat"><?php echo e(__('Login')); ?></button>
                    <?php if($qrCode): ?>
                    <a href="<?php echo e(route('qrCode')); ?>" type="button" class="btn btn-primary btn-block btn-flat"><?php echo e(__('QR Code')); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
    <!-- /.login-box-body -->
    <?php if($errors->any()): ?>
    <div class="login-box-body bg-red">
        <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <?php $__errorArgs = ['TheMessage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

          <!-- <div class="alert alert-success"> -->

            <!-- </div> -->
    </div>
    <?php endif; ?>
    
</div>
<?php $__env->stopSection(); ?>
<style type="text/css">
    .login-box-body,
    .register-box-body {
        padding: 29px !important;
    }
</style>
<?php $__env->startSection('js'); ?>
    <script>
        $(function () {
            $('#btnLogin').click(function (e) {
                e.preventDefault();
                $('#login-form').submit();
            });

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.default.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\DMT\resources\views/admin/auth/login.blade.php ENDPATH**/ ?>