<div class="modal draggable fade in detail-modal" id="user-info" role="dialog">
    <div class="modal-dialog modal-lg ui-draggable">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">

                <div class="modal-body row">
                    <ul class="nav nav-tabs">
                        @if($registrations->count())
                        <li class="active"><a data-toggle="tab" href="#tab1">@lang('admin.equipment.add')</a></li>
                        @endif
                        @if($changeRegistrations->count())
                        <li @if(!$registrations->count()) class="active" @endif><a data-toggle="tab" href="#tab2">@lang('admin.equipment.Change_device')</a></li>
                        @endif
                    </ul>
                    <div class="tab-content">
                    @if($registrations->count())
                    <div id="tab1" class="tab-pane fade in active">
                        <div class="form-group col-md-12">
                            <form id="equipment_register_form" class="detail-form">
                                @if(isset($itemInfo->id))
                                    <input type="hidden" name="form_id" value="{{ $itemInfo->id }}">
                                @endif
                                <table width="100%" class="table table-striped table-bordered table-hover data-table no-footer" id="reg-approve-table" role="grid" aria-describedby="register-table_info" style="">
                                    <thead>
                                    <tr role="row">
                                        <th>@lang('admin.equipment.select_type')</th>
                                        <th>@lang('admin.equipment.Request')</th>
                                        <th>@lang('admin.equipment.Approved')</th>
                                        <th width="30%">@lang('admin.absence.reason')</th>
                                        <th>@lang('admin.equipment.status')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($registrations as $item)
                                        <tr role="row" class="reg-list">
                                            <td>
                                                {{ $item->type_name }}
                                            </td>
                                            <td>
                                                {{ $item->total }}
                                            </td>
                                            <td>
                                                {{ $item->approved }}
                                            </td>
                                            <td style="text-align: left;padding:0px 20px">
                                                {{ $item->note }}
                                            </td>
                                            <td>
                                                @if($item->status == 0)
                                                    <span class="btn btn-success add-approve-btn"  data-id="{{ $item->id }}">@lang('admin.equipment.Approved_choose')</span>
                                                    @if($item->arr_code == null)
                                                    <span class="btn btn-danger add-approve-btn"  data-id="{{ $item->id }}" reject='1'>@lang('admin.equipment.rejected_requests')</span>
                                                    @else
                                                    <span class="btn btn-danger add-approve-btn"  data-id="{{ $item->id }}" reject='1'>@lang('admin.equipment.rejected_requests')</span>
                                                    @endif
                                                @elseif($item->status == 2)
                                                    <span class="btn btn-danger">@lang('admin.equipment.Approved_rejected_requests')</span>
                                                @else
                                                    <span class="btn btn-success add-approve-btn"  data-id="{{ $item->id }}">@lang('admin.view')</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                    @endif
                    @if($changeRegistrations->count())
                        <div id="tab2" class="tab-pane fade in @if(!$registrations->count()) active @endif">
                            <div class="form-group col-md-12">
                                <form id="equipment_register_form" class="detail-form">
                                    @if(isset($itemInfo->id))
                                        <input type="hidden" name="form_id" value="{{ $itemInfo->id }}">
                                    @endif
                                    <table width="100%" class="table table-striped table-bordered table-hover data-table no-footer" id="reg-approve-table" role="grid" aria-describedby="register-table_info" style="">
                                        <thead>
                                        <tr role="row">
                                            <th>@lang('admin.equipment.select_type')</th>
                                            <th>@lang('admin.equipment.DeviceChange')</th>
                                            <th>@lang('admin.equipment.New_equipment')</th>
                                            <th>@lang('admin.absence.reason')</th>
                                            <th>@lang('admin.action')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($changeRegistrations as $item)
                                            <tr role="row" class="reg-list">
                                                <td>
                                                    {{ $item->type_name }}
                                                </td>
                                                <td>
                                                    {{ $item->eq_name }}
                                                    ({{ $item->code}})
                                                </td>
                                                <td>
                                                    @if(isset($item->newEq))
                                                        {{ $item->newEq->name }}
                                                        ({{ $item->newEq->code }})
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $item->note }}
                                                </td>
                                                <td>
                                                    @if($item->status == 0)
                                                        <span class="btn btn-success add-approve-btn"  data-id="{{ $item->id }}">@lang('admin.equipment.Approved_choose')</span>
                                                        <span class="btn btn-danger add-approve-btn"  data-id="{{ $item->id }}" reject='1'>@lang('admin.equipment.rejected_requests')</span>
                                                    @elseif($item->status == 2)
                                                        <span class="btn btn-danger">@lang('admin.equipment.Approved_rejected_requests')</span>
                                                    @else
                                                        <span class="btn btn-success add-approve-btn"  data-id="{{ $item->id }}">@lang('admin.view')</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </form>
                            </div>

                        </div>
                    @endif
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
            </div>
        </div>

    </div>
</div>

<div class="modal draggable fade in modal-inside" id="add-reg-modal" role="dialog">

</div>




<div class="modal draggable fade in ui-draggable ui-draggable-handle" role="dialog" data-backdrop="static" id="reject-modal">
    <div class="modal-dialog modal-lg ui-draggable width550">

        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title-2">@lang('admin.overtime.reject_reason')&nbsp;<sup class="text-red">*</sup>:</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <form id="reject-form">
                        <input type="hidden" name="id" id="reg-id" value="">
                        <textarea name="Note" class="form-control" row="5" col="20"></textarea>
                    </form>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm save-rejects-form">@lang('admin.btnSave')</button>
            </div>
        </div>

    </div>

</div>
<script type="text/javascript" async>
    $('.save-form').click(function (e) {
        e.preventDefault();
        ajaxServer("{{ route('admin.EquipmentRegistrations') }}", 'post',  $('.detail-form').serializeArray(), function (data) {
            if (typeof data.errors !== 'undefined'){
                showErrors(data.errors[0]);

            }else{
                console.log(data);
                window.location.reload();
            }
        })
    });

    $(function () {
        $( ".ui-draggable" ).draggable();
        $(".selectpicker").selectpicker();
        $(".add-approve-btn").click(function () {
            var reject = $(this).attr('reject');
            if(typeof reject == 'undefined')
            reject = 0;
            if(reject){
                $("#reg-id").val($(this).attr('data-id'));

                $('#reject-modal').modal('show');
                $(".save-rejects-form").unbind().click(function(e){
                    e.preventDefault();
                    var note = $('textarea[name = "Note"]').val();
                    if(note == ''){
                        alert('Vui lòng điền lý do!');
                        return true;
                    }
                    showConfirm('Bạn có chắc muốn từ chối đơn này không?',function () {
                        $('.loadajax').show();
                        ajaxServer("{{ route('admin.EquipmentRegReject') }}", 'post',  $("#reject-form").serializeArray(), function (data) {
                            if(data == 1){
                                window.location.reload();
                            }
                        })
                    })
                });

            }else{
                ajaxServer( "{{ route('admin.EquipmentApproveDetail') }}/"+$(this).attr('data-id')+'/'+reject, 'GET',null, function (data) {
                        if(data == 1){
                            alert('Bạn có chắc muốn từ chối đơn này không?');
                            window.location.reload();
                        }
                        $('#add-reg-modal').empty().html(data);
                        $('#add-reg-modal').modal('show');
                        $('.loadajax').hide();
                    })
            }
        });
    });
</script>

