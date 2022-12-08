<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('js/dataTables.checkboxes.min.js') }}"></script>
<script src="{{ asset('js/dataTables.responsive.js') }}"></script>
<div class="modal draggable fade in" id="user-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('admin.user.add_new_user')</h4>
            </div>
            <div class="modal-body">
                @if(isset($userInfo->username))

                    @if(Auth::user()->role_group == 1)
                        <ul class="nav nav-tabs margin-bottom">
                            <li><a data-toggle="tab" href="#tab1">@lang('admin.user-detail.infoUser')</a></li>
                            <li class="active"><a data-toggle="tab" href="#tab2">@lang('admin.user-detail.role')</a>
                            </li>
                            <li><a data-toggle="tab" href="#tab3">@lang('admin.user-detail.changePassword')</a></li>
                        </ul>
                    @endif
                    @if(Auth::user()->role_group == 2)
                        <ul class="nav nav-tabs margin-bottom">
                            <li class="active"><a data-toggle="tab" href="#tab1">@lang('admin.user-detail.infoUser')</a>
                            </li>
                            <li><a data-toggle="tab" href="#tab3">@lang('admin.user-detail.changePassword')</a></li>
                        </ul>
                    @endif
                    <div class="tab-content">
                        <div id="tab1" class="tab-pane fade in @if(Auth::user()->role_group != 1)active @endif">
                            @endif
                            <div class="save-errors"></div>
                            <form class="form-horizontal" action="" method="POST" id="user-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="username">@lang('admin.user.username') <sup
                                                    class="text-red">*</sup>:</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" id="username" name="username"
                                                       maxlength="20" placeholder="@lang('admin.user.username')"
                                                       value="{{ isset($userInfo->username) ? $userInfo->username : null }}"
                                                       required>
                                            </div>
                                        </div>
                                        @if(!isset($userInfo->username))
                                            <div class="form-group password" style="">
                                                <label class="control-label col-sm-5"
                                                       for="pwd">@lang('admin.user.password') <sup
                                                        class="text-red">*</sup>:</label>
                                                <div class="col-sm-7">
                                                    <input type="password" class="form-control" id="pwd" name="password"
                                                           maxlength="30" placeholder="@lang('admin.user.password')"
                                                           autocomplete="new-password">
                                                </div>
                                            </div>
                                            <div class="form-group confirm-password" style="">
                                                <label class="control-label col-sm-5"
                                                       for="cfpwd">@lang('admin.user.password_confirmation') <sup
                                                        class="text-red">*</sup>:</label>
                                                <div class="col-sm-7">
                                                    <input type="password" class="form-control" id="cfpwd"
                                                           name="password_confirmation" maxlength="30"
                                                           placeholder="@lang('admin.user.password_confirmation')"
                                                           autocomplete="new-password">
                                                </div>
                                            </div>
                                        @else
                                            <input type="hidden" name="id" value="{{ $userInfo->id }}">
                                        @endif
                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="fullName">@lang('admin.user.full_name') &nbsp;<sup
                                                    class="text-red">*</sup>:</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" id="fullname" name="FullName"
                                                       placeholder="@lang('admin.user.full_name')"
                                                       value="{{ isset($userInfo->FullName) ? $userInfo->FullName : null }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="birthday">@lang('admin.user.birthday') <sup
                                                    class="text-red">*</sup>:</label>
                                            <div class="col-sm-7">
                                                <div class="input-group date" id="birthday">
                                                    <input type="text" class="form-control dtpkTime" name="Birthday"
                                                           placeholder="@lang('admin.user.birthday')" autocomplete="off"
                                                           value="{{ isset($userInfo->Birthday) ? FomatDateDisplay($userInfo->Birthday, FOMAT_DISPLAY_DAY) : null }}">
                                                    <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="gender">@lang('admin.user.gender')&nbsp;<sup
                                                    class="text-red">*</sup>:</label>
                                            <div class="col-sm-7">
                                                <label class="radio-inline">
                                                    <input type="radio" name="Gender"
                                                           value="0" {{ isset($userInfo->Gender) && !$userInfo->Gender ? "checked" : "checked"}}>@lang('admin.user.male')
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="Gender"
                                                           value="1" {{ isset($userInfo->Gender) && $userInfo->Gender ? "checked" : ""}}>@lang('admin.user.female')
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group" style="">
                                            <label class="control-label col-sm-5"
                                                   for="married">@lang('admin.user.marital_status')&nbsp;<sup
                                                    class="text-red">*</sup>:</label>
                                            <div class="col-sm-7">
                                                <label class="radio-inline">
                                                    <input type="radio" name="MaritalStt"
                                                           value="0" {{ isset($userInfo->MaritalStt) && !$userInfo->MaritalStt ? "checked" : "checked"}}>@lang('admin.user.single')
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="MaritalStt"
                                                           value="1" {{ isset($userInfo->MaritalStt) && $userInfo->MaritalStt ? "checked" : ""}}>@lang('admin.user.married')
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="tel">@lang('admin.user.telephone')&nbsp;<sup
                                                    class="text-red">*</sup>:</label>
                                            <div class="col-sm-7">
                                                <input type="tel" class="form-control" id="tel" name="Tel"
                                                       placeholder="@lang('admin.user.telephone')"
                                                       maxlength="15"
                                                       value="{{ isset($userInfo->Tel) ? $userInfo->Tel : null }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5" for="email">@lang('admin.user.email')
                                                :</label>
                                            <div class="col-sm-7">
                                                <input type="email" class="form-control" id="email" name="email"
                                                       placeholder="@lang('admin.user.email')"
                                                       value="{{ isset($userInfo->email) ? $userInfo->email : null }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="email-user">@lang('admin.user.email-user') :</label>
                                            <div class="col-sm-7">
                                                <input type="email" class="form-control" id="email-user"
                                                       name="email_user" placeholder="@lang('admin.user.email-user')"
                                                       value="{{ isset($userInfo->email_user) ? $userInfo->email_user : null }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="perAddress">@lang('admin.user.location'):</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" id="perAddress"
                                                       name="PerAddress" maxlength="100"
                                                       placeholder="@lang('admin.user.location')"
                                                       value="{{ isset($userInfo->PerAddress) ? $userInfo->PerAddress : null }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="curAddress">@lang('admin.user.current_address'):</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" id="curAddress"
                                                       name="CurAddress" maxlength="100"
                                                       placeholder="@lang('admin.user.current_address')"
                                                       value="{{ isset($userInfo->CurAddress) ? $userInfo->CurAddress : null }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="relativeName">@lang('admin.user.relative_person'):</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" id="relativeName"
                                                       name="RelativeName" maxlength="100"
                                                       placeholder="@lang('admin.user.relative_person')"
                                                       value="{{ isset($userInfo->RelativeName) ? $userInfo->RelativeName : null }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="relationship">@lang('admin.user.relationship'):</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" id="relationship"
                                                       name="Relationship" maxlength="100"
                                                       placeholder="@lang('admin.user.relationship')"
                                                       value="{{ isset($userInfo->Relationship) ? $userInfo->Relationship : null }}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="telRelative">@lang('admin.user.relative_person_telephone')
                                                :</label>
                                            <div class="col-sm-7">
                                                <input type="tel" class="form-control" id="telRelative"
                                                       name="TelRelative" maxlength="100"
                                                       placeholder="@lang('admin.user.relative_person_telephone')"
                                                       value="{{ isset($userInfo->TelRelative) ? $userInfo->TelRelative : null }}">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">

                                        <div class="form-group">
                                            <label class="control-label col-sm-5" for="group">@lang('admin.user.group')
                                                &nbsp;<sup class="text-red">*</sup>:</label>
                                            <div class="col-sm-7">
                                                <select class="form-control selectpicker show-tick show-menu-arrow"
                                                        name="role_group"
                                                        data-live-search="true"
                                                        data-live-search-placeholder="@lang('admin.meeting.search')"
                                                        tabindex="-98">
                                                    <option
                                                        value="2" {{ isset($userInfo->role_group) && $userInfo->role_group == 2 ? 'selected' : '' }}>
                                                        Admin
                                                    </option>
                                                    <option
                                                        value="3" {{ isset($userInfo->role_group) && $userInfo->role_group == 3 ? 'selected' : '' }}>
                                                        User
                                                    </option>
                                                    <option
                                                        value="4" {{ isset($userInfo->role_group) && $userInfo->role_group == 4 ? 'selected' : '' }}>
                                                        Guest
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5" for="IDFM">@lang('admin.user.IDFM')
                                                &nbsp;<sup class="text-red">*</sup>:</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" id="IDFM" name="IDFM"
                                                       placeholder="@lang('admin.user.IDFM')"
                                                       maxlength="3"
                                                       value="{{ isset($userInfo->IDFM) ? $userInfo->IDFM : null }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-5" for="CardNo2">Mã thẻ từ</label>
                                            <div class="col-sm-7">
                                                <input type="text" class="form-control" id="CardNo2" name="CardNo2"
                                                       placeholder="Mã thẻ từ"
                                                       value="{{ isset($userInfo->CardNo2) ? $userInfo->CardNo2 : null }}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-5" for="RoomId">@lang('admin.user.room')
                                                &nbsp;<sup class="text-red">*</sup>:</label>
                                            <div class="bootstrap-select col-sm-7">
                                                <select class="form-control selectpicker show-tick show-menu-arrow"
                                                        placeholder="@lang('admin.user.room')" name="RoomId"
                                                        id="meetingRoomchoose"
                                                        data-live-search="true"
                                                        data-live-search-placeholder="@lang('admin.meeting.search')"
                                                        data-size="6" tabindex="-98" required>
                                                    <option value="">[@lang('admin.meeting.please_select')]</option>
                                                    {!! GenHtmlOption($rooms, 'id', 'Name', isset($userInfo->RoomId) ? $userInfo->RoomId : '') !!}
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="sTimeOfDay">@lang('admin.user.work_time')&nbsp;<sup
                                                    class="text-red">*</sup>:</label>
                                            <div class="col-sm-7">
                                                <div class="input-group sTimeOfDay" id="sTimeOfDay">
                                                    <input type="text" class="form-control" id="sTimeOfDay-input"
                                                           name="STimeOfDay"
                                                           value="{{ isset($userInfo->STimeOfDay) ? $userInfo->STimeOfDay : config('settings.start_time') }}">
                                                    <span class="input-group-addon">
                                                <span class="fa fa-clock-o"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="SBreakOfDay">@lang('admin.user.SBreakOfDay')&nbsp;<sup
                                                    class="text-red">*</sup>:</label>
                                            <div class="col-sm-7">
                                                <div class="input-group SBreakOfDay" id="SBreakOfDay">
                                                    <input type="text" class="form-control" id="SBreakOfDay-input"
                                                           name="SBreakOfDay"
                                                           value="{{ isset($userInfo->SBreakOfDay) ? $userInfo->SBreakOfDay : config('settings.SBreakOfDay') }}">
                                                    <span class="input-group-addon">
                                                <span class="fa fa-clock-o"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="EBreakOfDay">@lang('admin.user.EBreakOfDay')&nbsp;<sup
                                                    class="text-red">*</sup>:</label>
                                            <div class="col-sm-7">
                                                <div class="input-group EBreakOfDay" id="EBreakOfDay">
                                                    <input type="text" class="form-control" id="EBreakOfDay-input"
                                                           name="EBreakOfDay"
                                                           value="{{ isset($userInfo->EBreakOfDay) ? $userInfo->EBreakOfDay : config('settings.EBreakOfDay') }}">
                                                    <span class="input-group-addon">
                                                <span class="fa fa-clock-o"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="eTimeOfDay">@lang('admin.user.end_time_of_day'):</label>
                                            <div class="col-sm-7">
                                                <div class="input-group eTimeOfDay" id="eTimeOfDay">
                                                    <input type="text" class="form-control" id="eTimeOfDay-input"
                                                           name="ETimeOfDay"
                                                           value="{{ isset($userInfo->id) ? ( isset($userInfo->ETimeOfDay) ? $userInfo->ETimeOfDay : '' ) : config('settings.end_time')}}">
                                                    <span class="input-group-addon">
                                                <span class="fa fa-clock-o"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="sDate">@lang('admin.user.join_date'):</label>
                                            <div class="col-sm-7">
                                                <div class="input-group date" id="sDate">
                                                    <input type="text" class="form-control dtpkTime" id="sDate-input"
                                                           name="SDate" placeholder="@lang('admin.user.join_date')"
                                                           autocomplete="off"
                                                           value="{{ isset($userInfo->SDate) ? FomatDateDisplay($userInfo->SDate, FOMAT_DISPLAY_DAY) : null }}">
                                                    <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="expirationdate">@lang('admin.user.expiration_date'):</label>
                                            <div class="col-sm-7">
                                                <div class="input-group date" id="sDate">
                                                    <input type="text" class="form-control dtpkTime"
                                                           id="expirationdate-input" name="expirationdate"
                                                           placeholder="@lang('admin.user.expiration_date')"
                                                           autocomplete="off"
                                                           value="{{ isset($userInfo->expirationdate) ? FomatDateDisplay($userInfo->expirationdate, FOMAT_DISPLAY_DAY) : null }}">
                                                    <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="officalDate">@lang('admin.user.official_date'):</label>
                                            <div class="col-sm-7">
                                                <div class="input-group date" id="officalDate">
                                                    <input type="text" class="form-control dtpkTime"
                                                           id="officalDate_input" name="OfficialDate"
                                                           placeholder="@lang('admin.user.official_date')"
                                                           value="{{ isset($userInfo->OfficialDate) ? FomatDateDisplay($userInfo->OfficialDate, FOMAT_DISPLAY_DAY) : null }}">
                                                    <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5"
                                                   for="daysOff">@lang('admin.user.out_date'):</label>
                                            <div class="col-sm-7">
                                                <div class="input-group date" id="eDate">
                                                    <input type="text" class="form-control dtpkTime" id="daysOff"
                                                           name="DaysOff" placeholder="@lang('admin.user.out_date')"
                                                           value="{{ isset($userInfo->DaysOff) ? \Carbon\Carbon::parse($userInfo->DaysOff)->format('d/m/Y') : null }}">
                                                    <span class="input-group-addon"><span
                                                            class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5" for="note">@lang('admin.user.note')
                                                :</label>
                                            <div class="col-sm-7">
                                            <textarea class="form-control" rows="1" id="note" maxlength="100"
                                                      name="Note" placeholder="@lang('admin.user.note')"
                                                      value="{{ isset($userInfo->Note) ? $userInfo->Note : null }}">{{ isset($userInfo->Note) ? $userInfo->Note : null }}</textarea>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-5" for="group">@lang('admin.user.status')
                                                &nbsp;<sup class="text-red">*</sup>:</label>
                                            <div class="col-sm-7">
                                                <label class="radio-inline">
                                                    <input type="radio" name="Active"
                                                           value="1" checked>@lang('admin.user.active')
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="Active"
                                                           value="0" {{ isset($userInfo->Active) && !$userInfo->Active ? "checked" : ""}}>@lang('admin.user.deactive')
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-5" for="group">@lang('admin.user.workAt')&nbsp;<sup class="text-red">*</sup>:</label>
                                            <div class="col-sm-7">
                                                <label class="radio-inline">
                                                    <input type="radio" name="workAt" value="1" checked>@lang('admin.user.AcWorkAt')
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="workAt" value="0" {{ isset($userInfo->workAt) && !$userInfo->workAt ? "checked" : ""}}>@lang('admin.user.deWorkAt')
                                                </label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group text-center"></div>
                            </form>
                        </div>
                        @if(isset($userInfo->username))
                            <div id="tab2" class="tab-pane fade in @if(Auth::user()->role_group == 1)active @endif">
                                <div class="row">
                                    <div class="table-responsive  col-md-12" id="default">
                                        <table width="100%"
                                               class="table tbl-role table-striped table-bordered table-hover table-user-groups"
                                               id="q1">
                                            <thead class="thead-default">
                                            <tr>
                                                <th class="width3 no-sort">
                                                    <input type="checkbox" class="checkAll">
                                                </th>

                                                <th class="width3">
                                                    @lang('admin.daily.Screen_Name')
                                                </th>
                                                <th class="width3">
                                                    @lang('admin.role_power')
                                                </th>

                                            </tr>
                                            </thead>
                                            <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>

                                            </tfoot>
                                            <tbody id="role-list">

                                            @foreach($listRole as $item)
                                                <tr class="even gradeC">
                                                    <td class="text-center">
                                                        <input type="checkbox"
                                                               {{ $item->checked ? 'checked' : '' }} data-id="{{ $item->ScreenDetailAlias }}"
                                                               class="role-item">
                                                    </td>
                                                    <td class="text-center">{{ $item->ScreenName }}</td>
                                                    <td class="text-center">{{ $item->ScreenDetailName }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>

                                        </table>

                                    </div>
                                </div>
                            </div>
                            <div id="tab3" class="tab-pane fade in">
                                <div class="row">
                                    <div class="col-xs-12 col-md-10 col-md-push-1 col-lg-7 col-lg-push-2 well">
                                        <form class="form-horizontal" id="form-change-password">
                                            <div class="form-inputs">
                                                <input type="text" class="form-control hidden" name="id"
                                                       value="{{isset($userInfo->id) ? $userInfo->id : ''}}">
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3 col-md-4"
                                                           for="new_password">@lang('admin.user-detail.newPassword')
                                                        &nbsp;<sup class="text-red">*</sup>:</label>
                                                    <div class="col-sm-9 col-md-7">
                                                        <input class="form-control" id="new_password"
                                                               name="new_password" type="password" autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-sm-3 col-md-4"
                                                           for="password_confirmation">@lang('admin.user-detail.confirmPassword')
                                                        &nbsp;<sup class="text-red">*</sup>:</label>
                                                    <div class="col-sm-9 col-md-7">
                                                        <input class="form-control" id="confirm_password"
                                                               name="new_password_confirmation" type="password"
                                                               autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                    </div>
                @endif
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                            id="cancel">@lang('admin.btnCancel')</button>
                    <button type="submit" class="btn btn-primary btn-sm"
                            id="save-user-form">@lang('admin.btnSave')</button>
                    <button class="btn btn-primary"
                            id="password_modal_save">@lang('admin.user-detail.changePassword')</button>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="userId" value="{{ isset($userInfo->id) ? $userInfo->id : '' }}">
<style>
    .pagination {
        display: inline-block;
        padding-left: 0;
        margin: 0px;
        border-radius: 4px;
        float: right;
    }

    .pagination > li > a, .pagination > li > span {

        padding: 4px 8px;
        margin-left: -1px;
        line-height: 1.42857143;
        color: #337ab7;
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #ddd;
    }

    tfoot th {
        text-align: center;
    }

    #q1_filter {
        float: right;
    }

    #q1_length {
        float: left;
        margin-right: 20px;
    }

    .btn-default {
        height: 35px;
    }
