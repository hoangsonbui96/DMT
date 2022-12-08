<div class="modal fade in detail-modal" id="absent-unapprove" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">@lang('admin.' . ($request->task ? $request->task : 'equipment') . '.rejected_requests') {{ $request->id }}</h4>
            </div>
            <div class="modal-body">
                <form id="form_unApr">
                    <input type="hidden" id="req-id" value="">
                    <div class="form-group">
                        <label for="reason-unapprove">@lang('admin.absence.reason')&nbsp;<sup class="text-red">*</sup>:</label>
                        <textarea class="form-control" id="reason-unapprove" name="Comment" rows="3"></textarea>
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
        var data = $('#form_unApr').serializeArray();
        if (data[0]['value'] == '') {
            showErrors('Vui lòng nhập lý do');
        } else {
            showConfirm('Bạn có chắc muốn hủy?',
                function () {
                    ajaxGetServerWithLoader(ApproveUrl+'/'+itemId+'/del', 'GET', $('#form_unApr').serializeArray(),function (data) {
                        if (typeof data.errors !== 'undefined') {
                            showErrors(data.errors);
                            return;
                        }
                        locationPage();
                    });
                }
            );
        }
    })
</script>
