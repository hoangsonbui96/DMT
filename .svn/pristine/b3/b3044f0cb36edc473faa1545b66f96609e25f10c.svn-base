<div id="error" class="tab-pane fade in active">
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <input type="hidden" name="TaskID" value="{{ $task->id }}">
            <input type="hidden" name="ProjectID" value="{{ $task->ProjectID }}">
            <input type="hidden" name="ScreenName" value="{{ isset($reportLast) ? $reportLast->ScreenName : '' }}">
            <div class="form-group">
                <label class="control-label col-sm-4" for="Date">Ngày duyệt &nbsp;<sup class="text-red">*</sup>:</label>
                <div class="col-sm-8">
                    <div class="input-group date" id="sDate">
                        <input type="text" class="form-control date-input" id="modal-date-input" name="Date" placeholder="@lang('admin.working-day')"
                               value="{{ isset($reportLast) ? \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $reportLast->DateCreate)->format('H:i d/m/Y') : \Illuminate\Support\Carbon::today()->format('d/m/Y')}}" disabled>
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4" for="accepted_by">Người báo lỗi<sup class="text-red">*</sup>:</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="accepted_by" disabled value="{{ isset($error) ? $error->user->FullName : '' }}">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4" for="note">Mô tả lỗi &nbsp;<sup class="text-red">*</sup>:</label>
                <div class="col-sm-8" maxlength="200">
                    <textarea class="form-control" rows="4" id="contents" name="Contents" disabled
                              placeholder="Nội dung">{{ isset($error) ? $error->Descriptions : '' }}</textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4" for="note">Ghi chú lỗi:</label>
                <div class="col-sm-8" maxlength="200">
                    <textarea class="form-control" rows="4" id="contents" name="Note" disabled
                              placeholder="Nội dung">{{ isset($error) ? $error->Note : '' }}</textarea>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="form-group">
                <label class="control-label col-sm-4" for="perAddress">@lang('admin.daily.time_working')&nbsp;<sup class="text-red">*</sup>:</label>
                <div class="col-sm-8 div-work-time">
                    <input type="text" class="form-control working_time" placeholder="@lang('admin.daily.time_working')" name="WorkingTime"
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
                    <input type="range" class=" progressing" name="Progressing" min="{{ $result }}" max="100"
                           value="{{ isset($reportLast) ? $reportLast->Progressing : 0 }}" oninput="this.nextElementSibling.value = this.value" step="5">
                    <output style="display: inline">{{ isset($reportLast) ? round($reportLast->Progressing) : 0 }}</output><span>%</span>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4" for="note">@lang('admin.contents')&nbsp;<sup class="text-red">*</sup>:</label>
                <div class="col-sm-8" maxlength="200">
                        <textarea class="form-control" rows="4" id="contents" name="Contents"
                          placeholder="Nội dung">{{ isset($reportLast) ? $reportLast->Contents : '' }}</textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4" for="note">@lang('admin.note') :</label>
                <div class="col-sm-8">
                    <textarea class="form-control note" rows="4" id="note" maxlength="200"
                              name="Note" placeholder="@lang('admin.note')">{{ isset($reportLast) ? $reportLast->Note : '' }}</textarea>
                </div>
            </div>
            <div class="from-group pull-right">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="button" class="btn btn-primary btn-sm save-form" name="saveBtn">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async>
   var submitErrorReport = () => {
        let data = $('#form-report').serializeArray();
       let headers = {
           'Authorization': 'Bearer {{ \Illuminate\Support\Facades\Session::get('api-user') }}',
       };
        ajaxGetServerWithLoaderAPI("{{ route('admin.ApiReportErrorReview')}}", headers, 'POST', data, function (data) {
            if (data.success === true && data.status_code === 200){
                $('#list-not-finish').empty();
                $('#list-working').empty();
                $('#list-finish').empty();
                $('#list-review').empty()
                // loadData();
                $('#btn-search').click();
                loadProjectInfo();
                $('#popupModal').find('.modal').modal('hide');
            }
        }, function (data) {
            if (data.responseJSON.success === false || data.responseJSON.success === null) {
                showErrors(data.responseJSON.error);
                return null;
            }

        })
    }

    $('button[name="saveBtn"]').click(e => {
        e.preventDefault();
        e.stopPropagation();
        submitErrorReport();
    })
</script>