</style>
<script type="text/javascript" async>
    $('#user-info .modal-footer #password_modal_save').hide();
    $('#user-info .modal-body .nav.nav-tabs li a').on('click', function (e) {
        e.preventDefault();
        if ($.trim($(this).attr('href')) == '#tab3') {
            $('#user-info .modal-footer #save-user-form').hide();
            $('#user-info .modal-footer #password_modal_save').show();
        } else {
            $('#user-info .modal-footer #save-user-form').show();
            $('#user-info .modal-footer #password_modal_save').hide();
        }
    });

    $('#save-user-form').click(function () {
        ajaxGetServerWithLoader('{{route('admin.UserStore')}}', 'POST', $('#user-form').serializeArray(), function (data) {
            locationPage();
        }, function (error) {
            const err= error.responseJSON.errors;
            if (typeof err === undefined){
                showErrors("Có lỗi đã xảy ra.");
            } else {
                showErrors(err);
            }
        });

    });
    $(function () {
        $('#sDate, #birthday,#eDate, #officalDate, .dtpkTime').datetimepicker({
            format: FOMAT_DATE,
        });

        $('#sTimeOfDay,#eTimeOfDay').datetimepicker({
            allowInputToggle: true,
            format: 'HH:mm',
            stepping: 15
        });
        $('#SBreakOfDay,#EBreakOfDay').datetimepicker({
            allowInputToggle: true,
            format: 'HH:mm',
            stepping: 15
        });

        $('#password_modal_save').click(function () {
            ajaxServer("{{ route('admin.changePassword') }}", 'POST', $('#form-change-password').serializeArray(), function (data) {
                if (typeof data.errors !== 'undefined') {
                    showErrors(data.errors);
                    return;
                } else {
                    showConfirmSuccess(data.success, function () {
                        $('#user-info').modal('toggle');
                    });
                }
            });
        });

        $(".selectpicker").selectpicker();
        //phan quyền
        var table = $('.tbl-role').DataTable({
            "ordering": true,
            "info": true,
            "columnDefs": [
                {"targets": 'no-sort', "orderable": false}
            ],
            "paging": true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Tìm kiếm",
            },
            initComplete: function () {
                var intCount = 0;
                $('#q1_wrapper').find('.row').eq(0).find('.col-sm-6').append('<div class="filter_"></div>');
                this.api().columns().every(function () {
                    if (intCount >= 1) {
                        var column = this;
                        var select = $('<select class="form-control input-sm"><option value="">' + (intCount == 1 ? 'Chọn màn hình' : 'Chọn quyền') + '</option></select>')
                            .appendTo($('.filter_').eq(intCount - 1).empty())
                            .on('change', function () {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );

                                column
                                    .search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                            });

                        column.data().unique().sort().each(function (d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>')
                        });
                    }

                    intCount++;
                });
            }
        });
    });
    // Jquery draggable
    $('.modal-dialog').draggable({
        handle: ".modal-header"
    });

    function showConfirmSuccess(data, func) {
        $.confirm({
            title: 'Thành công',
            icon: 'fa fa-check',
            type: 'blue',
            content: data + '',
            buttons: {
                ok: {
                    text: 'Ok',
                    btnClass: 'btn width5',
                    keys: ['enter'],
                    action: function () {
                        if (IsFunction(func)) {
                            func();
                        }
                    }
                }
            }
        });
    };
</script>

