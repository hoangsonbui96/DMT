<?php $__env->startSection('content'); ?>
    <style>
        .tbl-top {
            margin-top: 20px;
        }

        .table.table-bordered th,
        .table.table-bordered td {
            border: 1px solid #bdb9b9 !important;
            vertical-align: middle !important;
            background-color: #fff;
        }

        .table-scroll table {
            min-width: 1260px !important;
        }

        .td-hover {
            text-align: center;
            cursor: pointer;
        }

        tr .td-hover:hover {
            background-color: #c6e2ff;
        }
        .modal .box{
            margin-bottom: 0px !important;
        }
    </style>
    <section class="content-header">
        <h1 class="page-header">Ngày nghỉ phép</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <form class="form-inline" method="get" id="leave-search-form">
                    <div class="form-group pull-left margin-r-5" id="cmbSelectUser">
                        <div class="btn-group bootstrap-select show-tick show-menu-arrow user-custom"
                            id="action-select-user" loginUser = <?php echo e($loginUser); ?>>
                            <select class="selectpicker show-tick show-menu-arrow user-custom" id="select-user"
                                name="users_search[]" data-live-search="true" data-live-search-placeholder="Search"
                                data-size="5" data-actions-box="true" multiple>
                                <?php echo GenHtmlOption(
                                    $users,
                                    'id',
                                    'FullName',
                                    request()->get('users_search') ? request()->get('users_search') : null,
                                ); ?>

                                <option value="<?php echo e($loginUser); ?>" selected="selected"><?php echo e($loginUserName); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group pull-left margin-r-5 date" id="date">
                        <div class="input-group search date">
                            <input type="text" class="form-control" id="date-input" name="date" placeholder="dd/mm/yyyy" autocomplete="off"
                                value="<?php echo e(request()->get('date') ? request()->get('date') : Carbon\Carbon::now()->format(FOMAT_DISPLAY_DAY)); ?>">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <div class="p-1" data-valmsg-for="date-time"></div>
                    </div>
                    <div class="form-group pull-left">
                        <a class="btn btn-primary" id="btn-search-leave">
                            <?php echo app('translator')->get('admin.btnSearch'); ?>
                        </a>
                    </div>
                    <div class="form-group pull-right">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('action', $export)): ?>
                            <a class="btn btn-success" id="export-leave">
                                <?php echo app('translator')->get('admin.export-excel'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="">
                    <div class="table-responsive no-padding table-scroll">
                        <table class="table table-bordered table-striped" name="leave-table" style="margin-bottom: 0;">
                            <thead class="thead-default">
                                <tr>
                                    <th>STT</th>
                                    <th>Họ và tên</th>
                                    <th>Ngày bắt đầu <br> vào công ty</th>
                                    <th>Ngày ký HĐ<br>Chính thức</th>
                                    <th>Ngày nghỉ còn lại của <br>năm trước (có hiệu lực) (h)</th>
                                    <th>Ngày nghỉ trong năm <br>đến lúc này (h)</th>
                                    <th>Ngày nghỉ phép<br>(đã đăng ký)(h)</th>
                                    <th>Giờ khác (đi muộn, về sớm)<br>không đăng ký(h)</th>
                                    <th>Không chấm công (h)</th>
                                    <th>Nghỉ phép<br>còn lại (h)</th>
                                    
                                </tr>
                            </thead>
                            <tbody id="ShowDataLoad">
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
                                        
                                        <td class="text-center office-date" Office-date=<?php echo e($user->OfficialDate); ?>>
                                            <?php echo e(isset($user->OfficialDate) ? FomatDateDisplay($user->OfficialDate, FOMAT_DISPLAY_DAY) : ''); ?>

                                        </td>
                                        
                                        <td class="text-center">
                                            <?php echo e($user->last_year_before != 0 ? number_format($user->last_year_before, 2) : 0); ?> <br>
                                        </td>
                                        
                                        <td class="text-center">
                                            <?php echo e($user->this_year_before != 0 ? number_format($user->this_year_before, 2) : 0); ?>

                                        </td>
                                        
                                        <td class="td-hover absence under-line" style="position: relative" UserID="<?php echo e($user->id); ?>">
                                            <?php echo e($user->AbsenceSearchMonth != 0 ? number_format($user->AbsenceSearchMonth, 2) : 0); ?>

                                        </td>
                                        
                                        <td class="td-hover <?php echo e($user->late_soon != 0 ? 'late-soon under-line' : ''); ?>" style="position: relative" UserID="<?php echo e($user->id); ?>"> 
                                            <?php echo e($user->late_soon != 0 ? number_format($user->late_soon, 2) : 0); ?>

                                        </td>
                                        
                                        <td class="td-hover nokeeping under-line" UserID="<?php echo e($user->id); ?>" > 
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script type="text/javascript" defer>
        $(".selectpicker").selectpicker({
            noneSelectedText: 'Chọn nhân viên',
            deselectAllText: 'Bỏ chọn tất cả',
            selectAllText: 'Chọn tất cả',
            liveSearchPlaceholder: 'Nhập tên nhân viên'
            
        });
        SetDatePicker($('.date'));
    </script>
    <script>
        var urlSearchLeave = "<?php echo e(route('admin.Leave')); ?>";
        $(function() {
            $("#btn-search-leave").click(function() {
                $('.loadajax').show();
                let data = $('#leave-search-form').serialize();
                var searchDateVal = $('#date-input').val();
                //console.log(data + "test");
                ajaxGetServerWithLoader(urlSearchLeave, "GET", data, function(rst) {
                    if(!searchDateVal){
                        $("div[data-valmsg-for = 'date-time']").addClass('text-danger');
                        $("div[data-valmsg-for = 'date-time']").text("Ngày tìm kiếm không được để trống");
                    }else{
                        $('.loadajax').hide();
                        $('#ShowDataLoad').html(rst);
                    }   
                }, function() {
                    alert('lỗi');
                });
            });

            $('#date-input').on('change', function () {
                $("div[data-valmsg-for = 'date-time']").removeClass('text-danger');
                $("div[data-valmsg-for = 'date-time']").text("");
            });

            $('#date-input').on('keydown', function () {
                $("div[data-valmsg-for = 'date-time']").removeClass('text-danger');
                $("div[data-valmsg-for = 'date-time']").text("");
            });
        });
    </script>
    <script>
        var Title = 'Lý do vắng mặt';
        var title_late_soon = 'Đi muộn - Về sớm không đăng kí';
        var title_nokeeping = 'Không chấm công';

		$('.absence').click(function() {
            $('.loadajax').show();
			var dateTr = $("#date-input").val();
			var date = dateTr.split("/").reverse().join("-");
			var UserID = $(this).attr('UserID');
            var OfficeDate = $('.office-date').attr('Office-date');
            var Type = 1;
            console.log(UserID);

			ajaxGetServerWithLoader('<?php echo e(route('admin.leaveAbsence')); ?>', 'POST', {
				date: date,
				UserID: UserID,
                OfficeDate: OfficeDate,
                Type: Type,
			}, function (data) {
                $('.loadajax').hide();
				$('#popupModal').empty().html(data);
				$('.modal-title').html(Title);
				$('#modal-absence-list').modal('show');
			});
		});


        $('.late-soon').click(function() {
        $('.loadajax').show();
        var dateTr = $("#date-input").val();
        var date = dateTr.split("/").reverse().join("-");
        var UserID = $(this).attr('UserID');
        var OfficeDate = $('.office-date').attr('Office-date');

        ajaxGetServerWithLoader('<?php echo e(route('admin.leave.unregistered_list')); ?>', 'POST', {
            date: date,
            UserID: UserID,
            TypeSelect: 2,
            OfficeDate: OfficeDate,
            Type : 1,
        }, function (data) {
                $('.loadajax').hide();
                $('#popupModal').empty().html(data);
                $('.modal-title').html(title_late_soon);
                $('#modal-late-soon-list').modal('show');
            });
        });

        $('.nokeeping').click(function() {
            //$('.loadajax').show();
            var dateTr = $("#date-input").val();
            var date = dateTr.split("/").reverse().join("-");
            var UserID = $(this).attr('UserID');
            var OfficeDate = $('.office-date').attr('Office-date'); 
            var Type = 1;
            console.log(OfficeDate);
            ajaxGetServerWithLoader('<?php echo e(route('admin.leave.notimekeeping_list')); ?>', 'POST', {
            date: date,
            UserID: UserID,
            OfficeDate: OfficeDate,
            Type: Type,
            }, function (data) {
                    $('.loadajax').hide();
                    $('#popupModal').empty().html(data);
                    $('.modal-title').html(title_nokeeping);
                    $('#modal-no-keeping-list').modal('show');
            });
            
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.default.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\DMT\Modules/Leave\Resources/views/layouts/leave-list.blade.php ENDPATH**/ ?>