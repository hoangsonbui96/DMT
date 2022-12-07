<div class="modal draggable fade in detail-modal" id="absent-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-xs ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="">×</button>
                <h4 class="modal-title"><?php echo app('translator')->get('admin.add-absence'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <form class="form-horizontal detail-form" action="" method="POST" id="absence-form">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                        
                            <?php if(isset($absenceInfo->id)): ?>
                                <input type="hidden" name="id" value="<?php echo e($absenceInfo->id); ?>" id="id">
                            <?php else: ?>
                                <input type="hidden" id="realTime" value="false">
                            <?php endif; ?>
                            <input type="hidden" value="<?php echo e(isset($absenceInfo->UID) ? $absenceInfo->UID : Auth::user()->id); ?>" id="absenceUID">
                            <div class="form-group">
                                <label class="control-label col-sm-3" for=""><?php echo app('translator')->get('admin.rooms'); ?> &nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-sm-9">
                                    <select <?php echo e(!$isAdmin ? 'disabled' : ''); ?> class="selectpicker show-tick show-menu-arrow" id="select-roome" name="RoomID" data-size="5" <?php echo e(isset($absenceInfo->id) ? 'disabled' : ''); ?>>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('action',$add)): ?>
                                            <?php echo GenHtmlOption($rooms, 'id', 'Name', isset($absenceInfo->RoomID) ? $absenceInfo->RoomID : (!isset($absenceInfo->RoomID) ? $userLogged->RoomId : '')); ?>

                                        <?php else: ?>
                                            <option value="<?php echo e($roomUser->id); ?>" selected><?php echo e($roomUser->Name); ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- nhân viên -->
                            <div class="form-group">
                                <label class="control-label col-sm-3" for=""><?php echo app('translator')->get('admin.staff'); ?> &nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-sm-9 select-abreason">
                                    <select <?php echo e(!$isAdmin ? 'disabled' : ''); ?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 class="selectpicker show-tick show-menu-arrow" id="selectUser" data-size="5" name="UID"
                                            data-live-search="true" data-live-search-placeholder="Search" <?php echo e(isset($absenceInfo->id) ? 'disabled' : ''); ?>>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('action',$add)): ?>
                                            <input type="hidden" value="1" id="role">
                                        <?php else: ?>
                                            <option value="<?php echo e($userLogged->id); ?>" selected><?php echo e($userLogged->FullName); ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Lý do ~ ReasonID -->
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="member"><?php echo app('translator')->get('admin.absence.name'); ?>&nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-sm-9 select-abreason">
                                    <select class='selectpicker show-tick show-menu-arrow' id='select-absentreason' name="MasterDataValue" data-size="5">
                                        <option value="">[<?php echo app('translator')->get('admin.chooseAbsentreason'); ?>]</option>
                                        <?php echo GenHtmlOption($master_datas, 'DataValue', 'Name', isset($absenceInfo->MasterDataValue )  ? $absenceInfo->MasterDataValue : ''); ?>

                                    </select>
                                </div>
                            </div>

                            <!-- Thời gian: Bắt đầu -->
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="sDate"><?php echo app('translator')->get('admin.absence.sdate'); ?> &nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-sm-9">
                                    <div class="input-group date" id="sDate">
                                        <input type="text" class="form-control" id="sDate-input" placeholder="Ngày bắt đầu" name="SDate" autocomplete="off"
                                               value="<?php echo e(isset($absenceInfo->SDate) ? FomatDateDisplay($absenceInfo->SDate, FOMAT_DISPLAY_DATE_TIME) : null); ?>">
                                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Thời gian: Kết thúc -->
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="sDate"><?php echo app('translator')->get('admin.absence.edate'); ?> &nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-sm-9">
                                    <div class="input-group date" id="eDate">
                                        <input type="text" class="form-control date" id="eDate-input" placeholder="Ngày kết thúc" name="EDate" autocomplete="off"
                                               value="<?php echo e(isset($absenceInfo->EDate) ? FomatDateDisplay($absenceInfo->EDate, FOMAT_DISPLAY_DATE_TIME) : null); ?>">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Chi tiết lý do ~ Reason  -->
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="desc"><?php echo app('translator')->get('admin.absence.reason'); ?> &nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="reason" placeholder="Reason" name="Reason"
                                           value="<?php echo e(isset($absenceInfo->Reason) ? $absenceInfo->Reason : null); ?>">
                                </div>
                            </div>

                            <!-- Ghi chú - Remark  -->
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="desc"><?php echo app('translator')->get('admin.note'); ?> :</label>
                                <div class="col-sm-9 remark">
                                    <textarea class="form-control" id="remark" name="Remark" rows="3" placeholder="Remark"><?php echo e(isset($absenceInfo->Remark) ? $absenceInfo->Remark : null); ?></textarea>
                                </div>
                            </div>

                            <!-- Request manager -->
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="member"><?php echo app('translator')->get('admin.request_manager'); ?>&nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-sm-9">
                                    <select class='selectpicker show-tick show-menu-arrow' data-actions-box="true" data-size="5" id='select-leader' name="RequestManager[]" multiple>
                                        <?php echo GenHtmlOption($request_manager, 'user_id', 'FullName', isset($absenceInfo->UID) ? $absenceInfo->RequestManager : ''); ?>

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel"><?php echo app('translator')->get('admin.btnCancel'); ?></button>


                    <button type="submit" class="btn btn-primary btn-sm" id="save" ><?php echo app('translator')->get('admin.btnSave'); ?></button>

            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async>
    var sTimeOfUser = '08:30';
    var eTimeOfUser = '17:30';
    var sHour = '08';
    var eHour = '17';
    var sMinute = '30';
    var eMinute = '30';

    $(function () {
        $(".selectpicker").selectpicker();
        SetDateTimePicker($('#sDate'), {
            format: 'DD/MM/YYYY HH:mm',
            // stepping: 5,
        });
        SetDateTimePicker($('#eDate'), {
            format: 'DD/MM/YYYY HH:mm',
            // stepping: 5,
        });

        // if create new record
        if($('#realTime').val() == 'false'){
            // change select user
            $('#selectUser').on('change', function () {
                if ($('#selectUser option:selected').attr('stime-id') != 'null'){
                    sTimeOfUser = $('#selectUser option:selected').attr('stime-id');
                }
                if($('#selectUser option:selected').attr('etime-id') != 'null'){
                    eTimeOfUser = $('#selectUser option:selected').attr('etime-id');
                }
                if($('#selectUser option:selected').attr('etime-id') == 'null'){
                    sHour   = parseInt(sTimeOfUser.split(':')[0]);
                    sMinute = sTimeOfUser.split(':')[1];
                    eHour   = sHour + 9;
                    convertToString = ''+eHour+'';
                    eTimeOfUser = convertToString+':'+sMinute;
                }
                //tách chuỗi thời gian
                sHour   = sTimeOfUser.split(':')[0];
                eHour   = eTimeOfUser.split(':')[0];
                sMinute = sTimeOfUser.split(':')[1];
                eMinute = eTimeOfUser.split(':')[1];

                var now = new Date();

                now = new Date(now.getFullYear(), now.getMonth(), now.getDate(), sHour, sMinute);
                $('#sDate').data("DateTimePicker").options({
                    date: now
                });

                now = new Date(now.getFullYear(), now.getMonth(), now.getDate(), eHour, eMinute);
                $('#eDate').data("DateTimePicker").options({
                    date: now
                });
            });
        }
    });

    if ($("#role").val() == 1){
        getUsersByRoom($('#select-roome option:selected').val());
    }

    $("select[name='RoomID']").on('change', function() {
        getUsersByRoom($(this).val());
    });

    // gat list user with room
    function getUsersByRoom(roomId) {
        ajaxServer(genUrlGet([
            '<?php echo e(route('admin.getUsersByRoom')); ?>',
            '/' + roomId,
        ]), 'POST', null, function (data) {
            html = ``;
            let id = $('#absenceUID').val();
            for (key in data) {
                let strSelected = '';
                if (data[key].id == id) {
                    strSelected = 'selected';
                }
                html += `<option value="${data[key].id}" stime-id="${data[key].STimeOfDay}" etime-id="${data[key].ETimeOfDay}" ${strSelected}>${data[key].FullName}</option>`;
            }
            $("#selectUser").html(html);
            $("#selectUser").selectpicker('refresh');

            if($('#realTime').val() == 'false') {
                if ($('#selectUser option:selected').attr('stime-id') != 'null') {
                    sTimeOfUser = $('#selectUser option:selected').attr('stime-id');
                }
                if ($('#selectUser option:selected').attr('etime-id') != 'null') {
                    eTimeOfUser = $('#selectUser option:selected').attr('etime-id');
                }
                if ($('#selectUser option:selected').attr('etime-id') == 'null') {
                    sHour   = parseInt(sTimeOfUser.split(':')[0]);
                    sMinute = sTimeOfUser.split(':')[1];
                    eHour   = sHour + 9;
                    convertToString     = ''+eHour+'';
                    eTimeOfUser = convertToString+':'+sMinute;
                }

                sHour   = sTimeOfUser.split(':')[0];
                eHour   = eTimeOfUser.split(':')[0];
                sMinute = sTimeOfUser.split(':')[1];
                eMinute = eTimeOfUser.split(':')[1];

                var now = new Date();
                now = new Date(now.getFullYear(), now.getMonth(), now.getDate(), sHour, sMinute);
                $('#sDate').data("DateTimePicker").options({
                    date: now
                });

                now = new Date(now.getFullYear(), now.getMonth(), now.getDate(), eHour, eMinute);
                $('#eDate').data("DateTimePicker").options({
                    date: now
                });
            }
        });
    }

    //click save form
    $('#save').click(function () {
        // pulus value of select disable in data serialize
        var data = $('#absence-form').serializeArray();
        var objRoom = {};
        objRoom['name'] = 'RoomID';
        objRoom['value'] = $('#select-roome option:selected').val();
        var objUser = {};
        objUser['name'] = 'UID';
        objUser['value'] = $('#selectUser option:selected').val();
        data.push(objRoom,objUser);
        console.log(data);
        ajaxGetServerWithLoader("<?php echo e(route('admin.ManagementStore')); ?>", 'POST', data, function (data) {
            if (typeof data.errors !== 'undefined') {
                showErrors(data.errors);
                return ;
            }

            locationPage();
        }, function (data) {
            if (typeof data.responseJSON.error !== 'undefined') {
                showErrors(data.responseJSON.error);
                return ;
            }
        });
    });

    // Jquery draggable (cho phép di chuyển popup)
    $('.modal-dialog').draggable({
        handle: ".modal-header"
    });
</script>

<?php /**PATH D:\DMT\resources\views/admin/includes/absence-detail.blade.php ENDPATH**/ ?>