<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('js/dataTables.checkboxes.min.js') }}"></script>
<script src="{{ asset('js/dataTables.responsive.js') }}"></script>

<div class="modal draggable fade in detail-modal" id="user-info" role="dialog">
    <div class="modal-dialog modal-lg ui-draggable">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title-2">Nhóm {{ $group->name }}</h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>

                <div class="row">
                    <div class="table-responsive  col-md-12" id="default">
                        <table width="100%" class="table tbl-role table-striped table-bordered table-hover table-user-groups" id="q1">
                            <thead class="thead-default">
                                <tr>
                                    <th class="width3 no-sort">
                                        <input type="checkbox" class="checkAll">
                                    </th>

                                    <th class="width3">
                                        @lang('admin.daily.Screen_Name')
                                    </th>
                                    <th class="width3">
                                        @lang('admin.role_power')
                                    </th>

                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>

                            </tfoot>
                            <tbody id="role-list">

                                @foreach($list as $item)
                                    <tr class="even gradeC">
                                    <td class="text-center">
                                    <input type="checkbox" {{ $item->checked ? 'checked' : '' }} data-id="{{ $item->ScreenDetailAlias }}" class="role-item">
                                    </td>
                                    <td class="text-center">{{ $item->ScreenName }}</td>
                                    <td class="text-center">{{ $item->ScreenDetailName }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
            </div>
        </div>

    </div>
</div>
<input type="hidden" name="groupId" value="{{ $group->id }}">
<style>
    .pagination {
        display: inline-block;
        padding-left: 0;
        margin: 0px;
        border-radius: 4px;
        float: right;
    }
    .pagination>li>a, .pagination>li>span {

        padding: 4px 8px;
        margin-left: -1px;
        line-height: 1.42857143;
        color: #337ab7;
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #ddd;
    }
    tfoot th{
        text-align: center;
    }
    #q1_filter{
        float:right;
    }
    #q1_length{
        float:left;
        margin-right: 20px;
    }
</style>
<script>
    $(function () {
        $('.selectpicker').selectpicker();

        var table = $('.tbl-role').DataTable({
            "ordering": true,
            "info": true,
            "columnDefs": [
                { "targets": 'no-sort', "orderable": false}
            ],
            "paging": true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Tìm kiếm",
            },
            initComplete: function () {
                var intCount = 0;
                $('#q1_wrapper').find('.row').eq(0).find('.col-sm-6').append('<div class="filter_"></div>');
                this.api().columns().every( function () {
                    if(intCount >= 1){
                        var column = this;
                        var select = $('<select class="form-control input-sm"><option value="">'+(intCount==1 ? 'Chọn màn hình' : 'Chọn quyền')+'</option></select>')
                            .appendTo( $('.filter_').eq(intCount-1).empty() )
                            .on( 'change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );

                                column
                                    .search( val ? '^'+val+'$' : '', true, false )
                                    .draw();
                            } );

                        column.data().unique().sort().each( function ( d, j ) {
                            select.append( '<option value="'+d+'">'+d+'</option>' )
                        } );
                    }

                    intCount++;
                } );
            }
        });



    });

</script>

