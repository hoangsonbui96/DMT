<div class="modal draggable fade in detail-modal" id="info-candidate" role="dialog">
    <div class="modal-dialog modal-lg ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">@lang('admin.interview.add-candidate')</h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <form class="form-horizontal" action="" method="POST" enctype="multipart/form-data" id="candidate-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            @if(isset($candidateInfo->id))
                                <input type="text" class="form-control hidden" name="id" value="{{$candidateInfo->id}}">
                            @endif
                            <div class="form-group">
                                <label class="control-label col-lg-3" for="Name">@lang('admin.interview.name-job'):</label>
                                <div class="col-lg-9">
                                    <select class='selectpicker show-tick show-menu-arrow' id='select-job' name="JobID">
                                        @foreach($jobs as $job)
                                            <option value="{{$job->id}}" {{isset($job->id) && $job->id == $rmb_id ? 'selected' : ''}}>{{$job->Name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-3" for="image">CV Path&nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-lg-9" id="cvfile">
                                    <span class="input-group-btn">
                                        <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
                                            <i class="fa fa-file-pdf-o"></i> @lang('admin.document.select_file')
                                        </a>
                                        <input id="thumbnail" class="form-control" type="text" name="CVpath" style="width: 215px;float: right"
                                               value="{{ isset($candidateInfo->CVpath) ? $candidateInfo->CVpath : '' }}">
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="text" class="col-lg-3 control-label">@lang('admin.name')&nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" id="fullname" placeholder="Tên ứng viên" name="FullName"
                                           value="{{isset($candidateInfo->FullName) ? $candidateInfo->FullName : null}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="text" class="col-lg-3 control-label">@lang('admin.email')&nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" id="email" placeholder="Email" name="Email"
                                           value="{{isset($candidateInfo->Email) ?$candidateInfo->Email : null}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="text" class="col-lg-3 control-label">@lang('admin.partner.tel')&nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" id="tel" placeholder="@lang('admin.partner.tel')" name="Tel"
                                     value="{{isset($candidateInfo->Tel) ?$candidateInfo->Tel : null}}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="control-label col-lg-3" for="birthday">@lang('admin.user.birthday')&nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-lg-9">
                                    <div class='input-group date' id='birthday'>
                                        <input type="text" class="form-control" id="birthday-input" placeholder="@lang('admin.user.birthday')" name="Birthday" autocomplete="off"
                                               value="{{isset($candidateInfo->Birthday) ? FomatDateDisplay($candidateInfo->Birthday, FOMAT_DISPLAY_DAY) : null}}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="text" class="col-lg-3 control-label">@lang('admin.interview.Household')&nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" id="peraddress" placeholder="@lang('admin.interview.Household')" name="PerAddress"
                                           value="{{isset($candidateInfo->PerAddress) ?$candidateInfo->PerAddress : null}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="text" class="col-lg-3 control-label">@lang('admin.interview.Staying')&nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" id="curaddress" placeholder="@lang('admin.interview.Current_residence')" name="CurAddress"
                                           value="{{isset($candidateInfo->CurAddress) ?$candidateInfo->CurAddress : null}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="text" class="col-lg-3 control-label">@lang('admin.note'):</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" id="note" placeholder="Note" name="Note"
                                           value="{{isset($candidateInfo->Note) ?$candidateInfo->Note : null}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm" rmb-id="{{$rmb_id}}"id="save">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async>
    var rmb_id = $('#save').attr('rmb-id');
    var ajaxUrl1 = "{{ route('admin.CandidateList') }}";
    $(function () {
        $('.selectpicker').selectpicker();
        SetDatePicker($('.date'));

        $('#lfm').filemanager('file', {prefix: route_prefix});
    })
    $('#save').click(function () {
        ajaxGetServerWithLoader('/akb/interview-storeCandidate', 'POST', $('#candidate-form').serializeArray(),
            function (data) {
                if (typeof data.errors !== 'undefined') {
                    showErrors(data.errors);
                    return;
                } else {
                    $('.loadajax').show();
                    $.ajax({
                        url: ajaxUrl1 + '/' + rmb_id,
                        success: function (data) {
                            $('#popupModal').empty().html(data);
                            $('.modal-title').html(newTitle1);
                            $('.detail-modal').modal('show');
                            $('.loadajax').hide();
                        }
                    });
                }
            })
    });
    var a = $('#select-job option:selected').html();
    $('#info-candidate').on('hidden.bs.modal', function () {

        $('#info-candidate').modal('hide');
        $.ajax({
            url: ajaxUrl1+'/'+rmb_id,
            success: function (data) {
                $('#popupModal').empty().html(data);
                $('.modal-title').html(newTitle1+'['+a+']');
                $('.detail-modal').modal('show');
            }
        });
    });
    $('#info-candidate').on('show.bs.modal', function () {
        $('#cvfile #file').show();
        $('#cvfile #update-control').hide();
    });

</script>

