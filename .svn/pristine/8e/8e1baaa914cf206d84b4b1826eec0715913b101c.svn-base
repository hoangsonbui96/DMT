<div class="modal draggable fade in detail-modal" id="user-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable width550">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('admin.calendar.add')</h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                <form class="detail-form" role="form" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label>@lang('admin.calendar.name')&nbsp;<sup class="text-red">*</sup>:</label>
                            <input type="text" class="form-control" name="Name" placeholder="@lang('admin.calendar.name')" value="{{ isset($itemInfo->Name) ? $itemInfo->Name : null }}" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('admin.calendar.title')&nbsp;<sup class="text-red">*</sup>:</label>
                            <input type="text" class="form-control" name="Title" placeholder="@lang('admin.calendar.title')" value="{{ isset($itemInfo->Title) ? $itemInfo->Title : null }}" required>
                        </div>

                        <div class="form-group">
                            <label>@lang('admin.status')&nbsp;:</label>
                            <input name="Active" type="checkbox" data-onstyle="primary"  data-on="@lang('admin.on')" data-width="150" data-off="@lang('admin.off')" data-toggle="toggle" {{ isset($itemInfo->Active) && $itemInfo->Active ? 'checked' : null }} >
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
    $('.save-form').click(function () {
        $('.loadajax').show();
        var unApproveUrl = "{{ route('admin.CalendarData') }}";
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
        $('input[name=Active]').bootstrapToggle();
    });

</script>
