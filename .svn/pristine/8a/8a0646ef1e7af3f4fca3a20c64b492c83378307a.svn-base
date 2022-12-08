<div class="modal draggable fade in detail-modal" id="user-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable ">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('admin.user.add_new_user')</h4>
            </div>
            <div class="modal-body">
                <table width="100%" class="table table-striped table-bordered table-hover data-table no-footer dataTable" id="table-equipment-view-history" role="grid" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>@lang('admin.equipment.Handover_user')</th>
                            <th>@lang('admin.equipment.The_handover_user')</th>
                            <th>@lang('admin.equipment.Updated_person')</th>
                            <th>@lang('admin.equipment.updated_at')</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($histories as $history)
                        <tr>
                            <td>{{ $history->old_user_owner }}</td>
                            <td>{{ $history->user_owner }}</td>
                            <td>{{ $history->created_user }}</td>
                            <td>{{FomatDateDisplay($history->created_at,FOMAT_DISPLAY_DATE_TIME) }}</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
            </div>
        </div>

    </div>
</div>



<script type="text/javascript" async>

</script>

