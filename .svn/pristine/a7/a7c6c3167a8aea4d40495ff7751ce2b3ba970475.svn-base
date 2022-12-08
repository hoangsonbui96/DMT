<div class="modal fade in detail-modal" id="user-info" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content work-content">
            <div class="modal-header">
                <span class="close" data-dismiss="modal" id="close-work-form">×</span>
            <h4 class="modal-title quick-edit-title"><span>{{ $itemInfo->name }}</span>
                <div class="form-group" style="display:none">
                    <input type="text" class="form-control work-title-input" data-id="{{ $itemInfo->id }}" style="height:26px;">
                </div>
            </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-9 work-left">
                        <div class="row">
                            <div class="work-member col-md-6">
                                @if($listWorkMember->count())
                                <h3 class="h3-work-title h3-members"><i class="fa fa-users"></i>@lang('admin.project.Member')</h3>
                                @else
                                <h3 class="h3-work-title" style="display:none"><i class="fa fa-users"></i>@lang('admin.project.Member')</h3>
                                @endif
                                <ul id="assigned-member">
                                @foreach($listWorkMember as $member)
                                <li>{{ $controller->getShortName($member->FullName) }}</li>
                                @endforeach
                                </ul>
                            </div>
                            <div class="col-md-6">

                            </div>
                        </div>

                        <div class="row">
                            <div class="work-description col-md-12">
                                <h3 class="h3-work-title"><i class="fa fa-file-text"></i>@lang('admin.masterdata.description')</h3>
                                <div class="form-group work-desc-detail">
                                    <div class="desc @if(is_null($itemInfo->description)) new-desc @endif">
                                    <span class="txt-desc">{{$itemInfo->description}}</span>
                                        @if(is_null($itemInfo->description))
                                        <span class="txt-place-holder">@lang('admin.work-detail.Add_a_description_of_this_tag')</span>
                                        @endif
                                    </div>
                                    <textarea class="form-control" placeholder="Thêm mô tả về thẻ này" work-id="{{ $itemInfo->id }}"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="work-tasks col-md-12">
                                <h3 class="h3-work-title">
                                    <i class="fa fa-tasks"></i>
                                    @lang('admin.work-detail.To_do_list')
                                </h3>
                                @foreach($tasks as $task)
                                <div class="task-item" data-id="{{ $task->id }}">
                                    <h4 class="quick-edit-title"><i class="fa fa-check-square-o" style="font-size: 20px"></i> <span class="{{ $task->status == 1 ? 'task-complete' : '' }}">{{ $task->name }}</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control task-title-input" data-id="{{ $task->id }}" style="height:26px;">
                                        </div>
                                    </h4>
                                    <div class="btn btn-default btn-task"><i class="fa fa-plus"></i></div>
                                    <div class="task-history">
                                        <table class="table table-bordered table-task-history">
                                            <thead>
                                                <tr>
                                                    <th>@lang('admin.day')</th>
                                                    <th>@lang('admin.absence.start')</th>
                                                    <th>@lang('admin.absence.end')</th>
                                                    <th>@lang('admin.overtime.break_time')</th>
                                                    <th>@lang('admin.project.Member')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($task->histories as $history)
                                                <tr>
                                                <td>{{ $history->date }}</td>
                                                <td>{{ Carbon\Carbon::parse($history->start_time)->format('H:i') }}</td>
                                                <td>{{ Carbon\Carbon::parse($history->end_time)->format('H:i') }}</td>
                                                <td>{{ $history->rest_time+0 }}</td>
                                                <td>{{ $history->FullName }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>

                                        </table>
                                        <span class="btn btn-success">@lang('admin.Add_history')</span>
                                    </div>
                                <div class="new-th" data-id="{{ $task->id }}">
                                        <div><input type="text" name="date" class="form-control dpicker" placeholder="Ngày"></div>
                                        <div><input type="text" name="STime" class="form-control tpicker" placeholder="Giờ bắt đầu" value=""></div>
                                        <div><input type="text" name="ETime" class="form-control tpicker" placeholder="Giờ kết thúc"></div>
                                        <div><input type="text" name="total" class="form-control" placeholder="Thời gian nghỉ" value="0"></div>
                                        <div style="margin-bottom: 0px;text-align:right;">
                                            <span class="btn btn-success">@lang('admin.btnSave')</span>
                                            <span class="btn btn-danger">@lang('admin.btnCancel')</span>
                                        </div>
                                    </div>

                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row">
                            <div class="work-histories col-md-12">
                                <h3 class="h3-work-title">
                                    <i class="fa fa-history"></i>
                                    @lang('admin.on')
                                </h3>

                            </div>
                        </div>

                    </div>
                    <div class="col-md-3 work-right">
                        <h3 class="h3-work-title">@lang('admin.masterdata.add')</h3>
                    <ul class="work-menu">
                            <li id="work-user" class="open-work-task-action"><i class="fa fa-user"></i>@lang('admin.project.Member')
                                <ul class="work-task-action work-user" style="display:none;"  work-id="{{ $itemInfo->id }}">
                                    <li class="wl-action-head">@lang('admin.project.Member')</li>
                                    @foreach($members as $member)
                                <li class="wl-li wk-li-user" data-name="{{ $member->FullName }}" data-id="{{ $member->id }}">{{ $member->FullName }} <i class="fa fa-check" style="float:right;margin-top:3px;{{ !in_array($member->id, $workMemberArr) ? 'display:none' : '' }}"></i></li>
                                    @endforeach
                                </ul>
                            </li>
                            <li id="work-task" class="open-work-task-action"><i class="fa fa-tasks"></i> @lang('admin.work-detail.To_do')
                                <ul class="work-task-action work-task" style="display:none;"  work-id="{{ $itemInfo->id }}">
                                    <li class="wl-action-head">@lang('admin.work-detail.To_do')</li>

                                    <li class="wl-li wk-li-task">
                                        <select name="taskType" class="form-control" tabindex="-98" required="">
                                            {{-- <option value="">Loại công việc</option> --}}
                                            @foreach($taskTypes as $type)
                                            <option value="{{ $type->DataValue }}">{{ $type->Name }}</option>
                                            @endforeach
                                        </select>
                                    </li>
                                    <li class="wl-li wk-li-task"><input type="text" name="newTask" class="form-control" placeholder="Tiêu đề công việc" value="Nhiệm vụ mới"></li>
                                    <li class="wl-li wk-li-task"><span class="btn btn-success">@lang('admin.masterdata.add')</span></li>

                                </ul>
                            </li>
                            <li><i class="fa fa-clock-o"></i>@lang('admin.work-detail.Expiration_date')</li>
                            {{-- <li>Thêm nhiệm vụ</li> --}}
                        </ul>

                    </div>
                </div>

            </div>
            <div class="modal-footer">

            </div>
        </div>

    </div>
</div>
<script>

    // $('.dtpkTime').datepicker();
    $(function () {
        $(".open-work-task-action").click(function(){
            $(".work-task-action").hide();
            $(this).find(".work-task-action").show();
        });
        $(".selectpicker").selectpicker();
        $(".dpicker").datetimepicker({
            format: 'YYYY/MM/DD',
        });
        $(".tpicker").datetimepicker({
            format: 'HH:mm',
        });

    });
    //setup before functions
    var typingTimer;                //timer identifier
    var doneTypingInterval = 1000;  //time in ms (5 seconds)
    $('.work-desc-detail textarea').keyup(function(e){

        if(e.keyCode == 13 && !e.shiftKey){
            $(this).val($(this).val().replace(/[\r\n\v]+/g, ''));
            $(this).closest("div").find("textarea").hide();
            $(".desc").show();

        }
        clearTimeout(typingTimer);
        var workId = $(this).attr("work-id");
        var workDesc = $(this).val();
        // console.log(workDesc);
        // console.log(workId);
        $(this).closest("div").find(".txt-desc").text(workDesc)

        if(!isEmpty(workDesc)){
            $(".txt-place-holder").remove();
            $(".desc").removeClass("new-desc");
        }
        else
        {

            $(".desc").addClass("new-desc");
            $('.desc').append(`<span class="txt-place-holder">Thêm mô tả về công việc này</span>`);
        }

        if (!isEmpty(workDesc)) {
            typingTimer = setTimeout(function(){
                $.ajax({
                    url: "{{ route('admin.showWork') }}",
                    type: 'post',
                    data: {workId: workId, desc: workDesc, action: 'updateDesc'},
                    success: function (data) {
                        if (typeof data.errors !== 'undefined'){
                            // $('.loadajax').hide();

                            showErrors(data.errors);
                        }else{
                            console.log(data);
                        }

                    },
                    fail: function (error) {
                        console.log(error);
                    }
                });
            }, doneTypingInterval);
        }
    });
    $("#work-task").click(function(e){
        var target= $(e.target);
        if (!target.is('.work-task-action') && !target.is('.work-task-action li') && !target.is('.work-task-action li input')  && !target.is('.work-task-action li span')  && !target.is('.work-task-action li select')  && !target.is('.work-task-action li button')   && !target.is('.work-task-action li div')) {
            $("[name='newTask']").val("Việc cần làm");
        }
    });

    //cap nhat title cho work
    //on keyup, start the countdown
    $('.work-title-input').keyup(function(e){
        if(e.keyCode == 13){
            $(".modal-title .form-group").hide();
        }
        clearTimeout(typingTimer);
        var workId = $(this).attr("data-id");
        var workTitle = $(this).val();
        $(this).closest(".modal-title").find("span").text(workTitle);
        $(".sort-item[data-id='"+workId+"']").find('span').text(workTitle);
        if ($(this).val()) {
            typingTimer = setTimeout(function(){
                $.ajax({
                    url: "{{ route('admin.showWork') }}",
                    type: 'post',
                    data: {workId: workId, title: workTitle, action: 'updateTitle'},
                    success: function (data) {
                        if (typeof data.errors !== 'undefined'){
                            // $('.loadajax').hide();

                            showErrors(data.errors);
                        }else{
                            console.log(data);
                            // $(".sort-item[data-id='"+workId+"']").find('span').text(workTitle);
                        }

                    },
                    fail: function (error) {
                        console.log(error);
                    }
                });
            }, doneTypingInterval);
        }
    });

</script>

