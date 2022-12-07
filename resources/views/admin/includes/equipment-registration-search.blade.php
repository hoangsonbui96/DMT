<form class="form-inline" id="meeting-search-form" action="" method="">

    <div class="form-group">
        <div class="input-group search">
            <input type="text" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
        </div>
    </div>

    <div class="form-group">
        <select class="selectpicker show-tick show-menu-arrow" id="select-nameRegister" name="user_id" data-live-search="true" data-size="5" data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
            <option value="">@lang('admin.equipment.register_user')</option>
            {!!
                GenHtmlOption($registerUsers, 'id', 'FullName', isset($request['user_id']) ? $request['user_id'] : '')
            !!}
        </select>
    </div>

    <div class="form-group">
        <select class="selectpicker show-tick show-menu-arrow" id="select-MeetingHostID" name="form_status" data-size="5" data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
            <option value="">@lang('admin.equipment.status')</option>
            <option value="0" {{ isset($request['form_status'] ) && $request['form_status'] == 0 ? 'selected' : '' }}>@lang('admin.approved')</option>
            <option value="1" {{ isset($request['form_status'] ) && $request['form_status'] == 1 ? 'selected' : '' }}>@lang('admin.not_approved')</option>
        </select>
    </div>

    <div class="form-group">
        <button type="button" class="btn btn-primary btn-search" id="btn-search-meeting">@lang('admin.btnSearch')</button>
        @can('action',$add)
        <button type="button" class="btn btn-primary btn-detail" id="add-new-meeting-btn">@lang('admin.equipment.add_registration_form')</button>
        @endcan
    </div>
</form>
<script type="text/javascript" async>
    $(function () {
        $(".selectpicker").selectpicker();
        $('.btn-search').click(function () {
            $('#meeting-search-form').submit();
        });
    })
</script>
