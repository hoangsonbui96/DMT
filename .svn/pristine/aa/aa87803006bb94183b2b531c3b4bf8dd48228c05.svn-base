<input type="hidden" id="jobLastPage" value="{{$jobs->lastPage()}}">
@foreach ($jobs as $key => $job)
<tr>
    <td style="text-align: center"><label style="background-color:{{$job->color}};color:#eeeee4;width:3.5rem;border-radius:0.5rem">{{ ($jobs->currentPage() -1) * $jobs->perPage() + $key+1}}</label></td>
    <td> {{$job['name']}} </td>
    <td> {{$job['description']}} </td>
    <td>
        @foreach ($job->phases->unique() as $phase)
        <ol>
            <li value="{{$phase->order}}">
                {{$phase->name}} 
            </li>
        </ol>
        @endforeach
    </td>
    <td style="text-align: center">
        <a style="color: black" class="show-members-btn" href="javascript:void(0)" job-id="{{$job->id}}">
            <button type="button" class="btn btn-default" style="width: 100%;background-color:#eeeee4">
            {{count($job->members)}}
            </button>
        </a>
    </td>
    <td style="text-align: center">
        <a style="color: black" href="{{route('admin.showTasks')}}?projectId={{$job->project->id}}&jobId={{$job['id']}}">
            <button type="button" class="btn btn-default" style="width: 100%;background-color:#eeeee4">
                {{count($job->tasks) }}
            </button>
        </a>
    </td>
    <td style="text-align: center">{{count($job->todoTasks)}}</td>
    <td style="text-align: center">{{count($job->doingTasks)}}</td>
    <td style="text-align: center">{{count($job->reviewTasks)}}</td>
    <td style="text-align: center">{{count($job->doneTasks)}}</td>
    <td style="text-align: center"> {{$job->progress}} </td>
    <td style="text-align: center" title="Tiến độ đang thực hiện trên dự án / Tiến độ dự kiến trên dự án"> {{$job->generalProgress}}/{{$job->percentInProject}}</td>
    <td style="text-align: center"> {{FomatDateDisplay($job->start_date, FOMAT_DISPLAY_DAY)}} </td>
    <td style="text-align: center"> {{FomatDateDisplay($job->end_date, FOMAT_DISPLAY_DAY)}} </td>
    @if ($managePermission)
        <td class="text-center">
            <span class="action-col" onclick="updateProject({{ $job->project_id}},null,{{ $job['id']}})">
                <i class="fa fa-pencil-square-o" aria-hidden="true" title="Chỉnh sửa"></i></span>
            <span class="action-col" onclick="destroy(null,{{$job['id']}},{{$job['project_id']}})">
                <i class="fa fa-times" aria-hidden="true" title="Xóa"></i></span>
        </td>
    @endif
   
</tr>
@endforeach