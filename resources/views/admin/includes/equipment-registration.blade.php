<div class="modal draggable fade in detail-modal" id="user-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable">

        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">

                <div class="save-errors"></div>
                    @csrf
                    <div class="box-body row">
                        <div class="form-group col-sm-6">
                            <label>@lang('admin.equipment.Kind_of_change')&nbsp;<sup class="text-red">*</sup>:</label>
                            <select class="form-control selectpicker show-tick show-menu-arrow" name="change_id" id="device-change" data-live-search="true" data-live-search-placeholder="Search" data-size="6" tabindex="-98">
                                <option value="">@lang('admin.equipment.The_changes')</option>
                                <option value="1">@lang('admin.equipment.add')</option>
                                <option value="2">@lang('admin.equipment.Change_device')</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-6">
                            <label>@lang('admin.equipment.type')&nbsp;<sup class="text-red">*</sup>:</label>
                            <select class="form-control selectpicker show-tick show-menu-arrow" name="eqType" id="device-type" data-live-search="true" data-live-search-placeholder="Search" data-size="6" tabindex="-98">
                                <option value="">@lang('admin.equipment.type_list')</option>
                                @foreach($types as $type)
                                <option value="{{ $type->type_id }}" data-name="{{ $type->type_name }}">{{ $type->type_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-sm-5">
                            <label>@lang('admin.equipment.device')&nbsp;<sup class="text-red">*</sup>:</label>
                            <select class="form-control selectpicker show-tick show-menu-arrow" name="code" id="device-code" data-live-search="true" data-live-search-placeholder="Search" data-size="6" tabindex="-98">
                                <option value="">@lang('admin.equipment.type_list')</option>
                            </select>
                        </div>
                        <div class="form-group col-sm-4">
                            <label>@lang('admin.equipment.Device_status')&nbsp;<sup class="text-red">*</sup>:</label>
                            <select class="form-control selectpicker show-tick show-menu-arrow" name="status" id="device-status" data-live-search="true" data-live-search-placeholder="Search" data-size="6" tabindex="-98">
                                <option value="">[@lang('admin.equipment.status_list')]</option>
                                {!!
                                    GenHtmlOption($status_list, 'id', 'Name','')
                                !!}
                            </select>
                        </div>
                        <div class="form-group col-sm-3">
                            <label>@lang('admin.equipment.quantity')&nbsp;<sup class="text-red">*</sup>:</label>
                            <select class="form-control selectpicker show-tick show-menu-arrow" name="total" id="device-total" data-live-search="true" data-live-search-placeholder="Search" data-size="6" tabindex="-98">
                                <option value="1" selected="">1</option>
                                @for($i = 2 ; $i<21 ;$i++)
                                    <option value="{{$i}}" >{{$i}}</option>
                                @endfor
                                </select>
                        </div>
                        <div class="form-group col-sm-10">
                            <label>@lang('admin.absence.reason')&nbsp;<sup class="text-red">*</sup>:</label>
                            <textarea class="form-control" name="note" id="device-note" placeholder="@lang('admin.absence.reason')" rows="3"></textarea>
                        </div>
                        <div class="form-group col-sm-2">
                            <br>
                            <br>
                            <button type="button" class="btn btn-success" id="btnAddReg">@lang('admin.btnAddReg')</button>
                            <button type="button" class="btn btn-success" id="btnUpdateReg" style="display: none;">@lang('admin.btnUpdateReg')</button>
                        </div>
                        <div class="form-group col-sm-12">
                             <form id="equipment_register_form" class="detail-form">
                                @if(isset($itemInfo->id))
                                    <input type="hidden" name="form_id" value="{{ $itemInfo->id }}">
                                @endif
                                <table width="100%" class="table table-striped table-bordered table-hover data-table no-footer" id="register-table" role="grid" aria-describedby="register-table_info" style="">
                                    <thead>
                                    <tr role="row">
                                        <th>@lang('admin.equipment.Kind_of_change')</th>
                                        <th>@lang('admin.equipment.select_type')</th>
                                        <th>@lang('admin.equipment.name')</th>
                                        <th>@lang('admin.equipment.quantity')</th>
                                        <th>@lang('admin.absence.reason')</th>
                                        <th>@lang('admin.action')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($registrations as $item)
                                            <tr role="row" class="reg-list">
                                                <td>
                                                    {!! $item->change_id == 1 ? '<span class="label label-info btn-apporove-register">Thêm thiết bị</span>' : '<span class="label label-warning btn-apporove-register">Đổi thiết bị</span>' !!}
                                                </td>
                                                <td>
                                                    {{ $item->type_name }}
                                                </td>
                                                <td>
                                                    {{ $item->eq_name }}
                                                </td>
                                                <td>
                                                    {{ $item->total }}
                                                </td>
                                                <td>
                                                    {{ $item->note }}
                                                </td>

                                                <td>
                                                    @if($item->status == 0 && is_null($item->approved_user))
                                                    <i class="fa fa-edit"></i>
                                                    <i class="fa fa-trash"></i>
                                                    @endif
                                                    <input type="hidden" name="id[]" value="{{ $item->id }}">
                                                    <input type="hidden" name="note[]" value="{{ $item->note }}">
                                                    <input type="hidden" name="total[]" value="{{ $item->total }}">
                                                    <input type="hidden" name="eq[]" value="{{ $item->code }}">
                                                    <input type="hidden" name="eqType[]" value="{{ $item->type_id }}">
                                                    <input type="hidden" name="changeType[]" value="{{ $item->change_id }}">

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                             </form>
                        </div>
                    </div>
                    <!-- /.box-body -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                {{-- @if((isset($itemInfo) && $itemInfo->requests - $itemInfo->processed_requests - $itemInfo->rejected_requests > 0) || !isset($itemInfo)) --}}
                <button type="submit" class="btn btn-primary btn-sm save-form">@lang('admin.btnSave')</button>
                {{-- @endif --}}
            </div>
        </div>

    </div>
</div>

<script type="text/javascript" async>
    var Id = $('input[name="form_id"]').val();
    if(!!!Id){
        $('#btnAddReg').prop('disabled', false);
    }else{
        $('#btnAddReg').prop('disabled', true);
    }
    $('.save-form').click(function () {
        var info = $('#register-table tbody tr td').text();
        if(!!!info){
            alert('Không có đơn nào cả?');
            return true;
        }
        $('.loadajax').show();
        ajaxServer("{{ route('admin.EquipmentRegistrations') }}", 'post',  $('.detail-form').serializeArray(), function (data) {
            if (typeof data.errors !== 'undefined'){
                $('.loadajax').hide();
                showErrors(data.errors[0]);
            }else{
                $('.loadajax').hide();
                window.location.reload();
            }
        })
    });
    // $('.dtpkTime').datepicker();
    $(function () {
        var insideClick = false;
        var index;
        var id='';
        var checkLoop = false;
        $( ".ui-draggable" ).draggable();
        $(".selectpicker").selectpicker();
        $("select[name='change_id']").on('change', function() {
            // $("select[name='eqType']").change();
            if($(this).val() == 2){
                // $("select[name='total']").val(1);
                $("select[name='total']").val(1).trigger('change');
                $("select[name='total']").attr('disabled', 'disabled');
                // $("select[name='total']").selectpicker('refresh');
                $("select[name='code']").val('').trigger('change');
                $("select[name='status']").val('').trigger('change');
                $("select[name='code']").removeAttr('disabled');
                $("select[name='status']").removeAttr('disabled');

            }else{
                $("select[name='code']").val('').trigger('change');
                $("select[name='status']").val('').trigger('change');

                $("select[name='code']").attr('disabled', 'disabled');
                $("select[name='status']").attr('disabled', 'disabled');
                $("select[name='total']").removeAttr('disabled');
            }

            ajaxServer("{{ route('ajax.equipmentTypeList') }}", 'post', {'change_id': $(this).val()}, function (data) {
                html = `<option value="">@lang('admin.equipment.type_list')</option>`;
                    for(key in data){
                        html += `<option value="`+data[key].type_id+`">`+data[key].type_name+`</option>`;
                    }
                    $("select[name='eqType']").html(html);
                    $("select[name='eqType']").selectpicker('refresh');
            })
        });
        $("select[name='change_id']").change();
        $("select[name='eqType']").on('change', function() {
            // $("select[name='change_id']").change();
            var eqType = [$("[name='eqType']").val()];
            // console.log(eqType);
            var eqOwner = '{{ \Illuminate\Support\Facades\Auth::user()->id }}';
            ajaxServer("{{ route('ajax.equipmentList') }}", 'post', {'eqType':eqType, 'eqOwner':eqOwner}, function (data) {
                html = `<option value="">@lang('admin.equipment.list')</option>`;
                    for(key in data){
                        html += `<option value="`+data[key].code+`">`+data[key].name+`</option>`;
                    }
                    $("select[name='code']").html(html);
                    $("select[name='code']").selectpicker('refresh');
            })
        });
        /*tinh trang thiet bi */
        $("select[name='code']").on('change', function() {
            ajaxServer("{{ route('ajax.equipmentStatus') }}", 'post', {'code': $(this).val()}, function (data) {
                html = `<option value="">@lang('admin.equipment.status_list')</option>`;
                    for(key in data){
                        html += `<option value="`+data[key].id+`" selected>`+data[key].Name+`</option>`;
                    }
                    $("select[name='status']").html(html);
                    $("select[name='status']").selectpicker('refresh');
            })
        });

        $(".fa-edit").click(function () {
            $('#btnAddReg').prop('disabled', false);
            index = $('tr.reg-list').index($(this).closest('tr'));

            $("select[name='change_id']").val($("[name='changeType[]']:eq("+index+")").val()).trigger('change');
            var isDone=false;
            $(document).ajaxStop(function () {
                if(isDone)return;
                isDone=true;
                $("select[name='eqType']").val($("[name='eqType[]']:eq("+index+")").val()).trigger('change');
                // console.log('test');
                var isDone2 = false;
                $(document).ajaxStop(function () {
                    if(isDone2)return;
                    isDone2=true;
                    $("select[name='code']").val($("[name='eq[]']:eq("+index+")").val()).trigger('change');
                    // console.log(t);
                    $("select[name='total']").val($("[name='total[]']:eq("+index+")").val()).trigger('change');
                    $("textarea[name='note']").val($("[name='note[]']:eq("+index+")").val());
                    // console.log('test');
                    insideClick = true;
                    id = $("[name='id[]']:eq("+index+")").val();
                });
            });


            $("#btnAddReg").text('Cập nhật');
        });
        $(".fa-trash").click(function () {
            $(this).closest('tr').remove();
        });

        $("#btnAddReg").click(function () {
            if(!!!Id){
                $('#btnAddReg').prop('disabled', false);
            }else{
                $('#btnAddReg').prop('disabled', true);
            }
            // if(insideClick) console.log('test');
            var eqType = $("[name='eqType']").val();
            var eqTypeName = $("[name='eqType']  option:selected").text();
            // console.log(eqTypeName);
            var change_id = $("[name='change_id']").val();

            var status = $("[name='status']").val();
            var note = $("#device-note").val();
            var total = $("[name='total']").val();
            if(change_id == 2){
                var code = $("[name='code']").val();
                var eqName = $("[name='code'] option:selected").text();
            }
            else{
                eqName = '';
                var code = '';
            }
            if(change_id == 0){
                alert('Vui lòng chọn loại thay đổi!');
                if(!!!Id){
                    $('#btnAddReg').prop('disabled', false);
                }else{
                    $('#btnAddReg').prop('disabled', true);
                }
                return;
            }
            if(eqType == 0){
                alert('Vui lòng chọn loại thiết bị!');
               if(!!!Id){
                    $('#btnAddReg').prop('disabled', false);
                }else{
                    $('#btnAddReg').prop('disabled', true);
                }
                return;
            }
            if(change_id == 2){
                if(code == 0){
                    alert("Vui lòng chọn thiết bị cần đổi!");
                    if(!!!Id){
                        $('#btnAddReg').prop('disabled', false);
                    }else{
                        $('#btnAddReg').prop('disabled', true);
                    }
                    return;
                }
                if(status == 0){
                    alert("Chưa chọn tình trạng thiết bị!");
                    if(!!!Id){
                        $('#btnAddReg').prop('disabled', false);
                    }else{
                        $('#btnAddReg').prop('disabled', true);
                    }
                    return;
                }
            }
            if(note == 0){
                alert("Chưa nhập lý do!");
                if(!!!Id){
                    $('#btnAddReg').prop('disabled', false);
                }else{
                    $('#btnAddReg').prop('disabled', true);
                }
                return;
            }
            if(note.match(/[(&|@|!|#|^|$|%|*|+|=)]/)){
                alert("Lý do không thể có kí tự đặc biệt!");
                $('#btnAddReg').prop('disabled', false);
                return;
            }
            if(change_id == 1){
                change_name = '<span class="label label-info btn-apporove-register">Thêm thiết bị</span>';
            }else{
                change_name = '<span class="label label-warning btn-apporove-register">Đổi thiết bị</span>';
            }

            // if(!insideClick)
            checkAdd(change_id, eqType, code, index, id);
            var isOk = false;
            $(document).ajaxStop(function () {
                if(isOk) return;
                else isOk = true;
                // console.log('tét');
                // console.log(checkLoop);
                if(checkLoop){
                    alert('Đăng ký thay đổi thiết bị đã tồn tại');
                    $('#btnAddReg').prop('disabled', false);
                }else{
                    var new_record = `
                <td>
                    `+change_name+`
                </td>
                <td>
                    `+eqTypeName+`
                </td>
                <td>
                    `+eqName+`
                </td>
                <td>
                    `+total+`
                </td>
                <td>
                    `+note+`
                </td>
                <td>
                    <i class="fa fa-edit"></i>
                    <i class="fa fa-trash"></i>
                    <input type="hidden" name="id[]" value="`+id+`">
                    <input type="hidden" name="note[]" value="`+note+`">
                    <input type="hidden" name="total[]" value="`+total+`">
                    <input type="hidden" name="eq[]" value="`+code+`">
                    <input type="hidden" name="eqType[]" value="`+eqType+`">
                    <input type="hidden" name="changeType[]" value="`+change_id+`">
                </td>
            `;
                    if(!insideClick){
                        $("#register-table tbody").append(`<tr role="row" class="reg-list">`+new_record+`</tr>`);
                    }else{
                        $('tr.reg-list:eq('+index+')').html(new_record);
                        insideClick = false;
                    }

                    // $("select[name='change_id']").change();
                    $("select[name='change_id']").val('').trigger('change');
                    $("select[name='eqType']").val('').trigger('change');
                    $("select[name='total']").val(1).trigger('change');
                    $("textarea[name='note']").val('');
                    $(".fa-edit").click(function () {
                        index = $('tr.reg-list').index($(this).closest('tr'));

                        $("select[name='change_id']").val($("[name='changeType[]']:eq("+index+")").val()).trigger('change');
                        var isDone=false;
                        $(document).ajaxStop(function () {
                            if(isDone)return;
                            isDone=true;
                            $("select[name='eqType']").val($("[name='eqType[]']:eq("+index+")").val()).trigger('change');
                            // console.log('test');
                            var isDone2 = false;
                            $(document).ajaxStop(function () {
                                if(isDone2)return;
                                isDone2=true;
                                // var t = $("select[name='code']").val($("[name='eq[]']:eq("+index+")").val()).trigger('change');
                                //  console.log(t);
                                $("select[name='code']").val($("[name='eq[]']:eq("+index+")").val()).trigger('change');
                                $("select[name='total']").val($("[name='total[]']:eq("+index+")").val()).trigger('change');
                                $("textarea[name='note']").val($("[name='note[]']:eq("+index+")").val());
                                // console.log('test');
                                insideClick = true;
                                id = $("[name='id[]']:eq("+index+")").val();
                            });
                        });
                        $("#btnAddReg").text('Cập nhật');
                    });
                    $(".fa-trash").click(function () {
                        $(this).closest('tr').remove();
                    });
                    $("#btnAddReg").text('Thêm');
                }
            });

        });

        //function check add registration
        function checkAdd(changeId, typeId, eqId, index=null, id=null) {
            //check data base

            //check current form
            // checkLoop = false;
            for(var i=0;i < $("[name='eq[]']").length; i++){
                if(i != index){
                    if(isEmpty(eqId)){
                        if(changeId == $("[name='changeType[]']:eq("+i+")").val() && typeId == $("[name='eqType[]']:eq("+i+")").val() ){
                            checkLoop = true;
                            break;
                        }
                    }else{
                        if(eqId == $("[name='eq[]']:eq("+i+")").val()){
                            checkLoop = true;
                            break;
                        }
                    }
                }

            }

            ajaxServer("{{ route('ajax.checkAddReg') }}", 'post', {'changeId': changeId, 'typeId': typeId, 'eqId': eqId, 'id': id}, function (data) {
                if(data == 0) checkLoop = true;
            })
        }
    });

</script>

