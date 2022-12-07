<div class="modal draggable fade in detail-modal" id="absent-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog ui-draggable" style="width: 820px">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="">×</button>
                <h4 class="modal-title">@lang('admin.working-schedule.add-new')</h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <form class="form-horizontal detail-form" action="" method="POST" id="working-schedule-form">
                    @csrf
{{--                    <div class="row">--}}
{{--                        <div class="col-md-12 col-sm-12 col-xs-12">--}}
{{--                            @if(isset($working_schedule_info->id))--}}
{{--                                <input type="hidden" name="id" value="{{$working_schedule_info->id }}" id="id">--}}
{{--                                <input type="hidden" name="AssignIDOld" value="{{$working_schedule_info->AssignIDOld }}"--}}
{{--                                       id="AssignIDOld">--}}
{{--                            @else--}}
{{--                                <input type="hidden" id="realTime" value="false">--}}
{{--                            @endif--}}
{{--                            <input type="hidden"--}}
{{--                                   value="{{isset($working_schedule_info->UserID) ? $working_schedule_info->UserID : Auth::user()->id}}"--}}
{{--                                   id="absenceUID">--}}

{{--                            <!-- User Assign -->--}}
{{--                            <div class="form-group">--}}
{{--                                <label class="control-label col-sm-4"--}}
{{--                                       for="member">@lang('admin.working-schedule.assign-id')&nbsp;<sup--}}
{{--                                        class="text-red">*</sup>:</label>--}}
{{--                                <div class="col-sm-8">--}}
{{--                                    <select class="selectpicker show-tick show-menu-arrow sl-user select_project"--}}
{{--                                            data-actions-box="true" data-size="5" id='select-user' name="assign_id[]"--}}
{{--                                            data-live-search="true" data-live-search-placeholder="Search"--}}
{{--                                            data-width="100%" multiple>--}}
{{--                                        @if( isset($working_schedule_info->AssignID) && $working_schedule_info->AssignID == 0)--}}
{{--                                            <option value=0 name="sendMaillAll" selected>Tất cả nhân viên</option>--}}
{{--                                        @else--}}
{{--                                            <option value=0 name="sendMaillAll">Tất cả nhân viên</option>--}}
{{--                                        @endif--}}
{{--                                        {!!  GenHtmlOption($user_assign, 'id', 'FullName', isset($working_schedule_info->AssignID) ? explode(',', $working_schedule_info->AssignID) : null)!!}--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="form-group">--}}
{{--                                <label class="control-label col-sm-4"--}}
{{--                                       for="member">@lang('admin.working-schedule.listPosition')&nbsp;:</label>--}}
{{--                                <div class="col-sm-8">--}}
{{--                                    <select class="selectpicker show-tick show-menu-arrow sl-user select_project"--}}
{{--                                            data-actions-box="true" id="" data-size="5" name="listPosition[]"--}}
{{--                                            data-live-search="true" data-live-search-placeholder="Search"--}}
{{--                                            data-width="100%" multiple>--}}
{{--                                        {!!  GenHtmlOption($groupDataKey, 'DataValue', 'Name', isset($working_schedule_info->listPosition) ? explode(',', $working_schedule_info->listPosition) : "")!!}--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <!-- Ngày -->--}}
{{--                            <div class="form-group">--}}
{{--                                <label class="control-label col-sm-4" for="date">@lang('admin.working-schedule.date')--}}
{{--                                    &nbsp;<sup class="text-red">*</sup>:</label>--}}
{{--                                <div class="col-sm-8">--}}
{{--                                    <div class="input-group date" id="date">--}}
{{--                                        <input type="text" class="form-control" id="date-imput"--}}
{{--                                               placeholder="Ngày bắt đầu" name="date_work" autocomplete="off"--}}
{{--                                               value="{{ isset($working_schedule_info->Date) ? FomatDateDisplay($working_schedule_info->Date, FOMAT_DISPLAY_DATE_TIME) : null }}">--}}
{{--                                        <span class="input-group-addon">--}}
{{--                                        <span class="glyphicon glyphicon-calendar"></span>--}}
{{--                                    </span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="form-group">--}}
{{--                                <label class="control-label col-sm-4" for="date">@lang('admin.working-schedule.sdate')--}}
{{--                                    &nbsp;<sup class="text-red">*</sup>:</label>--}}
{{--                                <div class="col-sm-8">--}}
{{--                                    <div class="input-group date" id="sdate">--}}
{{--                                        <input type="text" class="form-control" id="date-imput"--}}
{{--                                               placeholder="Thời gian bắt đầu" name="stime" autocomplete="off"--}}
{{--                                               value="{{ isset($working_schedule_info->STime) ? FomatDateDisplay($working_schedule_info->STime, FOMAT_DISPLAY_TIME) : null }}">--}}
{{--                                        <span class="input-group-addon">--}}
{{--                                            <span class="glyphicon glyphicon-calendar"></span>--}}
{{--                                        </span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="form-group">--}}
{{--                                <label class="control-label col-sm-4" for="date">@lang('admin.working-schedule.edate')--}}
{{--                                    &nbsp;<sup class="text-red">*</sup>:</label>--}}
{{--                                <div class="col-sm-8">--}}
{{--                                    <div class="input-group date" id="edate">--}}
{{--                                        <input type="text" class="form-control" id="date-imput"--}}
{{--                                               placeholder="Thời gian kết thúc" name="etime" autocomplete="off"--}}
{{--                                               value="{{ isset($working_schedule_info->ETime) ? FomatDateDisplay($working_schedule_info->ETime, FOMAT_DISPLAY_TIME) : null }}">--}}
{{--                                        <span class="input-group-addon">--}}
{{--                                            <span class="glyphicon glyphicon-calendar"></span>--}}
{{--                                        </span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <!-- Nội dung -->--}}
{{--                            <div class="form-group">--}}
{{--                                <label class="control-label col-sm-4" for="desc">@lang('admin.contents') &nbsp;<sup--}}
{{--                                        class="text-red">*</sup>:</label>--}}
{{--                                <div class="col-sm-8">--}}
{{--                                    <textarea class="form-control" id="content" placeholder="@lang('admin.contents')"--}}
{{--                                              name="content"--}}
{{--                                              rows="3">{{isset($working_schedule_info->Content) ? $working_schedule_info->Content : '' }}</textarea>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="form-group" id="myForm">--}}
{{--                                <div class="col-sm-4"></div>--}}
{{--                                <div class="col-sm-8">--}}
{{--                                    <input type="radio" id="inAKB" name="gender"--}}
{{--                                           value="0" {{ isset($working_schedule_info->in_out) && !$working_schedule_info->in_out ? "checked" : "checked"}}>--}}
{{--                                    <label for="male">Trong AKB</label>--}}
{{--                                    <input type="radio" id="outAKB" name="gender"--}}
{{--                                           value="1" {{ isset($working_schedule_info->in_out) && $working_schedule_info->in_out ? "checked" : ""}}>--}}
{{--                                    <label for="female">Ngoài AKB</label><br>--}}

