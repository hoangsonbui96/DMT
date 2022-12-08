@extends('admin.layouts.default.app')
@push('pageJs')
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
          rel="stylesheet">
    <link href="{{ asset('css/work-task/style.css') }}" rel="stylesheet">
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/support-common.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/web-animations/2.3.2/web-animations.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/haltu/muuri@0.9.3/dist/muuri.min.js"></script>
    <style>
        .task-child {
            position: relative;
            background-color: orange;
            text-align: left;
            margin-right: 10px;
        }
        .task-child:before,
        .task-child:after {
            content: '';
            position: absolute;
            background-color: inherit;
        }
        .task-child,
        .task-child:before,
        .task-child:after {
            width:  16px;
            height: 16px;
            border-top-right-radius: 30%;
        }

        .task-child {
            transform: rotate(-60deg) skewX(-30deg) scale(1,.866);
        }
        .task-child:before {
            transform: rotate(-135deg) skewX(-45deg) scale(1.414,.707) translate(0,-50%);
        }
        .task-child:after {
            transform: rotate(135deg) skewY(-45deg) scale(.707,1.414) translate(50%);
        }
        
        .card-body .task-prop {
            flex-direction: row;
            display: flex;
            /* justify-content: center; */
            align-items: center;
            margin: 0.15rem 0.25rem 0.25rem 0.25rem;
            /* border: solid 1px black; */
            width: 100%;
            /* height: 4rem; */
            /* background-color: #d3d3d3; */
            color: black;
            /* border-radius: 6px; */
                
            background-color: rgba(220,220,220, .3) ;
            padding: 5px 5px;
            height: 100%;
            flex-direction: row;
            justify-content: space-evenly;
            border-radius: 0.45rem;
            text-align: center;
        }

        .card-body .task-info {
            flex-direction: row;
            display: flex;
            justify-content: space-between;
            font-size: 1.75rem;
            margin: 0rem 0.25rem 0.25rem 0.25rem;
           
        }
        .card-body .task-info a:hover{
            text-decoration: none;
        }

        .card-body .task-info > div {
            flex-wrap: wrap;
        }

        .task-prop > i {
            color: black;
            font-size: 1.75rem;
        }

        .task_prop_left {
            width: 30%;
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            color: #12233f;
        }

        .task_prop_right {
            min-width: 105.2px;
            font-size: 14px;
            color: #12233f;
        }

        .numberPhase {
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            justify-content: center;
            vertical-align: middle;
            align-items: center;
            color: #fff;
            text-align: center;
            font: 10px Arial, sans-serif;
        }
        .numberJob {
            border-radius: 3px;
            width: 28px;
            height: 28px;
            display: flex;
            justify-content: center;
            vertical-align: middle;
            align-items: center;
            color: #fff;
            text-align: center;
            margin-left: 5px;

            font: 10px Arial, sans-serif;
        }
        .tags {  
            display: flex;
            align-items: center;
            max-width: 65%;
        }

        .tags a {    
            display: inline-block;
            height: 16px;
            line-height: 16px;
            position: relative;
            margin: 4px 8px 4px 4px;
            padding: 0 10px 0 12px;
            background-color: grey ;  
            -webkit-border-bottom-right-radius: 3px;    
            border-bottom-right-radius: 3px;
            -webkit-border-top-right-radius: 3px;    
            border-top-right-radius: 3px;
            -webkit-box-shadow: 0 1px 2px rgba(0,0,0,0.2);
            box-shadow: 0 1px 2px rgba(0,0,0,0.2);
            color: white;
            font-size: 12px;
            /* font-family: "Lucida Grande","Lucida Sans Unicode",Verdana,sans-serif; */
            text-decoration: none;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        .tags a:before {
            content: "";
            position: absolute;
            top:0;
            left: -8px;
            width: 0;
            height: 0;
            border-color: transparent grey transparent transparent;
            border-style: solid;
            border-width: 8px 8px 8px 0;        
        }

        .tags a:after {
            content: "";
            position: absolute;
            top: 5px;
            left: 1px;
            float: left;
            width: 5px;
            height: 5px;
            -webkit-border-radius: 50%;
            border-radius: 50%;
            background: #fff;
            -webkit-box-shadow: -1px -1px 2px rgba(0,0,0,0.4);
            box-shadow: -1px -1px 2px rgba(0,0,0,0.4);
        }

        .list-items::-webkit-scrollbar
        {
            width: 5px;
            background-color: #F5F5F5;
        }

        .selected-sort {
            background: #cdd2d4 !important;
        }

        .content-wrapper {
            min-height: 100vh !important;
        }
        .lists-container {
            display: grid;
            grid-auto-columns: 24.7%;
            grid-auto-flow: column;
            grid-column-gap: 0.5rem;
        }

        .list {
            display: grid;
            grid-template-rows: auto minmax(auto, 1fr) auto;
        }

        .list-items {
            display: grid;
            grid-row-gap: 0.6rem;
        }

        .list,
        .list-items li {
            margin: 0;
            border-radius: 10px;
        }

        .date-input{
            padding-left: 1.25rem;
        }
        .ellipsis-name {
            width: 21em;
            white-space: nowrap;
            overflow: hidden !important;
            text-overflow: ellipsis;
        }
        .header-list {
            display: flex;
            justify-content: space-between;
            column-gap: 2;
            padding: 0 10px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        .header-sort {
            display:flex;
            padding:0;
        }
        .task-sort {
            display: flex !important;
            align-items: center !important;
        }
        
    </style>
@endpush

@section('content')
    <section class="content-header" style="margin-bottom: 20px">
        <h1 class="page-header" style="display: inline">
            {{-- <a href="javascript:void(0)" id="hiddenDetail"> --}}
                Dự án: {{ $project->NameVi }}
                @if($project->NameShort != '')
                    ({{ $project->NameShort }})
                @endif
            {{-- </a> --}}
        </h1>
        <a href="javascript:" class="btn btn-primary pull-right" onclick="comeBack()"><i class="fa fa-arrow-left"
                                                                                         aria-hidden="true"></i></a>
    </section>
    <section class="content" style="padding-top: 0">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form class="form-inline" id="list-project-task-form" action="" method="GET">
                    <input type="hidden" name="projectId" value="{{$project->id}}">
                    <div class="table-responsive table-detail" hidden>
                        @component('admin.component.table')
                            @slot('columnsTable')
                                <tr>
                                    <th>Tiếng Nhật</th>
                                    <th>@lang('admin.task-working.customer')</th>
                                    <th>Mô tả</th>
                                    <th>Thành viên</th>
                                    <th>@lang('admin.task-working.task_total')</th>
                                    <th>@lang('admin.task-working.total_time')</th>
                                    <th>@lang('admin.task-working.progress')</th>
                                    <th>@lang('admin.startDate')</th>
                                    <th>@lang('admin.endDate')</th>
                                </tr>
                            @endslot
                            @slot('dataTable')
                                <tr>
                                    <td>{{$project->NameJa}}</td>
                                    <td>{{$project->Customer}}</td>
                                    <td>{{$project->Description}}</td>
                                    <td>{{count($project->members)}}</td>
                                    <td>{{count($project->tasks)}}</td>
                                    <td>N/A</td>
                                    <td>N/A</td>
                                    <td>{{ isset($project->StartDate) ? FomatDateDisplay($project->StartDate, FOMAT_DISPLAY_DAY) :
                            null }}</td>
                                    <td>{{ isset($project->EndDate) ? FomatDateDisplay($project->EndDate, FOMAT_DISPLAY_DAY) : null
                            }}</td>
                                </tr>
                            @endslot
                            @slot('pageTable')
                                <nav id="paginator"></nav>
                            @endslot
                        @endcomponent
                    </div>
                    {{-- search box --}}
                    <div class="form-group" style="width: 100%;">
                        <div class="from-group pull-left">
                            <div class="btn-group show-tick show-menu-arrow margin-r-5" id="">
                                <input class="form-control" name="Keywords" placeholder="Nhập từ khóa..."
                                       autocomplete="off"
                                       data-role="tagsinput" value="{{isset($request['Keywords']) ? $request['Keywords'] : ''}}">
                            </div>

                            <div class="form-group margin-r-5">
                                <div class="btn-group bootstrap-select show-tick show-menu-arrow"
                                     id="action-select-user">
                                    <select class="selectpicker show-tick show-menu-arrow" id="select-phase"
                                            name="phaseId"
                                            title="Chọn Phase" data-live-search="true" data-size="5"
                                            data-live-search-placeholder="Tìm kiếm theo Phase" data-actions-box="true"
                                            tabindex="-98">
                                        <option value="">Chọn Phase...</option>
                                        {!! GenHtmlOption($project->phases, 'id', 'name', isset($request['phaseId']) ?
                                        $request['phaseId'] : null)
                                        !!}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group margin-r-5">
                                <div class="btn-group bootstrap-select show-tick show-menu-arrow"
                                     id="action-select-user">
                                    <select class="selectpicker show-tick show-menu-arrow" id="select-job" name="jobId"
                                            title="Chọn Job" data-live-search="true" data-size="5"
                                            data-live-search-placeholder="Tìm kiếm theo Job"
                                            data-actions-box="true"
                                            tabindex="-98">
                                        <option value="">Chọn Job...</option>
                                        {!! GenHtmlOption($project->jobs, 'id', 'name', isset($request['jobId']) ?
                                        $request['jobId'] : null)
                                        !!}
                                    </select>
                                </div>
                            </div>
                            @if($permissions['create'])
                                <div class="form-group margin-r-5">
                                    <div class="btn-group bootstrap-select show-tick show-menu-arrow"
                                         id="action-select-user">
                                        <select class="selectpicker show-tick show-menu-arrow" id="select-task-user"
                                                name="taskUserIds[]"
                                                multiple title="@lang('admin.chooseUser')" data-live-search="true"
                                                data-size="5"
                                                data-live-search-placeholder="Tìm kiếm theo nhân viên" data-actions-box="true"
                                                data-done-button="true"
                                                data-deselectAllText="Bỏ chọn hết"
                                                data-selectAllText="Chọn hết"
                                                tabindex="-98">
                                            {!! GenHtmlOption($allUsers, 'id', 'FullName', isset($request['taskUserIds']) ?
                                            $request['taskUserIds'] : null)
                                            !!}
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="btn-group show-tick show-menu-arrow margin-r-5" id="">
                                <div class="input-group date">
                                    <input type="text" class="form-control datepicker"
                                           placeholder="@lang('admin.startDate')" name="startTime" autocomplete="off" value="{{$request['startTime'] ?? ''}}">
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>

                            <div class="btn-group show-tick show-menu-arrow margin-r-5" id="">
                                <div class="input-group date">
                                    <input type="text" class="form-control datepicker"
                                           placeholder="@lang('admin.endDate')"
                                           name="endTime"  value="{{$request['endTime'] ?? ''}}" autocomplete="off">
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-search pull-right"
                                    id="btn-search">@lang('admin.btnSearch')</button>
                        </div>
                        <div class="form-group pull-right">
                            @if($permissions['create'])
                                <button type="button" class="btn btn-primary add-task-btn" item-status="0"
                                        id="btn-new-task">@lang('admin.task-working.new-task')</button>
                            @endif
                            {{-- @can('export-task')--}}
                            {{-- <button type="button" class="btn btn-success" --}} {{--
                            id="btn-export">@lang('admin.export-excel')</button>--}}
                            {{-- @endcan--}}
                        </div>
                    </div>
                    {{-- end search box --}}
                </form>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <section class="lists-container">
                    <div class="list">
                        <div class="header-list header-border idea-bg">
                            <div class="header-sort">
                                <button style="border: none; outline: none; background-color: transparent"
                                    {{-- onclick="openMenu(this, 1)"  --}}
                                    tabindex="0" data-toggle="tooltip" title="Thao tác">
                                    <span name="amount_task" class="bx-shadow idea"></span>
                                </button>
                                <h3 class="list-title "
                                        style="display: flex; flex-direction: row; justify-content: space-between">
                                        @lang('admin.task-working.column_unfinished')
                                </h3>
                            </div>
                            <div class="task-sort">
                                <div class="btn-group bootstrap-select show-tick show-menu-arrow" id="">
                                    <select class="selectpicker show-tick show-menu-arrow" id="" onchange="sortTask(this)" name="sort-task" data-item="list-not-finish" data-status="1">
                                        <option value="sort-phase">Sắp xếp theo Phase</option>
                                        <option value="sort-job">Sắp xếp theo Job</option>
                                        <option value="sort-user">Sắp xếp theo Người thực hiện</option>
                                        <option value="sort-start">Sắp xếp theo Ngày bắt đầu</option>
                                        <option value="sort-alphabet">Sắp xếp theo Tên Task</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <ul class="list-items" name="father-old" id="list-not-finish"
                            data-item="@lang('admin.task-working.value_unfinished')">
                        </ul>
                        @if($permissions['create'])
                            <a href="javascript:void(0)" data-item="1" class="open-task add-task-btn" item-status="1">
                                <span><i class="fa fa-plus" aria-hidden="true"></i></span>
                                <span style="margin-left: 1em; font-weight: 100 !important;">Thêm task mới</span>
                            </a>
                        @endif
                    </div>
                    <div class="list">
                        <div class="header-list header-border progress-bg">
                            <div class="header-sort">
                                <button style="outline: none; border: none; background-color: transparent" tabindex="0"
                                        data-toggle="tooltip" title="Thao tác" 
                                        {{-- onclick="openMenu(this, 2)" --}}
                                        >
                                    <span name="amount_task" class="bx-shadow progress"></span>
                                </button>
                                <h3 class="list-title "
                                    style="display: flex; flex-direction: row; justify-content: space-between">
                                    @lang('admin.task-working.column_working')
                                </h3>
                            </div>
                            <div class="task-sort">
                                <div class="btn-group bootstrap-select show-tick show-menu-arrow" id="">
                                    <select class="selectpicker show-tick show-menu-arrow" id="" onchange="sortTask(this)" name="sort-task" data-item="list-working" data-status="2">
                                        <option value="sort-phase">Sắp xếp theo Phase</option>
                                        <option value="sort-job">Sắp xếp theo Job</option>
                                        <option value="sort-user">Sắp xếp theo Người thực hiện</option>
                                        <option value="sort-start">Sắp xếp theo Ngày bắt đầu</option>
                                        <option value="sort-alphabet">Sắp xếp theo Tên Task</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <ul class="list-items" name="father-new" id="list-working"
                            data-item="@lang('admin.task-working.value_working')">
                        </ul>
                        {{-- @can('create-task', $project) --}}
                        {{-- <a href="javascript:void(0)" data-item="2" class="open-task add-task-btn" item-status="2">
                            <span><i class="fa fa-plus" aria-hidden="true"></i></span>
                            <span style="margin-left: 1em; font-weight: 100 !important;">Thêm task mới</span>
                        </a> --}}
                        {{-- @endcan --}}
                    </div>

                    <div class="list">
                        <div class="header-list header-border review-bg">
                            <div class="header-sort">
                                <button style="outline: none; border: none; background-color: transparent" tabindex="0"
                                        data-toggle="tooltip" title="Thao tác" 
                                        {{-- onclick="openMenu(this, 3)" --}}
                                        >
                                    <span name="amount_task" class="bx-shadow review"></span>
                                </button>
                                <h3 class="list-title "
                                    style="display: flex; flex-direction: row; justify-content: space-between">
                                    @lang('admin.task-working.column_review')
                                </h3>
                            </div>
                            <div class="task-sort">
                                <div class="btn-group bootstrap-select show-tick show-menu-arrow" id="" style="padding-right: 10px">
                                    <select class="selectpicker show-tick show-menu-arrow" id="" onchange="sortTask(this)" name="sort-task" data-item="list-review" data-status="3">
                                        <option value="sort-phase">Sắp xếp theo Phase</option>
                                        <option value="sort-job">Sắp xếp theo Job</option>
                                        <option value="sort-user">Sắp xếp theo Người thực hiện</option>
                                        <option value="sort-start">Sắp xếp theo Ngày bắt đầu</option>
                                        <option value="sort-alphabet">Sắp xếp theo Tên Task</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <ul class="list-items" id="list-review" data-item="@lang('admin.task-working.value_review')">
                        </ul>
                    </div>
                    {{-- @if($permissions['create']) --}}
                        <div class="list">
                            <div class="header-list header-border finish-bg">
                                <div class="header-sort">
                                    <button style="outline: none; border: none; background-color: transparent" tabindex="0"
                                            data-toggle="tooltip" title="Số Task" 
                                            {{-- onclick="openMenu(this, 4)" --}}
                                            >
                                        <span name="amount_task" class="bx-shadow finish"></span>
                                    </button>
                                    <h3 class="list-title"
                                        style="display: flex; flex-direction: row; justify-content: space-between">
                                        @lang('admin.task-working.column_finished')
                                    </h3>
                                </div>
                                <div class="task-sort">
                                    <div class="btn-group bootstrap-select show-tick show-menu-arrow" id="" style="padding-right: 10px">
                                        <select class="selectpicker show-tick show-menu-arrow" id="" onchange="sortTask(this)" name="sort-task" data-item="list-finish" data-status="4">
                                            <option value="sort-phase">Sắp xếp theo Phase</option>
                                            <option value="sort-job">Sắp xếp theo Job</option>
                                            <option value="sort-user">Sắp xếp theo Người thực hiện</option>
                                            <option value="sort-start">Sắp xếp theo Ngày bắt đầu</option>
                                            <option value="sort-alphabet">Sắp xếp theo Tên Task</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <ul class="list-items" id="list-finish"
                                data-item="@lang('admin.task-working.value_finished')">
                            </ul>
                        </div>
                    {{-- @endif --}}

                </section>
                <div id="list-action" class="card box-menu hide" data-item="">
                    <ul class="list-group" id="main-menu" style="border-radius: 4px">
                        <li class="list-group-item text-center list-title ">
                            <p style="font-size: 15px">Thao tác</p>
                        </li>
                        <a href="javascript:" class="list-group-item list-group-item-action" onclick="openSortMenu()">Sắp
                            xếp theo..</a>
                        {{-- @if(in_array(auth()->id(), explode(',', $project->Leader)) || auth()->user()->role_group == 2)
                        --}}
                        <a href="javascript:" class="list-group-item list-group-item-action text-danger"
                           onclick="deleteTasks(event, $(this).offsetParent())">Xóa tất cả</a>
                        {{-- @endif --}}
                    </ul>
                    <ul class="list-group hide" id="sort-menu">
                        <li class="list-group-item text-center">
                            <div class="d-flex"
                                 style="display: flex; flex-direction: row; align-content: center; align-items: center">
                                <i class="fa fa-chevron-left" aria-hidden="true" onclick="openMainMenu()"></i>
                                <span class="list-title " style="font-size: 15px; margin-left: 2.6em">Sắp xếp Danh
                                sách</span>
                            </div>
                        </li>
                        <a href="javascript:" onclick="sortTask(this)" class="list-group-item list-group-item-action"
                           name="sort-user">Theo người thực hiện</a>
                        <a href="javascript:" onclick="sortTask(this)" class="list-group-item list-group-item-action"
                           name="sort-phase">Theo Phase</a>
                        <a href="javascript:" onclick="sortTask(this)" class="list-group-item list-group-item-action"
                           name="sort-job">Theo Job</a>
                        <a href="javascript:" onclick="sortTask(this)" class="list-group-item list-group-item-action"
                           name="sort-important">Độ ưu tiên</a>
                        <a href="javascript:" onclick="sortTask(this)" class="list-group-item list-group-item-action"
                           name="sort-start-close">Ngày bắt đầu (Gần nhất)</a>
                        <a href="javascript:" onclick="sortTask(this)" class="list-group-item list-group-item-action"
                           name="sort-start-far">Ngày bắt đầu (Xa nhất)</a>
                        <a href="javascript:" onclick="sortTask(this)" class="list-group-item list-group-item-action"
                           name="sort-end-close">Ngày hết hạn (Gần nhất)</a>
                        <a href="javascript:" onclick="sortTask(this)" class="list-group-item list-group-item-action"
                           name="sort-end-far">Ngày hết hạn (Xa nhất)</a>
                        <a href="javascript:" onclick="sortTask(this)" class="list-group-item list-group-item-action"
                           name="sort-alphabet">Tên thẻ (theo thứ tự bảng chữ cái)</a>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{ Module::asset('ProjectManager:js/tasks.js')}}"></script>
    <script type="text/javascript" async>
        const projectId = {{ isset($request['projectId'])? $request['projectId'] : 0 }};
        let phaseId = {{ isset($request['phaseId'])? $request['phaseId'] : 0 }};
        let jobId = {{ isset($request['jobId']) ? $request['jobId'] : 0 }};
        $(document).ready(e => {
            if (typeof qs['task'] !== "undefined") {
                mainTask(qs['task'], e);
            }
            $('.list-items').each(function (index, item) {
                if ($(item).find('li').length > 0) {
                    $(item).find('.drop-it').css('display', 'none');
                }
            })
            $('#item-3').click(function () {
                $('.hide3').toggle();
                $(this).toggleClass('open1');
            })

            let data = $("#list-project-task-form").serializeArray();
            data.push({name: "projectId", value: projectId});
            $('span[name="amount_task"]').text(0);
            $(".ui-sortable").empty();
            loadData(data);
            // loadData({
            //     'projectId': projectId,
            //     'phaseId': phaseId,
            //     'jobId': jobId
            // });

            loadProjectInfo();
            $('#hiddenDetail').trigger('click');
        });
        SetDatePicker($('.date'), {
            format: "dd/mm/yyyy",
            todayHighlight: true,
            autoclose: true,
        });

        $('.selectpicker').selectpicker();

        const _uid = "{{ auth()->user()->id }}";
        const not_finish = "@lang("admin.task-working.value_unfinished")";
        const working = "@lang("admin.task-working.value_working")";
        const review = "@lang("admin.task-working.value_review")";
        const finish = "@lang("admin.task-working.value_finished")";
        const newTitle = "@lang('admin.task-working.title_new_task')";
        const ajaxUrl = "{{ route('admin.showTaskForm', [$project->id, null]) }}";
        const headers = {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-type': 'application/json',
            'Authorization': 'Bearer {{ \Illuminate\Support\Facades\Session::get('api-user') }}',
        };
        const headers_u = {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Authorization': 'Bearer {{ \Illuminate\Support\Facades\Session::get('api-user') }}'
        }
        let is_check_flag = false;
        let updateTitle = 'Đây là tên của Task';
        let parent_first;

        // URL string
        const urlLoadData = "{{ route('admin.getAllTasks') }}";
        const urlSuggestAjax = "{{ route('admin.ApiTaskSuggest',$project->id) }}";
        const urlSaveNewPos = "{{ route('admin.TaskChangeStatus')}}";
        const urlDeleteTask = "{{ route('admin.TaskDelete') }}";
        const urlTaskReport = "{{ route('admin.openTaskReport') }}";
        const urlErrorTaskReport = "{{ route('admin.openErrorTaskReport') }}";
        const urlLoadProInfo = "{{  route('admin.ApiAllProject', $project->id) }}";
        // End URL string
        // $('.modal').modal({backdrop: 'static', keyboard: false})

        $('#hiddenDetail').click(function (e) {
            $('.table-detail').slideToggle();
            $('.lists-container > .list').toggleClass('list-long');
        });

        $("a[name='add-task-current']").on('click', function () {
            let item = $(this).attr('data-item');
            addCurrentTask(item);
            $('.loadajax').show();
        })

        // $("#list-project-task-form").submit(e => {
        //     e.preventDefault();
        //     let data = $("#list-project-task-form").serializeArray();
        //     data.push({name: "projectId", value: projectId});
        //     $('span[name="amount_task"]').text(0);
        //     $(".ui-sortable").empty();
        //     loadData(data);
        // })

        let list_items = $(".list-items");
        $(list_items).on('click', 'li', function (e) {
            e.preventDefault();
            if (e.ctrlKey) {
                $(this).toggleClass('selected-sort');
            }
        });

        $(list_items).sortable({
            distance: 10,
            connectWith: '.list-items',
            items: '>li:not(.disable-mov)',
            helper: function (e, item) {
                if (!item.hasClass('selected-sort')) {
                    item.addClass('selected-sort');
                }
                let elements = $('.selected-sort').not('.ui-sortable-placeholder').clone();
                item.siblings('.selected-sort').addClass('hidden');
                let helper = $('<ul/>');
                return helper.append(elements);
            },
            start: function (even, ui) {
                parent_first = ui.item.parent().attr('id');
                const elements = ui.item.siblings('.selected-sort').not('.ui-sortable-placeholder');
                ui.item.data('items', elements);
            },
            receive: function (even, ui) {
                ui.item.after(ui.item.data('items'));
                countTask();
            },
            stop: function (even, ui) {
                let to_id = ui.item.parent().attr('id');
                let duration = ui.item.find('.data-duration').attr('data-duration');
                let startTime = ui.item.find('.task-start').text();
                if (parent_first !== to_id) {
                    if(!duration || duration == 0 || !startTime){
                        cancelSortable(this, ui);
                        showErrors("Task chưa có thời gian thực hiện, vui lòng liên hệ quản lý!")
                    }else{
                        draggingTask(
                        this,
                        ui,
                        parent_first,
                        to_id,
                        even);
                    }
                } else {
                    $(this).children().each((index, value) => {
                        if ($(value).attr("data-position") != (index + 1)) {
                            $(value).attr("data-position", (index + 1)).addClass("update-position");
                        }
                    });
                    saveNewPosition();
                }
                $("html").unbind('mousemove', ui.item.data("move_handler"));
                ui.item.removeData("move_handler");
                ui.item.siblings('.selected-sort').removeClass('hidden');
                $('.selected-sort').removeClass('selected-sort')
            },
        });

        // $(window).resize(function () {
        //     if ($('.active-list')) {
        //         let item = $('.active-list')[0];
        //         let menu = $('#list-action');
        //         $(menu).position({
        //             of: $(item),
        //             my: 'left+25 top+25',
        //             at: 'left top'
        //         })
        //     }
        // })

        $(document).mouseup(function (e) {
            if ($(e.target).closest("#list-action").length === 0) {
                $('#sort-menu').addClass('hide');
                $('#main-menu').removeClass('hide');
                $('#list-action').addClass('hide');
                $('.active-list').removeClass('active-list');
            }
        })

        $(document).click(e => {
            if (!$(e.target).closest(".form-menu").length && $("a[name='openFormDay']")[0] !== $(e.target)[0]) {
                $("body").find(".form-menu").addClass("hide");
            }
        })


        $(".add-task-btn").click(function (e) {
            e.preventDefault();
            var ajaxUrl = "{{ route('admin.showTaskForm') }}";
            var title = "Thêm Task mới";
            let searchKeys = $("#list-project-task-form").serializeArray();
            searchKeys = searchKeys.reduce((acc, {name, value}) => ({...acc, [name]: value}),{});
            ajaxGetServerWithLoader(
                genUrlGet([ajaxUrl]),
                'GET',
                {
                    projectId: projectId,
                    phaseId: phaseId,
                    jobId: jobId,
                    searchKeys: searchKeys
                },
                function (data) {
                    $('#popupModal').empty().html(data);
                    $('.modal-title').html(title);
                    $('#status').val(status);
                    $('.detail-modal').modal('show');
                }
            );
        });

        function showTaskDetail(id) {
            var ajaxUrl = "{{ route('admin.showTaskDetail') }}";
            var title = "Thông tin chi tiết Task";
            ajaxGetServerWithLoader(
                genUrlGet([ajaxUrl]),
                'GET',
                {
                    projectId: projectId,
                    phaseId: phaseId,
                    jobId: jobId,
                    taskId: id
                },
                function (data) {
                    $('#popupModal').empty().html(data);
                    // $('.modal-title').html(title);
                    $('.detail-modal').modal('show');
                }
            );
        }

    </script>
@endsection
