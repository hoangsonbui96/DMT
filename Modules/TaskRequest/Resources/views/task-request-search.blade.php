<form id="daily-search-form" class="form-inline" action="{{ route("admin.TaskRequest") }}" method="GET">
    <div class="form-group pull-left margin-r-5">
        <div class="form-group">
            <select class="selectpicker show-tick show-menu-arrow" id="select-user" name="UserID" data-live-search="true" data-size="5" data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
                <option value="">@lang('admin.chooseUser')</option>
                {!! GenHtmlOption($users, 'id', 'FullName', isset($request['UserID']) ? $request['UserID'] :'') !!}
            </select>
        </div>
    </div>
    <div class="form-group pull-left margin-r-5">
        <select class="selectpicker show-tick show-menu-arrow" id="select-ProjectID" name="ProjectID" data-live-search="true" data-live-search-placeholder="Search" data-size="6" tabindex="-98">
            <option value="">@lang('admin.overtime.project')</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}"  {{ isset($request['ProjectID'] ) && $request['ProjectID'] == $project->id ? 'selected' : '' }}>{{ $project->NameVi }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group pull-left margin-r-5">
        <div class="input-group search date" id="date-daily-report">
            <input type="text" class="form-control" id="date-input" name="StartTime" value="{{!isset($request['StartTime']) ? Carbon\Carbon::now()->firstOfMonth()->format(FOMAT_DISPLAY_DAY) : $request['StartTime'] }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group pull-left margin-r-5">
        <div class="input-group search date" id="">
            <input type="text" class="form-control" id="date-input_end" name="EndTime" value="{{!isset($request['EndTime']) ? Carbon\Carbon::now()->endOfMonth()->format(FOMAT_DISPLAY_DAY) : $request['EndTime'] }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group pull-left margin-r-5">
        <button type="button" class="margin-r-5 btn btn-primary btn-search" id="btn-search-daily" >@lang('admin.btnSearch')</button>
        {{-- <button type="button" class="btn btn-primary btn-show-summary">@lang('admin.daily.show_daily_report')</button> --}}
    </div>

    @can('action', $add)
        <div class="form-group pull-left margin-r-5">
            <button type="button" class="btn btn-primary" id="add_daily" req="{{\Request::get('UserID')}}">@lang('taskrequest::admin.task-request.add_new')</button>
        </div>
    @endcan

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
            '{{ route('admin.getUsersByActive') }}',
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
        ajaxGetServerWithLoader("{{ route('admin.TaskRequestDetail') }}", "GET", null, function (data) {
            $('#popupModal').empty().html(data);
            $('#task-request-detail').modal('show');
        });
    })

</script>
