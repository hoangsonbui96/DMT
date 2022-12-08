<style>
    .dot {
        height: 8px;
        width: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .space {
        margin-top: 20px;
    }

    .btn-task {
        text-align: left !important;
    }

    .btn-task:hover {
        background-color: #6c757d;
        color: white;
    }

    .single-line {
        margin: 5px 0;
    }

    .box-file {
        position: relative;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid rgba(0, 0, 0, .125);
        border-radius: .25rem;
    }

    .alert-success_akb {
        background-color: #ccf4cc;
        color: #3c763d;
        width: fit-content;
        padding: 2px 5px;
        border-radius: 5px;
    }

    .alert-danger_akb {
        background-color: #facccb;
        color: #a94442;
        width: fit-content;
        padding: 2px 5px;
        border-radius: 5px;
    }

    .file-a {
        border: 1px solid #3c8dbc;
        padding: 5px;
        border-radius: 3px;
        margin: 5px 8px 0 0;
    }

    .file-a:hover {
        background-color: #3c8dbc;
        color: white !important;
    }

    .more {
        display: none;
    }

    .more-file {
        display: none;
    }

    .attachment-thumbnail {
        border-radius: 3px;
        min-height: 80px;
        margin: 0 0 8px;
        overflow: hidden;
        position: relative;
    }

    .attachment-thumbnail:hover {
        background-color: rgba(9, 30, 66, .04);
    }

    .attachment-thumbnail:hover .time-file {
        display: block;
    }

    .attachment-thumbnail-preview {
        background-color: rgba(9, 30, 66, .04);
        background-position: 50%;
        background-size: contain;
        background-repeat: no-repeat;
        border-radius: 3px;
        height: 80px;
        margin-top: -40px;
        position: absolute;
        top: 50%;
        left: 0;
        text-align: center;
        text-decoration: none;
        z-index: 1;
        width: 90px;
    }

    .attachment-thumbnail-preview-ext {
        color: #5e6c84;
        display: block;
        font-size: 18px;
        font-weight: 700;
        height: 100%;
        line-height: 80px;
        text-align: center;
        text-transform: uppercase;
        text-decoration: none;
        width: 100%;
    }

    .attachment-thumbnail-details {
        box-sizing: border-box;
        cursor: pointer;
        padding: 8px 8px 0 100px;
        min-height: 80px;
        margin: 0;
        z-index: 0;
    }

    .attachment-thumbnail-name {
        font-weight: 700;
        word-wrap: break-word;
    }

    .attachment-thumbnail-details-title-options {
        margin-top: 8px;
        color: #5e6c84;
        display: block
    }

    .action-comment {
        background-color: #fff;
        border-radius: 3px;
        box-shadow: 0 1px 2px -1px rgb(9 30 66/25%), 0 0 0 1px rgb(9 30 66/8%);
        box-sizing: border-box;
        clear: both;
        display: inline-block;
        margin: 4px 2px 4px 0;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    .header-title {
        font-weight: 600;
        font-size: 15px;
    }

    .title-task {
        font-weight: 600;
        line-height: 1.6;
        word-wrap: break-word;
    }

    .title-task:hover .hide-flag {
        display: inline;
    }

    .just-send {
        background-color: #e5efff !important;
    }

    .tab-bar-item {
        display: inline-block;
        width: 104px;
        color: #001a33;
        cursor: pointer;
        margin-top: 1px;
        padding: 10px 0 18px 0;
        font-size: 15px;
        font-weight: 500;
        text-align: center;
    }

    .tab-bar-item_content {
        padding: 5px 12px 21px;
        font-size: 15px;
        cursor: pointer;
    }

    .tab-bar-item.active-bar {
        border-bottom: 1px solid #0068ff;
    }

    .tab-bar-item_content.active-bar_content {
        color: #0068ff;
        font-weight: 600;
    }

    .media-filter {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        padding: 12px 0 18px 0;
    }

    .time-file {
        float: right;
        display: none;
    }

    .active-badges {
        background-color: #0068ff;
        color: white
    }

    .link {
        margin-right: 10px;
    }
    .date-input{
        padding-left: 1.25rem;
    }
    .modal-content .label {
        background-color:  rgb(135,206,250,0.3);
        color: black;
        font-size: 100%;
    }
</style>
<div class="modal draggable fade in detail-modal modal-css" role="dialog" data-backdrop="static">
    <div class="modal-dialog ui-draggable modal-lg">
        <!-- Modal content-->
        <div class="modal-content drag" style="color: #42526e; background-color: #f4f5f7">
            <div class="modal-header ui-draggable-handle" style="border-bottom-color: #dbdbdb">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title" style="word-break: break-word;padding-right:50px;font-weight: bold;">{{$task->Name}}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col col-md-12 d-flex flex-column" style="padding: 0">
                        <div class="tab-content" style="padding: 0 15px;">
                            <div class="general-info tab-info-task">
                                <div class="folder-view row">
                                    <div id="general-info" class="">
                                        <div class="col-lg-9 col-md-9 col-sm-12">
                                            <div class="sign"></div>
                                            <div class="space">
                                                <p class="header-title">Thời gian</p>
                                                <ul>
                                                    <li>
                                                        <span class="label time-label time-start">{{$task->StartDate ? date('H:i d/m/Y',strtotime($task->StartDate)) : 'Chưa có thời gian bắt đầu'}}</span> - 
                                                        <span class="label time-label time-end">{{$task->EndDate ? date('H:i d/m/Y',strtotime($task->EndDate)) : 'Chưa có thời gian kết thúc'}}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="space">
                                                <p class="header-title">Nội dung</p>
                                                <ul>
                                                    <li>{{$task->Description ?? 'Không có nội dung mô tả chi tiết.'}}</li>
                                                </ul>
                                            </div>
                                            <div class="space">
                                                <p class="header-title">Ghi chú</p>
                                                <ul>
                                                    <li>{{$task->Note ?? 'Không có ghi chú.'}}</li>
                                                </ul>
                                            </div>
                                            @if(($countIssues = count($task->issues)) > 0)
                                                <p class="header-title">Trả lại {{$countIssues}} lần</p>
                                                <div class="" style="overflow-y: auto;max-height: 50vh">
                                                    @foreach ($task->issues->sortByDesc('issued_at') as $issue)
                                                    <ul>
                                                        <li><strong>{{date('H:i d/m/Y',strtotime($issue->issued_at))}}:</strong> {{$issue->content ?? 'Không có lý do cụ thể.'}} <i><u>{{$issue->issuer->FullName}}</u></i></li>
                                                    </ul>
                                                    @endforeach

                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-12">
                                            @if ($task->UserId != null)
                                                <div class="space">
                                                    <p class="header-title">Người thực hiện</p>
                                                    <div style="margin-top: -3px">
                                                        <span style="font-size: 14px">
                                                            @if($task->member)
                                                                {{$task->member->FullName }}
                                                            @else
                                                                Chưa có
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="space">
                                                    <p class="header-title">Người giao việc</p>
                                                    <div style="margin-top: -3px">
                                                            <span style="font-size: 14px">
                                                                @if($task->giver)
                                                                    {{$task->giver->FullName }}
                                                                @else
                                                                    Chưa có
                                                                @endif
                                                            </span>
                                                    </div>
                                                </div>
                                                <div class="space">
                                                    <p class="header-title">Trạng thái</p>
                                                    <div style="margin-top: -3px">
                                                        @switch($task->Status)
                                                            @case(1)
                                                            <i class="dot idea task-status" style="margin-right: 3px"></i>
                                                            <span class="label" >Chưa thực hiện</span>
                                                            @break
                                                            @case(2)
                                                            <i class="dot idea task-status" style="margin-right: 3px"></i>
                                                            <span class="label" >Đang thực hiện</span>
                                                            @break
                                                            @case(3)
                                                            <i class="dot idea task-status" style="margin-right: 3px"></i>
                                                            <span class="label" >Đang chờ duyệt</span>
                                                            @break
                                                            @default
                                                            <i class="dot idea task-status" style="margin-right: 3px"></i>
                                                            <span class="label" >Hoàn thành</span>
                                                            @break
                                                        @endswitch
                                                    </div>
                                                </div>
                                            @else
                                                <div class="space">
                                                    <p class="header-title">Trạng thái</p>
                                                    <div style="margin-top: -3px">
                                                        <i class="dot idea" style="margin-right: 3px"></i>
                                                        <span class="label label-default">Chưa giao việc</span>
                                                    </div>
                                                </div>
                                            @endif  
                                            <div class="space">
                                                <p class="header-title">Số giờ đã thực hiện</p>
                                                <ul>
                                                    <li><span class="label" style="background-color:rgba(88, 203, 238, 0.3);width:3rem;border-radius: 0.5rem;font-size:100%;color:black">{{$task->WorkedTime ?? 0}}</span></li>
                                                </ul>
                                            </div>
                                            <div class="space">
                                                <p class="header-title">Tác vụ</p>
                                                <div style="margin-top: 8px">
                                                    <div class="form-group" style="margin-bottom: 10px">
                                                        @if($task->Status > 1)
                                                            <button class="btn btn-block btn-secondary btn-task"
                                                                    onclick="reportTask({{ $task->id }}, '{{ $task->Name }}', event)">
                                                                @if(isset($error))
                                                                    <i class="fa fa-exclamation-triangle blinking text-danger"
                                                                    aria-hidden="true"></i>
                                                                    <span class="" style="font-size: 15px; padding: 0 5px">Báo
                                                                        lỗi</span>
                                                                @else
                                                                    <i class="fa fa-tasks mr-2" aria-hidden="true"></i>
                                                                    <span style="font-size: 15px; padding: 0 5px">Báo cáo</span>
                                                                @endif
                                                            </button>
                                                        @endif    
                                                    </div>
                                                </div>
                                                @if($permissions['edit'])
                                                    <div class="form-group" style="margin-bottom: 10px">
                                                        <button
                                                            class="btn btn-block btn-secondary btn-task edit-task-btn"
                                                            issue="edit"
                                                            {{--
                                                            onclick="detailTask({{ $task->id }}, event, '{{ $task->name }}', {{ $task->project_id }})"
                                                            --}}>
                                                            <i class="fa fa-pencil mr-2" aria-hidden="true"></i><span
                                                                style="font-size: 15px; padding: 0 5px">Chỉnh sửa</span>
                                                        </button>
                                                    </div>
                                                @endif
                                                @if($permissions['createChildTask'])
                                                    <div class="form-group" style="margin-bottom: 10px">
                                                        <button
                                                            class="btn btn-block btn-secondary btn-task create-child-task-btn"
                                                            issue="createChildTask"
                                                            {{--
                                                            onclick="detailTask({{ $task->id }}, event, '{{ $task->name }}', {{ $task->project_id }})"
                                                            --}}>
                                                            <i class="fa fa-users" aria-hidden="true"></i><span
                                                                style="font-size: 15px; padding: 0 5px">Tạo Task con</span>
                                                        </button>
                                                    </div>
                                                @endif
                                                {{-- @endcan --}}
                                                {{-- @can('delete-task', $task) --}}
                                                @if($permissions['delete'])
                                                    <div class="form-group" style="margin-bottom: 10px">
                                                        <button class="btn btn-block btn-secondary btn-task"
                                                                onclick="deleteTask('{{ $task->id }}')">
                                                            <i class="fa fa-trash mr-2" aria-hidden="true"></i><span
                                                                style="font-size: 15px; padding: 0 5px">Xóa task</span>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="modal-footer">--}}
            {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>--}}
            {{-- </div>--}}
        </div>
    </div>