{{--                                </div>--}}
{{--                            </div>--}}
{{--                                --}}
{{--                            <!-- Dự án -->--}}
{{--                            <div class="form-group" id='FormprojectID'>--}}
{{--                                <label class="control-label col-sm-4" for="member">@lang('admin.daily.Project')--}}
{{--                                    &nbsp;<sup class="text-red">*</sup>:</label>--}}
{{--                                <div class="col-sm-8">--}}
{{--                                    <select class="selectpicker show-tick show-menu-arrow sl-user select_project" id=""--}}
{{--                                            data-size="5" name="ProjectID[]"--}}
{{--                                            data-live-search="true" data-live-search-placeholder="Search"--}}
{{--                                            data-width="100%" data-size="5">--}}
{{--                                        <option value="">@lang('admin.daily.chooseProject')</option>--}}
{{--                                        {!!--}}
{{--                                            GenHtmlOption($all_project, 'id', 'NameVi',isset($working_schedule_info->projectID) ? $working_schedule_info->projectID : '')--}}
{{--                                        !!}--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <!-- Địa chỉ -->--}}
{{--                            <div class="form-group" id="myFormhide">--}}
{{--                                <label class="control-label col-sm-4" for="desc">@lang('admin.partner.address')--}}
{{--                                    &nbsp;:</label>--}}
{{--                                <div class="col-sm-8">--}}
{{--                                    <textarea class="form-control" id="address"--}}
{{--                                              placeholder="@lang('admin.partner.address')" name="address"--}}
{{--                                              rows="3">{{isset($working_schedule_info->Address) ? $working_schedule_info->Address : '' }}</textarea>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <!-- phòng -->--}}
{{--                            <div class="form-group" id="myFormRooms">--}}
{{--                                <label class="control-label col-sm-4" for="member">@lang('admin.rooms')&nbsp;<sup--}}
{{--                                        class="text-red">*</sup>:</label>--}}
{{--                                <div class="col-sm-8">--}}
{{--                                    <select class="selectpicker show-tick show-menu-arrow sl-user select_project" id=""--}}
{{--                                            data-size="5" name="rooms"--}}
{{--                                            data-live-search="true" data-live-search-placeholder="Search"--}}
{{--                                            data-width="100%" data-size="5">--}}
{{--                                        <option value="">@lang('admin.rooms')</option>--}}
{{--                                        {!!--}}
{{--                                             GenHtmlOption($rooms, 'id', 'Name',isset($working_schedule_info->roomsID) ? $working_schedule_info->roomsID : '')--}}
{{--                                        !!}--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="form-group" id="myFormFlash">--}}
{{--                                <label class="control-label col-sm-4" for="member">Bật đèn trước:</label>--}}
{{--                                <div class="col-sm-8">--}}
{{--                                    <select class="selectpicker show-tick show-menu-arrow sl-user select_project" id=""--}}
{{--                                            data-size="5" name="minute"--}}
{{--                                            data-live-search="true" data-live-search-placeholder="Search"--}}
{{--                                            data-width="100%" data-size="5">--}}
{{--                                        <!-- <option value="">Phút</option> -->--}}
{{--                                        {!!--}}
{{--                                             GenHtmlOption($master_datas_working, 'DataDescription', 'Name',isset($working_schedule_info->minuteRoom) ? $working_schedule_info->minuteRoom : '')--}}
{{--                                        !!}--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}


{{--                            <!-- Ghi chú -->--}}
{{--                            <div class="form-group">--}}
{{--                                <label class="control-label col-sm-4" for="desc">@lang('admin.note') :</label>--}}
{{--                                <div class="col-sm-8 remark">--}}
{{--                                    <textarea class="form-control" id="note" name="note" rows="3"--}}
{{--                                              placeholder="@lang('admin.note')">{{isset($working_schedule_info->Note) ? $working_schedule_info->Note : '' }}</textarea>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <!-- nhân viên -->--}}
{{--                            <div class="form-group">--}}
{{--                                <label class="control-label col-sm-4" for="">@lang('admin.working-schedule.user-id')--}}
{{--                                    &nbsp;<sup class="text-red">*</sup>:</label>--}}
{{--                                <div class="col-sm-8 select-abreason">--}}
{{--                                    <input type="text" class="form-control"--}}
{{--                                           value="{{ isset($working_schedule_info->UserID) && $working_schedule_info->UserID != 0 ? App\User::find($working_schedule_info->UserID)->FullName : \Illuminate\Support\Facades\Auth::user()->FullName }}"--}}
{{--                                           disabled>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="container">--}}
{{--                        <div class="row flex-row col-sm-9 col-xs-12">--}}
{{--                            <div class="col-md-12 col-xs-12">--}}
<!--                                Hidden input-->
                                @if(isset($working_schedule_info->id))
                                    <input type="hidden" name="id" value="{{$working_schedule_info->id }}" id="id">
                                    <input type="hidden" name="AssignIDOld" value="{{$working_schedule_info->AssignIDOld }}"
                                           id="AssignIDOld">
                                @else
                                    <input type="hidden" id="realTime" value="false">
                                @endif
                                <input type="hidden"
                                       value="{{isset($working_schedule_info->UserID) ? $working_schedule_info->UserID : \Illuminate\Support\Facades\Auth::id()}}"
                                       id="absenceUID">

<!--                                Three layer-->
                            @component("admin.component.three-layer")
                                @slot("label")
                                    Chức vụ/thành viên
                                @endslot
                                @slot("class1")
                                    sl-user select_project
                                @endslot
                                @slot("data_name1")
                                    listPosition[]
                                @endslot
                                @slot("title1")
                                    Chức vụ
                                @endslot
                                @slot("genHtmlOption1")
                                    {!!  GenHtmlOption($groupDataKey, 'DataValue', 'Name', '', '', 'data-users', 'PositionUser')!!}
                                @endslot

                                @slot("class2")
                                    sl-user select_project select-assign
                                @endslot
                                @slot("data_id2")
                                    select-user
                                @endslot
                                @slot("data_name2")
                                    assign_id[]
                                @endslot
                                @slot("title2")
                                    Thành viên
                                @endslot
                                @slot("genHtmlOption2")
                                    {!!  GenHtmlOption($user_assign, 'id', 'FullName', isset($working_schedule_info->AssignID) ? explode(',', $working_schedule_info->AssignID) : null)!!}
                                @endslot
                                @slot("tbody_style")
                                    max-height: 20rem !important; display: block;overflow: auto;
                                @endslot
                            @endcomponent

<!--                                Ngày-->
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="date">@lang('admin.working-schedule.date')
                                        &nbsp;<sup class="text-red">*</sup>:</label>
                                    <div class="col-sm-9">
                                        <div class="input-group date" id="date">
                                            <input type="text" class="form-control" id="date-imput"
                                                   placeholder="Ngày bắt đầu" name="date_work" autocomplete="off"
                                                   value="{{ isset($working_schedule_info->Date) ? FomatDateDisplay($working_schedule_info->Date, FOMAT_DISPLAY_DATE_TIME) : null }}">
                                            <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                        </div>
                                    </div>
                                </div>
<!--                                Giờ bắt đầu-->
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="date">@lang('admin.working-schedule.sdate')
                                        &nbsp;<sup class="text-red">*</sup>:</label>
                                    <div class="col-sm-9">
                                        <div class="input-group date" id="sdate">
                                            <input type="text" class="form-control" id="date-imput"
                                                   placeholder="Thời gian bắt đầu" name="stime" autocomplete="off"
                                                   value="{{ isset($working_schedule_info->STime) ? FomatDateDisplay($working_schedule_info->STime, FOMAT_DISPLAY_TIME) : null }}">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

<!--                                Thời gian kết thúc-->
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="date">@lang('admin.working-schedule.edate')
                                        &nbsp;<sup class="text-red">*</sup>:</label>
                                    <div class="col-sm-9">
                                        <div class="input-group date" id="edate">
                                            <input type="text" class="form-control" id="date-imput"
                                                   placeholder="Thời gian kết thúc" name="etime" autocomplete="off"
                                                   value="{{ isset($working_schedule_info->ETime) ? FomatDateDisplay($working_schedule_info->ETime, FOMAT_DISPLAY_TIME) : null }}">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

<!--                                Nội dung-->
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="desc">@lang('admin.contents') &nbsp;<sup
                                            class="text-red">*</sup>:</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" id="content" placeholder="@lang('admin.contents')"
                                                  name="content"
                                                  rows="3">{{isset($working_schedule_info->Content) ? $working_schedule_info->Content : '' }}</textarea>
                                    </div>
                                </div>

<!--                                Checkbox-->
                                <div class="form-group" id="myForm">
                                    <label class="control-label col-sm-3">Công tác&nbsp;<sup
                                            class="text-red">*</sup>:</label>
                                    <div class="col-sm-9">
                                        <label class="radio-inline">
                                            <input type="radio" id="inAKB" name="gender"
                                                   value="0" checked>
                                            Trong AKB
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" id="outAKB" name="gender"
                                               value="1" {{ isset($working_schedule_info->in_out) && $working_schedule_info->in_out ? "checked" : ""}}>
                                            Ngoài AKB
                                        </label>
                                    </div>
                                </div>

<!--                                Dự án-->
                                <div class=form-group id="FormprojectID">
                                    <label class="control-label col-sm-3" for="member">@lang('admin.daily.Project')
                                        &nbsp;<sup class="text-red">*</sup>:</label>
                                    <div class="col-sm-9">
                                        <select class="selectpicker show-tick show-menu-arrow sl-user select_project" id=""
                                                data-size="5" name="ProjectID[]"
                                                data-live-search="true" data-live-search-placeholder="Search"
                                                data-width="100%" data-size="5">
                                            <option value="">@lang('admin.daily.chooseProject')</option>
                                            {!!
                                                GenHtmlOption($all_project, 'id', 'NameVi',isset($working_schedule_info->projectID) ? $working_schedule_info->projectID : '')
                                            !!}
                                        </select>
                                    </div>
                                </div>

<!--                                Địa chỉ-->
                                <div class="form-group" style="margin-bottom: 1.5rem" id="myFormhide">
                                    <label class="control-label col-sm-3" for="desc">@lang('admin.partner.address')
                                        &nbsp;:</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" id="address"
                                                  placeholder="@lang('admin.partner.address')" name="address"
                                                  rows="3">{{isset($working_schedule_info->Address) ? $working_schedule_info->Address : '' }}</textarea>
                                    </div>
                                </div>

<!--                                Phòng-->
                                <div class="form-group" id="myFormRooms">
                                    <label class="control-label col-sm-3" for="member">@lang('admin.rooms')&nbsp;<sup
                                            class="text-red">*</sup>:</label>
                                    <div class="col-sm-9">
                                        <select class="selectpicker show-tick show-menu-arrow sl-user select_project" id=""
                                                data-size="5" name="rooms"
                                                data-live-search="true" data-live-search-placeholder="Search"
                                                data-width="100%" data-size="5">
                                            <option value="">@lang('admin.rooms')</option>
                                            {!!
                                                 GenHtmlOption($rooms, 'id', 'Name',isset($working_schedule_info->roomsID) ? $working_schedule_info->roomsID : '')
                                            !!}
                                        </select>
                                    </div>
                                </div>
<!--                                Mở đèn-->
                                <div class="form-group" id="myFormFlash">
                                    <label class="control-label col-sm-3" for="member">Bật đèn trước:</label>
                                    <div class="col-sm-9">
                                        <select class="selectpicker show-tick show-menu-arrow sl-user select_project" id=""
                                                data-size="5" name="minute"
                                                data-live-search="true" data-live-search-placeholder="Search"
                                                data-width="100%" data-size="5">
                                            {!!
                                                 GenHtmlOption($master_datas_working, 'DataDescription', 'Name',isset($working_schedule_info->minuteRoom) ? $working_schedule_info->minuteRoom : '')
                                            !!}
                                        </select>
                                    </div>
                                </div>

<!--                                Ghi chú-->
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="desc">@lang('admin.note') :</label>
                                    <div class="col-sm-9 remark">
                                        <textarea class="form-control" id="note" name="note" rows="3"
                                                  placeholder="@lang('admin.note')">{{isset($working_schedule_info->Note) ? $working_schedule_info->Note : '' }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="">@lang('admin.working-schedule.user-id')
                                        &nbsp;<sup class="text-red">*</sup>:</label>
                                    <div class="col-sm-9 select-abreason">
                                        <input type="text" class="form-control"
                                               value="{{ isset($working_schedule_info->UserID) && $working_schedule_info->UserID != 0 ? App\User::find($working_schedule_info->UserID)->FullName : \Illuminate\Support\Facades\Auth::user()->FullName }}" disabled>
                                    </div>
                                </div>
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    </div>--}}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                        id="cancel">@lang('admin.btnCancel')</button>
                <button type="submit" class="btn btn-primary btn-sm" id="save">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async>

    $(function () {
        $(".selectpicker").selectpicker({
            noneSelectedText: ''
        });
        SetDateTimePicker($('#date'), {
            format: 'DD/MM/YYYY',
        });
        SetDateTimePicker($('#sdate'), {
            format: 'HH:mm',
        });
        SetDateTimePicker($('#edate'), {
            format: 'HH:mm',
        });
    });

    $("#myFormhide").hide();
    $("#FormprojectID").hide();


    var selectPosition = $("select[name='listPosition\[\]']");
    var selectAssign = $("select[name='assign_id\[\]']");
    var tbody_three_layer = $("#three-layer").find("tbody");

    var {members, clean} = updateMembers(selectPosition, selectAssign, "data-users");
    var members_update = Object.assign({}, clean);

    @if(isset($working_schedule_info))
        $(selectAssign).val().map(function (item) {
                let exist = false;
                for (let key in members){
                    if (members[key].hasOwnProperty(item)){
                        members_update[key][item] = members[key][item];
                        exist = true;
                        if (members_update["xxx"].hasOwnProperty(item))
                            delete members_update["xxx"][item];
                    } else {
                        if (!exist)
                            members_update["xxx"][item] = $(selectAssign).find(`option[value=${item}]`).text();
                    }
                }
            });
    @endif

    // Onchange select position
    $(selectPosition).change(function (event) {
        event.preventDefault();
        let arrUser = [];
        let arrUserNew = [];
        let keyOld = [];
        let keyNew = $(this).val();

        for (let key in members_update){
            let a = Object.keys(members_update[key]);
            if (a.length !== 0){
                a.map(function (i) {
                    if (!arrUser.includes(i))
                        arrUser.push(i);
                })
            }
        }

        keyNew.map(function (key) {
            let a = Object.keys(members[key]);
            a.map(function (i) {
                if (!arrUserNew.includes(i))
                    arrUserNew.push(i);
            })
        })

        for (let key in members_update){
            if (Object.keys(members_update[key]).length !== 0
                && Object.keys(members_update[key]).length === Object.keys(members[key]).length){
                keyOld.push(key)
            }
        }
        // if (keyOld.length > keyNew.length) {
        //     let c = keyOld.filter(x => !keyNew.includes(x));
        //     let userDelete = [];
        //     c.map(function (key){
        //         Object.keys(members[key]).map(function (id) {
        //             if (!userDelete.includes(id)){
        //                 userDelete.push(id);
        //             }
        //         })
        //     });
        //     for (let key in members_update){
        //         userDelete.map(function (id) {
        //             if (members_update[key].hasOwnProperty(id)){
        //                 delete members_update[key][id];
        //             }
        //         })
        //     }
        // } else {
            // for (let key in members){
            //     members_update[key] = {};
            // }
            for (let k in members){
                let arr = Object.keys(members[k]);
                let same = getArraysIntersection(arr, arrUserNew);
                same.map(function (i) {
                    members_update[k][i] = members[k][i];
                })
            }
        // }
        updateComponent(null, "update");
    })

    // Onchange select assign
    $(selectAssign).change(function (event) {
        event.preventDefault();
        for (let key in members){
            members_update[key] = {};
        }
        for (let key in members) {
            let exist = false;
            $(this).val().map(function (id_user) {
                if (members[key].hasOwnProperty(id_user)){
                    members_update[key][id_user] = members[key][id_user];
                    exist = true;
                    if (members_update["xxx"].hasOwnProperty(id_user)){
                        delete members_update["xxx"][id_user];
                    }
                } else {
                    if (!exist)
                        members_update["xxx"][id_user] = $(selectAssign).find(`option[value=${id_user}]`).text();
                }
            });
        }
        updateComponent(null, "update", 1);
    })

    // Update component table, select assign, select position
    function updateComponent(item = null, action = "update") {
        if (action === "delete") {
            let closest_tr = $(item).closest("tr");
            let id_user = $(closest_tr).attr("data-count");
            for (let key in members_update) {
                if (members_update[key].hasOwnProperty(id_user)) {
                    delete members_update[key][id_user];
                }
            }
        }
        // console.log(members_update);
        let unique_member = [];
        let unique_position = [];

        // Update table
        $(tbody_three_layer).empty();
        for (let key in members_update) {
            for (let id_user in members_update[key]) {
                if (!unique_member.includes(id_user)) {
                    unique_member.push(id_user);
                }
            }
        }

        // Draw table
        unique_member.map(function (id) {
            let name = $(selectAssign).find(`option[value="${id}"]`).text();
            $(tbody_three_layer).append(`
                <tr data-count="${id}">
                    <td class="user center-important" style="width: 100%; text-align: left !important;">${name}</td>
                    <td class="delDevice center-important">
                        <button type="button"
                                class="btn btn-outline-danger btn-xs btn-del-member"
                                onclick="updateComponent(this, 'delete')">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            `);
        });

        // Update select assign
        $(selectAssign).val(unique_member);
        let count_member = unique_member.length;
        if (count_member !== 0){
            $('#countMember').html(`(${count_member})`);
            $('#memberGroup').show();
        } else {
            $('#countMember').html(``);
            $('#memberGroup').hide();
        }

        // Update select position
        for (let key in members_update) {
            if (key === "xxx" && members_update["xxx"].hasOwnProperty(0)){
                unique_position = Object.keys(members_update);
            } else {
                let l_members = Object.keys(members[key]).length;
                let l_members_update = Object.keys(members_update[key]).length;
                if (l_members_update >= l_members && l_members_update !== 0 && l_members !== 0) {
                    unique_position.push(key);
                }
            }
        }
        // $(selectPosition).val(unique_position);
        $(selectPosition).val([]);
        $(selectAssign).selectpicker("refresh");
        $(selectPosition).selectpicker("refresh");
    }



    $(document).ready(function () {
        updateComponent();
        var inAKB = $("#inAKB");
        var outAKB = $("#outAKB");
        if ($(outAKB).is(":checked")) {
            $("#myFormhide").show();
            $("#FormprojectID").show();
            $("#myFormRooms").hide();
            $("#myFormFlash").hide();
        }
        if ($(inAKB).is(":checked")) {
            $("#myFormRooms").show();
            $("#myFormFlash").show();
            $("#myFormhide").hide();
            $("#FormprojectID").hide();
        }

        $("#inAKB").click(function () {
            $("#myFormhide").hide();
            $("#myFormRooms").show();
            $("#myFormFlash").show();
            $("#FormprojectID").hide();
        });
        $("#outAKB").click(function () {
            $("#myFormhide").show();
            $("#myFormRooms").hide();
            $("#myFormFlash").hide();
            $("#FormprojectID").show();
        });
    });

    //click save form
    $('#save').click(async function () {
        var data = $('#working-schedule-form').serializeArray();
        // add lich cong tac
        ajaxGetServerWithLoader("{{ route('admin.WorkingScheduleStore') }}", 'POST', data, function (data) {
            if (typeof data.errors !== 'undefined') {
                showErrors(data.errors);
                return;
            }
            locationPage();
        }, function (data) {
            if (typeof data.responseJSON.errors !== 'undefined') {
                showErrors(data.responseJSON.errors);
                return;
            }
        });
    });
    // Jquery draggable (cho phép di chuyển popup)
    $('.modal-dialog').draggable({
        handle: ".modal-header"
    });
</script>
