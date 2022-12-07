<style>
    .select-w100{
        width: 100% !important;
    }
</style>
<div class="modal draggable fade in open-modal" id="monthly-modal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg ui-draggable ">
        @php
            $listEditor = array();
        @endphp
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">Thêm báo cáo quản lý dự án [{{ \auth()->user()->FullName }}]</h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal" method="POST" id="daily-form">
                    @csrf
                    <input type="hidden" class="form-control hidden " id="mReport_id"
                           value="{{ isset($meetingInfo->id) ? $meetingInfo->id : null }}">
                    <div class="form-group">
                        <label for="meetingName" class="control-label col-sm-3">Tiêu đề<sup
                                class="text-red">*</sup>:</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" maxlength="100"
                                   value="{{ isset($meetingInfo->MeetingName) ? $meetingInfo->MeetingName : null }}"
                                   id="meetingName" name="MeetingName" placeholder="Tiêu đề">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="">Người nhận&nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="col-sm-9 select-abreason">
                            <select class="selectpicker show-tick show-menu-arrow select-w100" id="select-user"
                                    name="ChairID" data-live-search="true" data-size="5"
                                    data-live-search-placeholder="Search" data-actions-box="true"
                                    tabindex="-98" title="Người nhận báo cáo">
                                {!! GenHtmlOption($selectUser, 'id', 'FullName',isset($meetingInfo->ChairID) ? $meetingInfo->ChairID : auth()->id()) !!}
                            </select>
                        </div>
                    </div>
                    @component("admin.component.three-layer")
                        @slot("label")
                            Chức vụ/thành viên
                        @endslot
                        @slot("class1")
                            select_assign
                        @endslot
                        @slot("data_name1")
                            listPosition[]
                        @endslot
                        @slot("title1")
                            Chức vụ
                        @endslot
                        @slot("genHtmlOption1")
                            {!! GenHtmlOption($groupDataKey, 'DataValue', 'Name', '', '', 'data-users', 'PositionUser') !!}
                        @endslot

                        @slot("data_name2")
                            AssignID[]
                        @endslot
                        @slot("title2")
                            Thành viên
                        @endslot
                        @slot("genHtmlOption2")
                            {!! GenHtmlOption($user_assign, 'id', 'FullName', isset($AssignID) ? explode(',', $AssignID) : null) !!}
                        @endslot
{{--                        @slot("columnsTable")--}}
{{--                            <th></th>--}}
{{--                            <th></th>--}}
{{--                        @endslot--}}

                        @slot("tbody_style")
                            max-height: 20rem !important; display: block;overflow: auto;
                        @endslot
                    @endcomponent

                    <div class="form-group">
                        <label class="control-label col-sm-3" for="selectProject">Dự án:</label>
                        <div class="col-sm-9 select-abreason">
                            <select class="selectpicker show-tick show-menu-arrow sl-user select_project"
                                    id="selectProject" name="ProjectID" data-live-search="true"
                                    data-live-search-placeholder="Search" data-width="100%" data-size="5">
                                <option value="">@lang('admin.daily.chooseProject')</option>
                                {!! GenHtmlOption(isset($meetingInfo->id) ? $all_project : $projects, 'id', 'NameVi', isset($meetingInfo->ProjectID) ? $meetingInfo->ProjectID : '') !!}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="">Thời gian&nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="col-sm-9 select-abreason">
                            <div class="form-row" style="display: flex; justify-content: space-between">
                                <div class="input-group date" id="sdate" style="padding: 0; width: 48%">
                                    <input type="text" class="form-control datepicker"
                                           placeholder="Thời gian bắt đầu" name="MeetingTimeFrom"
                                           autocomplete="off"
                                           value="{{ isset($meetingInfo->MeetingTimeFrom) ? FomatDateDisplay($meetingInfo->MeetingTimeFrom, FOMAT_DISPLAY_DAY) : null }}">
                                    <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                                <div class="input-group date" id="edate" style="padding: 0; width: 48%">
                                    <input type="text" class="form-control datepicker"
                                           placeholder="Thời gian kết thúc" name="MeetingTimeTo"
                                           autocomplete="off"
                                           value="{{ isset($meetingInfo->MeetingTimeTo) ? FomatDateDisplay($meetingInfo->MeetingTimeTo, FOMAT_DISPLAY_DAY) : null }}">
                                    <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="timeEnd">Hạn nộp:</label>
                        <div class="col-sm-9 select-abreason">
                            <div class="input-group" id="time-end">
                                <input type="text" class="form-control"
                                       id="timeEnd"
                                       placeholder="Hạn nộp báo cáo" name="TimeEnd"
                                       autocomplete="off"
                                       value="{{ isset($meetingInfo->TimeEnd) ? FomatDateDisplay($meetingInfo->TimeEnd, FOMAT_DISPLAY_DATE_TIME) : null }}">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="">Kiểu báo cáo:</label>
                        <div class="col-sm-9">
                            <label for="radio1" class="radio-inline">
                                <input id="radio1" data-toggle="toggle" type="radio" name="isPrivate" value="1" checked>
                                Riêng tư
                            </label>

                            <label for="radio2" class="radio-inline">
                                <input id="radio2" data-toggle="toggle" type="radio" name="isPrivate" value="0"
                                    {{(isset($meetingInfo->Secret) && $meetingInfo->Secret == 0) ? "checked" : ""}}>
                                Công khai
                            </label>
                        </div>
                    </div>

