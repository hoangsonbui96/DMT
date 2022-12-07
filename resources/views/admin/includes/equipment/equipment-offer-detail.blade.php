@php
    $canAppr = false;
@endphp

@can('action', $approve)
    @php
        $canAppr = true;
    @endphp
@endcan

@php
    $complete = true;
    if (isset($equipment_offer_detail)) {
        foreach ($equipment_offer_detail as $item) {
            if ($item->Status != 2 && ($item->BuyDate == '0000-00-00' || $item->BuyDate == null)) {
                $complete = false;
                break;
            }
        }
    }

    $canSave = true;
    $canEdit = true;
    $canEditAfterAppr = true;
    $canDelete = false;
    if (!$canAppr) {
        if (isset($equipment_offer_info) && isset($equipment_offer_info->id)) {
            if (isset($equipment_offer_info->OfferUserID) && $equipment_offer_info->OfferUserID != \Illuminate\Support\Facades\Auth::user()->id) {
                $canSave = false;
                $canEdit = false;
                $canEditAfterAppr = false;
            } elseif (isset($equipment_offer_info->Approved) && $equipment_offer_info->Approved == 0) {
                 $canEditAfterAppr = false;
            } elseif (isset($equipment_offer_info->Approved) && $equipment_offer_info->Approved == 1) {
                 if ($complete) {
                    $canEdit = false;
                    $canEditAfterAppr = false;
                    $canSave = false;
                    $canDelete = true;
                 } else {
                    $canEdit = false;
                    $canDelete = true;
                 }
            } else {
                $canSave = false;
                $canEdit = false;
                $canEditAfterAppr = false;
            }
        }
    } else {
        if (isset($equipment_offer_info) && isset($equipment_offer_info->id)) {
            if (isset($equipment_offer_info->Approved) && $equipment_offer_info->Approved == 0) {
                 $canEditAfterAppr = false;
            } elseif (isset($equipment_offer_info->Approved) && $equipment_offer_info->Approved == 1) {
                $complete = false;
                $canEdit = false;
                $canEditAfterAppr = true;
                $canSave = true;
                $canDelete = true;
            } else {
                $canSave = false;
                $canEdit = false;
                $canEditAfterAppr = false;
            }
        }
    }
@endphp

<style>
    .form-group label {
        padding-top: 1%;
    }

    /*div.bootstrap-select.col-sm-4 {*/
    /*    padding-left: 0;*/
    /*    padding-right: 0;*/
    /*}*/

    .modal .modal-dialog {
        width: 80%;
    }

    @media only screen and (min-device-width : 768px) and (max-device-width : 1024px) {
        .modal .modal-dialog {
            width: 100%;
        }

        .modal .modal-dialog .modal-body{
            padding: 15px 0;
        }

        .modal .modal-dialog .box-body{
            padding: 10px 0;
        }
    }

    @media only screen and (min-width: 1030px) and (max-width: 1366px) {
        .modal .modal-dialog {
            width: 95%;
        }
    }

    @media only screen and (min-width: 1370px) and (max-width: 1919px) {
        .modal .modal-dialog {
            width: 90%;
        }
    }
