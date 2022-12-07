<table>
    <thead>
    <tr>
        <th colspan="{{$intTotalCol}}">@lang('admin.overtime.report')</th>
    </tr>
    <tr>
        <th>@lang('admin.Staffs_name')</th>
        @foreach($projects as $project)
            <th>{{ $project->NameVi}}</th>
        @endforeach
        <th>@lang('admin.overtime.total')</th>
        <th>@lang('admin.overtime.percent')</th>
    </tr>
    </thead>
    <tbody>
    @foreach($userList as $user)
        <tr>
            <td>{{ $user->FullName }}</td>
            @foreach($user->workOnProject as $work)
                <td>{{ $work }}</td>
            @endforeach
            <td>{{ $user->totalOvertime }}</td>
            <td>{{ array_sum($totalOvertimeOnProject) > 0 ? number_format($user->totalOvertime/array_sum($totalOvertimeOnProject)*100, 2) : 0 }}%</td>
        </tr>
    @endforeach
    <tr>
        <td>@lang('admin.overtime.total')</td>
        @foreach($totalOvertimeOnProject as $item)
            <td>{{ $item }}</td>
        @endforeach
        <td>{{ array_sum($totalOvertimeOnProject) }}</td>
        <td>100%</td>
    </tr>

    </tbody>
</table>
