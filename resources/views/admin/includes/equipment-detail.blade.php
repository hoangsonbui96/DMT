<div class="modal draggable fade in detail-modal" id="user-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('admin.user.add_new_user')</h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                <form class="detail-form" role="form" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>@lang('admin.equipment.type')&nbsp;<sup class="text-red">*</sup>:</label>
                                <select class="form-control selectpicker" name="type_id" >
                                    <option value="">@lang('admin.equipment.select_type')</option>
                                    {!!
                                        GenHtmlOption($types, 'type_id', 'type_name', isset($itemInfo->type_id) ? $itemInfo->type_id : '')
                                    !!}
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label>@lang('admin.equipment.status')&nbsp;:</label>
                                <select class="form-control selectpicker" name="status_id" >
                                    {!!
                                        GenHtmlOption($status_list, 'id', 'Name', isset($itemInfo->status_id) ? $itemInfo->status_id : '')
                                    !!}
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>@lang('admin.equipment.name')&nbsp;<sup class="text-red">*</sup>:</label>
                            <input type="text" class="form-control"  name="name" value="{{ isset($itemInfo->name) ? $itemInfo->name : null }}">
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label>@lang('admin.equipment.serial_number')&nbsp;:</label>
                                <input type="text" class="form-control" name="serial_number" value="{{ isset($itemInfo->serial_number) ? $itemInfo->serial_number : null }}">
                            </div>
                            <div class="col-sm-4">
                                <label>@lang('admin.equipment.buy_date')&nbsp;:</label>
                                <div class="input-group date" id= "dtpkTime">
                                    <input type="text" class="form-control dtpkTime" name="buy_date" value="{{ isset($itemInfo->buy_date) ? FomatDateDisplay($itemInfo->buy_date, FOMAT_DISPLAY_DAY) : null }}">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label>@lang('admin.equipment.period_date')&nbsp;:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="period_date"  value="{{ isset($itemInfo->period_date) ? \Carbon\Carbon::parse($itemInfo->period_date)->diffInMonths(\Carbon\Carbon::parse($itemInfo->buy_date)) : null }}">
                                <span class="input-group-addon">Tháng</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-8">
                                <label>@lang('admin.equipment.provider')&nbsp;:</label>
                                <input type="text" class="form-control" name="provider"  value="{{ isset($itemInfo->provider) ? $itemInfo->provider : null }}">
                            </div>
                            <div class="col-sm-4">
                                <label>@lang('admin.equipment.unit_price')&nbsp;:</label>
                                <input type="text" class="form-control" name="unit_price"  value="{{ isset($itemInfo->unit_price) ? $itemInfo->unit_price : null }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('admin.equipment.info')&nbsp;:</label>
                            <textarea class="form-control" name="info">{{ isset($itemInfo->info) ? $itemInfo->info : null }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>@lang('admin.equipment.note')&nbsp;:</label>
                            <textarea class="form-control" name="note">{{ isset($itemInfo->note) ? $itemInfo->note : null }}</textarea>
                            <input type="hidden" name="user_owner" value="{{ isset($itemInfo->user_owner) ? $itemInfo->user_owner : '' }}">
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>@lang('admin.equipment.deal_date')&nbsp;:</label>
                                <div class="input-group date" id= "date-deal">
                                    <input type="text" class="form-control date-deal" name="deal_date"  value="{{ isset($itemInfo->deal_date) ?  FomatDateDisplay($itemInfo->deal_date, FOMAT_DISPLAY_DAY): \Carbon\Carbon::now()->format(FOMAT_DISPLAY_DAY) }}">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label>@lang('admin.equipment.room_id')&nbsp;:</label>
                                <select name="room_id" class="form-control selectpicker">
                                    <option value="">@lang('admin.equipment.room_id')</option>
                                    {!!
                                        GenHtmlOption($put_rooms, 'id', 'Name', isset($itemInfo->room_id) ? $itemInfo->room_id : '')
                                    !!}
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    @if(isset($itemInfo->id) && !isset($copy))
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
        var buy_date = $('input[name="buy_date"]').val();
        var deal_date = $('input[name="deal_date"]').val();
        var serial_number = $('input[name="serial_number"]').val();
        var unApproveUrl = "{{ route('admin.Equipment') }}";
        ajaxServer(unApproveUrl, 'post', $('.detail-form').serializeArray(), function (data) {
            if (typeof data.errors !== 'undefined'){
                showErrors(data.errors[0]);
            }else{
                window.location.reload();
            }
        });
    });

    $(function () {
        SetDatePicker($('#date-deal,.date-deal'));
        SetDatePicker($('.dtpkTime,#dtpkTime'), {
            format: FOMAT_DATE.toLowerCase(),
            endDate: new Date(),
        });
        $('#sTimeOfDay,#eTimeOfDay').datetimepicker({
            allowInputToggle: true,
            format: 'HH:mm',
            stepping: 15
        });
        $(".selectpicker").selectpicker();
        $( ".ui-draggable" ).draggable();
        $("select[name='register_room_id']").on('change', function() {
            // console.log($(this).val());
            var unApproveUrl =  "{{ route('admin.getUsersByRoom') }}/"+$(this).val();
            ajaxServer(unApproveUrl, 'get',null, function (data) {
                html = `<option value="">@lang('admin.equipment.select_register')</option>`;
                    for(key in data){
                        html += `<option value="`+data[key].id+`">`+data[key].FullName+`</option>`;
                    }
                    $("select[name='user_owner']").html(html);
                    $("select[name='user_owner']").selectpicker('refresh');
            });
        });

        {{--$("select[name='status_id']").on('change', function() {--}}
        {{--    // console.log($(this).val());--}}
        {{--    if($(this).val() != 16){--}}
        {{--        $("select[name='register_room_id']").attr('disabled', 'disabled');--}}
        {{--        $("select[name='user_owner']").attr('disabled', 'disabled');--}}
        {{--        $("select[name='register_room_id']").val('').trigger('change');--}}
        {{--        // $("select[name='user_owner']").val('').trigger('change');--}}
        {{--    }else{--}}
        {{--        $("select[name='register_room_id']").removeAttr('disabled');--}}
        {{--        $("select[name='user_owner']").removeAttr('disabled');--}}
        {{--    }--}}
        {{--});--}}
        {{--@if(isset($itemInfo->status_id) && $itemInfo->status_id != 16)--}}
        {{--$("select[name='status_id']").change();--}}
        {{--@endif--}}
    });
</script>

