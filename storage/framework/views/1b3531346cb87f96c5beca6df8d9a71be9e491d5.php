<?php $__env->startPush('pageCss'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/timekeeping.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <style>
        .table th, .table td {
            border: 1px solid #bdb9b9 !important;
            text-align: center;
            vertical-align: middle !important;
            background-color: #fff;
        }

        .table-timekeeping .table tr th {
            background-color: #c6e2ff;
        }

        .warning123 td {
            background-color: #FEEFD0;
        }

        .hover-point:hover {
            background: #c6e2ff;
            cursor: pointer;
        }

        .selected-tr {
            background-color: rgb(255, 99, 132) !important;
            cursor: pointer;
            color: white;
        }

        .weekend-7 td {
            background: #CCCCFF !important;
        }

        .weekend-7 {
            background: #CCCCFF !important;
        }

        .weekend-cn td {
            background: #FF99CC !important;
        }

        .weekend-cn {
            background: #FF99CC !important;
        }

    </style>
    <section class="content-header">
        <h1 class="page-header"><?php echo app('translator')->get('menu.timekeeping-scheduler'); ?></h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="time_keeping">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-danger-handle" style="margin: 0; border: none">
                            <p><?php echo e($errors->first()); ?></p>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-8 col-sm-9 col-xs-12">
                            <form class="form-inline" method="get" id="timekeeping-search"
                                  action="<?php echo e(route("admin.TimekeepingScheduler")); ?>">
                                <div class="form-group pull-left margin-r-5" id="cmbSelectUser">
                                    <div class="btn-group bootstrap-select show-tick show-menu-arrow user-custom"
                                         id="action-select-user">
                                        <select class="selectpicker show-tick show-menu-arrow user-custom"
                                                id="select-user" name="users_search[]" data-live-search="true"
                                                data-live-search-placeholder="Search" data-size="5"
                                                data-actions-box="true"
                                                multiple>
                                            <?php echo GenHtmlOption($users, 'id', 'FullName', request()->get("users_search") ? request()->get("users_search") : null); ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="form-group pull-left margin-r-5 date" id="date">
                                    <div class="input-group search date">
                                        <input type="text" class="form-control" id="date-input" name="date"
                                               value="<?php echo e(request()->get('date') ?  request()->get('date') : Carbon\Carbon::now()->format(FOMAT_DISPLAY_DAY)); ?>">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group pull-left">
                                    <button type="submit" class="btn btn-primary" id="view-dReport">
                                        <?php echo app('translator')->get('admin.btnSearch'); ?>
                                    </button>
                                    <button type="button" class="btn btn-primary" id="displayTimekeeping">
                                        Ẩn bảng thống kê
                                    </button>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('action', $role["Export"])): ?>
                                        <button class="btn btn-success" id="export">
                                            <?php echo app('translator')->get('admin.export-excel'); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive table-timekeeping-detail">
                        <table class="table data-table" id="table1">
                            <thead class="thead-default">
                            <tr>
                                <th colspan="12"
                                    style="background: rgba(255, 225, 0, 0.5); text-align: left !important;">
                                    Dữ liệu thống kê vào :&nbsp; <?php echo e(\Carbon\Carbon::now()->format("H:i")); ?>

                                    ngày <?php echo e(\Carbon\Carbon::now()->format(FOMAT_DISPLAY_DAY)); ?>

                                </th>
                            </tr>
                            <tr>
                                <th><?php echo app('translator')->get('admin.timekeeping.work'); ?></th>
                                <th colspan="2"><?php echo e(number_format($users_keeping->totalKeeping, 2)); ?></th>
                                <th rowspan="4"></th>
                                <th>Số người làm việc tại công ty</th>
                                <th class="hover-point th-modal"
                                    data-alias="tkCompany"><?php echo e($users_keeping->checkinAtCompany); ?></th>
                                <th rowspan="4"></th>
                                <th><?php echo app('translator')->get('admin.timekeeping.users-late'); ?></th>
                                <th class="hover-point th-modal"
                                    data-alias="latecomers"><?php echo e($users_keeping->lateTimes); ?></th>
                                <th rowspan="4"></th>
                                <th><?php echo app('translator')->get('admin.timekeeping.sogiotre'); ?></th>
                                <th><?php echo e(number_format($users_keeping->lateHours/60, 2)); ?></th>
                            </tr>
                            <tr>
                                <th><?php echo app('translator')->get('admin.timekeeping.overtime'); ?></th>
                                <th colspan="2"><?php echo e(number_format($users_keeping->overKeeping/60, 2)); ?></th>
                                <th>Số người làm việc tại nhà</th>
                                <th class="hover-point th-modal"
                                    data-alias="tkHome"><?php echo e($users_keeping->checkinAtHome); ?></th>
                                <th><?php echo app('translator')->get('admin.timekeeping.users-soon'); ?></th>
                                <th class="hover-point th-modal"
                                    data-alias="backSoon"><?php echo e($users_keeping->soonTimes); ?></th>
                                <th><?php echo app('translator')->get('admin.timekeeping.sogiosom'); ?></th>
                                <th><?php echo e(number_format($users_keeping->soonHours/60, 2)); ?></th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                    <!-- Table daily report detail -->
                    <div class="table-responsive table-timekeeping">
                        <table class="table data-table" id="table_timekeeping">
                            <thead class="thead-default">
                            <tr>
                                <th class="thead-th-custom" rowspan="2"
                                    style="word-wrap: break-word;">STT
                                </th>
                                <th class="thead-th-custom width5" rowspan="2" colspan="2"
                                    style="word-wrap: break-word;"><?php echo app('translator')->get('admin.Staffs_name'); ?></th>
                                <th class="thead-th-custom" colspan="2"><?php echo app('translator')->get('admin.timekeeping.TGvaora'); ?></th>
                                <th class="thead-th-custom" rowspan="2"><?php echo app('translator')->get('admin.timekeeping.TimeWork'); ?></th>

                                <th class="thead-th-custom" rowspan="2"><?php echo app('translator')->get('admin.timekeeping.late'); ?>
                                    <br>(phút)
                                </th>
                                <th class="thead-th-custom" rowspan="2"><?php echo app('translator')->get('admin.timekeeping.soon'); ?>
                                    <br>(phút)
                                </th>
                                <th class="thead-th-custom" rowspan="2"><?php echo app('translator')->get('admin.timekeeping.T_Gio'); ?></th>
                                <th class="thead-th-custom" rowspan="2"><?php echo app('translator')->get('admin.timekeeping.T_GioTT'); ?></th>
                                <th class="thead-th-custom" rowspan="2"><?php echo app('translator')->get('admin.timekeeping.total_work'); ?></th>
                                <th class="thead-th-custom" rowspan="2" style="width:210px"><?php echo app('translator')->get('admin.absences'); ?></th>
                                <th class="thead-th-custom" rowspan="2"
                                    style="width:200px"><?php echo app('translator')->get('admin.timekeeping.type'); ?></th>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("action", $role["Edit"])): ?>
                                    <th class="thead-th-custom width5pt" rowspan="2"><?php echo app('translator')->get('admin.action'); ?></th>
                                <?php endif; ?>
                            </tr>
                            <tr>
                                <th class="thead-th-custom">Vào</th>
                                <th class="thead-th-custom">Ra</th>
                                
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $users_keeping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr data-user-uid="<?php echo e($user->id); ?>"
                                    data-item-id="<?php echo e(isset($user->timekeepings->first()->id)
                                    ? $user->timekeepings->first()->id : null); ?>"
                                    class="<?php echo e(!isset($user->timekeepings->first()->id) ? "warning123" : null); ?>">
                                    <td><?php echo e($index + 1); ?></td>
                                    <td colspan="2"><?php echo e($user->FullName); ?></td>
                                    <?php if(isset($user->timekeepings->first()->id)): ?>
                                        <td>
                                            <?php echo e(isset($user->timekeepings->first()->TimeIn) ? $user->timekeepings->first()->TimeIn : null); ?>

                                        </td>
                                        <td>
                                            <?php echo e(isset($user->timekeepings->first()->TimeOut) ? $user->timekeepings->first()->TimeOut : null); ?>

                                        </td>
                                        <td>
                                            <?php if($user->timekeepings->first()->STimeOfDay && $user->timekeepings->first()->ETimeOfDay != null): ?>
                                                <?php echo e($user->timekeepings->first()->STimeOfDay); ?>

                                                - <?php echo e($user->timekeepings->first()->SBreakOfDay ? $user->timekeepings->first()->SBreakOfDay : $WT002->Name); ?>

                                                <br>
                                                <?php echo e($user->timekeepings->first()->EBreakOfDay ? $user->timekeepings->first()->EBreakOfDay : $WT002->DataDescription); ?>

                                                - <?php echo e($user->timekeepings->first()->ETimeOfDay); ?>

                                            <?php endif; ?>
                                        </td>
                                        <?php
                                            $late = $user->timekeepings->first()->late != "00:00:00"
                                                ? \Carbon\Carbon::parse($user->timekeepings->first()->late)->format("H:i:s")
                                                : null;
                                            $soon = $user->timekeepings->first()->soon != "00:00:00"
                                               ? \Carbon\Carbon::parse($user->timekeepings->first()->soon)->format("H:i:s")
                                               : null
                                        ?>
                                        <td class="<?php echo e($late != null ? 'weekend-cn' : null); ?>">
                                            <?php echo e($late); ?>

                                        </td>
                                        <td class="<?php echo e($soon != null ? 'weekend-7' : null); ?>">
                                            <?php echo e($soon); ?>

                                        </td>
                                        <td>
                                            <?php echo e(round($user->timekeepings->first()->hours, 2, PHP_ROUND_HALF_UP)); ?>

                                        </td>

                                        <td>
                                            <?php echo e(isset($user->timekeepings->first()->hoursTT)
                                                ? \Carbon\Carbon::parse($user->timekeepings->first()->hoursTT)->format("H:i:s")
                                                : null); ?>

                                        </td>
                                        <td><?php echo e($user->timekeepings->first()->keeping > 1
                                                ? 1
                                                : number_format($user->timekeepings->first()->keeping, 2)); ?>

                                        </td>
                                        <td>
                                            <?php $__currentLoopData = $user->timekeepings->first()->absence; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $absence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($user->timekeepings->first()->weekday != 'T7' && $user->timekeepings->first()->weekday != 'CN'): ?>
                                                    <a class="action-col view view-absence"
                                                       style="text-decoration: none">
                                                        <?php echo e($absence->Name); ?>

                                                        (<?php echo e(\Carbon\Carbon::parse($absence->SDate)->format("H:i")); ?>

                                                        - <?php echo e(\Carbon\Carbon::parse($absence->EDate)->format("H:i")); ?>

                                                        )
                                                    </a>
                                                    <br/>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($user->timekeepings->first()->calendarEvent): ?>
                                                <a class="action-col view" style="text-decoration: none">
                                                    Làm bù
                                                    (<?php echo e($user->timekeepings->first()->calendarEvent->StartDate); ?>)</a>
                                                <br/>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(isset($user->timekeepings->first()->type) && count($user->timekeepings->first()->type) > 0): ?>
                                                <?php $__currentLoopData = $user->timekeepings->first()->type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span><?php echo e($type->Type); ?></span><br>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php elseif($user->timekeepings->first()->id): ?>
                                                <span><?php echo app('translator')->get('admin.import'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("action", $role["Edit"])): ?>
                                            <td class="text-center">
                                                <span class="action-col update edit update-timekeeping">
                                                    <i class="fa fa-pencil-square-o"
                                                       aria-hidden="true"></i>
                                                </span>
                                            </td>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <?php $__currentLoopData = $user->timekeepings->first()->absence; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $absence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a class="action-col view view-absence" style="text-decoration: none">
                                                    <?php echo e($absence->Name); ?>

                                                    (<?php echo e(\Carbon\Carbon::parse($absence->SDate)->format("H:i")); ?>

                                                    - <?php echo e(\Carbon\Carbon::parse($absence->EDate)->format("H:i")); ?>)
                                                </a>
                                                <br/>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </td>
                                        <td></td>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check("action", $role["Edit"])): ?>
                                            <td></td>
                                        <?php endif; ?>
                                    <?php endif; ?>
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
            noneSelectedText: 'Chọn nhân viên'
        });
        SetDatePicker($('.date'));
        $('#displayTimekeeping').click(function (e) {
            if ($('#displayTimekeeping').text() == 'Ẩn bảng thống kê') {
                $('#displayTimekeeping').text('Hiện bảng thống kê');
                $('.table-timekeeping-detail').slideToggle();
            } else {
                $('#displayTimekeeping').text('Ẩn bảng thống kê');
                $('.table-timekeeping-detail').slideToggle();
            }
        });
        $(document).ready(function () {
            $(".hover-point").click((e) => {
                $(".selected-tr").removeClass("selected-tr");
                $(e.target).addClass("selected-tr");
            })

            // Export button download file
            $("#export").click(function (event) {
                event.preventDefault();
                const data = $('#timekeeping-search').serialize();
                const url = "<?php echo e(route('admin.ExportTimekeepingScheduler')); ?>";
                const a = document.createElement("a");
                $(a).attr("target", "_blank");
                $(a).attr("href", url + "?" + data);
                $("body").append(a);
                a.click();
                a.remove();
            })
            // View absence
            $(".view-absence").click(function (e) {
                e.preventDefault();
                const date_input = $("#date-input").val();
                const self = $(e.target);
                const title = 'Lý do vắng mặt ngày ' + date_input + ' của ' + $(self).closest("tr").find("td:nth-child(2)").text();
                const data = {
                    date: date_input.split("/").reverse().join("-"),
                    UserID: $(self).closest("tr").attr("data-user-uid"),
                }
                ajaxGetServerWithLoader("<?php echo e(route('admin.AbsenceTimekeepingNew')); ?>", "POST", data, function (data) {
                    $('#popupModal').empty().html(data);
                    $('.modal-title').html(title);
                    $('#modal-absence-list').modal('show');
                })
            })
            // Action button
            $(".update-timekeeping").click(function (e) {
                e.preventDefault();
                const self = $(e.target);
                const date_input = $("#date-input").val();
                const id = $(self).closest("tr").attr("data-item-id");
                if (!id) {
                    return;
                }
                const url = "<?php echo e(route('admin.detailTimekeepingNew')); ?>/" + id;
                const data = {
                    date: date_input.split("/").reverse().join("-"),
                    searchUser: $(self).closest("tr").attr("data-user-uid"),
                }
                ajaxGetServerWithLoader(url, "GET", data, function (data) {
                    const title = 'Sửa chấm công';
                    $('#popupModal').empty().html(data);
                    $('.modal-title').html(title);
                    $('#timeKeeping-info').modal('show');
                })
            })

            // Open modal summary
            $(".th-modal").click(event => {
                const self = event.target;
                let date_input = $("#date-input").val();
                date_input = date_input.split("/").reverse().join("-")
                if ($(self).text() != 0) {
                    const alias = $(self).attr("data-alias");
                    ajaxGetServerWithLoader("<?php echo e(route('admin.latecomers')); ?>" + '/' + alias + '/' + date_input, "GET", null, function (data) {
                        $('#popupModal').empty().html(data);
                        $('#latecomers-modal').modal('show');
                    });
                } else {
                    setTimeout(function () {
                        $(".selected-tr").removeClass("selected-tr");
                    }, 250);
                }
            })
        })
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.default.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\DMT\resources\views/admin/layouts/default/checkin/timekeeping-scheduler.blade.php ENDPATH**/ ?>