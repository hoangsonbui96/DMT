<div class="modal draggable fade in detail-modal modal-css" id="absent-unapprove" role="dialog" data-backdrop="static">

    <div class="modal-dialog modal-sm ui-draggable">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">@lang('admin.overtime.reject_reason')</h4>
            </div>
            <div class="modal-body">
                <form id="form_unApr">
                    <input type="hidden" id="req-id" value="">
                    <div class="form-group">
                        <label for="reason-unapprove">@lang('admin.absence.reason')&nbsp;<sup class="text-red">*</sup>:</label>
                        <textarea class="form-control" id="reason-unapprove" name="Note" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('admin.btnCancel')</button>
                <button type="button" class="btn btn-primary" id="save-unApprove">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#save-unApprove').click(function () {
        var itemId = $('#req-id').val();
        // showConfirm('Bạn có chắc chắn lưu?',
        //     function () {
                 ajaxGetServerWithLoader(ajaxUrlApr+'/'+itemId+'/del', 'GET', $('#form_unApr').serializeArray(),function (data) {
                    if (typeof data.errors !== 'undefined') {
                        showErrors(data.errors);
                        return;
                    }
                    showSuccess(data.success);
                    locationPage();
                });
            // });
    })
</script>
