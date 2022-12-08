<div class="modal draggable fade in detail-modal" id="user-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('admin.equipment.Maintenance_title')</h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                <form class="detail-form" role="form" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        @component('admin.component.table')
                        @slot('columnsTable')
                            <tr>
                                <th>@lang('admin.equipment.name')</th>
                                <th>@lang('admin.equipment.Code')</th>
                                <th>@lang('admin.equipment.Content_corrected')</th>
                            </tr>
                        @endslot
                        @slot('dataTable')
                            <tr>
                                <td class="left-important">{{ $maintenance->name }}</td>
                                <td class="left-important">{{ $maintenance->serial_number }}</td>
                                <td class="left-important">{{ $maintenance->note }}</td>
                            </tr>
                            @endslot
                         @endcomponent
                    </div>
                    <!-- /.box-body -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
            </div>
        </div>

    </div>
</div>


