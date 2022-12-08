@php
    $canAdd = false;
    $canExport = false;
@endphp

@can('action', $addR)
    @php
        $canAdd = true;
    @endphp
@endcan
@can('action', $export)
    @php
        $canExport = true;
    @endphp
@endcan
<style>
    #meeting-search-form .form-group {
        margin-top: 5px;
    } 
</style>
<form id="meeting-search-form" class="form-inline" method="GET">
    <div class="form-group select-user">
        <div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-user">
            <select class="selectpicker show-tick show-menu-arrow" id="select-nameRegister" name="type_id" data-live-search="true" data-size="5"
                    data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
                <option value="">@lang('admin.equipment.type_name')</option>
                {!!
                    GenHtmlOption($eqTypes, 'type_id', 'type_name', isset($request['type_id']) ? $request['type_id'] : '')
                !!}
            </select>
        </div>
    </div>
    <div class="form-group select-user">
        <div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-user">
            <select class="selectpicker show-tick show-menu-arrow" id="select-MeetingHostID" name="status_id" data-live-search="true" data-size="5"
                    data-live-search-placeholder="Search"  data-actions-box="true" tabindex="-98">
                <option value="">@lang('admin.equipment.status')</option>
                {!!
                    GenHtmlOption($eqStatus, 'id', 'Name', isset($request['status_id']) ? $request['status_id'] : '')
                !!}
            </select>
        </div>
    </div>
    <div class="form-group select-user">
        <div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-user">
            <select class="selectpicker show-tick show-menu-arrow" id="select-Participant" name="created_user" data-live-search="true"
                    data-live-search-placeholder="Search" data-size="6" tabindex="-98">
                <option value="">@lang('admin.equipment.created_user')</option>
                {!!
                    GenHtmlOption($created_users, 'id', 'FullName', isset($request['created_user']) ? $request['created_user'] : '')
                !!}
            </select>
        </div>
    </div>
    @can('admin', $menu)
    <div class="form-group select-user">
        <div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-user">
            <select class="selectpicker show-tick show-menu-arrow" id="select-nameRegister" name="user_owner" data-live-search="true"
                    data-size="5" data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
                <option value="">@lang('admin.equipment.receive_owner')</option>
                <option value="0"{{ isset($request['user_owner'] ) && $request['user_owner'] == 0 ? 'selected' : '' }}>@lang('admin.equipment.store')</option>
                {!!
                    GenHtmlOption($owners, 'id', 'FullName', isset($request['user_owner']) ? $request['user_owner'] : '')
                !!}
            </select>
        </div>
    </div>
    @endcan
    <div class="form-group">
        <div class="input-group search date" id="sDate">
            <input type="text" class="form-control" placeholder="Ngày bắt đầu" name="DealDate[]" value="{{ isset($request['DealDate'] ) ? $request['DealDate'][0] : '' }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group search date" id="eDate">
            <input type="text" class="form-control" placeholder="Ngày kết thúc" name="DealDate[]" value="{{ isset($request['DealDate'] ) ? $request['DealDate'][1] : '' }}">
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
        <button type="button" class="btn btn-primary btn-search" id="btn-search-meeting">@lang('admin.btnSearch')</button>
        @if($canAdd)
            <button type="button" class="btn btn-primary btn-detail" id="add-new-meeting-btn">@lang('admin.equipment.handover')</button>
        @endif
    </div>
    @if($canExport)
        <div class="form-group">
            <button class="btn btn-success" id="btn-export">@lang('admin.export-excel')</button>
        </div>
    @endif
</form>

<script type="text/javascript" async>
    var check = {{count($errors) ? $errors->any() : 0}};
    if(check != 0){
        setTimeout(function(){ showErrors('{{$errors->first()}}'); }, 200);
    }
    $('.btn-search').click(function () {
        $('#meeting-search-form').submit();
    });
    $('#btn-export').click(function (e) {
        e.preventDefault();
        var type_id = $('select[name=type_id]').val();
        var status_id = $('select[name=status_id]').val();
        var created_user = $('select[name=created_user]').val();
        var user_owner = $('select[name=user_owner]').val();
        var DealDate = [];
        if($('#sDate input').val()!=''||$('#eDate input').val()!=''){
            DealDate.push($('#sDate input').val()==''?null:$('#sDate input').val());
            DealDate.push($('#eDate input').val()==''?null:$('#eDate input').val());
        }
        var search = $('input[name=search]').val();
        $('.loadajax').show();
        ajaxServer('{{ route('export.equipmentHistories') }}?type_id='+type_id+'&status_id='+status_id+'&created_user='+created_user+'&user_owner='+user_owner+'&DealDate='+DealDate+'&search='+search, 'GET',null, function (data) {
            if (typeof data.errors !== 'undefined'){
                $('.loadajax').hide();
                showErrors(data.errors[0]);
            }else{
                $('.loadajax').hide();
                window.location.href = '{{ route('export.equipmentHistories') }}?type_id='+type_id+'&status_id='+status_id+'&created_user='+created_user+'&user_owner='+user_owner+'&DealDate='+DealDate+'&search='+search;
            }
        })
    });
    SetDatePicker($('#sDate,#eDate'));
    $(".selectpicker").selectpicker();
</script>
