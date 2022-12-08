@if( isset($view) && $view == 0)
    <table>
        <thead>
        <tr>
            <th colspan="6">Danh Sách Tổng Quan Giờ Làm Thêm</th>
        </tr>
        <tr>
            <th>@lang('admin.stt')</th>
            <th>@lang('admin.user.full_name')</th>
            <th>Số lần T2-T7</th>
            <th>Số lần CN</th>
            <th>@lang('admin.overtime.overtime_days')</th>
            <th>@lang('admin.overtime.overtime_hours') (h)</th>
        </tr>
        </thead>
        <tbody>
        @foreach($list as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->FullName }}</td>
                <td>{{ $item->countTimeNotIsWeeken.' ('. $item->countHourNotIsWeeken.'h)'}}</td>
                <td>{{ $item->countTimeIsWeeken.' ('.$item->countHourIsWeeken.'h)'}}</td>
                <td>{{ $item->time }}</td>
                <td>{{ $item->totalHours }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <table>
        <thead>
        <tr>
            <th colspan="13">Danh sách giờ làm thêm</th>
        </tr>
        <tr>
            <th>@lang('admin.stt')</th>
            <th>@lang('admin.user.full_name')</th>
            <th>@lang('admin.overtime.time_work')</th>
            <th>@lang('admin.overtime.week_day')</th>
            <th>@lang('admin.overtime.break_time')(h)</th>
            <th>@lang('admin.overtime.work_hours')(h)</th>
            <th>@lang('admin.overtime.project')</th>
            <th>@lang('admin.overtime.content')</th>
            <th>@lang('admin.overtime.time_log_work')</th>
            <th>@lang('admin.overtime.time_accept_OT')</th>
            <th>@lang('admin.overtime.created_date')</th>
            <th>@lang('admin.overtime.approved_date') / @lang('admin.overtime.approved_person')</th>
            <th>@lang('admin.overtime.status')</th>
        </tr>
        </thead>
        <tbody>
        @foreach($overTimeOfUser as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['FullName'] }}</td>
                <td>
                    @if($item->STime != null && $item->ETime != null)
                        @if ((\Carbon\Carbon::parse($item->STime)->format(' d/m/Y')) == (\Carbon\Carbon::parse($item->ETime)->format(' d/m/Y')))
                            {{ FomatDateDisplay($item->STime, FOMAT_DISPLAY_DAY) }} {{ ' - ' }}
                            {{ $weekMap[\Carbon\Carbon::parse($item->STime)->dayOfWeek] }}<br>
                            {{ FomatDateDisplay($item->STime, FOMAT_DISPLAY_TIME) }} ~ {{ FomatDateDisplay($item->ETime, FOMAT_DISPLAY_TIME) }}
                        @else
                            {{  FomatDateDisplay($item->STime, FOMAT_DISPLAY_DATE_TIME) }} <br> ~ <br> {{ FomatDateDisplay($item->ETime, FOMAT_DISPLAY_DATE_TIME) }}
                        @endif
                    @else
                        {{ '???' }}
                    @endif
                </td>
                <td>
                    @if($item->STime != null && $item->ETime != null)
                        {{ $weekMap[\Carbon\Carbon::parse($item->STime)->dayOfWeek] }}
                    @else
                        {{ $weekMap[\Carbon\Carbon::parse($item->STimeLogOT)->dayOfWeek] }}
                    @endif
                </td>
                <td>{{ $item['BreakTime'] }}</td>

                @if($item->STime != null && $item->ETime != null)
                    @php
                        $OT_time = \Carbon\Carbon::parse($item->STime)->diffInSeconds(\Carbon\Carbon::parse($item->ETime)) /3600 - $item->BreakTime
                    @endphp
                    <td class="center-important">{{ number_format(($OT_time > 0 ? $OT_time : 0), 2)}}</td>
                @else
                    <td class="center-important">{{'???'}}</td>
                @endif

                <td>{{ $item['NameVi'] }}</td>
                <td>{!! nl2br(e($item['Content'])) !!}</td>

                <td class="center-important">
                    @if($item->STimeLogOT != null && $item->ETimeLogOT != null)
                        @if((\Carbon\Carbon::parse($item->STimeLogOT)->format(' d/m/Y')) == (\Carbon\Carbon::parse($item->ETimeLogOT)->format(' d/m/Y')))
                            {{ FomatDateDisplay($item->STimeLogOT, FOMAT_DISPLAY_DAY) }} {{ ' - ' }}
                                {{ $weekMap[\Carbon\Carbon::parse($item->STimeLogOT)->dayOfWeek] }}<br>
                            {{ FomatDateDisplay($item->STimeLogOT, FOMAT_DISPLAY_TIME) }} ~ {{ FomatDateDisplay($item->ETimeLogOT, FOMAT_DISPLAY_TIME) }}
                        @else
                            {{  FomatDateDisplay($item->STimeLogOT, FOMAT_DISPLAY_DATE_TIME) }} <br> ~ <br>
                            {{ FomatDateDisplay($item->ETimeLogOT, FOMAT_DISPLAY_DATE_TIME) }}
                        @endif
                    @else
                        @if((\Carbon\Carbon::parse($item->STime)->format(' d/m/Y')) == (\Carbon\Carbon::parse($item->ETime)->format(' d/m/Y')))
                            {{ FomatDateDisplay($item->STime, FOMAT_DISPLAY_DAY) }} {{ ' - ' }}
                                {{ $weekMap[\Carbon\Carbon::parse($item->STime)->dayOfWeek] }}<br>
                            {{ FomatDateDisplay($item->STime, FOMAT_DISPLAY_TIME) }} ~ {{ FomatDateDisplay($item->ETime, FOMAT_DISPLAY_TIME) }}
                        @else
                            {{  FomatDateDisplay($item->STime, FOMAT_DISPLAY_DATE_TIME) }} <br> ~ <br>
                            {{ FomatDateDisplay($item->ETime, FOMAT_DISPLAY_DATE_TIME) }}
                        @endif
                    @endif
                </td>
                @if($item->acceptedTimeOT != null)
                    <td class="center-important">{{ FomatDateDisplay($item->acceptedTimeOT, FOMAT_DISPLAY_CREATE_DAY) }}</td>
                @else
                    <td class="center-important">{{'' }}</td>
                @endif

                <td>{{  FomatDateDisplay($item['created_at'], FOMAT_DISPLAY_DATE_TIME) }}</td>
                <td>{{ isset($item['ApprovedDate']) ? FomatDateDisplay($item['ApprovedDate'], FOMAT_DISPLAY_DATE_TIME): ''}}
                    <br> {{ $item['NameUpdatedBy'] }}</td>
                <td>{{ $item['Approved'] == 0 ? 'Chưa duyệt' : 'Đã duyệt' }} </td>
            </tr>
        @endforeach

        </tbody>
    </table>
@endif