{{--                    <div class="container">--}}
{{--                        <div class="row flex-row col-sm-9 col-xs-12">--}}
{{--                            <div class="col-md-12 col-xs-12">--}}
{{--                            <!--Tiêu đề-->--}}
{{--                                <div class="col-md-12" style="padding-bottom: 1.5rem">--}}
{{--                                    <label class="control-label col-md-3" for="" style="text-align: left">Tiêu đề<sup--}}
{{--                                            class="text-red">*</sup>:</label>--}}
{{--                                    <div class="col-md-9">--}}
{{--                                        <input class="form-control" type="text" maxlength="100"--}}
{{--                                               value="{{ isset($meetingInfo->MeetingName) ? $meetingInfo->MeetingName : null }}"--}}
{{--                                               id="" name="MeetingName" placeholder="Tiêu đề...">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            <!--Người nhận báo cáo-->--}}
{{--                                <div class="col-md-12" style="padding-bottom: 1.5rem">--}}
{{--                                    <label class="control-label col-md-3" for="" style="text-align: left">Người nhận báo cáo&nbsp;<sup class="text-red">*</sup>:</label>--}}
{{--                                    <div class="col-md-9 select-abreason">--}}
{{--                                        <select class="selectpicker show-tick show-menu-arrow select-w100" id="select-user"--}}
{{--                                                name="ChairID" data-live-search="true" data-size="5"--}}
{{--                                                data-live-search-placeholder="Search" data-actions-box="true"--}}
{{--                                                tabindex="-98">--}}
{{--                                            <option value="">Người nhận báo cáo</option>--}}
{{--                                            {!! GenHtmlOption($selectUser, 'id', 'FullName',isset($meetingInfo->ChairID) ? $meetingInfo->ChairID : null) !!}--}}
{{--                                        </select>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            <!--Three layer-->--}}
{{--                                <div class="col-md-12" style="padding-bottom: 1.5rem">--}}
{{--                                    @component("admin.component.three-layer")--}}
{{--                                        @slot("label")--}}
{{--                                            Chức vụ/người tham gia--}}
{{--                                        @endslot--}}

{{--                                        @slot("class1")--}}
{{--                                            select_assign--}}
{{--                                        @endslot--}}
{{--                                        @slot("data_name1")--}}
{{--                                            listPosition[]--}}
{{--                                        @endslot--}}
{{--                                        @slot("title1")--}}
{{--                                            Chức vụ--}}
{{--                                        @endslot--}}
{{--                                        @slot("genHtmlOption1")--}}
{{--                                            {!! GenHtmlOption($groupDataKey, 'DataValue', 'Name', '', '', 'data-users', 'PositionUser') !!}--}}
{{--                                        @endslot--}}

{{--                                        @slot("data_name2")--}}
{{--                                            AssignID[]--}}
{{--                                        @endslot--}}
{{--                                        @slot("title2")--}}
{{--                                            Người tham gia--}}
{{--                                        @endslot--}}
{{--                                        @slot("genHtmlOption2")--}}
{{--                                            {!! GenHtmlOption($user_assign, 'id', 'FullName', isset($AssignID) ? explode(',', $AssignID) : null) !!}--}}
{{--                                        @endslot--}}