</div>
<script type="text/javascript">
    setSelectPicker();
    var display = false
    var comment = $("#comment");

    function showMore(e, more_class) {
        var moreText = $("." + more_class);
        if (!display) {
            $(e).html("Ẩn chi tiết");
            moreText.css("display", "block");
            display = true
        } else {
            $(e).html("Hiện chi tiết");
            moreText.css("display", "none");
            display = false;
        }
    }

    $(comment).keyup(function () {
        if ($(this).val().length >= 1) {
            $("button[name='post-comment']").removeAttr('disabled');
        } else {
            $("button[name='post-comment']").attr("disabled", "disabled");
        }
    });
    $(".date-template").html(renderDateTemplate("{{ $task->StartDate }}", "{{ $task->EndDate }}"));

    @if(!is_null($task->member))
    $("#important").html(
        displayImportant(
            {{ $task->member->id }},
            {{ $task->id }},
            '@if($task->Importance == 1) important @else hide-flag  @endif',
            22
        )
    );
    @endif

    $('form[name="add-file"]').submit(event => {
        event.preventDefault();
        $("#downloading").removeClass("hide");
        var form_data = new FormData();
        var total_files = document.getElementById('formFileMultiple').files.length;
        form_data.append("note", $("#comment").val());
        for (let index = 0; index < total_files; index++) {
            form_data.append("files[]", document.getElementById('formFileMultiple').files[index]);
        }
        $.ajax({
            url: '{{ route('admin.ApiTaskUploadFile', $task->id) }}',
            type: 'POST',
            data: form_data,
            headers: {
                'Authorization': 'Bearer {{ \Illuminate\Support\Facades\Session::get('api-user') }}',
            },
            cache: false,
            contentType: false,
            enctype: 'multipart/form-data',
            processData: false,
        }).done(response => {
            successAlert(total_files);
            updateConversation(response.data.document);
        }).fail(error => {
            errorAlert(error.responseJSON.error);
            return null;
        });
    });

    $("#comment").keypress(function (e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            $(this).closest("form").submit();
        }
    });

    var successAlert = length_files => {
        let messages = $("#message");
        $("#downloading").addClass("hide");
        $('form[name="add-file"]').trigger('reset');
        $("button[name='post-comment']").attr("disabled", "disabled");
        $(messages).removeClass();
        if (length_files !== 0) {
            $(messages).addClass("alert-success_akb");
            $(messages).html(`<i class="fa fa-check" aria-hidden="true"></i><span>Thành công</span>`);
        }
    }

    var errorAlert = error => {
        let messages = $("#message");
        $("#downloading").addClass("hide");
        $(messages).removeClass().addClass("alert-danger_akb");
        $(messages).html(`<i class="fa fa-times" aria-hidden="true"></i><span>${error}</span>`)
    }


    var updateConversation = data => {
        let file_list = "";
        $('.just-send').removeClass('just-send')
        if (data.DocName != null) {
            $(data.DocName.split("?")).each(function (index, item) {
                let download_link = "{{ asset("storage/app/public/files/shares/Task/".$task->id."/") }}" + item;
                file_list += `<a class="link" target="_blank" href="${download_link}"><u>${item}</u></a>`;
            })
        }
        $("#conversation-box").prepend(`
             <div class="single-line">
                <span>
                    <b>${data.Username}</b> - ${data.DiffHuman}
                </span>
                <div class="card">
                    <div class="note action-comment just-send">
                        <div style="padding: 8px 12px;">
                            <p>${data.Note}</p>
                            ${file_list}
                        </div>
                    </div>
                </div>
            </div>
        `);
        getListDoc();
    }

    var getListDoc = () => {
        let content_file = $("div.content-file");
        $.get("{{ route('admin.TaskWorkDoc', $task->id) }}", $('form[name="search-file"]').serialize(), response => {
            $(content_file).empty();
            $('#count-result').text(response.length);
            if (response.length !== 0) {
                $('div.body-file').removeClass('hide');
                for (let k in response) {
                    let file = response[k];
                    let hide = k > 3 ? 'more-file1' : '';
                    $('#count-doc').text(` - (${parseInt(k) + 1} files)`);
                    $(content_file).append(`
                    <div class="attachment-thumbnail ${hide}">
                        <a class="attachment-thumbnail-preview" target="_blank" href="${file.downloadLink}" >
                            <span class="attachment-thumbnail-preview-ext">${file.type}</span>
                        </a>
                        <p class="attachment-thumbnail-details">
                            <a style="color: black; font-size: 15px; font-weight: 500;" class="attachment-thumbnail-name" target="_blank" href="${file.downloadLink}">
                                ${file.fileName}</a>
                            <span class="attachment-thumbnail-details-title-options">
                                <i class="fa fa-cloud-download" aria-hidden="true"></i> <span>${(parseInt(file.size) / 1000).toFixed(2)}KB</span>
                                <small class="time-file">${file.timeCreate}</small>
                            </span>
                        </p>
                    </div>
                `);
                }
            }
        })
    }
    $('.tab-bar-item').click(function (e) {
        let tab_bar_item = $('.tab-bar-item');
        $(tab_bar_item).removeClass('active-bar');
        $(tab_bar_item).find('.tab-bar-item_content').removeClass('active-bar_content');
        $('span.badge-pill').removeClass('active-badges');
        $(this).addClass('active-bar');
        $(this).find('.tab-bar-item_content').addClass('active-bar_content');
        let attr_id = $(this).attr('id');
        $('.tab-info-task').addClass('hide');
        switch (attr_id) {
            case "general-menu":
                $('.general-info').removeClass('hide');
                break;
            case "store-menu":
                $('.store-file-info').removeClass('hide');
                $('span.badge-pill').addClass('active-badges');
                break;
            case "setting-menu":
                $('.setting-info').removeClass('hide');
                break;
        }

    })

    var liveSearchFile = (one, event) => {
        event.stopPropagation();
        getListDoc();
    }

    $('select.select-file').change(function () {
        getListDoc();
    });

    $(document).ready(function () {
        let taskEndTimeColor = DEFAULT_COLOR; 
        let currentTime = new Date().getTime();
        let taskEndTime = new Date('{{$task->EndDate}}').getTime();
        let taskStatus = '{{$task->Status}}';
        if(taskStatus == '' || Number(taskStatus) < 3){
            let compare = compare2Date(currentTime,taskEndTime,'date');
            if(compare <= 0){
                taskEndTimeColor = WARNING_COLOR ;
            }else if(compare <= 1){
                taskEndTimeColor = OVERDUE_COLOR;
            }else{
                taskEndTimeColor = DEFAULT_COLOR;
            }
        }
        $('.time-end').css('background-color',taskEndTimeColor);
        switch (taskStatus) {
            case 1:
                $('.task-status').css('background-color','rgb(135,206,250,0.3)');
                break;
            case 2:
                $('.task-status').css('background-color','rgba(0, 0, 255, 0.57)');
                break;
            case 3:
                $('.task-status').css('background-color','rgba(241, 148, 34, 0.3)');
                break;
            case 4:
                $('.task-status').css('background-color','rgba(0, 255, 0, 0.3)');
                break;
            default:
                $('.task-status').css('background-color','rgb(135,206,250,0.3)');
                break;
        }
    });


    $(".edit-task-btn,.create-child-task-btn").click(function (e) {
        e.preventDefault();
        var ajaxUrl = "{{ route('admin.showTaskForm') }}";
        let issues = $(this).attr('issue');
        switch (issues) {
            case 'edit':
                var title = "Thay đổi thông tin Task";
                break;
            case 'createChildTask':
                var title = "Tạo Task con";
                break;
            default:
                var title = "Thay đổi thông tin Task";
                break;
        }
        var taskId = {{$task->id}};
        var taskStatus = '{{$task->Status}}';
        var issue = $(this).attr('issue');
        if (taskStatus != 4 || issues === 'createChildTask') {
            ajaxGetServerWithLoader(
                genUrlGet([ajaxUrl]),
                'GET',
                {
                    taskId: taskId,
                    projectId: projectId,
                    phaseId: phaseId,
                    jobId: jobId,
                    issue: issue
                },
                function (data) {
                    $('.modal-backdrop').remove();
                    $('#popupModal').empty().html(data);
                    $('.modal-title').html(title);
                    $('.detail-modal').modal('show');
                }
            );
        } else {
            showErrors('Task đã hoàn thành không được sửa');
        }

    });
</script>
