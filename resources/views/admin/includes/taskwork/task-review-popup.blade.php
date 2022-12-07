<div class="modal fade" id="modal-error-review" tabindex="-1" role="dialog" aria-labelledby="modal-list-user" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title" style="word-break: break-word;">Báo lỗi [{{ $task->Name }}]</h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <form class="form-horizontal" action="" method="POST" id="form-review-error"  style="padding-top: 1em">
                    <input type="hidden" name="id" class="form-control hidden " value="{{ $task->id }}">
                    <div class="save-errors"></div>
                    <div class="tab-content">
                        <div id="report" class="tab-pane fade in active">
                            <meta name="csrf-token" content="{{ csrf_token() }}">
                            <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" >Người thực hiện<sup class="text-red">*</sup>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="NameVi" value="{{ $member }}" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4">Số lần báo lỗi<sup class="text-red">*</sup>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="ErrorCount" value="{{ $task->NumberReturn + 1}}" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4">@lang('admin.task-working.description')&nbsp;<sup class="text-red">*</sup>:</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" maxlength="200" rows="5" name="Descriptions" placeholder="@lang('admin.task-working.description')"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label col-sm-3">@lang('admin.times')&nbsp;<sup
                                                class="text-red">*</sup>:</label>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <div class="col-sm-6 col-xs-3" style="padding-right: 5px">
                                                    <div class="input-group date">
                                                        <input type="text" class="form-control datepicker"
                                                               placeholder="@lang('admin.startDate')" name="StartDate"
                                                               autocomplete="off"
                                                               value="{{ isset($task) ? FomatDateDisplay($task->StartDate, FOMAT_DISPLAY_DAY) : null }}">
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-xs-3" style="padding-left: 5px">
                                                    <div class="input-group date">
                                                        <input type="text" class="form-control datepicker"
                                                               placeholder="@lang('admin.endDate')" name="EndDate" autocomplete="off"
                                                               value="{{ isset($task) ? FomatDateDisplay($task->EndDate, FOMAT_DISPLAY_DAY) : null }}">
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" >@lang('admin.daily.progressing')&nbsp;<sup class="text-red">*</sup>:</label>
                                        <div class="col-sm-9 div-progressing">
                                            <input type="range" class=" progressing form-range" placeholder="Tiến độ - (80.5%)" name="Progressing" max="{{ isset($last_report) ? (int)$last_report->Progressing : 0 }}" min="0"
                                                   value="{{ isset($last_report) ? (int)$last_report->Progressing : 0 }}" oninput="this.nextElementSibling.value = this.value" step="5">
                                            <output style="display: inline">{{ isset($last_report) ? (int)$last_report->Progressing : 0 }}</output><span>%</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" >@lang('admin.note') :</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control " rows="5" maxlength="200" name="Note" placeholder="@lang('admin.note')"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">Hủy</button>
                <button type="submit" class="btn btn-primary btn-sm save-form" name="saveBtn">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script>
    SetDatePicker($('.date'), {
        todayHighlight: true,
    });
    $('.selectpicker').selectpicker();
    $(".datepicker").datepicker({
        autoclose: true,
        todayHighlight: true
    });

    $('button[name="saveBtn"]').click(() => {
            let data = $('#form-review-error').serializeArray();
            ajaxGetServerWithLoaderAPI("{{ route('admin.ApiAddErrorReview') }}", headers_u, 'POST', data, response => {
                if (response.status_code === 200 && response.success === true) {
                    $('#list-not-finish').empty();
                    $('#list-working').empty();
                    $('#list-finish').empty();
                    $('#list-review').empty()
                    $('#popupModal').find('.modal').modal('hide');
                    // loadData();
                    $('#btn-search').click();
                    loadProjectInfo();
                }
            }, res => {
                showErrors(res.responseJSON.error);
            });
    });

</script>
