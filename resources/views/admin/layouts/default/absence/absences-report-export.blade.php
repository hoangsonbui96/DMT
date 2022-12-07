<table>
    <thead>
        <tr></tr>
        <tr rowspan = "2">
            <th colspan="{{ $countCol }}">BÁO CÁO TỔNG HỢP VẮNG MẶT</th>
        </tr>
        <tr></tr>
        <tr>
            <td></td>
            <td>Từ ngày</td>
            <td >{{$request['date'][0]}}</td>
            <td></td>
            <td >Đến ngày</td>
            <td colspan="2">{{$request['date'][1]}}</td>
            <td colspan="{{ $countCol -10}}"></td>
            <td colspan="2">Ngày lập báo cáo</td>
            <td >{{$today}}</td>
        </tr>
        <tr></tr>
        <tr>
            <td></td>
            <td>Tổng</td>
            <td>{{array_sum($totalHour)}}</td>
            <td>{{array_sum($totalTime)}}</td>
            @foreach ($totalHour as $key=>$value)
            <td>{{$value}}</td>
            <td>{{$totalTime[$key]}}</td>
            @endforeach
        </tr>
        <tr>
            <th rowspan="2"><b>@lang('admin.stt')</b></th>
            <th rowspan="2"><b>@lang('admin.absence.fullname')</b></th>
            <th style="width:10px" rowspan="2"><b>Tổng<br>(giờ)</b></th>
            <th style="width:10px" rowspan="2"><b>Tổng<br>(lần)</b></th>
            @foreach($master_datas as $master_data)
            <th colspan="2" style="width:20px"><b>{{ $master_data->Name }}</b></th>
            @endforeach
        </tr>
        <tr>
            @foreach($master_datas as $master_data)
            <th style="width:10px"><b>Giờ</b></th>
            <th style="width:10px"><b>Lần</b></th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($absence_report as $key => $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->FullName }}</td>
            <td style="text-align: center"  bgcolor="#FDEADA">
                {{ $sumTotalHour[$key] }}
            </td>
            <td style="text-align: center"  bgcolor="#FDEADA">
                {{ array_sum($item->times) > 0 ? array_sum($item->times) : '-' }}
            </td>
            @foreach ($item->hours as $key=>$value)
            <td style="text-align: center"  >{{number_format($value/60, 2)}}</td>
            <td style="text-align: center"  >{{$item->times[$key]}}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>