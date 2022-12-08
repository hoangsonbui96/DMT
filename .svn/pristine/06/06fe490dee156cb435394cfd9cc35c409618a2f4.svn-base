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
                <!-- <h4 class="modal-title">@lang('admin.listPosition.addGroupPosition')</h4> -->
                <h4 style="font-size: 20px;">@lang('admin.listPosition.add')</h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <form class="form-horizontal detail-form" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label id='name'><span>@lang('admin.listPosition.nameGroupPosition')&nbsp;<sup class="text-red">*</sup>:</span></label>
                            <input type="text" class="form-control" name="Name" value="" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('admin.listPosition.dataValueGroupPosition')&nbsp;<sup class="text-red">*</sup>:</label>
                            <input type="text" class="form-control" name="DataValue" value="" required>
                        </div>
                        <div class="form-group">
                            <label class="description" id='dataDescription'><span>@lang('admin.listPosition.description')&nbsp;:</span></label>
                            <input type="text" class="form-control" name="DataDescription" value="" required>
                        </div>
                    </div>
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
        var saveUrl = "{{ route('admin.StoreGroupPosition') }}";
        ajaxServer(saveUrl, 'post', $('.detail-form').serializeArray(), function(data) {
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