<form class="form-inline" id="form-general" action="" method="GET">
    <div class="form-group">
        <div>
            <select class='selectpicker show-tick show-menu-arrow' data-live-search="true" data-actions-box="true"
                    data-size="6" id="user"
                    name="User[]" multiple data-title="Chọn thành viên">
                {!! GenHtmlOption($selectUsers, 'id', 'FullName', request()->get("User") ? request()->get("User") : null) !!}
            </select>
        </div>
    </div>
    @if(isset($chooseProjects))
        <div class="form-group">
            <div>
                <select class='selectpicker show-tick show-menu-arrow' data-live-search="true" data-actions-box="true"
                        data-size="6" id="project"
                        name="Project[]" multiple>
                    {!! GenHtmlOption($selectProjects, 'id', 'NameVi', request()->get("Project")) !!}
                </select>
            </div>
        </div>
    @endif
    @if(isset($chooseWorks))
        <div class="form-group">
            <div>
                <select class='selectpicker show-tick show-menu-arrow' data-live-search="true" data-actions-box="true"
                        data-size="6" id="work"
                        name="WorkType[]" multiple data-title="Chọn loại công việc">
                    {!! GenHtmlOption($selectWorks, 'DataValue', 'Name', request()->get("WorkType")) !!}
                </select>
            </div>
        </div>
    @endif
    <div class="form-group">
        <div class="input-group search date">
            <input type="text" class="form-control datepicker" id="sdate" name="StartDate"
                   value="{{ request()->get("StartDate") ? request()->get("StartDate") : \Carbon\Carbon::now()->startOfMonth()->format(FOMAT_DISPLAY_DAY) }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group search date">
            <input type="text" class="form-control datepicker" id="edate" name="EndDate"
                   value="{{ request()->get("EndDate") ? request()->get("EndDate") : \Carbon\Carbon::now()->endOfMonth()->format(FOMAT_DISPLAY_DAY) }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <input type="checkbox" data-toggle="toggle" data-on="Đầu việc"
               data-off="Dự án" {{ $t != "work" ? "checked" : "" }} data-onstyle="primary" data-offstyle="success"
               id="viewMode">
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-search"
                id="btn-search-meeting">@lang('admin.btnSearch')</button>
    </div>
    @can('action',$excel)
        <div class="form-group pull-right">
            <button class="btn btn-success" id="btn-export-daily">@lang('admin.export-excel')</button>
        </div>
    @endcan
</form>

<script type="text/javascript" async>
    $(function () {
        $(".selectpicker").selectpicker({
            noneSelectedText: 'Chọn dự án'
        });
        SetDatePicker($('.date'));

        $('#btn-export-daily').click(function (e) {
            e.preventDefault();
            // var sDate = $('#sdate').val();
            // var eDate = $('#edate').val();
            // var project = $('#project option:selected').val();
            // var array = $('#form-general').serializeArray();
            ajaxGetServerWithLoader("{{ route('export.exportDailyReport') }}", "GET", $('#form-general').serializeArray(),
                function (data) {
                    var req = window.location.search;
                    if (typeof data.errors !== 'undefined') {
                        showErrors(data.errors);
                        return;
                    }
                    window.location.href = '{{ route('export.exportDailyReport') }}' + req;
                });
        });

        $('#viewMode').change(function () {
            $('.loadajax').show();
            const url = $(this).prop("checked")
                ? "{{ route('admin.GeneralReports', ['order' => 'full-name', 'type' => 'asc']) }}"
                : "{{ route('admin.GeneralReports', ['order' => 'full-name', 'type' => 'asc', 't' => 'work']) }}";
            location.href = url + "?" + location.search.substr(1);
        })
        // $('.btn-search').click(function () {
        //
        //     var sDate = moment($('#sdate').val(), 'DD/MM/YYYY').format('YYYYMMDD');
        //     var eDate = moment($('#edate').val(), 'DD/MM/YYYY').format('YYYYMMDD');
        //
        //     var repSDate = sDate.replace(/\D/g, "");
        //     var repEDate = eDate.replace(/\D/g, "");
        //
        //     if (repSDate > repEDate && repSDate != '' && repEDate != '') {
        //         showErrors(['Ngày tìm kiếm không hợp lệ']);
        //     } else {
        //         $('#form-general').submit();
        //     }
        // });
    });
</script>
