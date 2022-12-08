<input type="hidden" id="phasesLastPage" value="{{$phases->lastPage()}}">
@foreach ($phases as $key => $item)
<tr>
    <td style="text-align: center"><label style="background-color:{{$item->color}};color:#eeeee4;width:3.5rem;border-radius:0.5rem"> {{ ($phases->currentPage() -1) * $phases->perPage() + $key+1}}</label> </td>
    <td> {{$item->phaseType['Name']}} </td>
    <td> {{$item['name']}} </td>
    <td> {{$item['description']}} </td>
    <td style="text-align: center">
        <a style="color: black;" class="show-members-btn" href="javascript:void(0)" phase-id="{{$item->id}}">
            <button type="button" class="btn btn-default" style="width: 100%;background-color:#eeeee4">
                {{count($item->members)}}
            </button>    
        </a>
    </td>
    <td style="text-align: center">
        <a style="color: black" href="{{route('admin.showTasks')}}?projectId={{$item->project->id}}&phaseId={{$item['id']}}">
            <button type="button" class="btn btn-default" style="width: 100%;background-color:#eeeee4">
                    {{count($item->tasks)}}
            </button>
        </a>
    </td>
    <td style="text-align: center">{{count($item->todoTasks)}}</td>
    <td style="text-align: center">{{count($item->doingTasks)}}</td>
    <td style="text-align: center">{{count($item->reviewTasks)}}</td>
    <td style="text-align: center">{{count($item->doneTasks)}}</td>
    <td style="text-align: center"> {{$item->progress}} </td>
    <td style="text-align: center" title="Tiến độ đang thực hiện trên dự án / Tiến độ dự kiến trên dự án"> {{$item->generalProgress}}/{{$item->percentInProject}}</td>
    <td style="text-align: center"> {{FomatDateDisplay($item->start_date, FOMAT_DISPLAY_DAY)}} </td>
    <td style="text-align: center"> {{FomatDateDisplay($item->end_date, FOMAT_DISPLAY_DAY)}} </td>
    @if ($managePermission)
        <td class="text-center">
            <span class="action-col" onclick="updateProject({{ $item->project_id}},{{ $item['id']}},null)">
                <i class="fa fa-pencil-square-o" aria-hidden="true" title="Chỉnh sửa"></i></span>
            <span class="action-col" onclick="destroy({{$item['id']}},null,{{$item->project_id}})">
                <i class="fa fa-times" aria-hidden="true" title="Xóa"></i></span>
        </td>
    @endif    
</tr>
@endforeach