@extends('admin.layouts.default.app')
@push('pageCss')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">


    <style>
        #page-wrapper{
            background: #49a3b7;
            overflow-x: scroll;
        }
        .page-header{
            color: white;
            border: none;
        }
    </style>

@endpush
@push('pageJs')
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.checkboxes.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.responsive.js') }}"></script>
    <script src="{{ asset('js/jquery.classyscroll.js') }}"></script>
@endpush

@section('content')
    <div id="container">
        <div class="group-top">
            <div class="col-lg-12">
                <h1 class="page-header">@lang('admin.project.projects_management')</h1>

            </div>
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-8 col-sm-12 col-xs-12">
                    {{--                    <span class="btn btn-primary" id="searchAll">Search</span>--}}
                </div>
                {{-- <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="add-dReport">
                        <form action="">
                            <button type="button" class="btn btn-primary btn-detail" id="add-new-room-btn">@lang('admin.project.add_new_project')</button>
                        </form>
                    </div>
                </div> --}}
                <div class="clear"></div>
            </div>
        </div>
        {{-- <div class="row">
            <div class="col-sm-12">
                <div class="pull-left">

                </div>
                <div class="pull-right">
                    <div id="dataTables-user_filter" class="dataTables_filter">
                        <label>
                            <form>
                                <input type="search" class="form-control input-sm" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
                                <input type='submit' value='Search' style="display: none"/>
                            </form>
                        </label>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div> --}}

        <div class="row" style="display:flex;">

            <div class="sortable-container">
                @foreach($workList as $item)
            <div class="work-list work-list-{{ $item->id }}" data-id="{{ $item->id }}">


                    <input type="hidden" name="workList[]" value="{{ $item->id }}">
                    <div class="head"><span>{{ $item->name }}</span>
                        <div class="form-group">
                        <input type="text" class="form-control wl-title-input" name="wlTitle[{{ $item->id }}]" data-id="{{ $item->id }}" style="height:26px;">
                        </div>
                        <a class="work-list-crt-btn" href="#" onclick="showWorkListAction({{ $item->id }})">...</a>
                        <ul class="work-list-action wl-{{ $item->id }}" style="display:none;">
                            <li class="wl-action-head">Thao tác</li>
                            <li class="wl-li del-work-list" onclick="softDeleteWorkList({{ $item->id }})"  data-id="{{ $item->id }}">Lưu trữ danh sách này</li>
                        </ul>
                    </div>
                <div class="sortAble connectedSortable sortAble-{{ $item->id }}" data-id="{{ $item->id }}">
                    @foreach($item->works as $work)
                    <div class="sort-item" data-id="{{ $work->id }}"><span>{{ $work->name }}</span>
                    <input type="hidden" name="work[]" value="{{ $work->id }}">
                    </div>
                    @endforeach
                    <div class="sort-item" data-id="0" style="display:none;"></div>

                    </div>
                    <div class="clear"></div>
                    <div class="new-work">
                        <div class="new">
                            <span><i class="fa fa-plus"></i> Thêm thẻ</span>
                            <form class="form-new-work-list" style="display:none;">
                                <div class="form-group">
                                <input type="hidden" name="workList" value="{{ $item->id }}">
                                    <textarea name="work" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-success btn-add-work">@lang('admin.btnSave')</button>
                                    <button class="btn btn-danger">@lang('admin.btnCancel')</button>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
                @endforeach
            </div>
            <div class="new-work-list">
                <div class="new">
                    <span><i class="fa fa-plus"></i> Thêm danh sách công việc</span>
                    <form class="form-new-work-list" style="display:none;">
                        <div class="form-group">
                            <input type="text" name="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-success btn-add-work-list">Thêm danh sách</button>
                            <button class="btn btn-danger">Hủy</button>
                        </div>
                    </form>
                </div>

            </div>


        </div>

        <div id="popupModal">

        </div>

    </div>

    <script>
    $( function() {
        var projectId = '{{ $projectId }}';
        $( ".sortAble" ).sortable({
            connectWith: ".connectedSortable",
            items: ".sort-item",
            stop: function( event, ui ) {
                var object = ui.item;
                var workId = object.attr("data-id");
                var WorkListId = object.parent().attr("data-id");
                var arr = [];
                $(".sortAble-"+WorkListId+" input[name='work[]']").each(function(data){
                    arr.push({name: 'id[]', value: $(this).val()});
                });
                arr.push({name: 'workList', value: WorkListId});
                arr.push({name: 'work', value: workId});
                console.log(arr);
                // return;
                $.ajax({
                    url: "{{ route('admin.TaskUpdateWorkOrder') }}",
                    type: 'post',
                    data: arr,
                    success: function (data) {
                        // console.log(data);

                    },
                    fail: function (error) {
                        console.log(error);
                    }
                });
                // $(".work-list").change(function(){
                //     console.log(1);
                // });
            }
        }).disableSelection();
        $(".sortable-container").sortable({
            axis: 'x',
            stop: function( event, ui ) {
                var arr = [];
                $("input[name='workList[]']").each(function(data){
                    arr.push({name: 'id[]', value: $(this).val()});
                });
                // console.log(arr);
                $.ajax({
                    url: "{{ route('admin.TaskUpdateWorkListOrder') }}",
                    type: 'post',
                    data: arr,
                    success: function (data) {
                        // console.log(data);

                    },
                    fail: function (error) {
                        console.log(error);
                    }
                });
            }
        });
        $(".sortable-container").disableSelection();

        function saveWorkOrder(){

        }
        // $("body").on('DOMSubtreeModified', ".sortAble", function() {
        //     console.log('changed');
        // });
        $(document).on('click', ".btn-danger", function(e) {
            e.preventDefault();
            // $(".form-new-work-list").slideUp();
            $(this).closest('form').animate({ height: 'toggle', opacity: 'toggle' }, 'fast');

        });
        $(document).on('click', ".new span", function() {
            $(".form-new-work-list").slideUp("fast");
            $(this).closest('div').find('form').animate({ height: 'toggle', opacity: 'toggle' }, 'fast');
        });
        //thêm công việc
        // $(".btn-add-work").click(function(e){
        $(document).on('click', ".btn-add-work", function(e) {
            e.preventDefault();
            var arr = $(this).closest('form').serializeArray();
            // arr.push({name : 'workListId' , value : projectId});
            // console.log(arr[0].value);
            $(this).closest('form')[0].reset();
            $(this).closest('form').hide();
            $.ajax({
                url: "{{ route('admin.TaskNewWork') }}",
                type: 'post',
                data: arr,
                success: function (data) {
                    if (typeof data.errors !== 'undefined'){
                        // $('.loadajax').hide();

                        showErrors(data.errors);
                    }else{
                        // console.log(data);
                        var encodedStr = data.name.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
                            return '&#'+i.charCodeAt(0)+';';
                        });
                        var html = `<div class="sort-item" data-id="`+data.id+`"><span>`+encodedStr+`</span>
                            <input type="hidden" name="work[]" value="`+data.id+`">
                        </div>`;

                        $(".sortAble-"+arr[0].value).append(html);

                    }

                },
                fail: function (error) {
                    console.log(error);
                }
            });

            });


        //thêm danh sách công việc
        // $(".btn-add-work-list").click(function(e){
        $(document).on('click', ".btn-add-work-list", function(e) {
            e.preventDefault();
            var arr = $(this).closest('form').serializeArray();
            arr.push({name : 'projectId' , value : projectId});
            console.log(arr);
            $(this).closest('form')[0].reset();
            $.ajax({
                url: "{{ route('admin.TaskNewWorkList') }}",
                type: 'post',
                data: arr,
                success: function (data) {
                    if (typeof data.errors !== 'undefined'){
                        // $('.loadajax').hide();

                        showErrors(data.errors);
                    }else{
                        console.log(data);
                        var encodedStr = data.name.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
                            return '&#'+i.charCodeAt(0)+';';
                        });
                        var html = `
                        <div class="work-list work-list-`+data.id+`" data-id="`+data.id+`">


                            <input type="hidden" name="workList[]" value="`+data.id+`">
                            <div class="head"><span>`+encodedStr+`</span>
                                <div class="form-group">
                                <input type="text" class="form-control wl-title-input" name="wlTitle[`+data.id+`]" data-id="`+data.id+`" style="height:26px;">
                                </div>
                                <a class="work-list-crt-btn" href="#" onclick="showWorkListAction(`+data.id+`)">...</a>
                                <ul class="work-list-action wl-`+data.id+`" style="display:none;">
                                    <li class="wl-action-head">Thao tác</li>
                                    <li class="wl-li" onclick="softDeleteWorkList(`+data.id+`)">Lưu trữ danh sách này</li>
                                </ul>
                            </div>
                            <div class="sortAble connectedSortable sortAble-`+data.id+`" data-id="`+data.id+`">

                            <div class="sort-item" data-id="0" style="display:none;"></div>

                            </div>
                            <div class="clear"></div>
                            <div class="new-work">
                                <div class="new">
                                    <span><i class="fa fa-plus"></i> Thêm công việc</span>
                                    <form class="form-new-work-list" style="display:none;">
                                        <div class="form-group">
                                        <input type="hidden" name="workList" value="`+data.id+`">
                                            <textarea name="work" class="form-control"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-success btn-add-work">Lưu</button>
                                            <button class="btn btn-danger">Hủy</button>
                                        </div>
                                    </form>
                                </div>

                            </div>

                            </div>`;
                        $(".sortable-container").append(html);

                        $( ".sortAble" ).sortable({
                            connectWith: ".connectedSortable",
                            items: ".sort-item",
                            stop: function( event, ui ) {
                                var object = ui.item;
                                var workId = object.attr("data-id");
                                var WorkListId = object.parent().attr("data-id");
                                var arr = [];
                                $(".sortAble-"+WorkListId+" input[name='work[]']").each(function(data){
                                    arr.push({name: 'id[]', value: $(this).val()});
                                });
                                arr.push({name: 'workList', value: WorkListId});
                                arr.push({name: 'work', value: workId});
                                console.log(arr);
                                // return;
                                $.ajax({
                                    url: "{{ route('admin.TaskUpdateWorkOrder') }}",
                                    type: 'post',
                                    data: arr,
                                    success: function (data) {
                                        // console.log(data);

                                    },
                                    fail: function (error) {
                                        console.log(error);
                                    }
                                });
                                // $(".work-list").change(function(){
                                //     console.log(1);
                                // });
                            }
                        }).disableSelection();
                    }

                },
                fail: function (error) {
                    console.log(error);
                }
            });

        });




        // $(".work-list-action").click(function(event){
        //     event.stopPropagation();
        // });
        // $('html').click(function() {
        //     $('.work-list-action').hide();
        // });

        $(document).on('click', ".head>span", function(e) {
            $(".head .form-group").hide();
            $(this).closest("div").find(".form-group").show();
            $(this).closest("div").find("input").focus();
            $(this).closest("div").find(".form-group input").val($(this).text());
        });
        //autocomplete save

        //setup before functions
        var typingTimer;                //timer identifier
        var doneTypingInterval = 1000;  //time in ms (5 seconds)

        //on keyup, start the countdown
        $('.wl-title-input').keyup(function(e){
            if(e.keyCode == 13){
                $(".head .form-group").hide();
            }
            clearTimeout(typingTimer);
            var workListId = $(this).attr("data-id");
            var workListTitle = $(this).val();
            $(this).closest(".head").find("span").text(workListTitle);
            if ($(this).val()) {
                typingTimer = setTimeout(function(){
                    $.ajax({
                        url: "{{ route('admin.TaskUpdateWorkListTitle') }}",
                        type: 'post',
                        data: {id: workListId, title: workListTitle},
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





    });

    function showWorkListAction(i){
        // $(".work-list-action").slideUp();
        $(".wl-"+i).animate({ height: 'toggle', opacity: 'toggle' }, 'fast');
    }
    function softDeleteWorkList(id){
        $.ajax({
            url: "{{ route('admin.TaskDeleteWorkList') }}/"+id,
            success: function (data) {
                if (typeof data.errors !== 'undefined'){
                    // $('.loadajax').hide();

                    showErrors(data.errors);
                }else{
                    // console.log(data);
                    $(".work-list-"+id).remove();
                }

            },
            fail: function (error) {
                console.log(error);
            }
        });
    }
    $(document).on('click', ".quick-edit-title>span", function(e) {
        $(this).hide();
        $(".quick-edit-title .form-group").hide();
        $(".quick-edit-title>span").show();
        $(this).closest("div").find(".form-group").show();
        $(this).closest("div").find("input").focus();
        $(this).closest("div").find(".form-group input").val($(this).text());
    });
    $(document).on('click', ".work-user li", function(e) {
        var object = $(this).find("i");
        var workId = $(this).closest('ul').attr("work-id");
        var t = object.is(":visible");
        if(t){
            object.hide();
        }else{
            object.show();
        }
        var html = "";
        var id = [];
        $(".wk-li-user i").each(function(){
            var dataName = getShortName($(this).closest("li").attr("data-name"));
            var dataId = $(this).closest("li").attr("data-id");
            if($(this).is(":visible")){
                html += `<li>`+dataName+`</li>`;
                id.push(dataId);
            }
        });

        if(isEmpty(html)){
            $(".h3-members").hide();
        }else{
            $(".h3-members").show();
        }
        $.ajax({
            url: "{{ route('admin.showWork') }}",
            type: "post",
            data: {action: "updateMember", listMember: id, workId: workId},
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
        $(".work-member ul").html(html);
    });


    //them mo ta cho work
    $(document).on('click', ".work-desc-detail .desc", function(e) {
        $(".work-desc-detail textarea").val($(this).find('.txt-desc').text())
        $(".work-desc-detail textarea").show();
        $(".work-desc-detail").find("textarea").focus();
        $('.desc').hide();
    });

    //them nhiem vu

    $(document).on('click', ".work-task .btn", function(e) {
        var workId = $(this).closest('ul').attr('work-id');
        var taskTitle = $("[name='newTask']").val();
        var taskType = $("select[name='taskType']").val();
        $.ajax({
            url: "{{ route('admin.showWork') }}",
            type: 'post',
            data: {workId: workId, taskTitle: taskTitle, action: 'newTask', taskType: taskType},
            success: function (data) {
                if (typeof data.errors !== 'undefined'){
                    // $('.loadajax').hide();

                    showErrors(data.errors);
                }else{
                    // console.log(data);
                    var encodedStr = data.name.replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
                            return '&#'+i.charCodeAt(0)+';';
                        });
                    var html = `
                    <div class="task-item" data-id="`+data.id+`">
                        <h4 class="quick-edit-title"><i class="fa fa-circle" style="font-size: 20px"></i> <span>`+encodedStr+`</span>
                            <div class="form-group">
                                <input type="text" class="form-control task-title-input" data-id="`+data.id+`" style="height:26px;">
                            </div>
                        </h4>
                        <div class="btn btn-default btn-task"><i class="fa fa-plus"></i></div>
                        <div class="task-history">
                            <table class="table table-bordered table-task-history">
                                <thead>
                                    <tr>
                                        <th>Ngày</th>
                                        <th>Bắt đầu</th>
                                        <th>Kết thúc</th>
                                        <th>Nghỉ(giờ)</th>
                                        <th>Thành viên</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>

                            </table>

                        </div>
                        <div class="new-th" data-id="`+data.id+`">
                            <div><input type="text" name="date" class="form-control dpicker" placeholder="Ngày" value="`+nextDate+`"></div>
                            <div><input type="text" name="STime" class="form-control tpicker" placeholder="Giờ bắt đầu" value="`+nextStartTime+`"></div>
                            <div><input type="text" name="ETime" class="form-control tpicker" placeholder="Giờ kết thúc"></div>
                            <div><input type="text" name="total" class="form-control" placeholder="Thời gian nghỉ" value="0"></div>
                            <div style="margin-bottom: 0px;text-align:right;">
                                <span class="btn btn-success">Lưu</span>
                                <span class="btn btn-danger">Thoát</span>
                            </div>

                        </div>

                    </div>






                    `;

                    $(".work-tasks").append(html);
                    $(".work-task").hide();
                }

            },
            fail: function (error) {
                console.log(error);
            }
        });
    });
    //end them nhiem vu

    $(document).on('keyup', ".task-title-input", function(e) {
        if(e.keyCode == 13){
            $(this).closest('div').hide();
            $(this).closest('.quick-edit-title').find('span').show();
        }
        clearTimeout(typingTimer);
        var taskId = $(this).attr("data-id");
        var taskTitle = $(this).val();
        $(this).closest(".quick-edit-title").find("span").text(taskTitle);

        if ($(this).val()) {
            typingTimer = setTimeout(function(){
                $.ajax({
                    url: "{{ route('admin.updateTask') }}",
                    type: 'post',
                    data: {taskId: taskId, title: taskTitle, action: 'updateTitle'},
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
    $(document).on("click", ".btn-task", function(){
        // $('.fa').removeClass('fa-minus');
        // $('.fa').addClass('fa-plus');
        var obj = $(this).find(".fa");
        obj.toggleClass('fa-plus');
        obj.toggleClass('fa-minus');
        obj.closest('.task-item').find('.task-history').toggle();
        $(".new-th").hide();
        // $('.task-history').hide();
        // $(this).closest('.task-item').find(".new-th").toggle();
        // if(obj.closest('.task-item').find('.task-history').is(":visible")){
        //     obj.closest('.task-item').find('.task-history').hide();
        // }else{
        //     obj.closest('.task-item').find('.task-history').show();
        // }
    });
    $(document).on("click", ".task-history>span.btn-success", function(){
        $(".new-th").hide();
        $(this).closest(".task-item").find(".new-th").show();
    });
    $(document).on("click", ".new-th>div>span.btn-danger", function(){
        $(".new-th").hide();
    });

    $(document).on('click', ".sort-item", function(e) {
        // console.log($(this).attr('data-id'));
        var title = $(this).find("span").text();
        // $('.loadajax').show();
        $.ajax({
            url: "{{ route('admin.showWork') }}/"+$(this).attr('data-id'),
            success: function (data) {

                $('#popupModal').empty().html(data);
                // $('.modal-title').text(title);
                // $('#user-form')[0].reset();
                $('.detail-modal').modal('show');
                // $('.loadajax').hide();
                $(".new-th input[name='STime']").val(nextStartTime);
                $(".new-th input[name='date']").val(nextDate);
            }
        });
    });

    var nextStartTime = "{{ isset($lastTaskHistory) ? Carbon\Carbon::parse($lastTaskHistory->end_time)->addMinute()->format('H:i') : '' }}";
    var nextDate = '{{ isset($lastTaskHistory) ? $lastTaskHistory->date : '' }}';
    $(document).on("click", ".new-th .btn-success", function(){
        var dataId = $(this).closest('.new-th').attr('data-id');
        // console.log(dataId);
        var date = $(".new-th[data-id='"+dataId+"'] input[name='date']").val();
        var STime = $(".new-th[data-id='"+dataId+"'] input[name='STime']").val();
        var ETime = $(".new-th[data-id='"+dataId+"'] input[name='ETime']").val();
        var total = $(".new-th[data-id='"+dataId+"'] input[name='total']").val();
        var taskId = $(this).closest(".new-th").attr("data-id");
        // console.log(taskId);
        // console.log(STime+','+ETime+','+date);
        if(STime >= ETime){
            coolAlert("Thời gian không hợp lệ!");
            return;
        }
        var dif = diff(STime, ETime);
        // console.log(dif);
        arr = dif.split(":");
        var hour = parseFloat(arr[0]);
        var minute =  parseFloat((arr[1]/60).toFixed(2));
        var hour = hour + minute;
        if(total>=hour){
            coolAlert("Thời gian làm không hợp lệ!");
            return;
        }
        $.ajax({
            url: "{{ route('admin.updateTask') }}",
            type: 'post',
            data: {taskId: taskId, date: date, STime: STime, ETime: ETime, total: total, action: 'newTaskHistory'},
            success: function (data) {
                if (typeof data.errors !== 'undefined'){
                    // $('.loadajax').hide();
                    showErrors(data.errors);
                    // console.log(data.errors)
                }else{
                    // console.log(data);
                    var html = `<tr>
                                <td>`+data.date+`</td>
                                <td>`+data.start_time+`</td>
                                <td>`+data.end_time+`</td>
                                <td>`+data.rest_time+`</td>
                                <td>`+data.FullName+`</td>
                                </tr>
                    `;
                    nextStartTime = data.nextStartTime;
                    nextDate = data.date;
                    $(".task-item[data-id='"+data.task_id+"']").find('tbody').append(html);
                    $(".new-th input[name='STime']").val(nextStartTime);
                    $(".new-th input[name='ETime']").val('');
                    $(".new-th input[name='date']").val(nextDate);
                }
            },
            fail: function (error) {
                console.log(error);
            }
        });

    });
    //danh dau task là hoàn thành

    $(document).on('click', ".task-item .fa-check-square-o", function(e) {
        $(this).closest("h4").find("span").toggleClass("task-complete");
        var taskId = $(this).closest('.task-item').attr('data-id');
        $.ajax({
            url: "{{ route('admin.updateTask') }}",
            type: 'post',
            data: {taskId: taskId, action: 'updateStatus'},
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
    });










    $('body').click(function(e) {

        var target = $(e.target);

        if (!target.is('.head input') && !target.is('.head>span')) {
            $('.head .form-group').hide();
        }
        if (!target.is('.work-list-action') && !target.is('.work-list-action li') && !target.is('.work-list-crt-btn')) {
            $('.work-list-action').slideUp("fast");
        }
        // console.log(target.is('.work-task-action'));
        if (!target.is('.work-task-action') && !target.is('.work-task-action li') && !target.is('.open-work-task-action') && !target.is('.work-task-action li input') && !target.is('.work-task-action li span')   && !target.is('.work-task-action li span')  && !target.is('.work-task-action li select')  && !target.is('.work-task-action li button')   && !target.is('.work-task-action li div')) {
            $('.work-task-action').slideUp("fast");
        }
        if (!target.is('.work-desc-detail') && !target.is('.work-desc-detail textarea') && !target.is('.work-desc-detail .desc')) {
            $('.work-desc-detail textarea').hide();
            $('.desc').show();
        }

        if (!target.is('.quick-edit-title input') && !target.is('.quick-edit-title>span')) {
            $('.quick-edit-title .form-group').hide();
            $(".quick-edit-title>span").show();
        }
    // console.log(target.is('.work-list-action'));
    })
    </script>

@endsection
