<table>
    <thead >
        <tr>
            <th colspan = "{{$columnumber}}">Báo cáo tổng hợp vắng mặt {{$dateRange->startDate->format('m/d/Y')}} - {{$dateRange->endDate->format('m/d/Y')}}</th>
        </tr>
        <tr>
            <th rowspan = "2">STT</th>
            <th rowspan = "2" style="width: 20px;">Họ và tên</th>
            <th rowspan = "2">Tổng <br> Giờ</th>
            <th rowspan = "2">Tổng <br> Lượt</th>
            <th colspan = "2">Nghỉ có lý do</th>
            <th colspan = "2">Nghỉ không lý do</th>
            <th colspan = "2">Đi muộn</th>
            <th colspan = "2">Về sớm</th>
            <th colspan = "">Không checkin</th>
            <th colspan = "">Không checkout</th>
        </tr>
        <tr>
            <th>Giờ</th>
            <th>Lượt</th>
            <th>Giờ</th>
            <th>Lượt</th>
            <th>Giờ</th>
            <th>Lượt</th>
            <th>Giờ</th>
            <th>Lượt</th>
            <th>Lượt</th>
            <th>Lượt</th>
        </tr>
    </thead>
    <tbody>
        @foreach($listUsers as $key => $user)
            <tr>
                <td>{{$key + 1}}</td>
                <td>{{$user->FullName}}</td>
                <td>=ROUND(E{{$key+4}}+G{{$key+4}}+I{{$key+4}}+K{{$key+4}},2)</td>
                <td>=ROUND(F{{$key+4}}+H{{$key+4}}+J{{$key+4}}+L{{$key+4}},2)</td>
                <td>{{number_format($user->hasReasonAbsentHours,2)}}</td>
                <td>{{$user->hasReasonAbsentTimes}}</td>
                <td>{{number_format($user->noReasonAbsentHours,2)}}</td>
                <td>{{$user->noReasonAbsentTimes}}</td>
                <td>{{number_format($user->checkinLateHours,2)}}</td>
                <td>{{$user->checkinLateTimes}}</td>
                <td>{{number_format($user->checkoutSoonHours,2)}}</td>
                <td>{{$user->checkoutSoonTimes}}</td>
                <td>{{$user->noCheckinTimes}}</td>
                <td>{{$user->noCheckoutTimes}}</td>
            </tr>
        @endforeach
        
    </tbody>
</table>