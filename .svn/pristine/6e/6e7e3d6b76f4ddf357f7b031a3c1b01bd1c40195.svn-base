<style>
    .date-input {
        padding-left: 1.25rem;
    }

</style>
@php
$diff_day = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($task->StartDate));
$option = '';
if ($diff_day < 1) {
    $option = '-' . $diff_day . 'd';
} else {
    $option = '-1d';
}
@endphp <div id="report" class="tab-pane fade in active">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <input type="hidden" name="taskId" value="{{ $task->id }}" id="id">
            <input type="hidden" name="projectId" value="{{ $task->ProjectId }}" id="id">
            <div class="form-group">
                <label class="control-label col-sm-4" for="Date">@lang('admin.working-day') &nbsp;<sup
                        class="text-red">*</sup>:</label>
                <div class="col-sm-8">
                    <div class="input-group date" id="sDate">
                        <input type="text" class="form-control date-input datepicker" id="modal-date-input" name="Date"
                            placeholder="@lang('admin.working-day')"
                            {{ $task->Status == 3 || $task->Status == 4 ? 'disabled' : '' }}
                            value="{{ (isset($task->lastReport) && $task->Status > 2)
                                ? \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $task->lastReport->DateCreate)->format('d/m/Y')
                                : \Illuminate\Support\Carbon::today()->format('d/m/Y') }}">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4" for="screen_name">@lang('admin.daily.Screen_Name')
                    <sup class="text-red">*</sup>
                    :</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control screen_name" id="" placeholder="Tên màn hình"
                        name="ScreenName" maxlength="50"
                        {{ $task->Status == 3 || $task->Status == 4 ? 'disabled' : '' }}
                        value="{{ (isset($task->lastReport) && $task->Status > 2) ? $task->lastReport->ScreenName : '' }}">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4" for="type">@lang('projectmanager::admin.task.Type')&nbsp;
                    <sup class="text-red">*</sup>
                    :</label>
                <div class="col-sm-8">
                    <div>
                        <select class='selectpicker show-tick show-menu-arrow' name="type" data-size="6"
                            data-actions-box="true" data-width="100%">
                            @if (isset($task->Type))
                                <option value="{{ $task->typeName->id }}" selected>{{ $task->typeName->name }}</option>
                            @else
                                <option value="" selected>Loại khác</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-4" for="contents">@lang('admin.contents')&nbsp;<sup
                        class="text-red">*</sup>:</label>
                <div class="col-sm-8">
                    <textarea class="form-control" rows="5" id="contents" name="Contents" maxlength="200"
                        {{ $task->Status == 3 || $task->Status == 4 ? 'disabled' : '' }}
                        placeholder="Nội dung">{{ (isset($task->lastReport) && $task->Status > 2) ? $task->lastReport->Contents : '' }}</textarea>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label class="control-label col-sm-4" for="perAddress">@lang('projectmanager::admin.WorkedTime')&nbsp;<sup
                        class="text-red">*</sup>:</label>
                <div class="col-sm-8 div-work-time">
                    <input type="number" min="0" class="form-control working_time" placeholder="@lang('projectmanager::admin.WorkedTime')"
                        name="WorkedTime" id="WorkedTime" readonly value="{{ $task->WorkedTime ?? 0 }}">
                </div>
            </div>

            <div class="form-group" {{ $task->Status == 3 || $task->Status == 4 ? 'hidden' : '' }}>
                <label class="control-label col-sm-4" for="perAddress">@lang('admin.daily.time_working')&nbsp;<sup
                        class="text-red">*</sup>:</label>
                <div class="col-sm-8 div-work-time">
                    <input type="number" min="0" class="form-control working_time" placeholder="@lang('admin.daily.time_working')"
                        name="WorkingTime" id="WorkingTime" value="">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-sm-4" for="curAddress">@lang('admin.daily.progressing')&nbsp;<sup
                        class="text-red">*</sup>:</label>
                <div class="col-sm-8 div-progressing">
                    <input type="range" class="progressing" placeholder="Tiến độ - (80.5%)" name="Progressing"
                        min="{{ $task->Progress }}" max="100"
                        value="{{ $request['is_fast_report'] == true ? 100 : $task->Progress ?? 0 }}"
                        oninput="this.nextElementSibling.value = this.value" step="5"
                        {{ $task->Status == 3 || $task->Status == 4 ? 'disabled' : '' }}>
                    @if ($request['is_fast_report'] == true)
                        <output style="display: inline">100</output><span>%</span>
                    @else
                        <output style="display: inline">{{ round($task->Progress) }}</output>
                        <span>%</span>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4">Giờ trễ/vượt(h):</label>
                <div class="col-sm-4 div-progressing">
                    <input type="number" min="0" class="form-control progressing" placeholder="" name="Delay" readonly
                        {{ $task->Status == 3 || $task->Status == 4 ? 'disabled' : '' }}
                        value="{{ isset($task->lastReport) && $task->lastReport->Delay != 0 ? $task->lastReport->Delay : '' }}">
                </div>
                <div class="col-sm-4 div-progressing">
                    <input type="number" min="0" class="form-control progressing" placeholder="" name="Soon" readonly
                        {{ $task->Status == 3 || $task->Status == 4 ? 'disabled' : '' }}
                        value="{{ isset($task->lastReport) && $task->lastReport->Soon != 0 ? $task->lastReport->Soon : '' }}">
                </div>
                <small class="help-block text-right" style="padding-right: 1.5em">Thời gian trễ/vượt tiến độ là thời
                    gian chênh lệch giữa thực hiện thực tế với dự kiến!</small>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4" for="note">@lang('admin.note') :</label>
                <div class="col-sm-8">
                    <textarea class="form-control note" rows="5" id="note" name="Note" placeholder="@lang('admin.note')"
                        {{ $task->Status == 3 || $task->Status == 4 ? 'disabled' : '' }}>{{ (isset($task->lastReport) && $task->Status > 2) ? $task->lastReport->Note : 'Task: ' . $task->Name }}</textarea>
                </div>
            </div>
            <div class="from-group pull-right">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                    id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm save-form" name="saveBtn"
                    {{ $task->Status == 3 || $task->Status == 4 ? 'disabled' : '' }}>@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async>
    $(document).ready(() => {
        $('select.selectpicker').selectpicker();
        SetDatePicker($('.date'), {
            todayHighlight: true,
            dateFormat: 'dd-mm-yy',
            // startDate: "{{ $option }}",
            // endDate: '+0d'
        });
        $('input[name="Delay"]').click(() => {
            const time_soon_input = $('input[name="Soon"]')
            $(time_soon_input).val('');
        })
        $('input[name="Soon"]').click(() => {
            const time_delay_input = $('input[name="Delay"]')
            $(time_delay_input).val('');
        })
        $('button[name="saveBtn"]').click((e) => {
            e.preventDefault();
            submitFormReport();
        })

        $('#WorkingTime').on('keyup', function() {
            calculateWorkedTime()
        })
        $("input[name='Progressing']").on('change', function() {
            calculateWorkedTime()
        })
    })

    function calculateWorkedTime() {
        if ({{ $task->Duration }} > 0) {
            let subtime = {{ $task->Duration - $task->dailyReports->sum('WorkingTime') }} - $('#WorkingTime').val();
            if (subtime > 0) {
                $("input[name='Delay']").val(0);
                if ($("input[name='Progressing']").val() == 100) {
                    $("input[name='Soon']").val(Math.abs(subtime));
                } else {
                    $("input[name='Soon']").val(0);
                };
            } else {
                $("input[name='Delay']").val(Math.abs(subtime));
                $("input[name='Soon']").val(0);
            }
            $("input[name='WorkedTime']").val(parseInt($(this).val()) + {{ $task->WorkedTime ?? 0 }})
        }
    }

    function submitFormReport() {
        let data = $('#form-report').serializeArray();
        let ajaxUrl = "{{ route('admin.reportTask') }}"
        ajaxGetServerWithLoader(
            genUrlGet([ajaxUrl]),
            'POST',
            data,
            function(res) {
                if (res.success == true) {
                    $('#list-not-finish').empty();
                    $('#list-working').empty();
                    $('#list-finish').empty();
                    $('#list-review').empty();
                    $('#btn-search').click();
                    // loadData();
                    loadProjectInfo();
                    $('#popupModal').find('.modal').modal('hide');
                    showSuccess(res.mes);
                } else {
                    showErrors(res.mes);
                }
            }
        );
    }

    function clearFormReport() {
        $("input[name='ScreenName']").val('');
        $('#work-type').selectpicker("val", "");
        $("textarea[name='Contents']").val('');
        $("input[name='WorkingTime']").val('');
        $("output").val(0);
        $("input[name='Progressing']").attr("min", 0);
        $("input[type='range']").val(0);
        $("input[name='Delay']").val('');
        $("input[name='Soon']").val('');
        $("textarea[name='Note']").val('');
    }
</script>
