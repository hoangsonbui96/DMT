<form class="form-inline" id="absenceReport-search-form">
    <div style="display: none;">
        <input type="text" class="form-control dtpicker" id="species" name="species"
                   value="{{$species}}">
    </div>
    <div class="form-group pull-left">
        <select class="selectpicker show-tick show-menu-arrow" id="select-user" name="UID" data-live-search="true" data-size="5"
                data-live-search-placeholder="Search" data-width="220px" data-actions-box="true" tabindex="-98">
            <option value="">@lang('admin.staff')</option>
            {!! GenHtmlOption($users, 'id', 'FullName', isset($request['UID']) ? $request['UID'] : '') !!}
        </select>
    </div>

    <div class="form-group pull-left">
        <div class="input-group search date">
            <input type="text" class="form-control dtpicker" id="s-date" placeholder="@lang('admin.startDate')" name="date[]"
                   value="{{ isset($request['date'] ) ? $request['date'][0] : \Carbon\Carbon::now()->startOfMonth()->format(FOMAT_DISPLAY_DAY) }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>

    <div class="form-group pull-left">
        <div class="input-group search date">
            <input type="text" class="form-control dtpicker" id="e-date" placeholder="@lang('admin.endDate')" name="date[]"
                   value="{{ isset($request['date'] ) ? $request['date'][1] : \Carbon\Carbon::now()->endOfMonth()->format(FOMAT_DISPLAY_DAY) }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group pull-left width3">
        <div class="input-group">
            <button type="button" class="btn btn-primary" id="btn-searchAbReport" >@lang('admin.btnSearch')</button>
        </div>
    </div>
    <div class="form-group pull-right">
        @can('action', $export)
            <a class="btn btn-success" id="AbRportExport">@lang('admin.export-excel')</a>
        @endcan
    </div>
</form>
<script type="text/javascript" async>
    SetDatePicker($('.date'));
    $(".selectpicker").selectpicker();
    $('#btn-searchAbReport').click(function () {
        var sDate = moment($('#s-date').val(),'DD/MM/YYYY').format('YYYYMMDD');
        var eDate = moment($('#e-date').val(),'DD/MM/YYYY').format('YYYYMMDD');
        var repSDate = sDate.replace(/\D/g, "");
        var repEDate = eDate.replace(/\D/g, "");

        if (repSDate > repEDate && repSDate != '' && repEDate != ''){
            showErrors(['Ngày tìm kiếm không hợp lệ']);
        }else{
            $('#absenceReport-search-form').submit();
        }
    });
</script>
