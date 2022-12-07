<div id="report" class="tab-pane fade in active">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <input type="hidden" name="id" value="{{ $id }}" id="id">
            <div class="form-group">
                <label class="control-label col-sm-4" for="Date">@lang('admin.working-day') &nbsp;<sup class="text-red">*</sup>:</label>
                <div class="col-sm-8">
                    <div class="input-group date" id="sDate">
                        <input type="text" class="form-control date-input datepicker" id="modal-date-input" name="Date" placeholder="@lang('admin.working-day')" {{ ($task->Status == 3 || $task->Status ==  4) ? 'disabled' : '' }}
                        value="{{ isset($reportLast) ? \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $reportLast->DateCreate)->format('d/m/Y') : \Illuminate\Support\Carbon::today()->format('d/m/Y')}}" disabled>
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4" for="screen_name">@lang('admin.daily.Screen_Name'):</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control screen_name" id="" placeholder="Tên màn hình" name="ScreenName" maxlength="50" {{ ($task->Status == 3 || $task->Status ==  4) ? 'disabled' : '' }}
                    value="{{ isset($reportLast) ? $reportLast->ScreenName : '' }}">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4" for="Type Of Work">@lang('admin.daily.Type_Of_Work') &nbsp<sup class="text-red">*</sup>:</label>
                <div class="col-sm-8">
                    <select class="selectpicker show-tick show-menu-arrow sl-user" id="work-type" data-size="5" name="TypeWork"
                            data-live-search="true" data-live-search-placeholder="Search" data-width="100%" data-size="5" {{ ($task->Status == 3 || $task->Status ==  4) ? 'disabled' : '' }}>
                        <option value="">@lang('admin.daily.chooseWorkType')</option>
                        {!! GenHtmlOption($list_works, 'DataValue', 'Name', isset($reportLast) ? $reportLast->TypeWork : '' ) !!}
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4" for="note">@lang('admin.contents')&nbsp;<sup class="text-red">*</sup>:</label>
                <div class="col-sm-8">
                    <textarea class="form-control" rows="5" id="contents" name="Contents" maxlength="200" {{ ($task->Status == 3 || $task->Status ==  4) ? 'disabled' : '' }}
                    placeholder="Nội dung">{{ isset($reportLast) ? $reportLast->Contents : '' }}</textarea>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label class="control-label col-sm-4" for="perAddress">@lang('admin.daily.time_working')&nbsp;<sup class="text-red">*</sup>:</label>
                <div class="col-sm-8 div-work-time">
                    <input type="text" class="form-control working_time" placeholder="@lang('admin.daily.time_working')" name="WorkingTime" {{ ($task->Status == 3 || $task->Status ==  4) ? 'disabled' : '' }}
                    value="{{ isset($reportLast) ? $reportLast->WorkingTime : '' }}">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4" for="curAddress">@lang('admin.daily.progressing')&nbsp;<sup class="text-red">*</sup>:</label>
                <div class="col-sm-8 div-progressing">
                    @php
                        $result = 0;
                        if (isset($reportLast)){
                            if ($reportLast->Progressing == 100){
                                $result = 0;
                            }else{
                                $result = $reportLast->Progressing;
                            }
                        }
                    @endphp
                    @if(isset($reportLast))
                    @endif
                    <input type="range" class=" progressing" placeholder="Tiến độ - (80.5%)" name="Progressing" min="{{ $result }}" max="100"
                           value="100" oninput="this.nextElementSibling.value = this.value" step="5" {{ ($task->Status == 3 || $task->Status ==  4) ? 'disabled' : '' }}>
                    <output style="display: inline">100</output><span>%</span>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4">Giờ trễ/vượt(h):</label>
                <div class="col-sm-4 div-progressing">
                    <input type="text" class="form-control progressing" placeholder="Giờ trễ - (2.5)" name="Timedelay" {{ ($task->Status == 3 || $task->Status ==  4) ? 'disabled' : '' }}
                    value="{{ (isset($reportLast) && $reportLast->Delay != 0) ? $reportLast->Delay : '' }}">
                </div>
                <div class="col-sm-4 div-progressing">
                    <input type="text" class="form-control progressing" placeholder="Giờ vượt + (2.5)" name="Timesoon" {{ ($task->Status == 3 || $task->Status ==  4) ? 'disabled' : '' }}
                    value="{{ (isset($reportLast) && $reportLast->Soon != 0) ? $reportLast->Soon : '' }}">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4" for="note">@lang('admin.note') :</label>
                <div class="col-sm-8">
                    <textarea class="form-control note" rows="5" id="note" name="Note" placeholder="@lang('admin.note')" {{ ($task->Status == 3 || $task->Status ==  4) ? 'disabled' : '' }}>{{ isset($reportLast) ? $reportLast->Note : '' }}</textarea>
                </div>
            </div>
            <div class="from-group pull-right">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm save-form" name="saveBtn" {{ ($task->Status == 3 || $task->Status ==  4) ? 'disabled' : '' }}>@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async>
    function submitFormReport(){
        let data = arrayToJson($('#form-report'));
        data['Date'] = $('input[name="Date"]').val();
        let headers = {
            'Authorization': 'Bearer {{ \Illuminate\Support\Facades\Session::get('api-user') }}',
            'Content-type': 'application/json'
        };
        ajaxGetServerWithLoaderAPI("{{ route('admin.ApiReportTask')}}", headers, 'POST', JSON.stringify(data), function (data) {
            if (data.success === true && data.status_code === 200){
                $('#list-not-finish').empty();
                $('#list-working').empty();
                $('#list-finish').empty();
                $('#list-review').empty()
                $('#btn-search').click();
                loadProjectInfo();
                $('#popupModal').find('.modal').modal('hide');
            }
        }, function (data) {
            if (data.responseJSON.success === false || data.responseJSON.success === null) {
                showErrors(data.responseJSON.error);
            }
        })
    }

    $(document).ready(() => {
        $('select.selectpicker').selectpicker();
        SetDatePicker($('.date-input'), { dateFormat: 'dd-mm-yy'})
        $('input[name="Timedelay"]').click(() => {
            const time_soon_input = $('input[name="Timesoon"]')
            $(time_soon_input).val('');
        })
        $('input[name="Timesoon"]').click(() => {
            const time_delay_input = $('input[name="Timedelay"]')
            $(time_delay_input).val('');
        })
        $('button[name="saveBtn"]').click((e) => {
            e.preventDefault();
            submitFormReport();
        })

    })
</script>
