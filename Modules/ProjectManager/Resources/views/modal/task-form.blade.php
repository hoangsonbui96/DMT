<style>
    .select-member .show-menu-arrow:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
        width: 100%;
    }

    .select-leader .show-menu-arrow:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
        width: 100%;
    }

    .tag-container {
        display: flex;
        flex-flow: row wrap;
    }

    .tag {
        pointer-events: none;
        background-color: #cdd2d4 !important;
        color: black;
        padding: 6px;
        margin: 8px 5px 5px 0;
        /*border-radius: 1rem;*/
    }

    .tag::before {
        pointer-events: all;
        display: inline-block;
        content: 'x';
        height: 20px;
        width: 20px;
        margin-right: 6px;
        text-align: center;
        color: red;
        cursor: pointer;
    }

</style>
<div class="modal fade in detail-modal" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width: 70%">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title" style=" word-break: break-word;"></h4>
            </div>
            <div class="modal-body" style="padding-top: 15px">
                <form class="form-horizontal detail-form" id="form-add-task">
                    <meta id="token" name="csrf-token" content="{{ csrf_token() }}">
                    <input id="projectId" name="projectId" value="{{ $project->id ?? null }}" type="hidden">
                    <input id="taskStatus" name="taskStatus" value="{{ isset($taskInfo) ? $taskInfo->Status : 1 }}"
                        type="hidden">
                    <input id="taskId" name="taskId"
                        value="{{ isset($taskInfo) ? ($issue == 'createChildTask' ? null : $taskInfo->id) : null }}"
                        type="hidden">
                    @if ($issue)
                        <input name="issue" value="{{ $issue }}" type="hidden">
                    @endif

                    <div class="save-errors"></div>
                    <div class="tab-content">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="projectName">@lang('projectmanager::admin.project.Name')
                                        &nbsp;
                                        :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="projectName" name="projectName"
                                            maxlength="100" value="{{ $project->NameVi ?? null }}" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="phaseId">@lang('projectmanager::admin.phase.Name')&nbsp;
                                        :</label>
                                    <div class="col-sm-9">
                                        <div>
                                            <select id="phase_select1" class='selectpicker show-tick show-menu-arrow'
                                                data-live-search="false" name="phaseId[]" data-size="6"
                                                data-live-search-placeholder="Chọn Phase" data-actions-box="true"
                                                data-width="100%" onchange="getTaskType(this,1)">
                                                <option value="">Chọn Phase</option>
                                                {!! GenHtmlOption($project->phases, 'id', 'name', isset($taskInfo->PhaseId) ? $taskInfo->PhaseId : (isset($searchKeys) ? ($searchKeys['phaseId'] ? $searchKeys['phaseId'] : '') : '')) !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="">@lang('projectmanager::admin.job.Name')&nbsp;
                                        :</label>
                                    <div class="col-sm-9">
                                        <div>
                                            <select class='selectpicker show-tick show-menu-arrow'
                                                data-live-search="false" name="jobId[]" data-size="6"
                                                data-live-search-placeholder="Chọn Job" data-actions-box="true"
                                                data-width="100%">
                                                <option value="">Chọn Job</option>
                                                {!! GenHtmlOption($project->jobs, 'id', 'name', isset($taskInfo->JobId) ? $taskInfo->JobId : (isset($searchKeys) ? ($searchKeys['jobId'] ? $searchKeys['jobId'] : '') : '')) !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="">@lang('projectmanager::admin.task.ParentName')&nbsp;:</label>
                                    <div class="col-sm-9">
                                        <div>
                                            <select class='selectpicker show-tick show-menu-arrow'
                                                data-live-search="false" id="parentTaskId1" name="parentTaskId[]"
                                                data-size="6" data-live-search-placeholder="Chọn Task chính"
                                                data-actions-box="true" data-width="100%">
                                                <option value="">Chọn Task chính</option>
                                                {!! GenHtmlOption($project->tasks, 'id', 'Name', isset($taskInfo->ParentId) ? $taskInfo->ParentId : ($issue == 'createChildTask' ? $taskInfo->id : '')) !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="nameVi">@lang('projectmanager::admin.task.name')
                                        &nbsp;<sup class="text-red">*</sup>:</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="name1"
                                            placeholder="@lang('projectmanager::admin.task.name')" name="name[]" maxlength="300"
                                            value="{{ isset($taskInfo) ? ($issue == 'createChildTask' ? 'Task con của Task ' . $taskInfo->Name : $taskInfo->Name) : null }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="type">@lang('projectmanager::admin.task.Type')&nbsp;
                                        :</label>
                                    <div class="col-sm-9">
                                        <div>
                                            <select id="taskTypeSelect1" class='selectpicker show-tick show-menu-arrow'
                                                data-live-search="false" name="type[]" data-size="6"
                                                data-live-search-placeholder="Chọn Loại Task" data-actions-box="true"
                                                data-width="100%">
                                                @if (!$selectTaskTypes)
                                                    <option value="" disabled selected>Loại khác</option>
                                                @else
                                                    {!! GenHtmlOption($selectTaskTypes, 'id', 'name', isset($taskInfo->Type) ? $taskInfo->Type : '') !!}
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="member">@lang('projectmanager::admin.Members')
                                        &nbsp;:</label>
                                    <div class="col-sm-9">
                                        <div class="select-member">
                                            <select class='selectpicker show-tick show-menu-arrow'
                                                data-live-search="true" id="selectMember1" name="members[]"
                                                data-size="5" data-live-search-placeholder="Chọn thành viên"
                                                data-actions-box="true" data-toggle="popover">
                                                <option value="">Chọn thành viên</option>
                                                @if (isset($taskInfo->member) && $taskInfo->member->Active == 0)
                                                    <option value="{{ $taskInfo->member->id }}" selected>
                                                        {{ $taskInfo->member->FullName }}</option>
                                                @endif
                                                @foreach ($project->activeUsers as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ isset($taskInfo->member) && $taskInfo->member->id == $user->id ? 'selected' : '' }}>
                                                        {{ $user->FullName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="sDate">@lang('projectmanager::admin.task.Duration')&nbsp;:</label>
                                    <div class="col-sm-9">
                                        <input type="text" pattern="\d*" maxlength="5" class="form-control"
                                            id="duration1" placeholder="@lang('projectmanager::admin.task.Duration')" name="duration[]" max="99"
                                            value="{{ isset($taskInfo) ? $taskInfo->Duration : null }}">
                                        <small class="help-block text-secondary italic">Số giờ dự kiến thực hiện Task là
                                            căn cứ để tính tiến độ dự án!</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-3" for="sDate">@lang('projectmanager::admin.Times')&nbsp;:</label>
                                    <div class="col-sm-9" id="select-Time">
                                        <div class="row">
                                            <div class="col-sm-6 col-xs-3">
                                                <div class="input-group datetime" id="start-date">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        id="sDate-input" placeholder="@lang('projectmanager::admin.Date Start')"
                                                        name="startDate[]" autocomplete="off"
                                                        value="{{ isset($taskInfo) ? $taskInfo->StartDate : null }}">

                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 col-xs-3">
                                                <div class="input-group datetime" id="end-date">
                                                    <input type="text" class="form-control datetimepicker-input"
                                                        id="eDate-input" placeholder="@lang('projectmanager::admin.Date End')"
                                                        name="endDate[]" autocomplete="off"
                                                        value="{{ isset($taskInfo) ? $taskInfo->EndDate : null }}">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="subType1" name="subType[]">

                        <div class="form-group">
                            <label class="control-label col-sm-1" for="description">@lang('projectmanager::admin.Content'):</label>
                            <div class="col-sm-11">
                                <textarea class="form-control description" rows="5" id="description" maxlength="300" name="description[]"
                                    placeholder="@lang('projectmanager::admin.Description')">{{ isset($taskInfo) ? ltrim($taskInfo->Description) : null }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-1" for="note">@lang('projectmanager::admin.Note'):</label>
                            <div class="col-sm-11">
                                <textarea class="form-control note" rows="2" id="note" maxlength="200" name="note[]"
                                    placeholder="@lang('projectmanager::admin.Note')">{{ isset($taskInfo) ? ltrim($taskInfo->Note) : null }}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-sm-1">@lang('projectmanager::admin.Tags'): </label>
                            <div class="col-sm-11">
                                <input class="form-control" name="hashtags" type="text" id="hashtags_1"
                                    placeholder="@lang('projectmanager::admin.Tags')" autocomplete="off" maxlength="20">
                                <small class="help-block text-secondary italic">Để thêm Tag vui lòng nhập nội dung sau
                                    đó nhấn phím Enter!</small>
                                <div class="tag-container">
                                    <p class="tag hide"></p>
                                    @if (isset($taskInfo))
                                        @foreach (array_filter(explode(',', $taskInfo->Tags)) as $tag)
                                            <p class="tag">{{ $tag }}</p>
                                        @endforeach
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                    id="cancel">@lang('projectmanager::admin.Cancel')</button>
                <button type="submit" class="btn btn-primary btn-sm save-form">@lang('projectmanager::admin.Save')</button>
            </div>
        </div>

    </div>
</div>

<div class="modal fade in" id="modal2" role="dialog" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">2nd Modal title</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="container"></div>
            <div class="modal-body">
                Content for the 2nd dialog / modal goes here... Content for the 2nd dialog / modal goes here.
            </div>
            <div class="modal-footer">
                <a href="#" data-dismiss="modal" class="btn">Close</a>
            </div>
        </div>
    </div>
</div>
<a id="launchModal2" hidden data-toggle="modal" href="#modal2">Launch modal 2</a>

<script type="text/javascript" async>
    $(document).ready(function() {
        setSelectPicker();
        $('[data-toggle="popover"]').popover()
        let startDate = '{{ $project->StartDate }}';
        let endDate = '{{ $project->EndDate }}';
        const workStartDate = startDate;
        const workEndDate = endDate;
        workStartDateArr = workStartDate.split('-');
        workEndDateArr = workEndDate.split('-');

        $('body').css({
            'overflow-y': ''
        });

        // SetDatePicker($('.date'), {
        //     todayHighlight: true,
        //     startDate: new Date(workStartDateArr[0],workStartDateArr[1]-1,workStartDateArr[2]),
        //     endDate: new Date(workEndDateArr[0],workEndDateArr[1]-1,workEndDateArr[2]),
        //     autoclose: true,
        // });

        myDateTimePicker($('#start-date'), {
            format: 'DD/MM/YYYY HH:mm',
            // sideBySide: true,
            // minDate: moment(`${workStartDateArr[1]}/${workStartDateArr[2]}/${workStartDateArr[0]}`),
            // maxDate: moment(`${workEndDateArr[1]}/${workEndDateArr[2]}/${workEndDateArr[0]}`),
        });

        myDateTimePicker($('#end-date'), {
            format: 'DD/MM/YYYY HH:mm',
            // stepping: 5,
        });

        // $('#sDate-input').datepicker({
        //     startDate: '02/03/2022'
        // });

        $('#parentTaskId1').on('change', function() {
            let parentName = $('#parentTaskId1 :selected').text();
            $('#name1').val('sub ' + parentName);
            $('#subType1').val(1);
        });

        $('#selectMember1').on('change', function() {
            getTaskEndTime();
        });

        $('#duration1').on('keyup', function() {
            getTaskEndTime();
        });

        $('#start-date').on('dp.change', function() {
            getTaskEndTime();
        });

        $('#end-date').on('dp.change', function() {
            getDoingTasks();
        });

        $('#toggle-one').bootstrapToggle();

        $('.draggable').draggable();

        // Jquery draggable
        $('.modal-dialog').draggable({
            handle: ".modal-header"
        });
        tagAction(1, 1);
        let taskInfo = '{{ empty($taskInfo) }}';
        if (taskInfo != '') {
            getTaskType(document.getElementById(`phase_select${COUNT_TAB_WORK}`), 1);
        }
    })

    var content = $(".tab-pane").html();
    var project = $(".tab-pane #projectName").val();

    // SetDatePicker($('.date'), {
    //     format: "dd/mm/yyyy",
    //     todayHighlight: true,
    //     autoclose: true,
    // });

    var COUNT_TAB_WORK = 1;

    function addNewForm(event) {
        event.preventDefault();
        const description = $($(".tab-pane")[COUNT_TAB_WORK - 1]).find("#note").val();
        COUNT_TAB_WORK = COUNT_TAB_WORK + 1;
        let id = $('.nav-tabs').children().length;
        if (id <= 5) {
            let tabId = "task_" + COUNT_TAB_WORK;
            let new_tab = `
                   <li class="li-task">
                        <a href="#${tabId}" data-toggle="tab">Task ${id}</a>
                        <span><i class="fa fa-times" aria-hidden="true"></i></span>
                    </li>
                `;
            let new_content = `
                    <div class='tab-pane tab-work active' id="${tabId}">
                        ${content}
                    </div>
                `;
            $(event.target).closest("li").before(new_tab);
            $(".tab-content").append(new_content)
            $(`#${tabId}`).find(`#phase_select${COUNT_TAB_WORK - 1}`).attr('id', `phase_select${COUNT_TAB_WORK}`);
            let elm = document.getElementById(`phase_select${COUNT_TAB_WORK}`);
            elm.setAttribute('onchange', `getTaskType(this,${COUNT_TAB_WORK})`);
            $(`#${tabId}`).find(`#taskTypeSelect${COUNT_TAB_WORK - 1}`).attr('id', `taskTypeSelect${COUNT_TAB_WORK}`);

            // SetDatePicker($('.date'), {
            //     todayHighlight: true,
            //     startDate: new Date(workStartDateArr[0],workStartDateArr[1]-1,workStartDateArr[2]),
            //     endDate: new Date(workEndDateArr[0],workEndDateArr[1]-1,workEndDateArr[2]),
            //     autoclose: true,
            // });
            $('.selectpicker').selectpicker();
            $(".nav-tabs li:nth-child(" + id + ") a").click();
            let input_tags = $("#task_" + COUNT_TAB_WORK).find('input[name="hashtags"]');
            $(input_tags).attr("id", "hashtags_" + COUNT_TAB_WORK);
            tagAction(id, COUNT_TAB_WORK);

            // set value suggest
            // $(`#${tabId}`).find('#note').val(description);
        }
    }

    function removeTab() {
        const ulTask = $('.ul-task');
        const liNumber = ulTask.children().length - 1;
        for (let i = 0; i < liNumber; i++) {
            const li = $(ulTask).find('li')[i];
            const a = $(li).find('a');
            $(a).text(`Task ${i + 1}`);
            // $(a).attr('href',`#task_${i+1}`);
            // $(`#task_${i}`).attr('id',`task_${i+1}`)
        }
    }

    $(function() {
        $('.save-form').click(function() {
            let data_post = $('.detail-form').serializeArray();
            phaseId = $('select[name="phaseId"]').val();
            jobId = $('select[name="jobId"]').val();
            taskStatus = $('select[name="taskStatus"]').val();
            let temp = {};
            $.each($('.tag-container .tag'), function(index, value) {
                let parent_id = $($(value).parents()[3]).attr("id");
                if (parent_id in temp) {
                    temp[parent_id].push($(value).text());
                } else {
                    temp[parent_id] = [$(value).text()];
                }
            });
            for (let key in temp) {
                data_post.push({
                    name: 'Tags[]',
                    value: temp[key]
                });
            }
            ajaxGetServerWithLoader("{{ route('admin.TaskSave') }}", 'POST', data_post, function(
                data) {
                if (typeof data.errors !== 'undefined') {
                    showErrors(data.errors);
                    return;
                }
                // $('ul.list-items').empty();
                // loadData({
                //     'projectId': projectId,
                //     'phaseId': phaseId,
                //     'jobId': jobId,
                //     'taskStatus': taskStatus
                // });
                // $('body').css({'overflow-y': 'auto','padding-right':'0'});
                // $('.loadajax').hide();
                // $('.detail-modal').modal('hide');
                // $('#popupModal').empty();
                // $('.modal-backdrop').remove();
                showSuccessAutoClose(data.data.mes);
                locationPage();
            });
        });

        $(".nav-tabs").on("click", "span", function() {
            var anchor = $(this).siblings('a');
            $(anchor.attr('href')).remove();
            $(this).parent().remove();
            // COUNT_TAB_WORK = COUNT_TAB_WORK - 1;
            removeTab();
            $(".nav-tabs li").children('a').first().click();
        });

    });

    function tagAction(index_li, COUNT_TAB_WORK) {
        let input, hashtagArray, container, t;
        input = document.querySelector('#hashtags_' + COUNT_TAB_WORK);
        let index_sub = index_li - 1;
        container = $('.tag-container')[index_sub];
        let deleteTags = document.querySelectorAll('.tag');
        let arr_text = [];
        $($(input).closest('.tag-container').find('.tag')).each(function() {
            arr_text.push($(this).text());
        });
        input.addEventListener('keyup', () => {
            if (event.which === 13 && input.value.length > 0) {
                let value_underscore = input.value.trim().split(' ').join('_');
                if (!arr_text.includes(value_underscore)) {
                    arr_text.push(value_underscore);
                    let text = document.createTextNode(value_underscore);
                    let p = document.createElement('p');
                    container.appendChild(p);
                    p.appendChild(text);
                    p.classList.add('tag');
                }
                input.value = '';
                deleteTags = document.querySelectorAll('.tag');
                for (let i = 0; i < deleteTags.length; i++) {
                    deleteTags[i].addEventListener('click', () => {
                        container.removeChild(deleteTags[i]);
                    });
                }
            }
        });

        for (let i = 0; i < deleteTags.length; i++) {
            deleteTags[i].addEventListener('click', () => {
                container.removeChild(deleteTags[i]);
            });
        }
    }

    function getDoingTasks() {
        $('.select-member').popover('destroy')
        let memberId = $('#selectMember1').children("option:selected").val();
        let projectId = $('#projectId').val();
        let taskId = $('#taskId').val();
        let startTime = $('#sDate-input').val();
        let endTime = $('#eDate-input').val();
        if (startTime == '' || endTime == '' || memberId == 0) {
            return
        }
        $.ajax({
            url: '{{ route('admin.getDoingTasks') }}',
            type: 'POST',
            data: {
                memberId: memberId,
                startTime: startTime,
                endTime: endTime,
                projectId: projectId,
                taskId: taskId
            },
            success: function(res) {
                if (res.success == true) {
                    showDoingTasks(res.data.tasks)
                    $('.loadajax').hide();
                } else {
                    console.log(res.error);
                }
            }
        });
    }

    function showDoingTasks(tasks) {
        let popoverBody = '';
        let content = '';
        $.each(tasks, function(key, value) {
            popoverBody += `
                <tr>
                    <td>${value.Name}</td>
                    <td>${value.StartDate}-${value.EndDate}</td>
                </tr>
            `;
            return popoverBody;
        });
        if (popoverBody != '') {
            content = `<div id="popoverContent" style ="max-width: 700px">
                            <p style="color:red"> Công việc đang làm thời gian này: </p>
                            <table class="table table-bordered table-striped" name="table" style="width:400px">
                                <thead class="thead-default">
                                    <tr>
                                        <th>Công việc</th>
                                        <th>Thời gian</th>
                                    </tr>
                                </thead>
                                <tbody id="popoverTbody">
                                    ${popoverBody}
                                </tbody>
                            </table>
                        </div>`;
            $('.select-member').popover({
                container: '.select-member',
                html: true,
                placement: 'right',
                sanitize: false,
                content: content
            });
            $('.select-member').popover('show')
        } else {
            content = '';
            $('.select-member').popover('hide')
        }
    }

    function getTaskType(e, task_i) {
        $.ajax({
            url: '{{ route('admin.getPhase') }}',
            type: 'GET',
            data: {
                phaseId: e.value
            },
            success: function(res) {
                let taskTypes = res.task_types;
                $(`#taskTypeSelect${task_i}`)
                    .find('option')
                    .remove()
                    .end()
                    .append('<option value="">Loại khác</option>')
                    .val('');
                if (jQuery.type(taskTypes) !== 'undefined') {
                    $.each(taskTypes, function(i, item) {
                        $(`#taskTypeSelect${task_i}`).append(
                            `<option value="${item.id}">${item.name}</option>`);
                    });
                }
                $('.selectpicker').selectpicker('refresh');
            }
        })
    }

    function getTaskEndTime() {
        let memberId = $('#selectMember1').children("option:selected").val();
        let startTime = $('#sDate-input').val();
        let duration = $('#duration1').val();
        let endTime = $('#eDate-input').val();
        let taskId = $('#taskId').val();
        if (startTime == '' || memberId == 0 || duration == 0 || taskId) {
            return
        }
        $('#eDate-input').prop("disabled", true);
        $.ajax({
            url: '{{ route('admin.getTaskEndTime') }}',
            type: 'POST',
            data: {
                userId: memberId,
                taskStartTime: startTime,
                taskDuration: duration,
            },
            success: function(res) {
                if (res.success == true) {
                    $('#sDate-input').val(res.data.startTime);
                    $('#eDate-input').val(res.data.endTime);
                    $('#sDate-input').prop("disabled", false);
                    $('#eDate-input').prop("disabled", false);
                    getDoingTasks();
                } else {
                    console.log(res.error);
                }
            }
        });
    }
</script>

