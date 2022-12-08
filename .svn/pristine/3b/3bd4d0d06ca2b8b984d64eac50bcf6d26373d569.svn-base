<div class="modal draggable fade in detail-modal" id="user-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable width550">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('admin.equipment.Fix_maintenance_schedule')</h4>
        </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                <form class="detail-form" role="form" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label>@lang('admin.equipment.Warranty_date')&nbsp;<sup class="text-red">*</sup>:</label>
                            <div class="input-group">
                                <input type="text" class="form-control date" name="date"  value="">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('admin.comtent')&nbsp;<sup class="text-red">*</sup>:</label>
                            <textarea class="form-control" name="note">{{ isset($itemInfo->note) ? $itemInfo->note : null }}</textarea>
                            <input type="hidden" name="" value="">
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
	$('.date').datetimepicker({
            format: FOMAT_DATE,
        });
</script>
