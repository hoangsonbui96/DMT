<?php $temp = 0 ?>
<?php $__currentLoopData = $users_leave; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $temp++;
    ?>
    <tr>
        <td class="text-center"><?php echo e($temp); ?></td>
        <td><?php echo e($user->FullName); ?></td>
        <td class="text-center">
            <?php echo e(isset($user->SDate) ? FomatDateDisplay($user->SDate, FOMAT_DISPLAY_DAY) : ''); ?>

        </td>
        <td class="text-center">
            <?php echo e(isset($user->OfficialDate) ? FomatDateDisplay($user->OfficialDate, FOMAT_DISPLAY_DAY) : ''); ?>

        </td>
        <td class="text-center">
            <?php echo e($user->last_year_before != 0 ? number_format($user->last_year_before, 2) : 0); ?> <br>
        </td>
        <td class="text-center">
            <?php echo e($user->this_year_before != 0 ? number_format($user->this_year_before, 2) : 0); ?>

        </td>
        <td class="td-hover <?php echo e($user->AbsenceSearchMonth != 0 ? 'absence under-line' : ''); ?>" UserID="<?php echo e($user->id); ?>" style="position: relative" office-load-date=<?php echo e($user->OfficialDate); ?>>
            <?php echo e($user->AbsenceSearchMonth != 0 ? number_format($user->AbsenceSearchMonth, 2) : 0); ?>

            
        </td>
        <td class="td-hover <?php echo e($user->late_soon != 0 ? 'late-soon under-line' : ''); ?>" style="position: relative" UserID="<?php echo e($user->id); ?>" Office-date=<?php echo e($user->OfficialDate); ?>>   
            <?php echo e($user->late_soon != 0 ? number_format($user->late_soon, 2) : 0); ?>

            
        </td>
        <td class="td-hover <?php echo e($user->no_timekeeping != 0 ? 'nokeeping under-line' : ''); ?>" UserID="<?php echo e($user->id); ?>" Office-date="<?php echo e($user->OfficialDate); ?>" Start-date="<?php echo e($user->SDate); ?>"> 
            <?php echo e($user->no_timekeeping != 0 ? number_format($user->no_timekeeping, 2) : 0); ?>

        </td>
        <td>
            Năm trước: 
            <?php echo e($user->last_year_after != 0 ? number_format($user->last_year_after, 2) : 0); ?> 
            <br>
            Hiện tại: 
            <?php echo e($user->this_year_after != 0 ? number_format($user->this_year_after, 2) : 0); ?>

            <br>
            Vượt quá: 
            <?php echo e($user->beyond_time != 0 ? number_format($user->beyond_time, 2) : 0); ?>

        </td>
        
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<script>
    var title_absence = 'Lý do vắng mặt';
    var title_late_soon = 'Đi muộn - Về sớm không đăng kí';
    var title_nokeeping = 'Không chấm công';

    $('.absence').click(function() {
        $('.loadajax').show();
        var dateTr = $("#date-input").val();
        var date = dateTr.split("/").reverse().join("-");
        var UserID = $(this).attr('UserID');
        var OfficeDate = $(this).attr('office-load-date');
        var Type = 1;

        ajaxGetServerWithLoader('<?php echo e(route('admin.leaveAbsence')); ?>', 'POST', {
            date: date,
            UserID: UserID,
            OfficeDate: OfficeDate,
            Type: Type,
        }, function (data) {
            $('.loadajax').hide();
            $('#popupModal').empty().html(data);
            $('.modal-title').html(title_absence);
            $('#modal-absence-list').modal('show');
        });
    });

    $('.nokeeping').click(function() {
        $('.loadajax').show();
        var dateTr = $("#date-input").val();
        var date = dateTr.split("/").reverse().join("-");
        var UserID = $(this).attr('UserID');
        var OfficeDate = $(this).attr('Office-date');
        var StartDate = $(this).attr('Start-date');
        var Type = 1;
        console.log(StartDate);

        ajaxGetServerWithLoader('<?php echo e(route('admin.leave.notimekeeping_list')); ?>', 'POST', {
            date: date,
            UserID: UserID,
            OfficeDate: OfficeDate,
            StartDate: StartDate,
            Type: Type,
        }, function (data) {
            $('.loadajax').hide();
            $('#popupModal').empty().html(data);
            $('.modal-title').html(title_nokeeping);
            $('#modal-no-keeping-list').modal('show');
        });
    });

    $('.late-soon').click(function() {
        $('.loadajax').show();
        var dateTr = $("#date-input").val();
        var date = dateTr.split("/").reverse().join("-");
        var UserID = $(this).attr('UserID');
        var OfficeDate = $(this).attr('Office-date');

        console.log(OfficeDate);
        ajaxGetServerWithLoader('<?php echo e(route('admin.leave.unregistered_list')); ?>', 'POST', {
            date: date,
            UserID: UserID,
            TypeSelect: 2,
            OfficeDate: OfficeDate,
            Type : 1
        }, function (data) {
            $('.loadajax').hide();
            $('#popupModal').empty().html(data);
            $('.modal-title').html(title_late_soon);
            $('#modal-late-soon-list').modal('show');
        });
    });
</script><?php /**PATH D:\DMT\Modules/Leave\Resources/views/includes/leave-load.blade.php ENDPATH**/ ?>