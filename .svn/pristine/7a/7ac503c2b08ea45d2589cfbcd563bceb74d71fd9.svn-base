<style>
    .bootstrap-select {
        width: 100% !important;

    }
</style>
<div class="modal draggable fade in detail-modal" id="user-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable width550">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('admin.listPosition.add')</h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                <form class="form-horizontal detail-form" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        <div class="form-group" id="dataValue">
                            <label>@lang('admin.listPosition.dataValue')&nbsp;<sup class="text-red">*</sup>:</label>
                            <input type="text" class="form-control" name="DataValue" value="{{ isset($itemInfo->DataValue) ? $itemInfo->DataValue : null }}" disabled>
                        </div>
                        <div class="form-group">
                            <label id='name'><span>@lang('admin.listPosition.name')&nbsp;<sup class="text-red">*</sup>:</span></label>
                            <input type="text" class="form-control" name="Name" value="{{ isset($itemInfo->Name) ? $itemInfo->Name : null }}" required>
                        </div>

                        <div class="form-group">
                            <label class="description" id='dataDescription'><span>@lang('admin.listPosition.description')&nbsp;:</span></label>
                            <input type="text" class="form-control" name="DataDescription" value="{{ isset($itemInfo->DataDescription) ? $itemInfo->DataDescription : null }}" required>
                        </div>
                        <div class="form-group">
                            <label class="description" id="level">@lang('admin.listPosition.level')&nbsp;<sup class="text-red">*</sup>:</label>
                            <div>
                                <div>
                                    <input type="radio" id="insertLevel" name="GenderLevel" value="0" {{'checked'}}>
                                    <label for="male">@lang('admin.listPosition.insertLevel')</label>
                                    <input type="radio" id="updateLevel" name="GenderLevel" value="1">
                                    <label for="female">@lang('admin.listPosition.updateLevel')</label><br>
                                </div>
                                <div>
                                    <select class="selectpicker show-tick show-menu-arrow sl-user select_project" id="" data-size="5" name="Level" data-live-search="true" data-live-search-placeholder="Search" data-width="100%" data-size="5">
                                        {!!
                                        GenHtmlOption($level, 'id', 'Name',isset($itemInfo->Level) ? $itemInfo->Level : '')
                                        !!}
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="description" id="listUser">@lang('admin.listPosition.listUser')&nbsp;<sup class="text-red">*</sup>:</label>
                            <div>
                                <select class='selectpicker show-tick show-menu-arrow' data-actions-box="true" data-size="5" id='selectUser' name="listUser[]" data-live-search="true" data-live-search-placeholder="Search" multiple>
                                    {!! GenHtmlOption($userAssign, 'id', 'FullName', isset($itemInfo->ListUser) ? explode(',',$itemInfo->ListUser) : null)!!}
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" class="form-control" name="DataKey" value="{{ isset($itemInfo->DataKey) ? $itemInfo->DataKey : '' }}" required>
                            <input type="hidden" class="form-control" name="DataValue" value="{{ isset($itemInfo->DataValue) ? $itemInfo->DataValue : '' }}" required>
                            <input type="hidden" class="form-control" name="TypeName" value="{{ isset($itemInfo->TypeName) ? $itemInfo->TypeName : '' }}" required>
                            <input type="hidden" class="form-control" name="oldLevel" value="{{ isset($itemInfo->oldLevel) ? $itemInfo->oldLevel : '' }}" required>
                            <input type="hidden" class="form-control" name="ListUserOld" value="{{ isset($itemInfo->ListUserOld) ? $itemInfo->ListUserOld : '' }}" required>
                            <input type="hidden" class="form-control" name="countLevel" value="{{ isset($countLevel) ? $countLevel : 0 }}" required>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    @if(isset($itemInfo->Name))
                    <input type="hidden" name="id" value="{{ $itemInfo->id }}">
                    @endif
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm save-form">@lang('admin.btnSave')</button>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript" async>
    $(function() {
        $(".selectpicker").selectpicker({
            noneSelectedText: '',
        });
    });
    if ($('input[name=DataKey]').val() == '') {
        DataKey = $('#add-new-room-btn').attr('data-value');
        TypeName = $('#add-new-room-btn').attr('data-valuetext');
        $('input[name=DataKey]').val(DataKey);
        $('input[name=TypeName]').val(TypeName);
        $('#dataValue').hide();
    }
    $('.save-form').click(function() {
        $('.loadajax').show();
        var unApproveUrl = "{{ route('admin.ListPosition') }}";
        ajaxServer(unApproveUrl, 'post', $('.detail-form').serializeArray(), function(data) {
            if (typeof data.errors !== 'undefined') {
                $('.loadajax').hide();
                showErrors(data.errors[0]);
            } else {
                $('.loadajax').hide();
                window.location.reload();
            }
        })
    });
</script>