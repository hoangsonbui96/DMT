<form id="meeting-search-form" class="form-inline" method="GET">
    <div class="form-group select-user">
        <div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-user">
            <select class="selectpicker show-tick show-menu-arrow" id="select-nameRegister" name="RegisterID" data-live-search="true" data-size="5" data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
                <option value="">@lang('admin.meeting.register')</option>
                    {!!
                        GenHtmlOption($users, 'id', 'FullName', isset($request['RegisterID']) ? $request['RegisterID'] : '')
                    !!}
            </select>
        </div>
    </div>
    <div class="form-group select-user">
        <div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-user">
            <select class="selectpicker show-tick show-menu-arrow" id="select-MeetingHostID" name="MeetingHostID" data-live-search="true" data-size="5" data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
                <option value="">@lang('admin.meeting.meeting_host')</option>
                {!!
                    GenHtmlOption($users, 'id', 'FullName', isset($request['MeetingHostID']) ? $request['MeetingHostID'] : '')
                !!}
            </select>
        </div>
    </div>
    <div class="form-group select-user">
        <div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-user">
            <select class="selectpicker show-tick show-menu-arrow" id="select-Participant" name="Participant" data-live-search="true" data-live-search-placeholder="Search" data-size="6"  tabindex="-98">
                <option value="">@lang('admin.meeting.meeting_users_2')</option>
                {!!
                    GenHtmlOption($users, 'id', 'FullName', isset($request['Participant']) ? $request['Participant'] : '')
                !!}
            </select>
        </div>
    </div>
    <div class="form-group select-user">
        <div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-user">
            <select class="selectpicker show-tick show-menu-arrow" id="select-roommeeting" name="RoomID" data-live-search="true" data-live-search-placeholder="Search" data-size="6" tabindex="-98">
                <option value="">@lang('admin.meeting.meeting_room')</option>
                {!!
                    GenHtmlOption($rooms, 'id', 'Name', isset($request['RoomID']) ? $request['RoomID'] : '')
                !!}
            </select>
        </div>
    </div>

    <div class="form-group">
        <div class="input-group search date" id="sDate">
            <input type="text" class="form-control dtpicker" id="date-timemeeting-input" autocomplete="off" placeholder="@lang('admin.startDate')" name="MeetingDate[]" value="{{ isset($request['MeetingDate'] ) ? $request['MeetingDate'][0] : '' }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group search date" id="eDate">
            <input type="text" class="form-control dtpicker" id="date-ftimemeeting-input" autocomplete="off" placeholder="@lang('admin.endDate')" name="MeetingDate[]" value="{{ isset($request['MeetingDate'] ) ? $request['MeetingDate'][1] : '' }}">
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="form-group select-user">
        <div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-user">
            <select class="selectpicker show-tick show-menu-arrow" id="select-actionmeeting" name="Status" data-live-search="true" data-live-search-placeholder="Search" data-size="6"  tabindex="-98">
                <option value="">@lang('admin.room.active')</option>
                <option value="1"  {{ isset($request['Status'] ) && $request['Status'] == 1 ? 'selected' : '' }}>Đang diễn ra</option>
                <option value="3"  {{ isset($request['Status'] ) && $request['Status'] == 3? 'selected' : '' }}>Chưa diễn ra</option>
                <option value="2" {{ isset($request['Status'] ) && $request['Status'] == 2 ? 'selected' : '' }}>Đã kết thúc</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group search">
            <input type="search" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
        </div>
    </div>
    <div class="form-group">
        <button type="button" class="btn btn-primary btn-search" id="btn-search-meeting">@lang('admin.btnSearch')</button>
        @can('action',$add)
        <button type="button" class="action-col btn btn-primary btn-detail" id="add-new-meeting-btn">@lang('admin.meeting.add_new_meeting')</button>
        @endcan
    </div>
</form>

<script language="javascript" async>
    var check = {{count($errors) ? $errors->any() : 0}};
    if(check != 0){
        setTimeout(function(){ showErrors('{{$errors->first()}}'); }, 200);
    }
    $('.btn-search').click(function () {
        $('#meeting-search-form').submit();
    });
    SetDatePicker($('#sDate,#eDate'));
</script>
