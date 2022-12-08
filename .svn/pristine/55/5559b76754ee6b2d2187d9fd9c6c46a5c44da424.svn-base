<div class="modal draggable fade in denyModal" id="denyModal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable">
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('admin.daily.Deny Report')</h4>
            </div>
            <div class="modal-body">
                <form action="{{route('admin.DenyReport')}}" id="fm-deny" method="post">
                <input type="hidden" id="reportID" name="reportID" value="{{$id}}">
                <textarea class="form-control" name="issue" id="issue" rows="10"
                    placeholder="Nhập lý do ">{{isset($issue)? $issue : null}}</textarea>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel">@lang('admin.daily.Close')</button>
                <button type="button" class="btn btn-primary btn-deny" onclick="denyReport()" id="btn-deny">@lang('admin.daily.Save')</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
     $('.modal-dialog').draggable({
        handle: ".modal-header"
    });
</script>
