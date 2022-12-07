<div class="modal draggable fade in detail-modal" id="user-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable width550">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('admin.masterdata.add')</h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                <form class="form-horizontal detail-form" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        <div class="form-group" id= "dataValue">
                            <label>@lang('admin.masterdata.data_value')&nbsp;<sup class="text-red">*</sup>:</label>
                            <input type="text" class="form-control" name="DataValue" value="{{ isset($itemInfo->DataValue) ? $itemInfo->DataValue : null }}" required>
                        </div>
                        <div class="form-group">
                            <label id= 'name'><span>@lang('admin.masterdata.name')&nbsp;<sup class="text-red">*</sup>:</span></label>
                            <input type="text" class="form-control" name="Name" value="{{ isset($itemInfo->Name) ? $itemInfo->Name : null }}" required>
                        </div>

                        <div class="form-group">
                            <label class="description" id= 'dataDescription'><span>@lang('admin.masterdata.description')&nbsp;:</span></label>
                            <input type="text" class="form-control" name="DataDescription" value="{{ isset($itemInfo->DataDescription) ? $itemInfo->DataDescription : null }}" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('admin.masterdata.data_display_order')&nbsp;<sup class="text-red">*</sup>:</label>
                            <input type="text" class="form-control" name="DataDisplayOrder" value="{{ isset($itemInfo->DataDisplayOrder) ? $itemInfo->DataDisplayOrder : null }}" required>
                        </div>
                        <div class="form-group">
                            <input type="hidden" class="form-control" name="DataKey"  value="{{ isset($itemInfo->DataKey) ? $itemInfo->DataKey : '' }}" required>
                            <input  type="hidden" class="form-control" name="TypeName" value="{{ isset($itemInfo->TypeName) ? $itemInfo->TypeName : '' }}" required>
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
    if($('input[name=DataKey]').val() == ''){
        DataKey = $('#add-new-room-btn').attr('data-value');
        TypeName = $('#add-new-room-btn').attr('data-valuetext');
        $('input[name=DataKey]').val(DataKey);
        $('input[name=TypeName]').val(TypeName);
        $('#dataValue').hide();
    }
    $('input[name=DataValue]').prop("disabled", true);

    $('.save-form').click(function () {
        $('.loadajax').show();
        var unApproveUrl = "{{ route('admin.MasterData') }}";
        ajaxServer(unApproveUrl, 'post',  $('.detail-form').serializeArray(), function (data) {
            if (typeof data.errors !== 'undefined'){
                $('.loadajax').hide();
                showErrors(data.errors[0]);
            }else{
                $('.loadajax').hide();
                window.location.reload();
            }
        })
    });

    $(function () {
        $( ".ui-draggable" ).draggable();

        if($('input[name=DataKey]').val() != 'EM' && $('input[name=DataKey]').val() != 'WT'){
            $('.description').append('&nbsp;<sup class="text-red">*</sup>:');
        }else{
            $('.description span').append('&nbsp;:');
        }
        if($('#add-new-room-btn').attr('data-value') == 'WT'){
            $('#name span').remove();
            $('#dataDescription span').remove();
            $('#name').append('Giờ bắt đầu&nbsp;&nbsp;<sup class="text-red">*</sup>:');
            $('#dataDescription').append('Giờ kết thúc&nbsp;&nbsp;<sup class="text-red">*</sup>:');
        }
    });

</script>

