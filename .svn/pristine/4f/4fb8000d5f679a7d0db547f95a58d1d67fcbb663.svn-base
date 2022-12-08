@if($view == 1)
    <table>
        <thead>
        <tr>
            <th colspan="{{ $col }}">Bảng tổng hợp báo cáo tháng {{$month}}</th>
        </tr>
        <tr>
            <th rowspan="2">No</th>
            <th>ToW%</th>
            @foreach($masterData as $data)
                <?php $key = $data->DataValue; ?>
                <td>{{ $total->totalHours > 0 ? number_format($total->$key/$total->totalHours*100, 2) : 0 }}%</td>
            @endforeach
            <td></td>
            <th rowspan="2">Project Precent</th>
        </tr>
        <tr>
            <th>@lang('admin.daily.Project')</th>
            @foreach($masterData as $data)
                <th>{{ $data->Name }}</th>
            @endforeach
            <th>Sum</th>
        </tr>
        </thead>
        <tbody>
        @foreach($total as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->NameVi }}</td>
                @foreach($masterData as $data)
                    <?php $key = $data->DataValue ?>
                    <td>{{ $item->$key+0 }}</td>
                @endforeach
                <td>{{ $item->totalHours }}</td>
                <td>{{ $total->totalHours > 0 ? number_format($item->totalHours/$total->totalHours*100, 2) : 0 }}%</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="{{ count($masterData) + 1 }}"></td>
            <th>Total</th>
            <th>{{ $total->totalHours }}</th>
            <td></td>
        </tr>
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th>@lang('admin.stt')</th>
                <th>@lang('admin.daily.Date')</th>
                <th>@lang('admin.daily.Project')</th>
                <th>@lang('admin.daily.Screen_Name')</th>
                <th>@lang('admin.daily.Type_Of_Work')</th>
                <th>@lang('admin.contents')</th>
                <th>@lang('admin.daily.Working_Time')</th>
                <th>@lang('admin.daily.progressing')</th>
                <th>@lang('admin.daily.Note')</th>
                <th>@lang('admin.daily.Date_Create')</th>
            </tr>
        </thead>
        <tbody>
        @foreach($dailyReports as $dailyReport)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ FomatDateDisplay($dailyReport->Date, FOMAT_DISPLAY_DAY) }}</td>
                <td>{!! nl2br(e($dailyReport->NameVi)) !!}</td>
                <td>{!! nl2br(e($dailyReport->ScreenName)) !!}</td>
                <td>{{ $dailyReport->Name }}</td>
                <td>{!! nl2br(e($dailyReport->Contents)) !!}</td>
                <td>{{ $dailyReport->WorkingTime }}</td>
                <td>{{ $dailyReport->Progressing.' %'}}</td>
                <td>{!! nl2br(e($dailyReport->Note)) !!}</td>
                <td>{{ FomatDateDisplay($dailyReport->DateCreate, FOMAT_DISPLAY_DAY) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <table >
        <thead>
        <tr>
            <th colspan="16">Bảng tổng hợp báo cáo năm {{ $year }}</th>
        </tr>
        <tr>
            <th rowspan="2" colspan="1">No</th>
            <td>Tổng</td>
{{--            <td>{{ $projects->sum('time') != 0 ? number_format(100*$projects->sum('T1')/$projects->sum('time'), 2) +0 : 0 }}%</td>--}}
{{--            <td>{{ $projects->sum('time') != 0 ? number_format(100*$projects->sum('T2')/$projects->sum('time'), 2) +0 : 0 }}%</td>--}}
{{--            <td>{{ $projects->sum('time') != 0 ? number_format(100*$projects->sum('T3')/$projects->sum('time'), 2) +0 : 0 }}%</td>--}}
{{--            <td>{{ $projects->sum('time') != 0 ? number_format(100*$projects->sum('T4')/$projects->sum('time'), 2) +0 : 0 }}%</td>--}}
{{--            <td>{{ $projects->sum('time') != 0 ? number_format(100*$projects->sum('T5')/$projects->sum('time'), 2) +0 : 0 }}%</td>--}}
{{--            <td>{{ $projects->sum('time') != 0 ? number_format(100*$projects->sum('T6')/$projects->sum('time'), 2) +0 : 0 }}%</td>--}}
{{--            <td>{{ $projects->sum('time') != 0 ? number_format(100*$projects->sum('T7')/$projects->sum('time'), 2) +0 : 0 }}%</td>--}}
{{--            <td>{{ $projects->sum('time') != 0 ? number_format(100*$projects->sum('T8')/$projects->sum('time'), 2) +0 : 0 }}%</td>--}}
{{--            <td>{{ $projects->sum('time') != 0 ? number_format(100*$projects->sum('T9')/$projects->sum('time'), 2) +0 : 0 }}%</td>--}}
{{--            <td>{{ $projects->sum('time') != 0 ? number_format(100*$projects->sum('T10')/$projects->sum('time'), 2) +0 : 0 }}%</td>--}}
{{--            <td>{{ $projects->sum('time') != 0 ? number_format(100*$projects->sum('T11')/$projects->sum('time'), 2) +0 : 0 }}%</td>--}}
{{--            <td>{{ $projects->sum('time') != 0 ? number_format(100*$projects->sum('T12')/$projects->sum('time'), 2) +0 : 0 }}%</td>--}}
{{--            <td>{{ $projects->sum('time') }}</td>--}}

            <td>{{ $statistic_year['TotalYear']['TotalHours'] != 0 ? number_format(100*$statistic_year['T1']['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) + 0 : 0 }} %
                <br>
                {{$statistic_year['T1']['TotalHours']}} h
            </td>
            <td>{{ $statistic_year['TotalYear']['TotalHours'] != 0 ? number_format(100*$statistic_year['T2']['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) + 0 : 0 }} %
                <br>
                {{$statistic_year['T2']['TotalHours']}} h
            </td>
            <td>{{ $statistic_year['TotalYear']['TotalHours'] != 0 ? number_format(100*$statistic_year['T3']['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) + 0 : 0 }} %
                <br>
                {{$statistic_year['T3']['TotalHours']}} h
            </td>
            <td>{{ $statistic_year['TotalYear']['TotalHours'] != 0 ? number_format(100*$statistic_year['T4']['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) + 0 : 0 }} %
                <br>
                {{$statistic_year['T4']['TotalHours']}} h
            </td>
            <td>{{ $statistic_year['TotalYear']['TotalHours'] != 0 ? number_format(100*$statistic_year['T5']['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) + 0 : 0 }} %
                <br>
                {{$statistic_year['T5']['TotalHours']}} h
            </td>
            <td>{{ $statistic_year['TotalYear']['TotalHours'] != 0 ? number_format(100*$statistic_year['T6']['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) + 0 : 0 }} %
                <br>
                {{$statistic_year['T6']['TotalHours']}} h
            </td>
            <td>{{ $statistic_year['TotalYear']['TotalHours'] != 0 ? number_format(100*$statistic_year['T7']['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) + 0 : 0 }} %
                <br>
                {{$statistic_year['T7']['TotalHours']}} h
            </td>
            <td>{{ $statistic_year['TotalYear']['TotalHours'] != 0 ? number_format(100*$statistic_year['T8']['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) + 0 : 0 }} %
                <br>
                {{$statistic_year['T8']['TotalHours']}} h
            </td>
            <td>{{ $statistic_year['TotalYear']['TotalHours'] != 0 ? number_format(100*$statistic_year['T9']['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) + 0 : 0 }} %
                <br>
                {{$statistic_year['T9']['TotalHours']}} h
            </td>
            <td>{{ $statistic_year['TotalYear']['TotalHours'] != 0 ? number_format(100*$statistic_year['T10']['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) + 0 : 0 }} %
                <br>
                {{$statistic_year['T10']['TotalHours']}} h
            </td>
            <td>{{ $statistic_year['TotalYear']['TotalHours'] != 0 ? number_format(100*$statistic_year['T11']['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) + 0 : 0 }} %
                <br>
                {{$statistic_year['T11']['TotalHours']}} h
            </td>
            <td>{{ $statistic_year['TotalYear']['TotalHours'] != 0 ? number_format(100*$statistic_year['T12']['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) + 0 : 0 }} %
                <br>
                {{$statistic_year['T12']['TotalHours']}} h
            </td>
            <td>{{ $statistic_year['TotalYear']['TotalHours'] != 0 ? number_format(100*$statistic_year['TotalYear']['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) + 0 : 0 }} %
                <br>
                {{ $statistic_year['TotalYear']['TotalHours'] }} h
            </td>
            <td></td>
        </tr>
        <tr>
            <th>Project</th>
            <th>T1</th>
            <th>T2</th>
            <th>T3</th>
            <th>T4</th>
            <th>T5</th>
            <th>T6</th>
            <th>T7</th>
            <th>T8</th>
            <th>T9</th>
            <th>T10</th>
            <th>T11</th>
            <th>T12</th>
            <th>Total</th>
            <th>Percent</th>
        </tr>
        </thead>
        <tbody>
        @foreach($projects as $project)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $project->NameVi }}</td>
                <td>{{ $project->T1['TotalHours'] }}</td>
                <td>{{ $project->T2['TotalHours'] }}</td>
                <td>{{ $project->T3['TotalHours'] }}</td>
                <td>{{ $project->T4['TotalHours'] }}</td>
                <td>{{ $project->T5['TotalHours'] }}</td>
                <td>{{ $project->T6['TotalHours'] }}</td>
                <td>{{ $project->T7['TotalHours'] }}</td>
                <td>{{ $project->T8['TotalHours'] }}</td>
                <td>{{ $project->T9['TotalHours'] }}</td>
                <td>{{ $project->T10['TotalHours'] }}</td>
                <td>{{ $project->T11['TotalHours'] }}</td>
                <td>{{ $project->T12['TotalHours'] }}</td>
                <td>{{ $project->TotalYear['TotalHours'] }}</td>
                <td>{{ $statistic_year['TotalYear']['TotalHours'] != 0 ?number_format(100*$project->TotalYear['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) : 0 }}%</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
