<form class="form-inline" id="form-search-report" action="" method="">
    <div class="form-group">
        <div class="input-group search date">
            <input type="text" class="form-control datepicker" id="stime"  placeholder="Ngày bắt đầu" autocomplete="off"  name="date[]"
                   value="{{ isset($request['date'] ) ? $request['date'][0] : GetStartMoth() }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group search date">
            <input type="text" class="form-control datepicker" id="etime" placeholder="Ngày kết thúc" name="date[]" autocomplete="off"
                   value="{{ isset($request['date'] ) ? $request['date'][1] : GetEndMoth() }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-primary btn-search" id="btn-search-meeting">@lang('admin.btnSearch')</button>
         {{-- @can('action',$export)
            <button class="btn btn-success" id="btn-export">@lang('admin.export-excel')</button>
        @endcan --}}
    </div>
</form>
<script type="text/javascript" async>
    $(function () {
        SetDatePicker($('.date'));
        var sTime = $('#stime').val();
        var eTime = $('#etime').val();

        //js click export excel
        $('#btn-export').click(function (e) {
            e.preventDefault();
            window.location.href = '{{ route('export.ExportOvertimes') }}?date[0]='+sTime+'&date[1]='+eTime;
        });

        $('.btn-search').click(function () {
            var sTime = moment($('#stime').val(),'DD/MM/YYYY').format('YYYYMMDD');
            var eTime = moment($('#etime').val(),'DD/MM/YYYY').format('YYYYMMDD');
            var repSTime = sTime.replace(/\D/g, "");
            var repETime = eTime.replace(/\D/g, "");

            if (repSTime > repETime && repSTime != '' && repETime != ''){
                showErrors(['Ngày tìm kiếm không hợp lệ']);
            }else{
                $('#form-search-report').submit();
            }
        });
    });
</script>