{{--                                        @slot("columnsTable")--}}
{{--                                            <th>Người tham gia <span id="countMember"></span></th>--}}
{{--                                            <th></th>--}}
{{--                                        @endslot--}}
{{--                                        @slot("tbody_style")--}}
{{--                                            max-height: 20rem !important; display: block;overflow: auto;--}}
{{--                                        @endslot--}}
{{--                                    @endcomponent--}}
{{--                                </div>--}}
{{--                            <!--Tên dự án -->--}}
{{--                                <div class="col-md-12" style="padding-bottom: 1.5rem">--}}
{{--                                    <label class="control-label col-md-3" for="" style="text-align: left">Dự án:</label>--}}
{{--                                    <div class="col-md-9 select-abreason">--}}
{{--                                        <select class="selectpicker show-tick show-menu-arrow sl-user select_project"--}}
{{--                                                id="" name="ProjectID" data-live-search="true"--}}
{{--                                                data-live-search-placeholder="Search" data-width="100%" data-size="5">--}}
{{--                                            <option value="">@lang('admin.daily.chooseProject')</option>--}}
{{--                                            @if(isset($meetingInfo->id))--}}
{{--                                                {!!--}}
{{--                                                GenHtmlOption($all_project, 'id', 'NameVi', isset($meetingInfo->ProjectID) ? $meetingInfo->ProjectID : '')--}}
{{--                                                !!}--}}
{{--                                            @else--}}
{{--                                                {!!--}}
{{--                                                GenHtmlOption($projects, 'id', 'NameVi', isset($meetingInfo->ProjectID) ? $meetingInfo->ProjectID : '')--}}
{{--                                                !!}--}}
{{--                                            @endif--}}

