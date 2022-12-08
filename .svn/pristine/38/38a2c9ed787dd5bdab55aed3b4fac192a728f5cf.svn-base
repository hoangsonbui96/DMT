<div class="modal draggable fade in" data-backdrop="static" id="job-modal-edit" role="dialog">
    <div class="modal-dialog modal-xs ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="">×</button>
                <h4 class="modal-title">@lang('admin.interview.edit-job')</h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <div class="tab-content">
                    <form class="form-horizontal" method="POST" id="interviewJob-edit-form">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label col-xs-3"
                                        for="Name">@lang('admin.interview.name-job')<sup
                                            class="text-red">*</sup>:</label>
                                    <div class="col-xs-9">
                                        <input type="text" class="form-control" name="name" maxlength="200"
                                            value="{{ $interviewJob->Name }}">
                                        <input type="hidden" class="form-control" name="id"
                                            value="{{ $interviewJob->id }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-3 control-label"
                                        for="text">@lang('admin.interview.content_detail')<sup
                                            class="text-red">*</sup>:</label>
                                    <div class="col-xs-9">
                                        <textarea class="form-control" name="content" id="content"
                                            rows="4">{{ $interviewJob->Content }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="">@lang('admin.times')<sup
                                            class="text-red">*</sup>:</label>
                                    <div class="col-sm-9 select-abreason">
                                        <div class="form-row" style="display: flex; justify-content: space-between">
                                            <div class="input-group date" id="sdate" style="padding: 0; width: 48%">
                                                <input type="text" class="form-control datepicker"
                                                    placeholder="@lang('admin.overtime.stime')" name="date_start"
                                                    autocomplete="off" value="{{ $start_date }}">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            <div class="input-group date" id="edate" style="padding: 0; width: 48%">
                                                <input type="text" class="form-control datepicker"
                                                    placeholder="@lang('admin.overtime.etime')" name="date_end"
                                                    autocomplete="off" value="{{ $end_date }}">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3">@lang('admin.interview.status'):</label>
                                    {{-- <div class="col-sm-9" style="text-align: left;">
                                        <div class="toggle btn btn-primary" data-toggle="toggle"
                                            style="width: 150px; height: 0px;"><input type="checkbox" value=""
                                                data-toggle="toggle" id="toggle-active" data-on="Hoạt động"
                                                data-off="Không hoạt động" data-width="150" name="Active">
                                            <div class="toggle-group"><label class="btn btn-primary toggle-on">Hoạt
                                                    động</label><label class="btn btn-default active toggle-off">Không
                                                    hoạt động</label><span class="toggle-handle btn btn-default"></span>
                                            </div>
                                        </div>
                                    </div> --}}
                                    @if($interviewJob->Active == 1)
                                    <div class="col-xs-9">
                                        <input style="margin-top: 5px;width:26px;height:20px" class="" type="checkbox"
                                            name="active" id="job_active" value="1" checked>
                                    </div>
                                    @else
                                    <div class="col-xs-9">
                                        <input style="margin-top: 5px;width:26px;height:20px" class="" type="checkbox"
                                            name="active" id="job_active" value="0">
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                    id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm" id="update_job">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(".date").datepicker();
    $(document).on('change','#job_active',function(){
        let active = $(this).is(':checked');
        if(active === true){
            $(this).val('1');
        }else{
            $(this).val('0');
        }
    });
</script>
<script>
    var is_busy = false;
    var urlList = "{{ route('admin.interviewJob.list') }}";
    var urlUpdate = "{{ route('admin.interviewJob.update') }}";
    $(document).on('click','#update_job',function(){
        if(is_busy == true){
            return false;
         }
        let updateJob = $('#interviewJob-edit-form').serializeArray();
        is_busy = true;
        ajaxGetServerWithLoader(urlUpdate,"POST",updateJob,function(rst){
        $('.loadajax').hide();
        if ($.isEmptyObject(rst.errors)) {
            showSuccessConfirm(rst.success,function(){
                $('#job-modal-edit').modal('hide');
                loadInterviewJob();
            });
                //locationPage(urlList);
         } else {
            showErrors(rst.errors);
         }
        is_busy = false;
        },function(){
             alert('lỗi');
        });
    })
</script>
