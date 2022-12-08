<div class="modal-dialog modal-lg ui-draggable " data-backdrop="static">

    <!-- Modal content-->
    <div class="modal-content drag">
        <div class="modal-header ui-draggable-handle" style="cursor: move;">
            <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
            {!! $itemInfo->change_id == 1 ? '<h4 class="modal-title">Duyệt thêm thiết bị</h4>' : '<h4 class="modal-title">Duyệt đổi thiết bị</h4>' !!}

        </div>
        <div class="modal-body">

            <div class="save-errors"></div>
                @csrf
                <div class="box-body row">
                    <div class="form-group col-md-12">
                        <span id="device-reg">
                            <b>@lang('admin.equipment.Kind_of_change'):</b>
                            {!! $itemInfo->change_id == 1 ? '<span class="label label-info btn-apporove-register">Thêm thiết bị</span>' : '<span class="label label-warning btn-apporove-register">Đổi thiết bị</span>' !!} <br>
                            <b>@lang('admin.equipment.type'):</b> {{ $itemInfo->type_name }}<br>
                            @if(!is_null($itemInfo->eq))
                            <b>@lang('admin.equipment.name'):</b> {{ $itemInfo->eq->name }}<br>
                            @endif
                            <b>@lang('admin.equipment.Petitioner'):</b> {{ $itemInfo->user_id }}<br>
                            <b>@lang('admin.equipment.approved_Petitioner'):</b> {{ count($processedEq) }} / {{ $itemInfo->total }}<br>
                            <b>@lang('admin.equipment.Approved_note') : </b> {{ $itemInfo->note }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('admin.equipment.Source_equipment') &nbsp;<sup class="text-red">*</sup>:</label>
                            <select class="form-control selectpicker show-tick show-menu-arrow" name="device-source" id="device-source" data-live-search="true" data-live-search-placeholder="Search" data-size="6" tabindex="-98">
                                <option value="0">Kho</option>
                                @if(!is_null($itemInfo->eq))
                                    {!!
                                        GenHtmlOption($itemInfo->newOwners, 'id', 'FullName', isset($itemInfo->newSource) ? $itemInfo->newSource :'')
                                    !!}
                                @else
                                    {!!
                                        GenHtmlOption($itemSources, 'id', 'FullName', '')
                                    !!}
                                @endif

                            </select>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label>@lang('admin.equipment.device'):</label>
                                <div class="form-control selectpicker show-tick show-menu-arrow" id="device-list" style="height: auto;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                       <div class="form-group">
                           <label class="col-form-label" for="device-list">@lang('admin.equipment.listThietBi'):</label>
                           <form id="reg-approve-form">
                                <input type="hidden" name="reg" value="{{ $itemInfo->id }}">
                                <input type="hidden" name="new_source" value="0">
                                <table class="table table-striped form-control selectpicker show-tick show-menu-arrow add_table" style="height: auto;">
                                    <thead>
                                    <tr>
                                        <th class="width10">@lang('admin.equipment.Source')</th>

                                        <th class="width12">@lang('admin.equipment.device')</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                           $dem = 0;
                                        @endphp
                                        @foreach($processedEq as $eq)
                                            @if(!is_null($eq))
                                            <tr>
                                                {{-- <td>{{ $eq->source }}</td> --}}
                                                <td>{{ $eq->oldOwner }}</td>
                                                <td>
                                                    {{ $eq->name }} ({{ $eq->code }})
                                                    <input type="hidden" name="eq1[]" value="{{ $eq->code }}">
                                                </td>
                                                <td class="delDevice">
                                                    <i class="fa fa-check" style="font-size:18px;color:green;"></i>
                                                </td>
                                            </tr>
                                            @php
                                               $dem = $dem+1;
                                            @endphp
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </form>
                       </div>
                    </div>
                </div>
                <!-- /.box-body -->
        </div>
        <div class="modal-footer">
             <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
             @if($reg != 1)
                <button type="submit" class="btn btn-primary btn-sm" id='save-eqapp'>@lang('admin.btnSave')</button>
            @endif
        </div>
    </div>

</div>

<script type="text/javascript" async>
    $(function () {

        $( ".ui-draggable" ).draggable();
        $(".selectpicker").selectpicker();

        $("select[name='new_owner']").on('change', function() {
            $("[name='new_source']").val($(this).val());
        });
        $("select[name='device-source']").on('change', function() {

            var ownerName = $("option:selected", this).text();
            var ownerId = $(this).val();
            ajaxServer("{{ route('ajax.equipmentApproveList') }}", 'post',  {'user_owner': $(this).val(), 'type_id' : '{{ $itemInfo->type_id }}' }, function (data) {
                 $('#device-list').html('');
                    for(key in data)
                    if (data.hasOwnProperty(key) &&
                        /^0$|^[1-9]\d*$/.test(key) &&
                        key <= 4294967294
                    ) {
                        eq = [];
                        $("input[name='eq1[]']").each(function(){
                            data_value = $(this).val();
                            eq.push(data_value);
                        });
                        if(!inArray(data[key].code, eq)){
                            html = `<div class="checkbox"><label><input class="checkboxDevice" type="checkbox" value="`+data[key].code+`" name="eq[]" data-name="`+data[key].name+` <i>(`+data[key].code+`)</i>">`+data[key].name+` <i>(`+data[key].code+`)</i></label></div>`;
                            $('#device-list').append(html);
                        }

                    }
                    $("input[name='eq[]']").click(function () {
                        var count = $("input[name='eq1[]']").length;
                        if(count == '{{ $itemInfo->total }}'){
                            alert('Đã chọn đủ thiết bị!');
                            $(this).prop('checked', false);
                            return;
                        }
                        $(this).closest("div").fadeOut(300, function() { $(this).remove(); });

                        html = `<tr>

                            <td>`+ownerName+`</td>

                            <td>
                                `+$(this).attr('data-name')+`
                                <input type="hidden" name="eq1[]" value="`+$(this).val()+`">
                            </td>


                            <td class="delDevice">
                                <button type="button" class="btn btn-danger btn-xs btn-del-eq"><span class="glyphicon glyphicon-trash"></span>
                                </button>
                            </td>
                        </tr>`;
                        $(".add_table tbody").append(html);
                        $(".btn-del-eq").click(function () {
                            $(this).closest('tr').remove();
                            $("select[name='device-source']").change();
                        });

                    });
            })
        });
        $("select[name='device-source']").change();
        $('#save-eqapp').click(function () {
            showConfirm('Bạn có chắc muốn duyệt đơn  không?',
                function () {
                $('.loadajax').show();
                ajaxServer("{{ route('admin.EquipmentApproveDetail') }}", 'post',  $("#reg-approve-form").serializeArray(), function (data) {
                    if (typeof data.errors !== 'undefined'){
                        $('.loadajax').hide();
                        showErrors(data.errors[0]);
                    }else{
                        $('.loadajax').hide();
                        window.location.reload();
                    }
                })
            })
        });
    });
</script>
