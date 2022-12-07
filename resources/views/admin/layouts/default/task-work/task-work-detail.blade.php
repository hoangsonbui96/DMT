@extends('admin.layouts.default.app')
@push('pageJs')
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
          rel="stylesheet">
    <link href="{{ asset('css/work-task/style.css') }}" rel="stylesheet">
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/support-common.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script src="{{ asset('js/work-task/scripts.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/web-animations/2.3.2/web-animations.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/haltu/muuri@0.9.3/dist/muuri.min.js"></script>
    <style>
        .selected-sort {
            background: #cdd2d4 !important;
        }

        .content-wrapper {
            min-height: 100vh !important;
        }

        @if(auth()->user()->can('viewAll-task', $project->id))
            @supports (display: grid) {
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
            }
        }

        @else
            @supports (display: grid) {
            .lists-container {
                display: grid;
                grid-auto-columns: 33%;
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
            }
        }
        @endif
    </style>
@endpush
@section('content')
    <section class="content-header" style="margin-bottom: 20px">
        <h1 class="page-header" style="display: inline">
            <a href="javascript:void(0)" id="hiddenDetail">
                {{ $project->NameVi }}
                @if($project->NameShort != '')
                    ({{ $project->NameShort }})
                @endif
            </a>
        </h1>
        <a href="javascript:" class="btn btn-primary pull-right" onclick="comeBack()"><i class="fa fa-arrow-left"
                                                                                         aria-hidden="true"></i></a>
    </section>
    <section class="content" style="padding-top: 0">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form class="form-inline" id="list-project-task-form" action="" method="GET">
                    <div class="table-responsive table-detail">
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
                                <tr></tr>
                            @endslot
                            @slot('pageTable')
                                <nav id="paginator"></nav>
                            @endslot
                        @endcomponent
                    </div>
                    <div class="form-group" style="width: 100%;">
                        <div class="from-group pull-left">
                            <div class="btn-group show-tick show-menu-arrow margin-r-5" id="">
                                <input class="form-control" name="Keywords" placeholder="Nhập từ khóa..."
                                       autocomplete="off" data-role="tagsinput">
                            </div>
                            <div class="btn-group show-tick show-menu-arrow margin-r-5">
                                <select name="Choices[]" class="selectpicker form-control" multiple
                                        title="Chọn trạng thái" data-width="25rem" data-actions-box="true">
                                    <option value="@lang('admin.task-working.value_unfinished')">Chưa thực hiện</option>
                                    <option value="@lang('admin.task-working.value_working')">Đang thực hiện</option>
                                    <option value="@lang('admin.task-working.value_review')">Đang duyệt</option>
                                    @if(auth()->user()->can('viewAll-task', $project->id))
                                        <option value="@lang('admin.task-working.value_finished')">Hoàn thành</option>
                                    @endif
                                </select>
                            </div>
                            <div class="btn-group show-tick show-menu-arrow margin-r-5" id="">
                                <div class="input-group date">
                                    <input type="text" class="form-control datepicker"
                                           placeholder="@lang('admin.startDate')" name="StartDate" autocomplete="off"
                                    >
                                    <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                </div>
                            </div>
                            <div class="btn-group show-tick show-menu-arrow margin-r-5" id="">
                                <div class="input-group date">
                                    <input type="text" class="form-control datepicker"
                                           placeholder="@lang('admin.endDate')" name="EndDate" autocomplete="off"
                                    >
                                    <span class="input-group-addon">
                                             <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-search pull-right"
                                    id="btn-search">@lang('admin.btnSearch')</button>
                        </div>
                        <div class="form-group pull-right">
                            @can('create-task', $project)
                                <button type="button" class="btn btn-primary btn-detail"
                                        id="btn-new-task">@lang('admin.task-working.new-task')</button>
                            @endcan
                            {{--                                @can('export-task')--}}
                            {{--                                <button type="button" class="btn btn-success"--}}
                            {{--                                        id="btn-export">@lang('admin.export-excel')</button>--}}
                            {{--                                @endcan--}}
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <section class="lists-container">
                    <div class="list">
                        <div class="header-list header-border idea-bg">
                            <h3 class="list-title "
                                style="display: flex; flex-direction: row; justify-content: space-between">
                                @lang('admin.task-working.column_unfinished')
                                <span class="">
                                    <button style="border: none; outline: none; background-color: transparent"
                                            onclick="openMenu(this, 1)" tabindex="0" data-toggle="tooltip"
                                            title="Thao tác">
                                        <span name="amount_task" class="bx-shadow idea"></span>
                                    </button>
                                </span>
                            </h3>
                        </div>
                        <ul class="list-items" name="father-old" id="list-not-finish"
                            data-item="@lang('admin.task-working.value_unfinished')">
                        </ul>
                        @can('create-task', $project)
                            <a href="javascript:void(0)" data-item="1" class="open-task" name="add-task-current">
                                <span><i class="fa fa-plus" aria-hidden="true"></i></span>
                                <span style="margin-left: 1em; font-weight: 100 !important;">Thêm task mới</span>
                            </a>
                        @endcan
                    </div>

                    <div class="list">
                        <div class="header-list header-border progress-bg">
                            <h3 class="list-title "
                                style="display: flex; flex-direction: row; justify-content: space-between">
                                @lang('admin.task-working.column_working')
                                <span class="">
                                    <button style="outline: none; border: none; background-color: transparent"
                                            tabindex="0" data-toggle="tooltip" title="Thao tác"
                                            onclick="openMenu(this, 2)"><span name="amount_task"
                                                                              class="bx-shadow progress"></span></button>
                                </span>
                            </h3>
                        </div>
                        <ul class="list-items" name="father-new" id="list-working"
                            data-item="@lang('admin.task-working.value_working')">
                        </ul>
                        @can('create-task', $project)
                            <a href="javascript:void(0)" data-item="2" class="open-task" name="add-task-current">
                                <span><i class="fa fa-plus" aria-hidden="true"></i></span>
                                <span style="margin-left: 1em; font-weight: 100 !important;">Thêm task mới</span>
                            </a>
                        @endcan
                    </div>

                    <div class="list">
                        <div class="header-list header-border review-bg">
                            <h3 class="list-title "
                                style="display: flex; flex-direction: row; justify-content: space-between">
                                @lang('admin.task-working.column_review')
                                <span class="">
                                    <button style="outline: none; border: none; background-color: transparent"
                                            tabindex="0" data-toggle="tooltip" title="Thao tác"
                                            onclick="openMenu(this, 3)">
                                        <span name="amount_task" class="bx-shadow review"></span>
                                    </button>
                                </span>
                            </h3>
                        </div>

                        <ul class="list-items" id="list-review" data-item="@lang('admin.task-working.value_review')">
                        </ul>
                    </div>
                    @if(auth()->user()->can('viewAll-task',  $project->id))
                        <div class="list">
                            <div class="header-list header-border finish-bg">
                                <h3 class="list-title"
                                    style="display: flex; flex-direction: row; justify-content: space-between">
                                    @lang('admin.task-working.column_finished')
                                    <span class="">
                                   <button style="outline: none; border: none; background-color: transparent"
                                           tabindex="0" data-toggle="tooltip" title="Thao tác"
                                           onclick="openMenu(this, 4)">
                                       <span name="amount_task" class="bx-shadow finish"></span>
                                   </button>
                               </span>
                                </h3>
                            </div>
                            <ul class="list-items" id="list-finish"
                                data-item="@lang('admin.task-working.value_finished')"></ul>
                        </div>
                    @endif
                </section>
                <div id="list-action" class="card box-menu hide" data-item="">
                    <ul class="list-group" id="main-menu" style="border-radius: 4px">
                        <li class="list-group-item text-center list-title "><p style="font-size: 15px">Thao tác</p></li>
                        <a href="javascript:" class="list-group-item list-group-item-action" onclick="openSortMenu()">Sắp
                            xếp theo..</a>
                        @if(in_array(auth()->id(), explode(',', $project->Leader)) || auth()->user()->role_group == 2)
                        <a href="javascript:" class="list-group-item list-group-item-action text-danger"
                           onclick="deleteTasks(event, $(this).offsetParent())">Xóa tất cả</a>
                        @endif
                    </ul>
                    <ul class="list-group hide" id="sort-menu">
                        <li class="list-group-item text-center">
                            <div class="d-flex"
                                 style="display: flex; flex-direction: row; align-content: center; align-items: center">
                                <i class="fa fa-chevron-left" aria-hidden="true" onclick="openMainMenu()"></i>
                                <span class="list-title "
                                      style="font-size: 15px; margin-left: 2.6em">Sắp xếp Danh sách</span>
                            </div>
                        </li>
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
    <script type="text/javascript" async>
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
        const ajaxUrl = "{{ route('admin.TaskWorkAdd', [$project->id, null]) }}";
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
        const urlLoadData = "{{ route('admin.ApiTaskDisplay', [$project->id, null]) }}";
        const urlSuggestAjax = "{{ route('admin.ApiTaskSuggest',$project->id) }}";
        const urlSaveNewPos = "{{ route('admin.ApiChangeStatus') }}";
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

        $("#list-project-task-form").submit(e => {
            e.preventDefault();
            let data = {
                Keywords: $("input[name='Keywords']").val(),
                UserID: $("select[name='UserID']").val(),
                StartDate: $('input[name="StartDate"]').val(),
                EndDate: $('input[name="EndDate"]').val(),
                Choices: $("select[name='Choices[]']").val()
            };
            $('span[name="amount_task"]').text(0);
            $(".ui-sortable").empty();
            loadData(data);
        })

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
                if (parent_first !== to_id) {
                    draggingTask(this, ui, parent_first, to_id, even);
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
            $(".ui-sortable").empty();
            loadData();
            loadProjectInfo();
            $('#hiddenDetail').trigger('click');
        });
    </script>
@endsection
