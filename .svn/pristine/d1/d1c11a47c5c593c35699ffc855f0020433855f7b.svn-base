@extends('admin.layouts.default.app')

@section('content')
<style>
    .table th,
    .table td {
        border: 1px solid #bdb9b9 !important;
        text-align: center;
        vertical-align: middle !important;
        background-color: #fff;
    }

    .SummaryMonth .table tr th {
        background-color: #dbeef4;
    }

    .tbl-dReport .table tr th {
        background-color: #c6e2ff;
    }
</style>
<section class="content-header">
    <h1 class="page-header">@lang('admin.absence.absence-reports')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 margin-bottom">
            @include('admin.includes.absence-report-search')
            <input class="width8" type="checkbox" data-toggle="toggle" data-on="Hệ thống" data-off="Máy chấm công"
                data-onstyle="primary" data-offstyle="success" id="viewTime">
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="table-responsive tbl-dReport">
                <table class="table">
                    <thead class="thead-default">
                        <tr>
                            @if($species=='system')
                            <th class="width3" rowspan="2">@lang('admin.stt')</th>
                            <th rowspan="2">@lang('admin.user.full_name')</th>
                            @foreach($master_datas as $master_data)
                            <th colspan="2">
                                <a class="sort-link" data-link="{{ route("admin.AbsenceReports") }}/system/{{
                                    $master_data->DataValue }}/" data-sort="{{ $sort_link }}">
                                    {{ $master_data->Name }}</a>
                            </th>
                            @endforeach
                            <th colspan="2"><a class="sort-link" data-link="{{ route("admin.AbsenceReports")
                                    }}/system/sum/" data-sort="{{ $sort_link }}">@lang('admin.sum')</a></th>
                            <th rowspan="2">@lang('admin.action')</th>
                            @else
                            <th class="width3" rowspan="2">@lang('admin.stt')</th>
                            <th rowspan="2">@lang('admin.user.full_name')</th>
                            <th rowspan="2">Tổng(h)</th>
                            <th rowspan="2">Lần</th>
                            <th colspan="2">Nghỉ có lý do</th>
                            <th colspan="2">Nghỉ không lý do</th>
                            <th colspan="2">Đi muộn</th>
                            <th colspan="2">Về sớm</th>
                            <th>Không checkin</th>
                            <th>Không checkout</th>
                            @endif
                        </tr>
                        @if($species=='system')
                        <tr>
                            @foreach($master_datas as $master_data)
                            <th rowspan="1" colspan="1">Giờ</th>
                            <th rowspan="1" colspan="1">Lượt</th>
                            @endforeach
                            <th rowspan="1" colspan="1">Giờ</th>
                            <th rowspan="1" colspan="1">Lượt</th>
                        </tr>
                        @else
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
                        @endif
                    </thead>
                    <tbody>
                        @if($species=='system')
                        @foreach($absence_report as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="left-important">{{ $item->FullName }}</td>
                            @foreach($item->hours as $key => $value)
                            @if($value > 0)
                            <td>
                                {{ number_format($value/60, 1) }}
                            </td>
                            <td>
                                {{ $item->times[$key] }}
                            </td>
                            @else<td> - </td>
                            <td> - </td>@endif

                            @endforeach
                            {{-- <td>{{ number_format(array_sum($item->hours)/60, 2) }}h <br>
                                ({{ array_sum($item->times) }} lượt)

                            </td> --}}
                            <td>
                                {{ number_format((array_sum($item->hours) - $item->hours[7] - $item->hours[5])/60, 2) }}
                            </td>
                            <td>
                                {{ array_sum($item->times) }}
                            </td>
                            <td>
                                <a href="{{ route('admin.Absences') }}?UID={{ $item->UID }}"
                                    class="btn btn-default">@lang('admin.view')</a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <?php $num=0;?>
                        @foreach($listUsers as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="left-important">{{$item->FullName}}</td>
                            <td>{{number_format($item->noReasonAbsentHours + $item->checkinLateHours +
                                $item->checkoutSoonHours + $item->hasReasonAbsentHours,2) ?? '--'}}</td>
                            <td>{{$item->noReasonAbsentTimes + $item->checkinLateTimes + $item->checkoutSoonTimes +
                                $item->hasReasonAbsentTimes}}</td>
                            <td>{{$item->hasReasonAbsentHours > 0 ? number_format($item->hasReasonAbsentHours,2) :
                                '--'}}</td>
                            <td>{{$item->hasReasonAbsentTimes >0 ? $item->hasReasonAbsentTimes : '--'}}</td>
                            <td>{{$item->noReasonAbsentHours > 0 ? number_format($item->noReasonAbsentHours,2) : '--'}}
                            </td>
                            <td>{{$item->noReasonAbsentTimes > 0 ? $item->noReasonAbsentTimes : '--'}}</td>
                            <td>{{$item->checkinLateHours > 0 ? number_format($item->checkinLateHours,2) : '--'}}</td>
                            <td>{{$item->checkinLateTimes >0 ? $item->checkinLateTimes : '--'}}</td>
                            <td>{{$item->checkoutSoonHours > 0 ? number_format($item->checkoutSoonHours,2):'--'}}</td>
                            <td>{{$item->checkoutSoonTimes >0 ? $item->checkoutSoonTimes : '--'}}</td>
                            <td>{{$item->noCheckinTimes >0 ? $item->noCheckinTimes: '--'}}</td>
                            <td>{{$item->noCheckoutTimes > 0? $item->noCheckoutTimes : '--'}}</td>

                            {{-- @for($i=0;$i<=count($totalHours);$i++) @if($i==$num) <td>{{round($totalHours[$i],2)}}
                                </td>
                                @endif
                                @endfor
                                @for($i=0;$i<=count($totalTimes);$i++) @if($i==$num) <td>{{$totalTimes[$i]}}</td>
                                    @endif
                                    @endfor
                                    @for($i=0;$i<=count($arrayOffWorkHours);$i++) @if($i==$num) <td>
                                        {{round($arrayOffWorkHours[$i],2)}}</td>
                                        @endif
                                        @endfor
                                        @for($i=0;$i<=count($arrayOffWorkTimes);$i++) @if($i==$num) <td>
                                            {{$arrayOffWorkTimes[$i]}}</td>
                                            @endif
                                            @endfor
                                            @for($i=0;$i<=count($arrayOutHours);$i++) @if($i==$num) <td>
                                                {{round($arrayOutHours[$i],2)}}</td>
                                                @endif
                                                @endfor
                                                @for($i=0;$i<=count($arrayOutTimes);$i++) @if($i==$num) <td>
                                                    {{$arrayOutTimes[$i]}}</td>
                                                    @endif
                                                    @endfor
                                                    @for($i=0;$i<=count($arrayLateHours);$i++) @if($i==$num) <td>
                                                        {{round($arrayLateHours[$i],2)}}</td>
                                                        @endif
                                                        @endfor
                                                        @for($i=0;$i<=count($arrayLateTimes);$i++) @if($i==$num) <td>
                                                            {{$arrayLateTimes[$i]}}</td>
                                                            @endif
                                                            @endfor
                                                            @for($i=0;$i<=count($arrayEarlyHours);$i++) @if($i==$num)
                                                                <td>{{round($arrayEarlyHours[$i],2)}}</td>
                                                                @endif
                                                                @endfor
                                                                @for($i=0;$i<=count($arrayEarlyTimes);$i++)
                                                                    @if($i==$num) <td>{{$arrayEarlyTimes[$i]}}</td>
                                                                    @endif
                                                                    @endfor --}}
                        </tr>
                        <?php $num++;?>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div id="popupModal"></div>
        </div>
    </div>
</section>
@endsection
@section('js')
<script type="text/javascript" async>
    var sDate = '';
        var eDate = '';

        $(function () {
            if('{{$species}}'!='system'){
                $('#viewTime').prop('checked', false).change();
            }else{
                $('#viewTime').prop('checked', true).change();
            }
            
            if($('#s-date input:text').val() != ''){
                sDate = $('#s-date').val();
            }
            if($('#e-date input:text').val() != ''){
                eDate = $('#e-date').val();
            }

            $('.table tbody tr td:last-child a.btn-default').each(function(i, e) {
                let valueAttr = $(this).attr('href');
                $(this).attr('href', valueAttr+'&Date%5B%5D='+sDate+'&Date%5B%5D='+eDate+'&approve=2');
            });
            $('#AbRportExport').click(function (e) {
                e.preventDefault();
                var uid = $('#select-user option:selected').val();
                var sDate = $('#s-date').val();
                var eDate = $('#e-date').val();
                var species = '{{$species}}';
                console.log(species);
                ajaxGetServerWithLoader('{{ route('export.AbsencesReport') }}'+'?species='+species,'GET'
                    , $('#absenceReport-search-form').serializeArray(),function (data) {
                        if (typeof data.errors !== 'undefined'){
                            showErrors(data.errors);
                        }else{
                            window.location.href = '{{ route('export.AbsencesReport') }}'+'?species='+species+'&'+'UID='+uid+'&date[0]='+sDate+'&date[1]='+eDate;
                        }
                    });
            });
            $('#viewTime').change(function() {
                $('.loadajax').show();
                if($(this).prop('checked')){
                    window.location.href = '{{ route('admin.AbsenceReports') }}'+'?species=system';
                }else{
                    window.location.href = '{{ route('admin.AbsenceReports') }}'+'?species=timekeeper';
                }
            });
        })
</script>
@endsection