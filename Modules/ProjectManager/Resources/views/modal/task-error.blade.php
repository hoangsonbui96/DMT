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
                    <input type="hidden" name="taskId" class="form-control hidden " value="{{ $task->id }}">
                    <input type="hidden" name="projectId" class="form-control hidden " value="{{ $task->project->id }}">
                    <div class="save-errors"></div>
                    <div class="tab-content">
                        <div id="report" class="tab-pane fade in active">
                            <meta name="csrf-token" content="{{ csrf_token() }}">
                            <div class="row">
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label col-sm-4" >Người thực hiện<sup class="text-red">*</sup>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="Member" value="{{ $task->member->FullName }}" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4">Số lần báo lỗi<sup class="text-red">*</sup>:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="NumberReturn" value="{{ $task->NumberReturn + 1}}" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-4">@lang('projectmanager::admin.ErrorDes')&nbsp;:</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" maxlength="200" rows="5" name="Content" placeholder="@lang('projectmanager::admin.ErrorDes')">{{ $task->Issue ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" for="IssuedTime">@lang('projectmanager::admin.Times')&nbsp;<sup class="text-red">*</sup>:</label>
                                        <div class="col-sm-9">
                                            <div class="input-group datetime" id="issue-time">
                                                <input type="text" class="form-control datetimepicker-input"
                                                id="IssuedTime"
                                                placeholder="@lang('projectmanager::admin.task.IssuedTime')"
                                                name="IssuedTime" autocomplete="off"
                                                value="">

                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" >@lang('admin.daily.progressing')&nbsp;<sup class="text-red">*</sup>:</label>
                                        <div class="col-sm-9 div-progressing">
                                            <input type="range" class=" progressing form-range" placeholder="Tiến độ - (80.5%)" name="Progressing" max="{{ $task->Progress ?? 0 }}" min="0"
                                                   value="0" oninput="this.nextElementSibling.value = this.value" step="5">
                                            <output style="display: inline">0</output><span>%</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-3" >@lang('admin.note') :</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control " rows="5" maxlength="200" name="Note" placeholder="@lang('admin.note')">{{ isset($task->lastReport) ? $task->lastReport->Note : '' }}</textarea>
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
                <button type="submit" class="btn btn-primary btn-sm save-form" id="btnErrorSubmit" name="saveBtn">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
      
    });
    myDateTimePicker($('#issue-time'), {
        format: 'DD/MM/YYYY HH:mm',
        // stepping: 5,
    });
    $('.selectpicker').selectpicker();
    $(".datepicker").datepicker({
        autoclose: true,
        todayHighlight: true
    });

    $('#btnErrorSubmit').click(() => {
        submitFormReport();
    });

    function submitFormReport() {
        let data = $('#form-review-error').serializeArray();
        let ajaxUrl = "{{route('admin.reportErrorTask')}}"
        ajaxGetServerWithLoader(
            genUrlGet([ajaxUrl]),
            'POST',
            data,
            function(res) {
                if(res.success == true){
                    $('#list-not-finish').empty();
                    $('#list-working').empty();
                    $('#list-finish').empty();
                    $('#list-review').empty();
                    $('#btn-search').click();
                    // loadData();
                    loadProjectInfo();
                    $('#popupModal').find('.modal').modal('hide');  
                    showSuccess(res.mes);
                }else{
                    showErrors(res.mes);
                }
            }
        );
    }

</script>
