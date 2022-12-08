<style>
    .modal .tableFixHead table {
        border-collapse: separate;
        width: 100%;
        text-align: center;
    }

    .modal.tableFixHead thead th,
    .modal.tableFixHead thead td {
        height: 40px;
        border: 1px solid black;
    }

    .modal.tableFixHead thead th,
    .modal.tableFixHead tfoot th {
        background-color: white;
        background-clip: padding-box;
    }

    .modal.tableFixHead {
        overflow: auto;
        max-height: 900px;
    }

    .modal.tableFixHead thead {
        position: sticky;
        top: 0;
    }

    .modal.tableFixHead tfoot {
        position: sticky;
        bottom: 0;
    }
</style>
<div class="modal fade" id="modal-list-user" tabindex="-1" role="dialog" aria-labelledby="modal-list-user"
    aria-hidden="true">
    <div class="modal-dialog" role="document" style="overflow-y:inital; width:80%">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-sm-6 col-md-10 col-xs-10">
                        <h3 class="modal-title" id="title-modal"></h3>
                        <p class="mt-2">Thành viên của {{$workType}} - {{$workName}}</p>
                    </div>
                    <div class="col-sm-6 col-md-2 col-xs-2">
                        <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-body" style="overflow-y: auto;overflow-x: auto; height:100%;">
                <div class="tab-content">
                    <div class="tab-pane {{!isset($activeTab) || $activeTab === 'tab_phase' ? 'active' : ''}}"
                        id="tab-task">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <form class="form-inline" id="progress-search-fm">
                                    <div class="form-group pull-left margin-r-5">
                                        <div class="btn-group bootstrap-select show-tick show-menu-arrow"
                                            id="progress-select-user">
                                            <select class="selectpicker show-tick show-menu-arrow"
                                                name="progressUserIds[]" data-done-button="true" multiple
                                                id="progressUserIds" title="@lang('admin.chooseUser')"
                                                data-live-search="true" data-size="5"
                                                data-live-search-placeholder="Tìm kiếm" data-actions-box="true"
                                                tabindex="-98">
                                                {!! GenHtmlOption($members, 'id', 'FullName',
                                                isset($request['progressUserIds']) ?
                                                $request['progressUserIds'] : null)
                                                !!}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12" id="projectsList">
                                <div class="box tbl-top">
                                    <div class="box-body table-responsive no-padding tableFixHead"
                                        style="max-height: 500px; overflow: auto">
                                        <table class="table table-bordered table-striped sorted_table" name="table">
                                            <thead class="">
                                                <tr id="progress-tHead">
                                                    {{-- <th class="">
                                                        <a class="sort-link" order-by="id" sort-by='desc'>
                                                            @lang('admin.stt')
                                                        </a>
                                                    </th> --}}
                                                    <th>@lang('projectmanager::admin.Members')</th>
                                                    <th class="">@lang('projectmanager::admin.task.Total')</th>
                                                    <th class="">@lang('projectmanager::admin.task.Todo')</th>
                                                    <th class="">@lang('projectmanager::admin.task.Doing')</th>
                                                    <th class="">@lang('projectmanager::admin.task.Review')</th>
                                                    <th class="">@lang('projectmanager::admin.task.Done')</th>
                                                    <th class="">@lang('projectmanager::admin.PersonalProgress')(%)</th>
                                                    <th class="">@lang('projectmanager::admin.GeneralProgress')(%)</th>
                                                </tr>
                                            </thead>
                                            <tbody id="progress-projectBody">
                                                @foreach ($members as $key => $member)
                                                <tr id="member{{$member->id}}">
                                                    {{-- <td>{{$key +1}}</td> --}}
                                                    @if (
                                                        ($workType === 'Project' && $member->projectUsers[0]->is_leader == 1) ||
                                                        ($workType == 'phase' && $member->is_leader)
                                                    )
                                                    <td style="text-align: left">
                                                        {{$member->FullName}}
                                                        <span class="pull-right"><small>Quản lý</small></span>
                                                    </td>
                                                    @else
                                                    <td style="text-align: left">
                                                        {{$member->FullName}}
                                                    </td>
                                                    @endif
                                                    <td>{{count($member->tasks)}}</td>
                                                    <td>{{count($member->todoTasks)}}</td>
                                                    <td>{{count($member->doingTasks)}}</td>
                                                    <td>{{count($member->reviewTasks)}}</td>
                                                    <td>{{count($member->doneTasks)}}</td>
                                                    <td>{{$member->personalProgress}}</td>
                                                    <td>{{$member->generalProgress}}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>
                                                        @switch($workType)
                                                            @case('project')
                                                                Dự án 
                                                                @break
                                                            @case('phase')
                                                                Phase 
                                                                @break
                                                            @case('job')
                                                                Job 
                                                                @break
                                                            @default
                                                                Dự án 
                                                        @endswitch
                                                    </th>
                                                    <th id="taskTotal">{{$totalTasks}}</th>
                                                    <th id="generalTodoTasks">{{$generalTodoTasks}}</th>
                                                    <th id="generalDoingTasks">{{$generalDoingTasks}}</th>
                                                    <th id="generalReviewTasks">{{$generalReviewTasks}}</th>
                                                    <th id="generalDoneTasks">{{$generalDoneTasks}}</th>
                                                    <th></th>
                                                    <th id="progressTotal">{{$genaralProgress}}</th>
                                                </tr>
                                                {{-- @if (isset($project))
                                                <tr>
                                                    <th>Dự án</th>
                                                    <th id="projectTaskTotal">{{$project['totalTasks']}}</th>
                                                    <th id="projectTodoTasks">{{$generalTodoTasks}}</th>
                                                    <th id="projectDoingTasks">{{$generalDoingTasks}}</th>
                                                    <th id="projectReviewTasks">{{$generalReviewTasks}}</th>
                                                    <th id="projectDoneTasks">{{$generalDoneTasks}}</th>
                                                    <th></th>
                                                    <th id="projectProgress">{{$project['progress']}}</th>
                                                </tr>
                                                @endif --}}
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div id="progress-project-page-selection">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab-phase"></div>
                    <div class="tab-pane" id="tab-job"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(() => {
        setSelectPicker();
        $('#progress-select-user').find('.bs-donebutton').on('click',function(){
            let users = $('#progressUserIds').val();
            if(users[0] != undefined){
                $('#progress-projectBody').find('tr').hide();
                users.forEach(element => {
                    $(`#member${element}`).fadeIn();
                });
            }else{
                $('#progress-projectBody').find('tr').fadeIn();
            }
        })
        const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;

        const comparer = (idx, asc) => (a, b) => ((v1, v2) => 
            v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
            )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

        // do the work...
        const container = document.querySelector('#progress-tHead');
        container.querySelectorAll('th').forEach(th => th.addEventListener('click', (() => {
            const table = document.querySelector('#progress-projectBody');
            Array.from(table.querySelectorAll('tr:nth-child(n+1)'))
                .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
                .forEach(tr => table.appendChild(tr) );
        })));
    });
</script>