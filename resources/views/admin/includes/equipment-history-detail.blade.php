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
                    @csrf
                    <div class="box-body row">
                        <div class="col-sm-12 col-md-5">
                            <div class="form-group row">
                                <label class="col-sm-3">@lang('admin.equipment.type')&nbsp;:</label>
                                <div class="col-sm-9">
                                    <select class="selectpicker show-tick show-menu-arrow" name="eqType[]" id="select-typeEquipment"
                                            data-live-search="true" data-size="5" data-live-search-placeholder="Search" multiple=""
                                            ata-actions-box="true" title="Chọn loại thiết bị..." tabindex="-98">
                                        {!!
                                            GenHtmlOption($types, 'type_id', 'type_name', isset($request['type_id']) ? $request['type_id'] : '')
                                        !!}
                                    </select>

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3">@lang('admin.equipment.Source_equipment')&nbsp;:</label>
                                <div class="col-sm-9">
                                    <select class="selectpicker show-tick show-menu-arrow" id="select-user-owner" name="eqOwner"
                                            data-live-search="true" data-size="5" data-live-search-placeholder="Search" tabindex="-98">
                                        <option value="0" selected="" data-name="Kho">@lang('admin.equipment.store')</option>
                                        @foreach($owners as $owner)
                                            <option value="{{ $owner->id }}" data-name="{{ $owner->FullName }}">{{ $owner->FullName }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3">@lang('admin.equipment.device')&nbsp;<sup class="text-red">*</sup>:</i>
                                </label>
                                <div class="col-sm-9">
                                    <div class="device_list">

                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3">@lang('admin.equipment.Users') &nbsp;:</label>
                                <div class="col-sm-9">
                                    <select class="selectpicker show-tick show-menu-arrow" id="user-owner-new" name="eqReceiver"
                                            data-live-search="true" data-size="5" data-live-search-placeholder="Search" tabindex="-98">
                                        <option value="0" selected="" data-name="Kho">@lang('admin.equipment.store')</option>
                                        @foreach($receive_owners as $receive_owner)
                                            <option value="{{ $receive_owner->id }}" data-name="{{ $receive_owner->FullName }}">{{ $receive_owner->FullName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3">@lang('admin.equipment.Date_of_delivery')&nbsp;<sup class="text-red">*</sup>:
                                </label>
                                <div class="col-sm-9">
                                    <div class="input-group date" id="deal_date" style="width: 220px">
                                        <input type="text" class="form-control" id="deal_date_input" placeholder="@lang('admin.equipment.Date_of_delivery')"
                                               value="{{FomatDateDisplay(\Carbon\Carbon::now(), FOMAT_DISPLAY_DAY) }}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3">@lang('admin.note')&nbsp;:</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows="5" id="note"></textarea>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <button type="button" class="btn btn-primary" id="push-list">@lang('admin.equipment.Create_votes') <span class="glyphicon glyphicon-hand-right"></span></button>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="row">
                                <div class="col-lg-12">
                                    <h3 class="page-header">@lang('admin.equipment.List_of_equipment_handed')</h3>
                                </div>
                            </div>
                            <form id="form-handover">
                                <table class="table table-striped" id="table-eq-handover">
                                    <thead>
                                    <tr>
                                        <th>@lang('admin.equipment.Source')</th>
                                        <th>@lang('admin.equipment.name') (mã)</th>
                                        <th>@lang('admin.equipment.Users')</th>
                                        <th>@lang('admin.equipment.deal_date')</th>
                                        <th class="width10">@lang('admin.note')</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                    <!-- /.box-body -->
                     @if(isset($itemInfo->Name))
                    <input type="hidden" name="id" value="{{ $itemInfo->id }}">
                    @endif
            </div>
            <div class="modal-footer">
                 <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm save-form" id="saveHandover">@lang('admin.btnSave')</button>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript" async>
    var eqListAjaxUrl = "{{ route('ajax.equipmentList') }}";

    $(function () {
        $('#saveHandover').click(function (e) {
        e.preventDefault();
        var confirmMsg = 'Bạn có muốn xuất excel không?';
        var DealDate = [];
        var search=[];
        var user_owner =[];
        var array = $('#form-handover').serializeArray();
        console.log(array)
        if(array.length>4){
            for (var i = 0; i < array.length/4; i++) {
                var code = array[i*4]['value'];
                var type_id = '';
                var status_id = '';
                var created_user = {{Auth::user()->id}};
                user_owner.push(array[i*4+1]['value']);
                search.push(code);
                DealDate.push(array[i*4+2]['value']);
                DealDate.push(array[i*4+2]['value']);
            }
        }else{
            var type_id = '';
            var status_id = '';
            var created_user = {{Auth::user()->id}};
            user_owner.push(array[1]['value']);
            search.push(array[0]['value']);
            DealDate.push(array[2]['value']);
            DealDate.push(array[2]['value']);
        }
        ajaxServer("{{ route('ajax.saveHandover') }}", 'post',  $('#form-handover').serializeArray(), function (data) {
            if (typeof data.errors !== 'undefined'){
                $('.loadajax').hide();
                showErrors(data.errors[0]);
            }else{
                $('.loadajax').hide();
                $.confirm({
                    title: 'Xác nhận?',
                    // icon: 'fa fas fa-question',
                    content: confirmMsg + '',
                    buttons: {
                        ok: {
                            text: 'Đồng ý',
                            btnClass: 'btn width5',
                            keys: ['enter'],
                            action: function(){
                                $('.loadajax').show();
                                $.ajax({
                                    type: 'GET',
                                    url: '{{ route('export.equipmentHistories') }}?type_id='+type_id+'&status_id='+status_id+'&created_user='+created_user+'&user_owner='+user_owner+'&DealDate='+DealDate+'&search='+search,
                                    async: false,
                                    data:null,
                                    success: function(data) {
                                        if (typeof data.errors !== 'undefined'){
                                            $('.loadajax').hide();
                                            showErrors(data.errors[0]);
                                        }else{
                                            window.open('{{ route('export.equipmentHistories') }}?type_id='+type_id+'&status_id='+status_id+'&created_user='+created_user+'&user_owner='+user_owner+'&DealDate='+DealDate+'&search='+search);
                                            window.location.reload();
                                        }
                                    },
                                    error: function(jqXHR, textStatus, err) {

                                        if (jqXHR.status === 0) return;

                                        //Check authen then redirect to login
                                        if (jqXHR.status === 401 || jqXHR.statusCode === 401)
                                        {
                                            window.location.href = LOGIN_URL;
                                            return;
                                        }

                                        if (IsFunction(funcErr)){
                                            funcErr(jqXHR, textStatus, err);
                                        }
                                    }
                                });
                            }
                        },
                        cancel: {
                            text: 'Đóng',
                            btnClass: 'btn width5',
                            keys: ['esc'],
                            action: function(){
                                window.location.reload();
                            }
                        }
                    }
                });
            }
        });
        
        });

        SetDatePicker($('#deal_date'));
        $('#sTimeOfDay,#eTimeOfDay').datetimepicker({
            allowInputToggle: true,
            format: 'HH:mm',
            stepping: 15
        });
        $(".selectpicker").selectpicker();


        $('#push-list').click(function () {
            var eqOwner = $("[name='eqOwner']").val();
            var eqOwnerName = $("[name='eqOwner'] option:selected").attr('data-name');
            var userOwner = $("[name='eqReceiver']").val();
            var userOwnerName = $("[name='eqReceiver'] option:selected").attr('data-name');
            eq = [];
            $("input:checkbox[name='eq[]']:checked").each(function(){
                data_value = $(this).val();
                data_name = $(this).attr('data-name');
                eq.push({val: data_value, name: data_name});
            });

            var deal_date = $("#deal_date_input").val();
            var note = $('textarea#note').val();
            if(eqOwner == userOwner){
                alert('Nguồn thiết bị và người nhận không được trùng nhau!');
                return;
            }
            if(isEmpty(eq)){
                alert('Bạn chưa chọn thiết bị nào!');
                return;
            }
            // $("#table-eq-handover tbody").html('');
            for(key in eq){
                if (eq.hasOwnProperty(key) &&
                    /^0$|^[1-9]\d*$/.test(key) &&
                    key <= 4294967294
                ) {
                    $("input.checkboxDevice[value='"+eq[key].val+"']").closest('div').remove();
                    html = `<tr>

                    <td class="user center-important ">`+eqOwnerName+`</td>
                    <td class="center-important">
                        <i>`+eq[key].name+`</i>
                        <input type="hidden" name="eq1[]" value="`+eq[key].val+`">
                    </td>
                    <td class="user_new center-important">
                        `+userOwnerName+`
                        <input type="hidden" name="receive_owners[]" value="`+userOwner+`">
                    </td>
                    <td class="deal_date center-important">
                        `+deal_date+`
                        <input type="hidden" name="deal_date[]" value="`+deal_date+`">
                    </td>
                    <td class="note center-important">
                        <p style="word-wrap: break-word;width: 10em;">
                        `+note+`
                        </p>
                        <input type="hidden" name="note[]" value="`+note+`">
                    </td>
                    <td class="delDevice center-important">
                        <button type="button" class="btn btn-danger btn-xs btn-del-eq"><span class="glyphicon glyphicon-trash"></span>
                        </button>
                    </td>
                </tr>`;
                    $("#table-eq-handover tbody").append(html);


                    // console.log(obj);
                }
            }

            $(".btn-del-eq").click(function () {
                $(this).closest('tr').remove();
                $("select[name='eqType[]']").change();
            });

        });
        $("select[name='eqType[]'], select[name='eqOwner']").on('change', function() {
            var eqType = $("[name='eqType[]']").val();
            // console.log(eqType);
            var eqOwner = $("[name='eqOwner']").val();
            getEquipmentList(eqType, eqOwner);
        });
        $("select[name='eqType[]']").change();
    });

</script>

