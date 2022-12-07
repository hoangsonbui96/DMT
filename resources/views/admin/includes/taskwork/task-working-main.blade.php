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

</style>
<div class="modal draggable fade in detail-modal modal-css" role="dialog" data-backdrop="static">
    <div class="modal-dialog ui-draggable" style="width: 56%;">
        <!-- Modal content-->
        <div class="modal-content drag" style="color: #42526e; background-color: #f4f5f7">
            <div class="modal-header ui-draggable-handle" style="border-bottom-color: #dbdbdb">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title" style="word-break: break-word;">Chi tiết công việc</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col col-md-7 px-5 py-3" style="padding: 0 1.5em; border-right:0.1rem solid #dbdbdb;">
                        <div class="head" style="padding-bottom: 10px;border-bottom: 1px solid #dbdbdb">
                            <form action="" method="post"></form>
                            <h4 class="title-task">{{ $task->Name }}<span
                                    id="important"></span></h4>
                            <p style="line-height: 1.6; word-wrap: break-word;">{{ $task->Description }}</p>
                        </div>
                        {{--                        <div class="body body-file hide">--}}
                        {{--                            <div class="head">--}}
                        {{--                                <div class="row">--}}
                        {{--                                    <div class="col col-md-12 col-xs-12">--}}
                        {{--                                        <div--}}
                        {{--                                            style="display: flex; justify-content: space-between; align-items: center;padding: 10px 0;">--}}
                        {{--                                            <span style="font-size: 15px;"><i class="fa fa-paperclip"></i> Đính kèm tài liệu <span--}}
                        {{--                                                    id="count-doc"></span></span>--}}
                        {{--                                            <button class="btn btn-secondary" href="javascripts:void()"--}}
                        {{--                                                    onclick="showMore(this, 'more-file')">Hiện chi tiết--}}
                        {{--                                            </button>--}}
                        {{--                                        </div>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                                <div class="content" style="padding-left: 0; padding-right: 0; min-height: 0"></div>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
                        <div class="body">
                            <div class="head px-2">
                                <div class="row">
                                    <div class="col col-md-12 col-xs-12">
                                        <div class=""
                                             style="display: flex; justify-content: space-between; align-items: center;padding: 10px 0;">
                                            <span style="font-size: 15px;"><i class="fa fa-tasks"
                                                                              aria-hidden="true"></i> Hoạt động</span>
                                            <button class="btn btn-secondary" href="javascripts:void()"
                                                    onclick="showMore(this, 'more')">Hiện chi tiết
                                            </button>
                                        </div>
                                        <form method="POST" enctype="multipart/form-data" name="add-file">
                                            <div class="form-group">
                                                <textarea id="comment" name="note" placeholder="Thảo luận..."
                                                          rows="3"
                                                          class="form-control" style="resize: none"></textarea>
                                            </div>
                                            <div class="form-group"
                                                 style="display: flex; justify-content: space-between">
                                                <input class="" type="file" id="formFileMultiple" name="files[]"
                                                       multiple/>
                                                <button name="post-comment" class="btn btn-primary pull-right"
                                                        type="submit" disabled="disabled">
                                                    <i id="downloading" class="fa fa-spinner fa-spin hide"></i>
                                                    Lưu
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div id="message"></div>
                            <div class="content" style="padding: 0" id="conversation-box">
                                @foreach($task->documents as $index => $document)
                                    <div class="single-line {{ $index > 3 ? 'more' : ''  }}">
                                        <span>
                                            <b>{{ $document->User }}</b> - {{ \Carbon\Carbon::parse($document->created_at)->diffForHumans()}}
                                        </span>
                                        <div class="card">
                                            <div class="note action-comment">
                                                <div style="padding: 8px 12px;">
                                                    <p>{{ $document->Note }}</p>
                                                    @foreach($document->DocName as $doc)
                                                        @if($doc != "")
                                                            <a href="{{ asset("storage/app/public/files/shares/Task/".$task->id."/".$doc) }}"
                                                               class="link" target="_blank"
                                                               style="margin-right: 5px;"><u>{{ $doc }}</u></a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <small></small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
{{--                    @dd($task->project()->Leader([auth()->id()])->exists())--}}
                    <div class="col col-md-5 d-flex flex-column" style="padding: 0">
                        <div class="tab-right">
                            <div class="tab-bar" style=" border-bottom:0.1rem solid #dbdbdb;">
                                <div style="display: flex; flex-direction: row; justify-content: space-between;">
                                    <div class="tab-bar-item active-bar" id="general-menu">
                                        <span class="tab-bar-item_content active-bar_content">Thông tin</span>
                                    </div>
                                    <div class="tab-bar-item" id="store-menu">
                                        <span class="tab-bar-item_content">Files
                                            @if($count_file > 0)
                                                <span class="badge badge-pill badge-primary"
                                                      style="margin-left: 4px;">{{$count_file}}</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="tab-bar-item" id="setting-menu">
                                        <span class="tab-bar-item_content">Cài đặt</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-content" style="padding: 0 15px;">
                                <div class="general-info tab-info-task">
                                    <div class="folder-view">
                                        <div id="general-info" class="">
                                            <div class="sign"></div>
                                            <div class="space">
                                                <p class="header-title">Trạng thái</p>
                                                <div style="margin-top: -3px">
                                                    @switch($task->Status)
                                                        @case(1)
                                                        <i class="dot idea" style="margin-right: 3px"></i>
                                                        <span style="font-size: 14px">Chưa thực hiện</span>
                                                        @break
                                                        @case(2)
                                                        <i class="dot progress"
                                                           style="margin-right: 3px; margin-bottom: 0"></i>
                                                        <span style="font-size: 14px">Đang thực hiện</span>
                                                        @break
                                                        @case(3)
                                                        <i class="dot review" style="margin-right: 3px"></i>
                                                        <span style="font-size: 14px">Đang duyệt</span>
                                                        @break
                                                        @default
                                                        <i class="dot finish" style="margin-right: 3px"></i>
                                                        <span style="font-size: 14px">Hoàn thành</span>
                                                        @break
                                                    @endswitch
                                                </div>
                                            </div>
                                            <div class="space">
                                                <p class="header-title">Người thực hiện</p>
                                                <div style="margin-top: -3px">
                                                    <span style="font-size: 14px">
                                                        @if(!is_null($task->members()->first()) && isset($task->members()->first()->UserID))
                                                            {{ \App\User::withTrashed()->where('id', $task->members()->first()->UserID)->first()->FullName }}
                                                        @else
                                                            Chưa có
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="space">
                                                <p class="header-title">Thời gian</p>
                                                <div class="date-template"
                                                     style="margin-top: -3px; font-size: 14px; display: inline"></div>
                                                @if(isset($text_diff))
                                                    <div style="margin-left: 10px; display: inline">
                                                        <span class="{{ $text_class }}">{{ $text_diff }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($task->NumberReturn != 0)
                                                <div class="space">
                                                    <p class="header-title">Trả lại</p>
                                                    <span style="font-size: 14px">{{ $task->NumberReturn }} lần</span>
                                                </div>
                                            @endif
                                            <div class="space">
                                                <p class="header-title">Tác vụ</p>
                                                <div style="margin-top: 8px">
                                                    <div class="form-group" style="margin-bottom: 10px">
                                                        <button class="btn btn-block btn-secondary btn-task"
                                                                onclick="reportTask({{ $task->id }}, '{{ $task->Name }}', event)">
                                                            @if(isset($error))
                                                                <i class="fa fa-exclamation-triangle blinking text-danger"
                                                                   aria-hidden="true"></i>
                                                                <span class=""
                                                                      style="font-size: 15px; padding: 0 5px">Báo lỗi</span>
                                                            @else
                                                                <i class="fa fa-tasks mr-2" aria-hidden="true"></i>
                                                                <span
                                                                    style="font-size: 15px; padding: 0 5px">Báo cáo</span>
                                                            @endif
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="store-file-info tab-info-task hide">
                                    <div class="folder-view">
                                        <div id="store-file-info" class="">
                                            <div class="sign"></div>
                                            <div class="space" style="flex: 1 1 0%;">
                                                <form name="search-file">
                                                    <div>
                                                        <input onkeyup="liveSearchFile(this, event)" type="text"
                                                               class="form-control" name="file"
                                                               placeholder="Tìm kiếm file"
                                                               onkeydown="return event.key != 'Enter';">
                                                    </div>
                                                    <div class="media-filter">
                                                        <select name="type" class="selectpicker select-file" id=""
                                                                data-width="8rem">
                                                            <option value="" selected>Loại</option>
                                                            @foreach($type_file as $type)
                                                                <option value="{{ $type }}">.{{$type}}</option>
                                                            @endforeach
                                                        </select>
                                                        <select name="userPost" class="selectpicker select-file" id=""
                                                                data-width="10rem">
                                                            <option value="" selected>Người gửi</option>
                                                            @foreach($userPost as $user)
                                                                <option
                                                                    value="{{ $user->id }}">{{ $user->FullName }}</option>

                                                            @endforeach
                                                        </select>
                                                        <select name="date" class="selectpicker select-file" id=""
                                                                data-width="10rem">
                                                            <option value="" selected>Ngày gửi</option>
                                                            <option value="1-week">Tuần này</option>
                                                            <option value="1-month">Tháng này</option>
                                                            <option value="3-month">3 tháng qua</option>
                                                            <option value="1-year">1 năm qua</option>
                                                        </select>
                                                    </div>
                                                </form>
                                                @if($count_file > 0)
                                                    <div style="padding: 5px 0"><span><span id="count-result"></span>/{{$count_file}}</span>
                                                    </div>
                                                @endif
                                                <div
                                                    style="position: relative; overflow: hidden; width: 100%; height: 100%;"
                                                    id="list-file">
                                                    <div class="scroller">
                                                        <div class="innerScroller">
                                                            <div class="body body-file">
                                                                <div class="head">
                                                                    <div class="content content-file"
                                                                         style="padding:0;overflow: auto; max-height: 46rem;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="setting-info tab-info-task hide">
                                    <div class="folder-view">
                                        <div id="setting-info" class="">
                                            <div class="sign"></div>
                                            <div class="space">
                                                <p class="header-title">Chung</p>
                                                <div style="margin-top: 8px">
                                                    @can('edit-task', $task)
                                                        <div class="form-group" style="margin-bottom: 10px">
                                                            <button class="btn btn-block btn-secondary btn-task"
                                                                    onclick="detailTask({{ $task->id }}, event, '{{ $task->Name }}', {{ $task->ProjectID }})">
                                                                <i class="fa fa-pencil mr-2"
                                                                   aria-hidden="true"></i><span
                                                                    style="font-size: 15px; padding: 0 5px">Chỉnh sửa</span>
                                                            </button>
                                                        </div>
                                                    @endcan
                                                    @can('delete-task', $task)
                                                        <div class="form-group" style="margin-bottom: 10px">
                                                            <button class="btn btn-block btn-secondary btn-task"
                                                                    onclick="deleteTask('{{ $task->id }}')">
                                                                <i class="fa fa-trash mr-2" aria-hidden="true"></i><span
                                                                    style="font-size: 15px; padding: 0 5px">Xóa task</span>
                                                            </button>
                                                        </div>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--            <div class="modal-footer">--}}
            {{--                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>--}}
            {{--            </div>--}}
        </div>
    </div>
</div>
<script type="text/javascript">
    $(".selectpicker").selectpicker();
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
    @if(!is_null($task->members()->first()) && isset($task->members()->first()->UserID))
    $("#important").html(
        displayImportant({{ \App\User::withTrashed()->where('id', $task->members[0]->UserID)->first()->id }},
            {{ $task->id }}, '@if($task->Important == 1) important @else hide-flag  @endif', 22));
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
        if(e.which === 13 && !e.shiftKey) {
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
        getListDoc();
    })
</script>
