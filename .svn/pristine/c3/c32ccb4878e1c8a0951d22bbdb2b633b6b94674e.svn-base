<table>
    <thead>
        <tr>
            <th colspan="{{ $countCol }}">@lang('admin.absence.absence-reports')</th>
        </tr>
        <tr>
            <th rowspan="2">@lang('admin.stt')</th>
            <th rowspan="2">@lang('admin.absence.fullname')</th>
            <th colspan=" {{ count($master_datas) }}" rowspan="1">@lang('admin.absences')</th>
            <th rowspan="2">@lang('admin.sum')</th>
        </tr>
        <tr>
            @foreach($master_datas as $master_data)
                <th rowspan="1" colspan="1">{{ $master_data->Name }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
    @foreach($absence_report as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->FullName }}</td>
            @foreach($item->hours as $key => $value)
                <td>@if($value > 0){{ number_format($value/60, 1) }}h <br>({{ $item->times[$key] }} lượt) @else - @endif</td>
            @endforeach
            <td>{{ number_format(array_sum($item->hours)/60, 2) }}h <br>
                ({{ array_sum($item->times) }} lượt)
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
