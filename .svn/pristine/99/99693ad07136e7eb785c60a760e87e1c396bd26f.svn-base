<div class="modal draggable fade in detail-modal modal-css" id="overtime-info" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-xs ui-draggable">
        <!-- Modal content-->
        <div class="modal-content drag">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="save-errors"></div>
                <form class="form-horizontal detail-form" method="POST" id="overtime-form">
                    @csrf
                    @if(isset($OvertimeInfo->id))
                        <input type="hidden" name="id" value="{{ $OvertimeInfo->id }}" id="id">
                    @endif
                    <!-- nhân viên -->
                    <div class="form-group">
                        <input type="hidden" value="{{isset($OvertimeInfo->ProjectID) ? $OvertimeInfo->ProjectID : ''}}"
                               id="overtimeUserID">
                        <input type="hidden" value="{{$OvertimeInfo->TaskID ?? ''}}"
                               id="overtimeTaskID">
                        <label class="control-label col-sm-3" for="">@lang('admin.staff')&nbsp;<sup
                                class="text-red">*</sup>:</label>
                        <div class="col-sm-9">
                            <select class="selectpicker show-tick show-menu-arrow" id="selectUser" data-size="5"
                                    name="UserID" data-live-search="true" data-live-search-placeholder="Search">
                                @if(isset($userLogged->role_group) && $userLogged->role_group == 3)
                                    <option value="{{ $userLogged->id }}" selected>{{ $userLogged->FullName }}</option>
                                @else
                                    {!! GenHtmlOption($users, 'id', 'FullName', isset($OvertimeInfo->UserID) ? $OvertimeInfo->UserID : (!isset($OvertimeInfo->UserID) ? Auth::user()->id : '')) !!}
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="sDate">@lang('admin.daily.Working_Time')&nbsp;<sup
                                class="text-red">*</sup>:</label>
                        <div class="col-sm-9">
                            <div class="input-group date" id="sDate" style="margin-bottom: 10px;">
                                <input type="text" class="form-control" id="sDate-input"
                                       placeholder="@lang('admin.startDate')" name="STime" autocomplete="off"
                                       value="{{ isset($OvertimeInfo->STime) ? FomatDateDisplay($OvertimeInfo->STime, FOMAT_DISPLAY_DATE_TIME) : null }}">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <div class="input-group date" id="eDate">
                                <input type="text" class="form-control" id="eDate-input"
                                       placeholder="@lang('admin.endDate')" name="ETime" autocomplete="off"
                                       value="{{ isset($OvertimeInfo->ETime) ? FomatDateDisplay($OvertimeInfo->ETime, FOMAT_DISPLAY_DATE_TIME) : null }}">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="selectProjectID">@lang('admin.overtime.project')
                            &nbsp;<sup
                                class="text-red">*</sup>:</label>
                        <div class="bootstrap-select col-sm-9">
                            <select class="form-control selectpicker show-tick show-menu-arrow" name="ProjectID"
                                    id="selectProjectID" data-size="6" tabindex="-98">
                            </select>
                        </div>
                    </div>
                    {{-- <div class="form-group">
                        <label class="control-label col-sm-3" for="SelectNewProjectID">@lang('admin.overtime.newProject')
                            &nbsp;<sup
                                class="text-red">*</sup>:</label>
                        <div class="bootstrap-select col-sm-9">
                            <select class="form-control selectpicker show-tick show-menu-arrow" name="newProjectID"
                                    id="selectNewProjectID" data-size="6" tabindex="-98">
                            </select>
                        </div>
                    </div> --}}
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="SelectTaskID">@lang('admin.task.name') &nbsp;
                                :</label>
                        <div class="bootstrap-select col-sm-9">
                            <select class="form-control selectpicker show-tick show-menu-arrow" name="TaskID"  disabled
                                    id="selectTaskID" data-size="6" tabindex="-98">
                                    <option value="">Chọn Task</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3">@lang('admin.overtime.break_time')
                            &nbsp;<sup class="text-red">*</sup>:</label>
                        <div class="bootstrap-select col-sm-9">
                            <input type="number" min="0" class="form-control" placeholder="Thời gian nghỉ"
                                   name="BreakTime"
                                   value="{{ isset($OvertimeInfo->BreakTime) ? $OvertimeInfo->BreakTime : 0 }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="RoomId">@lang('admin.overtime.content') &nbsp;<sup
                                class="text-red">*</sup>:</label>
                        <div class="bootstrap-select col-sm-9">
                            <textarea name="Content" class="form-control" id="content" rows="3"
                                      placeholder="Nội dung">{{ isset($OvertimeInfo->Content) ? $OvertimeInfo->Content : null }}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="member">@lang('admin.overtime.request_manager')&nbsp;<sup
                                class="text-red">*</sup>:</label>
                        <div class="col-sm-9">
                            <select class='selectpicker show-tick show-menu-arrow' data-actions-box="true" data-size="5"
                                    id='select-leader' name="RequestManager[]" multiple>
                                @foreach($request_manager as $item)
                                    <option
                                        value="{{ $item->user_id }}" {{ isset($OvertimeInfo->UserID) && in_array($item->user_id, $OvertimeInfo->RequestManager) ? 'selected' : '' }}>{{ $item->FullName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                        id="cancel">@lang('admin.btnCancel')</button>
                @if(isset($OvertimeInfo->Approved) && $OvertimeInfo->Approved == (1||2))
                @else
                    <button type="button" class="btn btn-primary btn-sm" id="save">@lang('admin.btnSave')</button>
                @endif
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async>

    $(function () {
        $(".selectpicker").selectpicker();
        $('#sDate,#sDate-input,#eDate, #eDate-input').datetimepicker({
            format: 'DD/MM/YYYY HH:mm',
            stepping: 5
        });

        $('#save').click(function () {
            ajaxGetServerWithLoader("{{ route('admin.OvetimeStore') }}", 'POST', $('#overtime-form').serializeArray(), function (data) {
                if (typeof data.errors !== 'undefined') {
                    showErrors(data.errors);
                    return;
                }
                locationPage();
            });
        });

    });

    getProjectByUserId($('#selectUser option:selected').val());
    // getProjects();

    $("select[name='UserID']").on('change', function () {
        getProjectByUserId($('#selectUser option:selected').val());
        // getProjects();
    });

    $("#selectProjectID").on('change', function(){
        getTasks();
    });

    function getProjectByUserId(UserID) {
        ajaxServer(genUrlGet(['{{ route('admin.getProjectByUserId') }}', '/' + UserID,]), 'GET', null,
            function (data) {
                html = ``;
                html += `<option value="">Chọn dự án</option>`;
                let id = $('#overtimeUserID').val();
                for (key in data) {
                    let strSelected = '';
                    if (data[key].id == id) {
                        strSelected = 'selected';
                    }
                    html += `<option value="${data[key].id}" ${strSelected}>${data[key].NameVi}</option>`;
                }
                $("#selectProjectID").html(html);
                $("#selectProjectID").selectpicker('refresh');
                getTasks()
            });
    }


    function getTasks() {
        let html = ``;
        html += `<option value="">Chọn Tasks</option>`;
        if($("#selectProjectID").val() == ''){
            $("#selectTaskID").empty().html(html);
            $("#selectTaskID").selectpicker('refresh');
            $('#selectTaskID').prop('disabled',true);
        }else{
            let data = [];
            data['userId'] = $('#selectUser option:selected').val();
            data['projectId'] = $('#selectProjectID option:selected').val();
            $('#selectTaskID').prop('disabled',false);
            $.ajax({
                url: '{{route('admin.getTasks')}}',
                type: 'POST',
                data: {
                    projectId: data['projectId'],
                    memberId: data['userId']
                },
                success: function (res) {
                    let data = res.data;
                    let id = $('#overtimeTaskID').val();
                    for (key in data) {
                        let strSelected = '';
                        if (data[key].id == id) {
                            strSelected = 'selected';
                        }
                        html += `<option value="${data[key].id}" ${strSelected}>${data[key].Name}</option>`;
                    }
                    $("#selectTaskID").empty().html(html);
                    $("#selectTaskID").selectpicker('refresh');
                }
            });
        }

    }

    $('.modal-dialog').draggable({
        handle: ".modal-header"
    });
</script>

