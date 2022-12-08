@extends('admin.layouts.default.app')
@section('content')
    @push('pageCss')
	    <link rel="stylesheet" href="{{ asset('css/timekeeping.css') }}">
    @endpush
    <style>
        .table th, .table td {
            border: 1px solid #bdb9b9 !important;
            text-align: center;
            vertical-align: middle !important;
            background-color: #fff;
        }
        .table-timekeeping .table tr th {
            background-color: #c6e2ff;
        }
        .weekend-7 td { background: #CCCCFF !important; }
        .weekend-cn td { background: #FF99CC !important; }
        #table1 { margin-bottom: 0px; }
        #table_timekeeping { margin-top: 20px; }
    </style>
    <section class="content-header">
        <h1 class="page-header">Tổng hợp chấm công</h1>
    </section>
    <section class="content">
        <div class="row">
            {{-- <input type="hidden" class="form-control hidden " id="mDate"
                           value="{{ count($idUsers)}}"> --}}
            <div class="col-md-12">
                <form class="form-inline" method="get" id="timekeeping-search">
                    <div class="form-group pull-left margin-r-5 date" id="date">
                        <div class="input-group search date">
                            <input type="text" class="form-control" id="date-input" name="time" value="{{ isset(Request::all()['time']) ? Request::all()['time'] : Carbon\Carbon::now()->format('m/Y') }}" >
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    <div class="form-group pull-left">
                        <button type="button" class="btn btn-primary" id="view-dReport" name="view-dReport">@lang('admin.btnSearch')</button>
                    </div>
                    @can("action", $export)
                    <div class="form-group pull-right">
                        <a id= "exportExcel" class="btn btn-success" target="_blank" href="{{ route("admin.ExportTimeKeepingCompany") }}?date=">@lang('admin.export-excel')</a>
                    </div>
                    @endcan
                </form>
            </div>

        </div>

        <div class="table-responsive table-timekeeping">
            <table class="table data-table" id="table_timekeeping">
                <thead class="thead-default">
                    <tr>
                        <th class="no-sort thead-th-custom" rowspan="2" style="width:100px !important;">@lang('admin.day')</th>
                        <th class="thead-th-custom width5" rowspan="2" style="word-wrap: break-word;">@lang('admin.overtime.week_day')</th>
                        <th class="thead-th-custom" colspan="2">Số người đi muộn</th>
                        <th class="thead-th-custom" rowspan="2">Số người về sớm</th>

                        <th class="thead-th-custom" rowspan="2">Số người không chấm công
                        <th class="thead-th-custom" rowspan="2">Số người chấm công
                        <th class="thead-th-custom" rowspan="2">Số người chấm công từ xa</th>
                        <th class="thead-th-custom" rowspan="2">Số người chấm công tại công ty</th>
                    </tr>
                </thead>
                <tbody>
                    <tbody>
                        @foreach($allMonths as $key => $currentDate)
                            <tr date-item="{{ $key }}" {{ $currentDate['date'] == 'T7' ? 'class=weekend-7' : '' }} {{ $currentDate['date'] == 'CN' ? 'class=weekend-cn' : '' }}>
                                <td  class="date-td">{{ \Carbon\Carbon::parse($key)->format('d/m/Y') }}</td>
                                <td>{{ $currentDate['date'] }}</td>
                                <td colspan="2" class="th-modal hover-point" id="latecomers">
                                    @if(count($currentDate["arrUserActive"]) != (count($currentDate["arrUserActive"]) - count($currentDate["arrUserLateMonth"]) - count($currentDate["arrUserSoonMonth"])))
                                        {{ count($currentDate["arrUserLateMonth"])}}
                                    @endif
                                </td>
                                <td class="th-modal hover-point thead-th-custom" id="backSoon" rowspan="1">
                                    @if(count($currentDate["arrUserActive"]) != count($currentDate["arrUserActive"]) - count($currentDate["arrUserLateMonth"]) - count($currentDate["arrUserSoonMonth"]))
                                        {{count($currentDate["arrUserBackSoonMonth"])}}
                                    @endif
                                </td>
                                <td class="th-modal hover-point thead-th-custom"  id="notKeeping" rowspan="1">
                                    @if(count($currentDate["arrUserActive"]) != count($currentDate["arrUserActive"]) - count($currentDate["arrUserLateMonth"]) - count($currentDate["arrUserSoonMonth"]))
                                        {{count($currentDate["arrUserActive"]) - count($currentDate["arrUserLateMonth"]) - count($currentDate["arrUserSoonMonth"])}}</td>
                                    @endif

                                <td class="th-modal hover-point thead-th-custom" id="tkKeeping" rowspan="1">
                                    @if(count($currentDate["arrUserActive"]) != (count($currentDate["arrUserActive"]) - count($currentDate["arrUserLateMonth"]) - count($currentDate["arrUserSoonMonth"])))
                                        {{count($currentDate["arrUserLateMonth"]) + count($currentDate["arrUserSoonMonth"])}}
                                    @endif
                                </td>
                                <td class="th-modal hover-point thead-th-custom" id="tkHome" rowspan="1">
                                    @if(count($currentDate["arrUserActive"]) != (count($currentDate["arrUserActive"]) - count($currentDate["arrUserLateMonth"]) - count($currentDate["arrUserSoonMonth"])))
                                        {{count($currentDate["arrUserCheckinAtHomeMonth"])}}
                                    @endif
                                </td>
                                <td class="th-modal hover-point thead-th-custom" id="tkCompany" rowspan="1">
                                    @if(count($currentDate["arrUserActive"]) != (count($currentDate["arrUserActive"]) - count($currentDate["arrUserLateMonth"]) - count($currentDate["arrUserSoonMonth"])))
                                        {{count($currentDate["arrUserCheckinAtCpnMonth"])}}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
            </table>
        </div>

    </section>

    <script language="javascript">
        const data = @json($allMonths);
        let dataChange = new Date().toJSON().slice(0,10);
        let currentdate = new Date();
        let timeNow = 'Ngày hôm nay - ' + currentdate.getHours() + ":" + currentdate.getMinutes();

        $(document).ready(function () {

            $(".hover-point").click((e) => {
                $(".selected-tr").removeClass("selected-tr");
                $(e.target).addClass("selected-tr");
            })

            $(".date-td").click(e => {
                const self = e.target;
                const tr = $(self).parent();
                const date = $(tr).attr("date-item");
                dataChange = date;
                let dateNow = new Date().toJSON().slice(0,10);
                if (dateNow == date) {
                    timeNow = 'Ngày hôm nay - ' + currentdate.getHours() + ":" + currentdate.getMinutes();
                } else {
                    var sDate = moment(date.toString(),'YYYY-MM-DD').format('DD/MM/YYYY');
                    timeNow = sDate;
                }
                const data_date = data[date];
            })
        })

        $("#timeDate").text(timeNow);

        $(".th-modal").click(event => {
            const self = event.target;
            const tr = $(self).parent();
            const dateItem = $(tr).attr("date-item");
            if ($(self).text() != 0) {
                const id = $(self).attr("id");
                ajaxGetServerWithLoader("{{ route('admin.latecomers') }}" + '/' + id + '/' + dateItem, "GET", null, function (data) {
                    $('#popupModal').empty().html(data);
                    $('#latecomers-modal').modal('show');
                });
            }  else {
                setTimeout(function(params) {
                    $(".selected-tr").removeClass("selected-tr");
                }, 250);
            }
        })


        $(function () {
            SetMothPicker($('#date'));
            $('#view-dReport').click(function () {
				$('#timekeeping-search').submit();
			});
            $("#exportExcel").attr("href", "{{ route("admin.ExportTimeKeepingCompany") }}?date=" + $("#date-input").val());
        })
    </script>
@endsection


