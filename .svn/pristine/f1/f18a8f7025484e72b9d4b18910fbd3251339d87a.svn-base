<div class="modal draggable fade in" id="info-candidate" role="dialog">
    <div class="modal-dialog modal-lg ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">@lang('admin.interview.add-candidate')</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" enctype="multipart/form-data" id="candidateAddForm">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="control-label col-lg-3" for="Name">@lang('admin.interview.name-job')<sup
                                        class="text-red">*</sup>:</label>
                                <div class="col-lg-9">
                                    <select class="form-control" id="interviewJob" name="interviewJob">
                                        <option value="">Chọn công việc</option>
                                        @foreach($list_job_interview as $job)
                                        @if($jobId == $job->id)
                                        <option value="{{ $job->id }}" selected>{{ $job->name }}</option>
                                        @else
                                        <option value="{{ $job->id }}">{{ $job->name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-3"
                                    for="image">@lang('admin.interview.downloadCV')<sup
                                        class="text-red">*</sup>:</label>
                                <div class="col-lg-9" id="cvfile">
                                    {{-- <span class="input-group-btn">
                                        <a id="lfm" data-input="thumbnail" data-preview="holder"
                                            class="btn btn-primary text-white">
                                            <i class="fa fa-file-pdf-o"></i> Chọn file </a>
                                        <input id="thumbnail" class="form-control" type="text" name="CVpath"
                                            style="width: 215px;float: right" value="">
                                    </span> --}}
                                    <input class="form-control" id='cv_candidate' type="file" name="CVpath" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="text" class="col-lg-3 control-label">@lang('admin.partner.full_name')<sup
                                        class="text-red">*</sup>:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" id="fullname"
                                        placeholder="@lang('admin.interview.name-inter')" name="FullName" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="text" class="col-lg-3 control-label">@lang('admin.partner.email')<sup
                                        class="text-red">*</sup>:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" id="email"
                                        placeholder="@lang('admin.partner.email')" name="Email" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="text" class="col-lg-3 control-label">@lang('admin.partner.tel')<sup
                                        class="text-red">*</sup>:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" id="tel"
                                        placeholder="@lang('admin.partner.tel')" name="Tel" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-3"
                                    for="birthday">@lang('admin.partner.birthday'):</label>
                                <div class="col-lg-9">
                                    <div class="input-group date" id="birthday">
                                        <input type="text" class="form-control selectpicker" id="birthday-input"
                                            placeholder="@lang('admin.partner.birthday')" name="Birthday"
                                            autocomplete="off" value="">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label for="text"
                                    class="col-lg-3 control-label">@lang('admin.interview.Household'):</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" id="peraddress"
                                        placeholder="@lang('admin.interview.Household')" name="PerAddress" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="text"
                                    class="col-lg-3 control-label">@lang('admin.interview.Staying'):</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" id="curaddress"
                                        placeholder="@lang('admin.interview.Current_residence')" name="CurAddress"
                                        value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="text"
                                    class="col-lg-3 control-label">@lang('admin.interview.apply-position')<sup
                                        class="text-red">*</sup>:</label>
                                <div class="col-lg-9">
                                    <select class="form-control" id="ApplyPosition" name="ApplyPosition">
                                        <option value="">Chọn vị trí ứng tuyển</option>
                                        @foreach($apply_position as $position)
                                        <option value="{{ $position->DataValue }}">{{ $position->Name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="experience"
                                    class="col-lg-3 control-label">@lang('admin.interview.experience')<sup
                                        class="text-red">*</sup>:</label>
                                <div class="col-lg-9">
                                    <input class="form-control" type="text" name="Experience" id="experience"
                                        placeholder="Kinh nghiệm (tháng)" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="text" class="col-lg-3 control-label">@lang('admin.absence.remark'):</label>
                                <div class="col-lg-9">
                                    <textarea type="text" class="form-control" id="note" rows="4"
                                        placeholder="@lang('admin.absence.remark')" name="Note" value=""></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                    id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm" id="candidate_save">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#lfm').filemanager('file', {prefix: route_prefix});
    $(".date").datepicker();
</script>
<script>
    var is_busy = false;
    var urlListCandidate = "{{ route('admin.candidates.list') }}";
    var urlSaveCandidate = "{{ route('admin.candidates.store') }}";

   $(document).on('click','#candidate_save',function(){
        $('.loadajax').show();
        if(is_busy == true) return false;
        var formData = new FormData($('#candidateAddForm')[0]);
        let order_by = $('#order_by').val();
        let sort_by = $('#sort_by').val();
        is_busy = true;
        $.ajax({
            url: urlSaveCandidate,
            type: 'post',
            processData: false,
            contentType: false,
            data: formData,
            success: function (rst) {
                $('.loadajax').hide();
                if ($.isEmptyObject(rst.errors)) {
                    showSuccessConfirm(rst.success,function(){
                        loadCandidate(order_by,sort_by);
                        $('#info-candidate').modal('hide');
                    })
                 } else {
                    showErrors(rst.errors);
                 }
                 is_busy = false;
            },
            error: function () {
              $('.loadajax').hide();
              alert('loi');
            }

        });
    });

</script>
