<div class="modal draggable fade in detail-modal" id="list-candidate-modal" role="dialog">
    <div class="modal-dialog modal-lg ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <div class="group-top">
                    <div class="row" style="margin-bottom: 10px;">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="pull-right">
                                <button type="button" class="btn btn-primary btn-detail" rmb-id ="{{$rmb_id}}" id="add_new_candidate">@lang('admin.interview.add-candidate')</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="table-responsive tbl-dReport">
                        <table width="100%" class="table table-striped table-bordered table-hover table-user-groups">
                            <thead class="thead-default">
                            <tr>
                                <th>@lang('admin.stt')</th>
                                <th>@lang('admin.name')</th>
                                <th>@lang('admin.interview.address_now')</th>
                                <th>@lang('admin.tel')</th>
                                <th>@lang('admin.email')</th>
                                <th>@lang('admin.note')</th>
                                <th class="width8">@lang('admin.action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($candidates as $item)
                                <tr class="even gradeC" data-id="">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->FullName }}</td>
                                    <td>{{ $item->CurAddress }}</td>
                                    <td>{{ $item->Tel }}</td>
                                    <td>{{ $item->Email }}</td>
                                    <td>{{ $item->Note }}</td>
                                    <td class="text-center">
                                        <span class="action-col download-cv" data-path="{{ $item->CVpath }}" data-toggle="tooltip" title="@lang('admin.interview.downloadCV')"><i class="fa fa-download" aria-hidden="true"></i></span>
                                        <span class="action-col update edit ac" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                        <span class="action-col update delete" item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{--            {{ $absences->appends($query_array)->links() }}--}}
                    </div>
                </div>
                <div id="popupModal">

                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async>

    //open modal add info candidate
    var ajaxUrl2 = "{{ route('admin.CandidateInfo') }}";
    var newTitle2 = 'Thêm ứng viên';
    var rmb_id = $('#add_new_candidate').attr('rmb-id');

    $("#add_new_candidate").click(function () {
        $.ajax({
            url: ajaxUrl2+'/'+rmb_id,
            success: function (data) {
                $('#popupModal').empty().html(data);
                $('.modal-title').html(newTitle2);
                $('.detail-modal').modal('show');
                $('.loadajax').hide();
            }
        });
    });

    //close modal
    var ajaxUrl3 = "{{ route('admin.InterviewJob') }}";
    $('#list-candidate-modal').on('hidden.bs.modal', function () {
        $('#list-candidate-modal').modal('hide');
        $('.modal-backdrop').remove();
        $.ajax({
            url: ajaxUrl3,
            success: function (data) {
                $('.loadajax').hide();
            }
        });
    });

    //update
    $('.ac').off().click(function (e) {
        e.preventDefault();
        var itemId = $(this).attr('item-id');
        $('.loadajax').show();
        $.ajax({
            url: ajaxUrl2+'/'+rmb_id+'/'+itemId,
            success: function (data) {
                $('#popupModal').empty().html(data);
                $('.modal-title').html(updateTitle);
                $('.detail-modal').modal('show');
                $('.loadajax').hide();
            }
        });
    });

    //delete
    $('.delete').off().click(function () {
        t= confirm(confirmMsg);
        if(!t)
            return;
        var itemId = $(this).attr('item-id');
        $.ajax({
            url: ajaxUrl2+'/'+rmb_id+'/'+itemId+'/del',
            success: function (data) {
                if(data == 1){
                    $.ajax({
                        url: ajaxUrl1 + '/' + rmb_id,
                        success: function (data) {
                            $('#popupModal').empty().html(data);
                            $('.modal-title').html(newTitle1);
                            $('.detail-modal').modal('show');
                            $('.loadajax').hide();
                        }
                    });
                }
            }
        });
    });

    $('.download-cv').click(function () {
        var path = $('.download-cv').attr('data-path');
        ajaxGetServerWithLoader("{{ route('admin.downloadCV') }}?path="+path, 'GET','', function (data) {
            if (typeof data.errors !== 'undefined') {
                showErrors(data.errors);
                return ;
            }
            window.location.href= "{{ route('admin.downloadCV') }}?path="+path;
        })
    });
</script>

