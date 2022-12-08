<div class="modal draggable fade in" data-backdrop="static" id="interviewSheduleEdit" role="dialog">
    <div class="modal-dialog modal-xs ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="">×</button>
                <h4 class="modal-title">@lang('admin.interview.interview-update')
                </h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <div class="tab-content">
                    <form class="form-horizontal" method="POST" id="interviewSheduleEdit-form">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label col-xs-3"
                                        for="Name">@lang('admin.interview.name-job'):</label>
                                    <div class="col-xs-9">
                                        <input type="text" class="form-control" value="{{ $interviewJob->Name }}">
                                        <input type="hidden" name="interview_id" value="{{ $interview->id }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"
                                        for="text">@lang('admin.interview.candidate'):</label>
                                    <div class="col-xs-9">
                                        <input type="text" class="form-control" value="{{ $candidate->FullName }}"
                                            disabled>
                                        <input type="hidden" class="form-control" name="CandidateID"
                                            value="{{ $candidate->id }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="">@lang('admin.interview.date'):</label>
                                    <div class="col-sm-9 select-abreason">
                                        <div class="form-row">
                                            <div class="input-group date" id="sdate" style="padding: 0; width: 48%">
                                                <input type="text" class="form-control datepicker"
                                                    placeholder="@lang('admin.interview.date')" name="InterviewDate"
                                                    autocomplete="off" value="{{ $interviewDate }}">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="text"
                                        class="col-lg-3 control-label">@lang('admin.absence.remark'):</label>
                                    <div class="col-lg-9">
                                        <textarea type="text" class="form-control" id="note" rows="2"
                                            placeholder="@lang('admin.absence.remark')" name="Note" value="">{{ $interview->Note }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="text"
                                        class="col-lg-3 control-label">@lang('admin.interview.evaluation'):</label>
                                    <div class="col-lg-9">
                                        <textarea type="text" class="form-control" id="note" rows="4"
                                            placeholder="Đánh giá kết quả buổi phỏng vấn" name="Evaluate"
                                            value="">{{ $interview->Evaluate }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for=""
                                        class="col-lg-3 control-label">Kết quả phỏng vấn:</label>
                                    <div class="col-lg-9">
                                        <div class="row" style=" margin-bottom:15px;">
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="padding: 0px 2px !important;">
                                                        <input type="radio" name="approve" id="passInterview" value="1" style="width:26px;height:20px; accent-color: green;"
                                                        @if ($interview->Approve == 1) checked @endif>
                                                    </span>
                                                    <input type="text" class="form-control" value="Đạt" style="color: green;" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="padding: 0px 2px !important;">
                                                        <input type="radio" name="approve" id="failedInterview" value="2" style="width:26px;height:20px; accent-color: red;"
                                                        @if ($interview->Approve == 2) checked @endif >
                                                    </span>
                                                    <input type="text" class="form-control" value="Trượt" style="color: red;" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="sendMail"
                                        class="col-lg-3 control-label">@lang('admin.interview.send-mail'):</label>
                                    <div class="col-lg-9">
                                        <input style="margin-top: 5px;width:26px;height:20px" type="checkbox"
                                            name="sendMail" id="sendMail" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                    id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm"
                    id="save_interviewShedule">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script>
    SetDateTimePicker($('.date'),{
        format: 'DD/MM/YYYY HH:mm',
        stepping: 5,
    });

    $(document).on('change','#sendMail',function(){
        let active = $(this).is(':checked');
        if(active === true){
            $(this).val('1');
        }else{
            $(this).val('0');
        }
    });
</script>
<script>
    var urlSaveInterviewShedule = "{{ Route('admin.interviewShedule.update') }}";
    $(document).off('click','#save_interviewShedule').on('click','#save_interviewShedule',function(){
       var check_sendMail = $('#sendMail').is(':checked');
       if(check_sendMail == true){
          var content = "Bạn muốn cập nhập lại thông tin buổi phỏng vấn và gửi lại mail hẹn phỏng vấn cho ứng viên này?";
       }else{
        var content = "Bạn muốn cập nhập lại thông tin buổi phỏng vấn cho ứng viên này?";
       }
       let order_by = $('#order_by').val();
       let sort_by = $('#sort_by').val();
       showConfirm(content,function(){
        let saveData = $('#interviewSheduleEdit-form').serializeArray();
        ajaxGetServerWithLoader(urlSaveInterviewShedule,"POST",saveData,function(rst){
            $('.loadajax').hide();
            if ($.isEmptyObject(rst.errors)) {
                showSuccessConfirm(rst.success,function(){
                    loadCandidate(order_by,sort_by);
                    $('#interviewSheduleEdit').modal('hide');
                });
         } else {
            showErrors(rst.errors);
         }
        },function(){
             alert('lỗi');
        });
      });
    });
</script>
