<form class="form-inline" id="form-search-overtimes" action="" method="">
    <div class="form-group pull-left margin-r-5">
        <div class="input-group search">
            <input type="search" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
        </div>
    </div>
    <div class="form-group">
        <select class="selectpicker show-tick show-menu-arrow" id="select-user" name="UserID" data-live-search="true" data-size="5" data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
        <option value="">@lang('admin.chooseUser')</option>
                {!! GenHtmlOption($users, 'id', 'FullName', isset($request['UserID']) ? $request['UserID'] :'') !!}
        </select>
    </div>
    <div class="form-group">
        <select class="selectpicker show-tick show-menu-arrow" id="select-Participant" name="ProjectID" data-live-search="true" data-live-search-placeholder="Search" data-size="6" tabindex="-98">
            <option value="">@lang('admin.overtime.project')</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}"  {{ isset($request['ProjectID'] ) && $request['ProjectID'] == $project->id ? 'selected' : '' }}>{{ $project->NameVi }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <div class="input-group search date">
            <input type="text" class="form-control datepicker" id="sdate" placeholder="Ngày bắt đầu" autocomplete="off" name="OvertimeDate[]" value="{{ isset($request['OvertimeDate'] ) ? $request['OvertimeDate'][0] : GetStartMoth() }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group search date">
            <input type="text" class="form-control datepicker" id="edate" placeholder="Ngày kết thúc"  autocomplete="off"  name="OvertimeDate[]" value="{{ isset($request['OvertimeDate'] ) ? $request['OvertimeDate'][1] : GetEndMoth()}}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <button type="button" class="margin-r-5 btn btn-primary btn-search" id="btn-search-meeting">@lang('admin.search')</button>
    @can('action',$add)
        <button type="button" class="btn btn-primary btn-detail pull-right" id="add_new_overtime" >@lang('admin.overtime.add_new')</button>
    </div>
    @endcan
    <div class="clearfix"></div>
</form>
<script type="text/javascript" async>
    $(".selectpicker").selectpicker();
    $(function () {
        SetDatePicker($('.date'));
        $('.btn-search').click(function () {
        var sDate = moment($('#sdate').val(),'DD/MM/YYYY').format('YYYYMMDD');
        var eDate = moment($('#edate').val(),'DD/MM/YYYY').format('YYYYMMDD');
        var repSDate = sDate.replace(/\D/g, "");
        var repEDate = eDate.replace(/\D/g, "");
        var userId = $("#select-user option:selected").val() + '';
        if (repSDate > repEDate && repSDate != '' && repEDate != ''){
            showErrors(['Ngày tìm kiếm không hợp lệ']);
        }else{
            $('#form-search-overtimes').submit();
        }
        });
    });
</script>
