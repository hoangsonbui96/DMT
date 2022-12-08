@php
    $canAdd = false;
    $canexport = false;
    $canexportQR = false;
@endphp

@can('action', $add)
    @php
        $canAdd = true;
    @endphp
@endcan
@can('action', $export)
    @php
        $canexport = true;
    @endphp
@endcan
@can('action', $exportQR)
    @php
        $canexportQR = true;
    @endphp
@endcan
<style>
    #meeting-search-form .form-group {
        margin-top: 5px;
    }
</style>
<form id="meeting-search-form" class="form-inline" method="GET">
    <div class="form-group">
        <div class="btn-group bootstrap-select show-tick show-menu-arrow" >
            <select class="selectpicker show-tick show-menu-arrow" id="select-nameRegister" name="type_id" data-live-search="true" data-size="5" data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
                <option value="">@lang('admin.equipment.type_name')</option>
                {!!
                    GenHtmlOption($eqTypes, 'type_id', 'type_name', isset($request['type_id']) ? $request['type_id'] : '')
                !!}
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="btn-group bootstrap-select show-tick show-menu-arrow" >
            <select class="selectpicker show-tick show-menu-arrow" id="select-MeetingHostID" name="status_id" data-live-search="true" data-size="5" data-live-search-placeholder="Search"  data-actions-box="true" tabindex="-98">
                <option value="">@lang('admin.equipment.status')</option>
                {!!
                    GenHtmlOption($eqStatus, 'id', 'Name', isset($request['status_id']) ? $request['status_id'] : '')
                !!}
            </select>
        </div>
    </div>
    @can('admin', $menu)
    <div class="form-group">
        <div class="btn-group bootstrap-select show-tick show-menu-arrow" >
            <select class="selectpicker show-tick show-menu-arrow" id="select-Participant" name="room_id" data-live-search="true" data-live-search-placeholder="Search" data-size="6"  tabindex="-98">
                <option value="">@lang('admin.equipment.room_id')</option>
                {!!
                    GenHtmlOption($rooms, 'id', 'Name', isset($request['room_id']) ? $request['room_id'] : '')
                !!}
            </select>
        </div>
    </div>
    @endcan
    <div class="form-group">
        <div class="btn-group bootstrap-select show-tick show-menu-arrow" >
            <select class="selectpicker show-tick show-menu-arrow" id="select-roommeeting" name="warranty" data-size="6"  tabindex="-98">
                <option value="">@lang('admin.equipment.period_date')</option>
                <option value="1" {{ isset($request['warranty'] ) && $request['warranty'] == 1 ? 'selected' : '' }}>@lang('admin.equipment.warranty')</option>
                <option value="2" {{ isset($request['warranty'] ) && $request['warranty'] == 2 ? 'selected' : '' }}>@lang('admin.equipment.warranty_expired')</option>
            </select>
        </div>
    </div>
    @can('admin', $menu)
    <div class="form-group">
        <div class="btn-group bootstrap-select show-tick show-menu-arrow" >
            <select class="selectpicker show-tick show-menu-arrow" id="select-user-owner" name="user_owner" data-live-search="true" data-size="5" data-live-search-placeholder="Search"  data-actions-box="true" tabindex="-98">
                <option value="">@lang('admin.equipment.user_owner')</option>
                <option value="0" {{ isset($request['user_owner'] ) && $request['user_owner'] == 0 ? 'selected' : '' }}>@lang('admin.equipment.store')</option>
                {!!
                    GenHtmlOption($owners, 'id', 'FullName', isset($request['user_owner']) ? $request['user_owner'] : '')
                !!}
            </select>
        </div>
    </div>
    @endcan
    <div class="form-group">
        <div class="input-group search date" id="sDate">
            <input type="text" class="form-control" placeholder="@lang('admin.startDate')" name="DealDate[]" value="{{ isset($request['DealDate'] ) ? $request['DealDate'][0] : '' }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group search date" id="eDate">
            <input type="text" class="form-control" placeholder="@lang('admin.endDate')" name="DealDate[]" value="{{ isset($request['DealDate'] ) ? $request['DealDate'][1] : '' }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group search">
            <input type="text" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
        </div>
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-primary btn-search" id="btn-search-meeting" >@lang('admin.btnSearch')</button>
        @if ($canAdd)
        <button type="button" class=" action-col btn btn-primary btn-detail" id="add-new-meeting-btn">@lang('admin.equipment.add_store')</button>
        @endif
        @if ($canexport)
        <button type="button" class="action-col btn btn-success" id="btn-export">@lang('admin.export-excel')</button>
        @endif
        @if ($canexportQR)
        <button type="button" class="action-col btn btn-success" id="btn-exportQR">@lang('admin.Export_QR')</button>
        @endif
    </div>
</form>

<script type="text/javascript" async>
    var check = {{count($errors) ? $errors->any() : 0}};
    if(check != 0){
        setTimeout(function(){ showErrors('{{$errors->first()}}'); }, 200);
    }
    $('.btn-search').click(function () {
        $('#meeting-search-form').submit();
    });
    SetDatePicker($('#sDate,#eDate'));
    $(".selectpicker").selectpicker();

    $('#btn-export').click(function (e) {
        e.preventDefault();
        var type_id = $('#select-nameRegister').val();
        var status_id = $('#select-MeetingHostID').val();
        var room_id = $('#select-Participant').val();
        var warranty = $('#select-roommeeting').val();
        var user_owner = $('#select-user-owner').val();
        var sDate = $('#sDate input').val();
        var eDate = $('#eDate input').val();
        var search = $('input[name=search]').val();
        $('.loadajax').show();
        ajaxServer('{{ route('export.equipment') }}?type_id='+type_id+'&status_id='+status_id+'&room_id='+room_id+'&warranty='+warranty+'&user_owner='+user_owner+'&sDate='+sDate+'&eDate='+eDate+'&search='+search, 'GET',null, function (data) {
            if (typeof data.errors !== 'undefined'){
                $('.loadajax').hide();
                showErrors(data.errors[0]);
            }else{
                $('.loadajax').hide();
                window.location.href = '{{ route('export.equipment') }}?type_id='+type_id+'&status_id='+status_id+'&room_id='+room_id+'&warranty='+warranty+'&user_owner='+user_owner+'&sDate='+sDate+'&eDate='+eDate+'&search='+search;
            }
        })

    });

    $('#btn-exportQR').click(function (e) {
        e.preventDefault();
        var type_id = $('#select-nameRegister').val();
        var status_id = $('#select-MeetingHostID').val();
        var room_id = $('#select-Participant').val();
        var warranty = $('#select-roommeeting').val();
        var user_owner = $('#select-user-owner').val();
        var sDate = $('#sDate input').val();
        var eDate = $('#eDate input').val();
        var search = $('input[name=search]').val();
        $('.loadajax').show();
        ajaxServer('{{ route('exportQR.equipment') }}?type_id='+type_id+'&status_id='+status_id+'&room_id='+room_id+'&warranty='+warranty+'&user_owner='+user_owner+'&sDate='+sDate+'&eDate='+eDate+'&search='+search, 'GET',null, function (data) {
            if (typeof data.errors !== 'undefined'){
                $('.loadajax').hide();
                showErrors(data.errors[0]);
            }else{
                $('.loadajax').hide();
                window.location.href = '{{ route('exportQR.equipment') }}?type_id='+type_id+'&status_id='+status_id+'&room_id='+room_id+'&warranty='+warranty+'&user_owner='+user_owner+'&sDate='+sDate+'&eDate='+eDate+'&search='+search;
            }
        })

    });

</script>
