@push('pageCss')
    <link rel="stylesheet" href="{{ asset('css/timekeeping.css') }}">
@endpush
<div class="table-responsive table-timekeeping-detail">
    <table class="table table-striped table-bordered table-hover data-table">
        <thead class="thead-default">
        <tr>
            <th colspan="11">
                BẢNG CHI TIẾT CHẤM CÔNG
            </th>
        </tr>
        <tr>
            <th colspan="11">
                <div style="float: left;">@lang('admin.timekeeping.userId'): {{ $userSelect->IDFM }}</div>
                <div style="float: right; margin-right: 30px;">@lang('admin.Staffs_name'): {{ $userSelect->FullName }}</div>
            </th>
        </tr>
        <tr>
            <th>@lang('admin.timekeeping.work')</th>
            <th colspan="2">{{ number_format($timekeepings->totalKeeping, 2) }}</th>
            <th rowspan="4"></th>
            <th>@lang('admin.timekeeping.solantre')</th>
            <th>{{ $timekeepings->lateTimes }}</th>
            <th rowspan="4"></th>
            <th colspan="2">@lang('admin.timekeeping.sogiotre')</th>
            <th>{{ number_format($timekeepings->lateHours, 2) }}</th>

            <th rowspan="4"></th>

        </tr>
        <tr>
            <th>@lang('admin.timekeeping.overtime')</th>
            <th>{{ $timekeepings->overKeeping }}</th>
            <th>0.00</th>
            <th>@lang('admin.timekeeping.solansom')</th>
            <th>{{ $timekeepings->soonTimes }}</th>
            <th colspan="2">@lang('admin.timekeeping.sogiosom')</th>
            <th>{{ number_format($timekeepings->soonHours, 2) }}</th>

        </tr>
        <tr>
            <th>@lang('admin.event.day-off')</th>
            <th></th>
            <th></th>
            <th>@lang('admin.timekeeping.absence')</th>
            <th></th>
            <th colspan="2">@lang('admin.timekeeping.pheps')</th>
            <th></th>

        </tr>
        <tr>
            <th>@lang('admin.timekeeping.holiday')</th>
            <th></th>
            <th></th>
            <th>@lang('admin.timekeeping.absenceHaveMoney')</th>
            <th></th>
            <th colspan="2">@lang('admin.timekeeping.absenceNotMoney')</th>
            <th></th>
        </tr>
        <tr>
            <th colspan="11" style="background: yellow;">
                @lang('admin.interview.detail')
            </th>
        </tr>
        <tr>
            <th class="no-sort thead-th-custom" rowspan="2" style="width:15px;"><h1>@lang('admin.day')</h1></th>
            <th class="thead-th-custom" rowspan="2" style="word-wrap: break-word;">@lang('admin.overtime.week_day')</th>
            <th class="thead-th-custom" colspan="2">@lang('admin.timekeeping.TGvaora')</th>
            <th class="thead-th-custom" rowspan="2">@lang('admin.timekeeping.late')<br>(phút)</th>
            <th class="thead-th-custom" rowspan="2">@lang('admin.timekeeping.soon')<br>(phút)</th>
            <th class="thead-th-custom" rowspan="2">@lang('admin.times')</th>
            <th class="thead-th-custom" rowspan="2">@lang('admin.timekeeping.work')</th>
            <th class="thead-th-custom" colspan="2">@lang('admin.timekeeping.overtime')</th>
            <th class="thead-th-custom" rowspan="2">@lang('admin.timekeeping.absence')</th>
        </tr>
        <tr>
            <th class="thead-th-custom">Vào</th>
            <th class="thead-th-custom">Ra</th>
            <th class="thead-th-custom">N (phút)</th>
            <th class="thead-th-custom">Đ (phút)</th>
        </tr>

        </thead>
        <tbody>
        @foreach($timekeepings as $timekeeping)
            <tr

                {{ $timekeeping->weekday == 'CN' ? 'class=weekend-cn' : '' }}

            >
                <td>{{ FomatDateDisplay($timekeeping->Date, FOMAT_DISPLAY_DAY)}}</td>
                <td style="{{ $timekeeping->weekday == 'T7' ? 'background-color: blue !important;' : '' }}">{{ $timekeeping->weekday }}</td>
                <td>{{ $timekeeping->TimeIn }}</td>
                <td>{{ $timekeeping->TimeOut }}</td>
                <td>{{ $timekeeping->late }}</td>
                <td>{{ $timekeeping->soon }}</td>
                <td>{{ number_format($timekeeping->hours, 2) + 0 }}</td>
                <td>{{ $timekeeping->keeping > 1 ? 1 : number_format($timekeeping->keeping, 2) }}</td>
                <td>{{ $timekeeping->N }}</td>
                <td>0</td>
                <td>
                    @foreach($timekeeping->absence as $absence)
                        {{ $absence->Name }} ({{ $absence->STime }} - {{ $absence->ETime }})
                    @endforeach
                </td>

            </tr>
        @endforeach
        </tbody>
    </table>
</div>
