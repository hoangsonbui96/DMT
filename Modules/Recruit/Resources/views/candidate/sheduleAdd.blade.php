<div class="modal draggable fade in" data-backdrop="static" id="interviewShedule" role="dialog">
    <div class="modal-dialog modal-xs ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="">×</button>
                <h4 class="modal-title">@lang('admin.interview.add-interview')
                </h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <div class="tab-content">
                    <form class="form-horizontal" method="POST" id="interviewShedule-form">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label col-xs-3"
                                        for="Name">@lang('admin.interview.name-job'):</label>
                                    <div class="col-xs-9">
                                        <input type="text" class="form-control" value="{{ $interviewJob->Name }}"
                                            disabled>
                                        <input type="hidden" class="form-control" name="JobID"
                                            value="{{ $interviewJob->id }}">
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
                                    <label class="control-label col-sm-3" for="">@lang('admin.interview.date')<sup
                                            class="text-red">*</sup>:</label>
                                    <div class="col-sm-5 select-abreason">
                                        <div class="form-row">
                                            <div class="input-group date" id="sdate" style="padding: 0;">
                                                <input type="text" class="form-control datepicker" id="datepicker"
                                                    placeholder="@lang('admin.interview.date')" name="InterviewDate"
                                                    autocomplete="off" value="" @if($candidate->Status == 2) readonly @endif>
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
                                        <textarea type="text" class="form-control" id="note" rows="4"
                                            placeholder="@lang('admin.absence.remark')" name="Note" value="" @if($candidate->Status == 2) readonly @endif></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="sendMail"
                                        class="col-lg-3 control-label">@lang('admin.interview.send-mail'):</label>
                                    <div class="col-lg-9">
                                        <input style="margin-top: 5px;width:26px;height:20px" type="checkbox"
                                            name="sendMail" id="sendMail" value="1"  @if($candidate->Status == 2) disabled @endif checked>
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
    $(document).off('click','#save_interviewShedule').on('click','#save_interviewShedule',function(){
        var check_sendMail = $('#sendMail').is(':checked');
        if(check_sendMail == true){
            var urlSaveInterviewShedule = "{{ route('admin.interviewShedule.store') }}";
            var content = "Bạn muốn tạo lịch và gửi mail cho ứng viên này?";
        }else{
            var content = "Bạn muốn tạo lịch cho ứng viên này?";
        }
        let order_by = $('#order_by').val();
        let sort_by = $('#sort_by').val();
        showConfirm(content,function(){
        let saveData = $('#interviewShedule-form').serializeArray();
        console.log(saveData);
        ajaxGetServerWithLoader(urlSaveInterviewShedule,"POST",saveData,function(rst){
            $('.loadajax').hide();
            if ($.isEmptyObject(rst.errors)) {
                showSuccessConfirm(rst.success,function(){
                    $('#interviewShedule').modal('hide');
                    loadCandidate(order_by,sort_by);
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
