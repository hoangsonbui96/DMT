<div class="modal draggable fade in detail-modal" id="detail-doc" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-ms ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <form class="form-horizontal" action="" method="POST" id="doc-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            {{--Tên tài liệu--}}
                            @if(isset($infoDoc->id))
                                <input type="hidden" name="id" value="{{ $infoDoc->id }}" id="id">
                            @endif
                            <div class="form-group">
                                <label class="control-label col-sm-4">@lang('document::admin.document.dName') &nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="dName" placeholder="@lang('document::admin.document.dName')" name="dName"
                                           value="{{ isset($infoDoc->dName) ? $infoDoc->dName : null }}">
                                </div>
                            </div>

                            <!-- Kiểu tài liệu -->
                            <div class="form-group">
                                <label class="control-label col-sm-4">@lang('document::admin.document.dType')&nbsp;<sup class="text-red">*</sup>:</label>
                                <div class="col-sm-8">
                                    <select class='selectpicker show-tick show-menu-arrow' id='select-type' name="dType" data-size="5">
                                        <option value="">@lang('document::admin.document.select_doc')</option>
                                        {!!
                                            GenHtmlOption($masterData, 'DataValue', 'Name', isset($infoDoc->dType )  ? $infoDoc->dType : '')
                                        !!}
                                    </select>
                                </div>
                            </div>

                            <!-- Mô tả tài liệu -->
                            <div class="form-group">
                                <label class="control-label col-sm-4">Mô tả :</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" id="document-description" rows="2"
                                        name="dDescription">{{ isset($infoDoc->dDescription) ? $infoDoc->dDescription : '' }}</textarea>
                                </div>
                            </div>
                            
                            {{--Người xem--}}
                            <div class="form-group">
                                <label class="control-label col-sm-4">@lang('document::admin.document.userView') <sup class="text-red">*</sup> :</label>
                                <div class="col-sm-8 row">
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="padding: 0px 2px !important;">
                                                <input type="checkbox" name="selectAllUser" id="selectAllUser" value="0" style="width:26px;height:20px;" @if(isset($infoDoc->userView) & empty($infoDoc->userView['1'])) checked @endif>
                                            </span>
                                            <input type="text" class="form-control" value="Tất cả" style="" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-8" id="choose-users">
                                        <select class='selectpicker show-tick show-menu-arrow' data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true" data-size="5" id='userView' name="userView[]" multiple>
                                            {!!
                                                GenHtmlOption($user, 'id', 'FullName', isset($infoDoc->userView) ? $infoDoc->userView : '')
                                            !!}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{--Kiểu Upload--}}
                            <div class="form-group">
                                <label class="control-label col-sm-4" for="group">@lang('document::admin.document.tUpload')&nbsp; @if(!isset($infoDoc->id))<sup class="text-red">*</sup>@endif:</label>
                                <div class="col-sm-8">
                                    <label class="radio-inline">
                                        <input type="radio" name="typeUpload" id="click-url" value="1" checked>Url
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="typeUpload" id="click-upload" value="0" {{ isset($infoDoc->typeUpload) && !$infoDoc->typeUpload ? "checked" : ""}}>Upload File
                                    </label>
                                </div>
                            </div>

                            <div class="form-group" id="divUrl">
                                <label class="control-label col-sm-4">@lang('document::admin.document.url') &nbsp; @if(!isset($infoDoc->id))<sup class="text-red">*</sup>@endif:</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="dUrl" placeholder="Đường dẫn" name="dUrl"
                                           value="{{ isset($infoDoc->dUrl) ? $infoDoc->dUrl : '' }}">
                                </div>
                            </div>

                            <div class="form-group" id="divUpload">
                                <label class="control-label col-sm-4">@lang('document::admin.document.upload') &nbsp; @if(!isset($infoDoc->id))<sup class="text-red">*</sup>@endif:</label>
                                <div class="col-sm-6">
                                    <div >
                                        <span class="input-group-btn">
                                            <input id="myfiles" name="fileName" type="file" class="form-control">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm" id="save" >@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async>

    $(function () {
        $(".selectpicker").selectpicker();
        $( ".draggable" ).draggable();
    });

    $('#save').click(function () {

        var hostname = $(location).attr('hostname');
        var url      = $('#dUrl').val();
        var typeUpload = $('[name=typeUpload]:checked').val();

        //if isset url && choose upload url
        if($('#dUrl').val() != '' && typeUpload == 1) {
            if(url.indexOf(hostname) != -1){
                showErrors('Hiện tại chưa hỗ trợ đường dẫn này!');
                return;
            }
        }
        $('.loadajax').show();
        var formData = new FormData($('#doc-form')[0]);
        $.ajax({
            url: "{{ route('admin.insertUpdateDoc') }}",
            type: 'post',
            processData: false,
            contentType: false,
            data: formData,
            success: function (rst) {
                if ($.isEmptyObject(rst.errors)) {
                    $('#detail-doc').modal('hide');
                    $('.loadajax').hide();
                    locationPage();
                 } else {
                    showErrors(rst.errors);
                    $('.loadajax').hide();
                 }
            },
            error: function (data) {
                showErrors(data.errors);
                $('.loadajax').hide();
            }

        });
    });

    // changer type upload
    if($('#click-url').prop("checked")   == true){
        $('#divUpload').hide();
    }else{
        $('#divUrl').hide();
    }

    $('#click-url').on('change',function () {
        if($('#click-url').prop("checked")   == true){
            $('#divUpload').hide();
            $('#divUrl').show();
        }
    });
    $('#click-upload').on('change',function () {
        if($('#click-upload').prop("checked")   == true){
            $('#divUrl').hide();
            $('#divUpload').show();
        }
    });

    // Jquery draggable
    $('.modal-dialog').draggable({
        handle: ".modal-header"
    });

    function checkedSelectUsers(){
        var check_all_user = $('#selectAllUser').is(':checked');
        if(check_all_user == true){
            $('#userView').attr('disabled',true);
            $('.selectpicker').selectpicker('refresh');
        } else{
            $('#userView').attr('disabled',false);
            $('.selectpicker').selectpicker('refresh');
        }
    }
    $(function(){
        checkedSelectUsers();
    })
    $('#selectAllUser , #choose-users').on('click', function(){
        checkedSelectUsers();
    })
</script>

