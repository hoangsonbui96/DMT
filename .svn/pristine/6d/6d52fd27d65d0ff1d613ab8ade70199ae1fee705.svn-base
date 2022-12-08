<div class="modal draggable fade in detail-modal" id="user-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable width550">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('admin.room.add_new_room')</h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                <form class="detail-form" role="form" action="" method="POST" id="room-form">
                    @csrf
                    <div class="box-body">
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>@lang('admin.equipment.type_id')&nbsp;<sup class="text-red">*</sup>:</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id='type_id' name="type_id" value="{{ isset($itemInfo->type_id) ? $itemInfo->type_id : null }}" required maxlength="3">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>@lang('admin.equipment.type_name')&nbsp;<sup class="text-red">*</sup>:</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="type_name" value="{{ isset($itemInfo->type_name) ? $itemInfo->type_name : null }}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>@lang('admin.equipment.note')&nbsp;:</label>
                            </div>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="note">{{ isset($itemInfo->note) ? $itemInfo->note : null }}</textarea>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    @if(isset($itemInfo->type_id))
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

    $(function () {
        $('.save-form').click(function () {

            ajaxGetServerWithLoader(
                "{{ route('admin.EquipmentType') }}",
                "POST",
                $('.detail-form').serializeArray(),
                function (data) {

                    if (typeof data.errors !== 'undefined'){
                        showErrors(data.errors[0]);
                        return;
                    }

                    window.location.reload();
                }
            );
        });

        $( ".ui-draggable" ).draggable();
    });

</script>