{{--                                        </select>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            <!--Thời gian báo cáo-->--}}
{{--                                <div class="col-md-12" style="padding-bottom: 1.5rem">--}}
{{--                                    <label class="control-label col-sm-3" for="" style="text-align: left">Thời gian báo cáo:</label>--}}
{{--                                    <div class="col-sm-9 select-abreason" style="display: flex; justify-content: space-between;">--}}
{{--                                        <div class="input-group date" id="sdate" style="padding: 0; width: 48%">--}}
{{--                                            <input type="text" class="form-control datepicker" id="date-imput"--}}
{{--                                                   placeholder="Thời gian bắt đầu" name="MeetingTimeFrom"--}}
{{--                                                   autocomplete="off"--}}
{{--                                                   value="{{ isset($meetingInfo->MeetingTimeFrom) ? FomatDateDisplay($meetingInfo->MeetingTimeFrom, FOMAT_DISPLAY_DAY) : null }}">--}}
{{--                                            <span class="input-group-addon">--}}
{{--                                                <span class="glyphicon glyphicon-calendar"></span>--}}
{{--                                            </span>--}}
{{--                                        </div>--}}
{{--                                        <div class="input-group date" id="edate" style="padding: 0; width: 48%">--}}
{{--                                            <input type="text" class="form-control datepicker" id="date-imput"--}}
{{--                                                   placeholder="Thời gian kết thúc" name="MeetingTimeTo"--}}
{{--                                                   autocomplete="off"--}}
{{--                                                   value="{{ isset($meetingInfo->MeetingTimeTo) ? FomatDateDisplay($meetingInfo->MeetingTimeTo, FOMAT_DISPLAY_DAY) : null }}">--}}
{{--                                            <span class="input-group-addon">--}}
{{--                                                <span class="glyphicon glyphicon-calendar"></span>--}}
{{--                                            </span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            <!--Hạn nộp báo cáo-->--}}
{{--                                <div class="col-md-12" style="padding-bottom: 1.5rem">--}}
{{--                                    <label class="control-label col-sm-3" for="" style="text-align: left">Hạn nộp báo cáo:</label>--}}
{{--                                    <div class="col-sm-9 select-abreason">--}}
{{--                                        <div class="input-group" id="time-end" style="padding: 0;">--}}
{{--                                            <input type="text" class="form-control"--}}
{{--                                                   id="timeEnd"--}}
{{--                                                   placeholder="Hạn nộp báo cáo" name="TimeEnd"--}}
{{--                                                   autocomplete="off"--}}
{{--                                                   value="{{ isset($meetingInfo->TimeEnd) ? FomatDateDisplay($meetingInfo->TimeEnd, FOMAT_DISPLAY_DATE_TIME) : null }}">--}}
{{--                                            <span class="input-group-addon">--}}
{{--                                                <span class="glyphicon glyphicon-calendar"></span>--}}
{{--                                            </span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            <!--Kiểu báo cáo-->--}}
{{--                                <div class="col-md-12">--}}
{{--                                    <label class="col-md-3" for="" style="text-align: left">Kiểu báo cáo:</label>--}}
{{--                                    <div class="col-md-9">--}}
{{--                                        <div class="checkbox">--}}
{{--                                            <label style="padding: 0">--}}
{{--                                                <input data-toggle="toggle" type="radio" name="isPrivate" value="0"--}}
{{--                                                    checked>--}}
{{--                                                <span id="text_input">Công khai</span>--}}
{{--                                            </label>--}}
{{--                                            <label>--}}
{{--                                                <input data-toggle="toggle" type="radio" name="isPrivate" value="1"--}}
{{--                                                    {{(isset($meetingInfo->Secret) && $meetingInfo->Secret == 1) ? "checked" : ""}}>--}}
{{--                                                <span id="text_input">Riêng tư</span>--}}
{{--                                            </label>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel"
                        data-control="">@lang('admin.btnCancel')</button>
                <button type="button" class="btn btn-primary save-form" id="saveReport"
                        data-control="">@lang('admin.btnSave')</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript" async>
    var selectPosition = $("select[name='listPosition\[\]']");
    var selectAssign = $("select[name='AssignID\[\]']");
    var tbody_three_layer = $("#three-layer").find("tbody");

    var {members, clean} = updateMembers(selectPosition, selectAssign, "data-users");
    var members_update = Object.assign({}, clean);

    // $("#is_private").change(function () {
    //     $("#text_input").text($(this).is(":checked") ? "Riêng tư" : "Công khai");
    // })

    if ($("#mReport_id").val() !== ""){
        $(selectAssign).val().map(function (item) {
            let exist = false;
            for (let key in members){
                if (members[key].hasOwnProperty(item)){
                    members_update[key][item] = members[key][item];
                    exist = true;
                    if (members_update["xxx"].hasOwnProperty(item)){
                        delete members_update["xxx"][item];
                    }
                } else {
                    if (!exist)
                        members_update["xxx"][item] = $(selectAssign).find(`option[value=${item}]`).text();
                }
            }
        });
    }

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
        //        Object.keys(members[key]).map(function (id) {
        //            if (!userDelete.includes(id)){
        //                userDelete.push(id);
        //            }
        //        })
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
        // for (let key in members_update) {
        //     let l_members = Object.keys(members[key]).length;
        //     let l_members_update = Object.keys(members_update[key]).length;
        //     if (l_members_update >= l_members && l_members_update !== 0 && l_members !== 0) {
        //         unique_position.push(key);
        //     }
        // }
        $(selectPosition).val([]);

        $(selectAssign).selectpicker("refresh");
        $(selectPosition).selectpicker("refresh");
    }

    // Save report
    $('#saveReport').click(function (e) {
        e.preventDefault();
        let data = $('#daily-form').serializeArray();
        data.push({
            name: 'id',
            value: $('#mReport_id').val()
        });
        ajaxGetServerWithLoader("{{ route('admin.MeetingWeeklySave') }}", 'POST', data, function () {
            locationPage();
        }, function (error){
            $('.loadajax').hide();
            showErrors(error.responseJSON.errors);
        });
    });
    // Function ready
    $(document).ready(function () {
        $(".selectpicker").selectpicker();
        SetDatePicker($('.date'), {
            todayHighlight: true,
        });
        $(".datepicker").datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
        });
        SetDateTimePicker($("#time-end"), {
            format: "DD/MM/YYYY HH:mm"
        });
        SetDateTimePicker($("#timeEnd"), {
            format: "DD/MM/YYYY HH:mm"
        });
        updateComponent(null, "update");
    });
</script>