</style>
<div class="modal draggable fade in detail-modal" id="equipment-offer-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="">×</button>
                <h4 class="modal-title">@lang('admin.equipment-offer.add-new')</h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <form class="detail-form" action="" method="POST" id="equipment-offer-form">
                    @csrf
                    <div class="box-body">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            @if(isset($equipment_offer_info->id))
                                <input type="hidden" name="id" value="{{$equipment_offer_info->id }}" id="id">
                            @else
                                <input type="hidden" id="realTime" value="false">
                            @endif

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label class="col-sm-3">@lang('admin.equipment-offer.name')&nbsp;:</label>
                                    <div class="input-group col-sm-4">
                                        <input type="text" class="form-control" value="{{ isset($equipment_offer_info->id) ? \App\Http\Controllers\Admin\Equipment\EquipmentOfferController::createOfferNumber($equipment_offer_info->id) : '' }}" disabled>
                                    </div>
                                </div>
                                <div class="col-sm-6" id="selectpicker">
                                    <label class="col-sm-3" for="select-user">@lang('admin.equipment-offer.user-id')&nbsp;<sup class="text-red">*</sup>: </label>
                                    <select class='selectpicker col-sm-4 show-tick show-menu-arrow select-user' data-actions-box="true" data-size="5" id='select-user' name="offer_user_id" data-live-search="true" data-live-search-placeholder="Search" @if(!$canEdit) disabled @endif>
                                        {!!
                                            GenHtmlOption($users, 'id', 'FullName', isset($equipment_offer_info->OfferUserID) ? $equipment_offer_info->OfferUserID : \Illuminate\Support\Facades\Auth::user()->id)
                                        !!}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label class="col-sm-3">@lang('admin.equipment-offer.offer-date')&nbsp;<sup class="text-red">*</sup>:</label>
                                    <div class="input-group col-sm-4 date" id="dtpkTime">
                                        <input type="text" class="form-control dtpkTime" name="offer_date" value="{{ isset($equipment_offer_info->OfferDate) ? FomatDateDisplay($equipment_offer_info->OfferDate, FOMAT_DISPLAY_DAY) : date('d/m/Y') }}" @if(!$canEdit) disabled @endif>
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="col-sm-3" for="select-person">@lang('admin.equipment-offer.project')&nbsp;: </label>
                                    <select class='selectpicker col-sm-4 show-tick show-menu-arrow select-user' data-actions-box="true" data-size="5" id='select-project' name="project_id" data-live-search="true" data-live-search-placeholder="Search" @if(!$canSave) disabled @endif>
                                        <option value="0">Tất cả dự án</option>
                                        {!!
                                            GenHtmlOption($project, 'id', 'NameVi', isset($equipment_offer_info->ProjectID) ? $equipment_offer_info->ProjectID : '')
                                        !!}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <label class="col-sm-2"style="width: 14.66666667% !important;">@lang('admin.equipment-offer.content')&nbsp;<sup class="text-red">*</sup>:</label>
                                    <div class="input-group col-sm-10" style="width: 80%; !important;">
                                        <input type="text" class="form-control" name="content" value="{{ isset($equipment_offer_info->Content) ? $equipment_offer_info->Content : '' }}" @if(!$canSave) disabled @endif>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <table class='table table-bordered' id="tbl">
                                    <thead>
                                        <tr>
                                            <th class="width3">@lang('admin.stt')</th>
                                            <th class="width12">@lang('admin.equipment-offer.description')<sup class="text-red">*</sup></th>
                                            <th class="width3">@lang('admin.equipment.quantity')<sup class="text-red">*</sup></th>
                                            <th class="width9">@lang('admin.equipment-offer.unit-price')<sup class="text-red">*</sup></th>
                                            <th class="width9">@lang('admin.equipment-offer.final-unit-price')</th>
                                            <th class="width9">@lang('admin.equipment-offer.price')</th>
                                            <th class="width12">@lang('admin.equipment-offer.buy-address')</th>
                                            <th class="width8">@lang('admin.equipment-offer.buy-date')</th>
                                            <th class="width9">@lang('admin.equipment-offer.buy-user-id')</th>
                                            @if ($canDelete)
                                            <th class="width5">@lang('admin.equipment-offer.buy-delete')</th>
                                            @endif
                                            @if ($canEdit)
                                            <th class="width3"></th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if (isset($equipment_offer_detail))
                                    @foreach($equipment_offer_detail as $key => $item)
                                        <tr id="clone{{ $key + 1 }}">
                                            <input type="hidden" name="detail_id[]" value="{{ $item->id }}">
                                            <td class="text-center stt">{{ $key + 1 }}</td>
                                            <td class="text-center">
                                                <input type="text" class="form-control" name="description[]" value="{{ isset($item->Description) ? $item->Description : null }}" @if(!$canEdit) disabled @endif>
                                            </td>
                                            <td class="text-center">
                                                <input type="number" min="1" class="form-control" id="quantity{{ $key + 1 }}" name="quantity[]" onchange="changePrice({{ $key + 1 }})" value="{{ isset($item->Quantity) ? $item->Quantity : null }}" @if(!$canEdit) disabled @endif>
                                            </td>
                                            <td class="text-center">
                                                <div class="input-group">
                                                    <input type="text" min="0" class="form-control unit-price number-separator" id="unit-price{{ $key + 1 }}" name="unit_price[]" onchange="changePrice({{ $key + 1 }})" value="{{ isset($item->UnitPrice) ? $item->UnitPrice : null }}" @if(!$canEdit) disabled @endif>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="input-group">
                                                    <input type="text" min="0" id="final-unit-price{{ $key + 1 }}" class="form-control final-unit-price number-separator" name="final_unit_price[]" onchange="changePrice({{ $key + 1 }})" value="{{ isset($item->FinalUnitPrice) ? $item->FinalUnitPrice : null }}" @if(!$canEditAfterAppr) disabled @endif>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="input-group">
                                                    <input type="text" min="0" id="price{{ $key + 1 }}" class="form-control price number-separator" name="price[]" value="{{ isset($item->Price) ? $item->Price : null }}" @if(!$canEditAfterAppr) disabled @endif>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <input type="text" class="form-control" name="buy_address[]" value="{{ isset($item->BuyAddress) ? $item->BuyAddress : '' }}" @if(!$canSave) disabled @endif >
                                            </td>
                                            <td class="text-center">
                                                <div class="input-group date" id="dtpkTime">
                                                    <input type="text" class="form-control dtpkTime" name="buy_date[]" value="{{ isset($item->BuyDate) && '0000-00-00' !== $item->BuyDate ? FomatDateDisplay($item->BuyDate, FOMAT_DISPLAY_DAY) : '' }}" @if(!$canEditAfterAppr) disabled @endif>
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                                </div>
                                            </td>
                                            <td class="text-center" id="selectpicker">
                                                <select class='selectpicker form-control show-tick show-menu-arrow select-user' data-actions-box="true" data-size="5" name="buy_user_id[]" data-live-search="true" data-live-search-placeholder="Search" @if(!$canSave) disabled @endif>
                                                    {!!
                                                        GenHtmlOption($users, 'id', 'FullName', isset($item->BuyUserID) ? $item->BuyUserID : \Illuminate\Support\Facades\Auth::user()->id)
                                                    !!}
                                                </select>
                                            </td>
                                            @if ($canDelete)
                                            <td class="text-center">
                                                <input type="checkbox" name="status[]" value="{{ $item->id }}" @if (isset($item->BuyUserID) && $item->Status == 2) checked @endif @if ($complete) disabled @endif>
                                            </td>
                                            @endif
                                            @if($canEdit)
                                                <td class="text-center"><input type='button' class='sub-row' value='-' onclick="clearRow({{ $key + 1 }})"></td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    @endif
                                    </tbody>
                                </table>
                                @if($canEdit)
                                    <input type='button' id='add-row' value='Thêm' style="float: right">
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                @if($canSave)
                    <button type="submit" class="btn btn-primary btn-sm" id="save" >@lang('admin.btnSave')</button>
                @endif
            </div>
            <div style="display: none">
                <table style="display: none" id="tbl-clone">
                    <thead style="display: none"></thead>
                    <tbody style="display: none">
                        <tr id="clone" style="display: none">
                            <td class="text-center stt">1</td>
                            <td class="text-center">
                                <input type="text" class="form-control" name="description[]" value="">
                            </td>
                            <td class="text-center">
                                <input type="number" min="1" class="form-control quantity" id="quantity" name="quantity[]" value="">
                            </td>
                            <td class="text-center">
                                <div class="input-group">
                                    <input type="text" min="0" id="unit-price" class="form-control unit-price number-separator" name="unit_price[]" value="">
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="input-group">
                                    <input type="text" min="0" id="final-unit-price" class="form-control final-unit-price number-separator" name="final_unit_price[]" value="" disabled>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="input-group">
                                    <input type="text" min="0" id="price" class="form-control price number-separator" name="price[]" value="" disabled>
                                </div>
                            </td>
                            <td class="text-center">
                                <input type="text" class="form-control" name="buy_address[]" value="">
                            </td>
                            <td class="text-center">
                                <div class="input-group date" id="dtpkTime">
                                    <input type="text" class="form-control dtpkTime" name="buy_date[]" value="" disabled>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </td>
                            <td class="text-center" id="selectpicker">
                                <select class='selectpicker form-control show-tick show-menu-arrow' data-actions-box="true" data-size="5" name="buy_user_id[]" data-live-search="true" data-live-search-placeholder="Search">
                                    {!!
                                        GenHtmlOption($users, 'id', 'FullName', isset($equipment_offer_detail->BuyUserID) ? $equipment_offer_detail->BuyUserID : \Illuminate\Support\Facades\Auth::user()->id)
                                    !!}
                                </select>
                            </td>
                            <td class="text-center"><input type='button' class='sub-row' value='-' onclick="clearRow(0)"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async>

    $(".selectpicker.select-user").selectpicker({
        noneSelectedText: ''
    });

    $(".dtpkTime,#dtpkTime").each(function() {
        var dateValue = $(this).attr("value");
        if (dateValue != ""){
            $(this).datepicker({
                format: FOMAT_DATE.toLowerCase(),
                setDate: dateValue,
            });
        } else {
            $(this).datepicker({
                format: FOMAT_DATE.toLowerCase(),
                todayHighlight: true,
            });
        }
    });

    var count = parseInt('{{ isset($equipment_offer_detail) ? count($equipment_offer_detail) : 0 }}') + 1;
    if (count === 1) {
        addClone(count);
        count++;
    }

    $("#add-row").click(function () {
        addClone(count);
        count++;
    });

    function changePrice(id) {
        quantity = $('#quantity'+id).val();
        unit_price = convertToFloat($('#unit-price'+id).val());
        final_unit_price = convertToFloat($('#final-unit-price'+id).val());
        console.log(final_unit_price);
        if (typeof quantity != 'undefined' && quantity != '') {
            if (typeof final_unit_price == 'undefined' || final_unit_price === '' || isNaN(final_unit_price)) {
                if (typeof unit_price != 'undefined' && unit_price !== '' && !isNaN(unit_price)) {
                    $('#price'+id).val(unit_price*quantity);
                } else {
                    $('#price'+id).val(0);
                }
            } else {
                $('#price'+id).val(final_unit_price*quantity);
            }
            changeDisplaySeparator($('#price'+id))
        }
    }

    function clearRow(id) {
        if (id === 0) {
            $("#tbl tbody #clone").remove();
        }
        $("#tbl tbody #clone" + id).remove();
        const list_tr = $('#tbl tbody tr');
        $(list_tr).each( function (index_tr, item_tr) {
            $(item_tr).attr('id','clone'+(index_tr+1));
            $(item_tr).find('td.stt').html(index_tr+1);
            $(item_tr).find('td input[name="quantity[]"]').attr('id','quantity'+parseInt(index_tr+1));
            $(item_tr).find('td input[name="quantity[]"]').attr('onchange','changePrice('+parseInt(index_tr+1)+')');
            $(item_tr).find('td input[name="unit_price[]"]').attr('id','unit-price'+parseInt(index_tr+1));
            $(item_tr).find('td input[name="unit_price[]"]').attr('onchange','changePrice('+parseInt(index_tr+1)+')');
            $(item_tr).find('td input[name="final_unit_price[]"]').attr('id','final-unit-price'+ parseInt(index_tr+1));
            $(item_tr).find('td input[name="final_unit_price[]"]').attr('onchange','changePrice('+ parseInt(index_tr+1)+')');
            $(item_tr).find('td input[name="price[]"]').attr('id','price'+parseInt(index_tr+1));
            $(item_tr).find('td input[class="sub-row"]').attr('onclick', 'clearRow('+ parseInt(index_tr+1) +')');
        });
        count--;
    }

    function addClone(count) {
        var $clone = $("#tbl-clone tbody #clone").clone();

        $clone.attr({
            id: "clone" + count,
            style: ""
        });
        $("#tbl tbody").append($clone);
        $("#clone" + count + " td.stt").html(count);
        $("#clone" + count + " input").val('');
        $("#clone" + count + " input.price").val(0);
        $("#clone" + count + " input.unit-price").val(0);
        // $("#clone" + count + " input.final-unit-price").val(0);
        $("#clone" + count + " input.sub-row").val('-');
        $("#clone" + count + " td .quantity").attr('id', 'quantity'+count);
        $("#clone" + count + " td .quantity").attr('onchange', 'changePrice(' + count + ')');
        $("#clone" + count + " td .unit-price").attr('id', 'unit-price'+count);
        $("#clone" + count + " td .unit-price").attr('onchange', 'changePrice(' + count + ')');
        $("#clone" + count + " td .final-unit-price").attr('id', 'final-unit-price'+count);
        $("#clone" + count + " td .final-unit-price").attr('onchange', 'changePrice(' + count + ')');
        $("#clone" + count + " td .price").attr('id', 'price'+count);
        $("#clone" + count + " td .sub-row").attr('onclick', 'clearRow('+ count +')');
        SetDatePicker($('.dtpkTime,#dtpkTime'), {
            format: FOMAT_DATE.toLowerCase(),
            startDate: new Date(),
        });
        $("#clone" + count + " td .selectpicker").selectpicker();
    }

    //click save form
    $('#save').click(function () {
        var data = $('#equipment-offer-form').serializeArray();
        $('#equipment-offer-form :input[disabled], select[disabled]').each( function() {
            $name = $(this).attr('name');
            $value = $(this).val();
            if (typeof $name != 'undefined' && $name !== 'approved_user_id') {
                data.push({'name' : $name, 'value': $value});
            }
        });
        data = validateData(data);
        console.log(data)
        if (data.length <= 5) {
            showErrors('Vui lòng thêm đầy đủ dữ liệu trước khi lưu.');
            return ;
        }
        ajaxGetServerWithLoader("{{ route('admin.EquipmentOfferStore') }}", 'POST', data, function (data) {
            if (typeof data.error !== 'undefined') {
                showErrors(data.error);
                return ;
            }

            locationPage();
        }, function (data) {
            if (typeof data.responseJSON.error !== 'undefined') {
                showErrors(data.responseJSON.error);
                return ;
            }
        });
    });

    // Jquery draggable (cho phép di chuyển popup)
    $('.modal-dialog').draggable({
        handle: ".modal-header"
    });

    //Convert number in separator style to float
    function convertToFloat(stringNumber){
        if (typeof stringNumber !== 'undefined') {
            return parseFloat(stringNumber.replaceAll(',', ''));
        } else {
            return 0;
        }
    }

    //Display when load in class number-separator
    function displaySeparator(){
        var list = $('.number-separator');
        for (let n = 0; n < list.length; ++n) {
            if (/^[0-9.,]+$/.test(list[n].value)) {
                list[n].value = convertToFloat(list[n].value).toLocaleString('en');
            } else {
                list[n].value = list[n].value.substring(0, list[n].value.length - 1);
            }
        }
    }

    //Convert specific element in number-separator style
    function changeDisplaySeparator(e){
        if (/^[0-9.,]+$/.test($(e).val())) {
            $(e).val(convertToFloat($(e).val()).toLocaleString('en'));
        } else {
            $(e).val($(e).val().substring(0, $(e).val().length - 1));
        }
    }

    //validate data when serializeArray
    function validateData(data){
        $(data).each( (index, item) => {
            if ((item['name'] === 'unit_price[]' || item['name'] === 'final_unit_price[]' || item['name'] === 'price[]') && item['value'] != ''){
                item['value'] = convertToFloat(item['value']);
            }
        });
        return data;
    }

    $(document).ready(function () {
        displaySeparator()
    });
</script>

