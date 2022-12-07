<form id="daily-search-form" class="form-inline" action="<?php echo e(route("admin.TaskRequest")); ?>" method="GET">
    <div class="form-group pull-left margin-r-5">
        <div class="form-group">
            <select class="selectpicker show-tick show-menu-arrow" id="select-user" name="UserID" data-live-search="true" data-size="5" data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
                <option value=""><?php echo app('translator')->get('admin.chooseUser'); ?></option>
                <?php echo GenHtmlOption($users, 'id', 'FullName', isset($request['UserID']) ? $request['UserID'] :''); ?>

            </select>
        </div>
    </div>
    <div class="form-group pull-left margin-r-5">
        <select class="selectpicker show-tick show-menu-arrow" id="select-ProjectID" name="ProjectID" data-live-search="true" data-live-search-placeholder="Search" data-size="6" tabindex="-98">
            <option value=""><?php echo app('translator')->get('admin.overtime.project'); ?></option>
            <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($project->id); ?>"  <?php echo e(isset($request['ProjectID'] ) && $request['ProjectID'] == $project->id ? 'selected' : ''); ?>><?php echo e($project->NameVi); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="form-group pull-left margin-r-5">
        <div class="input-group search date" id="date-daily-report">
            <input type="text" class="form-control" id="date-input" name="StartTime" value="<?php echo e(!isset($request['StartTime']) ? Carbon\Carbon::now()->firstOfMonth()->format(FOMAT_DISPLAY_DAY) : $request['StartTime']); ?>">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group pull-left margin-r-5">
        <div class="input-group search date" id="">
            <input type="text" class="form-control" id="date-input_end" name="EndTime" value="<?php echo e(!isset($request['EndTime']) ? Carbon\Carbon::now()->endOfMonth()->format(FOMAT_DISPLAY_DAY) : $request['EndTime']); ?>">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group pull-left margin-r-5">
        <button type="button" class="margin-r-5 btn btn-primary btn-search" id="btn-search-daily" ><?php echo app('translator')->get('admin.btnSearch'); ?></button>
        
    </div>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('action', $add)): ?>
        <div class="form-group pull-left margin-r-5">
            <button type="button" class="btn btn-primary" id="add_daily" req="<?php echo e(\Request::get('UserID')); ?>"><?php echo app('translator')->get('taskrequest::admin.task-request.add_new'); ?></button>
        </div>
    <?php endif; ?>

</form>

<script language="javascript" async>
    SetDatePicker($('.date'), {
        todayHighlight: true,
    });
    $('.selectpicker').selectpicker();
    $(".datepicker").datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true,
    });

    var idUser = $('#action-select-user option:selected').val() + '';

    function getUsersByActive(val) {
        ajaxServer(genUrlGet([
            '<?php echo e(route('admin.getUsersByActive')); ?>',
            '/' + val,
        ]), 'GET', null, function(data) {
            html = ``;
            html += `<option value="">Chọn nhân viên</option>`;
            for(key in data) {
                var strSelected = '';
                if(data[key].id == idUser) {
                    strSelected = 'selected';
                }
                html += `<option value="`+data[key].id+`" ${strSelected}>`+data[key].FullName+`</option>`;
            }
            $('#select-user').html(html);
            $('#select-user').selectpicker('refresh');
        });
    }
    $('#select-user').selectpicker();

    $('.btn-search').click(function () {
        var userId = $("#select-user option:selected").val() + '';
        var sDate = moment($('#date-input').val(),'DD/MM/YYYY').format('YYYYMMDD');
        var eDate = moment($('#date-input_end').val(),'DD/MM/YYYY').format('YYYYMMDD');
        var repSDate = sDate.replace(/\D/g, "");
        var repEDate = eDate.replace(/\D/g, "");

        if (repSDate > repEDate){
            showErrors(['Ngày tìm kiếm không hợp lệ']);
        }else{
            $('#daily-search-form').submit();
        }

    });

    $("select[name='UserID']").on('change', function() {
        idUser = $('#action-select-user option:selected').val() + '';
    });


    $('#add_daily').click(function (event) {
        event.preventDefault();
        ajaxGetServerWithLoader("<?php echo e(route('admin.TaskRequestDetail')); ?>", "GET", null, function (data) {
            $('#popupModal').empty().html(data);
            $('#task-request-detail').modal('show');
        });
    })

</script>
<?php /**PATH D:\DMT\Modules/TaskRequest\Resources/views/task-request-search.blade.php ENDPATH**/ ?>