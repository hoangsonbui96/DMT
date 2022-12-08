@extends('admin.layouts.default.app')

@push('pageCss')
    <link rel="stylesheet" href="{{ asset('css/timekeeping.css') }}">
@endpush

@section('content')
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

        .warning123 td {
            background-color: #FEEFD0;
        }

        .hover-point:hover {
            background: #c6e2ff;
            cursor: pointer;
        }

        .selected-tr {
            background-color: rgb(255, 99, 132) !important;
            cursor: pointer;
            color: white;
        }

        .weekend-7 td {
            background: #CCCCFF !important;
        }

        .weekend-7 {
            background: #CCCCFF !important;
        }

        .weekend-cn td {
            background: #FF99CC !important;
        }

        .weekend-cn {
            background: #FF99CC !important;
        }

    </style>
    <section class="content-header">
        <h1 class="page-header">@lang('menu.timekeeping-scheduler')</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="time_keeping">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-danger-handle" style="margin: 0; border: none">
                            <p>{{ $errors->first() }}</p>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-8 col-sm-9 col-xs-12">
                            <form class="form-inline" method="get" id="timekeeping-search"
                                  action="{{ route("admin.TimekeepingScheduler") }}">
                                <div class="form-group pull-left margin-r-5" id="cmbSelectUser">
                                    <div class="btn-group bootstrap-select show-tick show-menu-arrow user-custom"
                                         id="action-select-user">
                                        <select class="selectpicker show-tick show-menu-arrow user-custom"
                                                id="select-user" name="users_search[]" data-live-search="true"
                                                data-live-search-placeholder="Search" data-size="5"
                                                data-actions-box="true"
                                                multiple>
                                            {!! GenHtmlOption($users, 'id', 'FullName', request()->get("users_search") ? request()->get("users_search") : null) !!}
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group pull-left margin-r-5 date" id="date">
                                    <div class="input-group search date">
                                        <input type="text" class="form-control" id="date-input" name="date"
                                               value="{{ request()->get('date') ?  request()->get('date') : Carbon\Carbon::now()->format(FOMAT_DISPLAY_DAY) }}">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group pull-left">
                                    <button type="submit" class="btn btn-primary" id="view-dReport">
                                        @lang('admin.btnSearch')
                                    </button>
                                    <button type="button" class="btn btn-primary" id="displayTimekeeping">
                                        Ẩn bảng thống kê
                                    </button>
                                    @can('action', $role["Export"])
                                        <button class="btn btn-success" id="export">
                                            @lang('admin.export-excel')
                                        </button>
                                    @endcan
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive table-timekeeping-detail">
                        <table class="table data-table" id="table1">
                            <thead class="thead-default">
                            <tr>
                                <th colspan="12"
                                    style="background: rgba(255, 225, 0, 0.5); text-align: left !important;">
                                    Dữ liệu thống kê vào :&nbsp; {{ \Carbon\Carbon::now()->format("H:i") }}
                                    ngày {{ \Carbon\Carbon::now()->format(FOMAT_DISPLAY_DAY) }}
                                </th>
                            </tr>
                            <tr>
                                <th>@lang('admin.timekeeping.work')</th>
                                <th colspan="2">{{ number_format($users_keeping->totalKeeping, 2) }}</th>
                                <th rowspan="4"></th>
                                <th>Số người làm việc tại công ty</th>
                                <th class="hover-point th-modal"
                                    data-alias="tkCompany">{{ $users_keeping->checkinAtCompany }}</th>
                                <th rowspan="4"></th>
                                <th>@lang('admin.timekeeping.users-late')</th>
                                <th class="hover-point th-modal"
                                    data-alias="latecomers">{{ $users_keeping->lateTimes }}</th>
                                <th rowspan="4"></th>
                                <th>@lang('admin.timekeeping.sogiotre')</th>
                                <th>{{ number_format($users_keeping->lateHours/60, 2) }}</th>
                            </tr>
                            <tr>
                                <th>@lang('admin.timekeeping.overtime')</th>
                                <th colspan="2">{{ number_format($users_keeping->overKeeping/60, 2) }}</th>
                                <th>Số người làm việc tại nhà</th>
                                <th class="hover-point th-modal"
                                    data-alias="tkHome">{{ $users_keeping->checkinAtHome }}</th>
                                <th>@lang('admin.timekeeping.users-soon')</th>
                                <th class="hover-point th-modal"
                                    data-alias="backSoon">{{ $users_keeping->soonTimes }}</th>
                                <th>@lang('admin.timekeeping.sogiosom')</th>
                                <th>{{ number_format($users_keeping->soonHours/60, 2) }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                    <!-- Table daily report detail -->
                    <div class="table-responsive table-timekeeping">
                        <table class="table data-table" id="table_timekeeping">
                            <thead class="thead-default">
                            <tr>
                                <th class="thead-th-custom" rowspan="2"
                                    style="word-wrap: break-word;">STT
                                </th>
                                <th class="thead-th-custom width5" rowspan="2" colspan="2"
                                    style="word-wrap: break-word;">@lang('admin.Staffs_name')</th>
                                <th class="thead-th-custom" colspan="2">@lang('admin.timekeeping.TGvaora')</th>
                                <th class="thead-th-custom" rowspan="2">@lang('admin.timekeeping.TimeWork')</th>

                                <th class="thead-th-custom" rowspan="2">@lang('admin.timekeeping.late')
                                    <br>(phút)
                                </th>
                                <th class="thead-th-custom" rowspan="2">@lang('admin.timekeeping.soon')
                                    <br>(phút)
                                </th>
                                <th class="thead-th-custom" rowspan="2">@lang('admin.timekeeping.T_Gio')</th>
                                <th class="thead-th-custom" rowspan="2">@lang('admin.timekeeping.T_GioTT')</th>
                                <th class="thead-th-custom" rowspan="2">@lang('admin.timekeeping.total_work')</th>
                                <th class="thead-th-custom" rowspan="2" style="width:210px">@lang('admin.absences')</th>
                                <th class="thead-th-custom" rowspan="2"
                                    style="width:200px">@lang('admin.timekeeping.type')</th>
                                @can("action", $role["Edit"])
                                    <th class="thead-th-custom width5pt" rowspan="2">@lang('admin.action')</th>
                                @endcan
                            </tr>
                            <tr>
                                <th class="thead-th-custom">Vào</th>
                                <th class="thead-th-custom">Ra</th>
                                {{-- <th class="thead-th-custom">N (phút)</th>
                                <th class="thead-th-custom">Đ (phút)</th> --}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users_keeping as $index => $user)
                                <tr data-user-uid="{{ $user->id }}"
                                    data-item-id="{{isset($user->timekeepings->first()->id)
                                    ? $user->timekeepings->first()->id : null}}"
                                    class="{{ !isset($user->timekeepings->first()->id) ? "warning123" : null }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td colspan="2">{{ $user->FullName }}</td>
                                    @if(isset($user->timekeepings->first()->id))
                                        <td>
                                            {{ isset($user->timekeepings->first()->TimeIn) ? $user->timekeepings->first()->TimeIn : null }}
                                        </td>
                                        <td>
                                            {{ isset($user->timekeepings->first()->TimeOut) ? $user->timekeepings->first()->TimeOut : null }}
                                        </td>
                                        <td>
                                            @if($user->timekeepings->first()->STimeOfDay && $user->timekeepings->first()->ETimeOfDay != null)
                                                {{ $user->timekeepings->first()->STimeOfDay}}
                                                - {{ $user->timekeepings->first()->SBreakOfDay ? $user->timekeepings->first()->SBreakOfDay : $WT002->Name }}
                                                <br>
                                                {{ $user->timekeepings->first()->EBreakOfDay ? $user->timekeepings->first()->EBreakOfDay : $WT002->DataDescription }}
                                                - {{ $user->timekeepings->first()->ETimeOfDay }}
                                            @endif
                                        </td>
                                        @php
                                            $late = $user->timekeepings->first()->late != "00:00:00"
                                                ? \Carbon\Carbon::parse($user->timekeepings->first()->late)->format("H:i:s")
                                                : null;
                                            $soon = $user->timekeepings->first()->soon != "00:00:00"
                                               ? \Carbon\Carbon::parse($user->timekeepings->first()->soon)->format("H:i:s")
                                               : null
                                        @endphp
                                        <td class="{{ $late != null ? 'weekend-cn' : null }}">
                                            {{ $late }}
                                        </td>
                                        <td class="{{ $soon != null ? 'weekend-7' : null }}">
                                            {{ $soon }}
                                        </td>
                                        <td>
                                            {{ round($user->timekeepings->first()->hours, 2, PHP_ROUND_HALF_UP) }}
                                        </td>

                                        <td>
                                            {{ isset($user->timekeepings->first()->hoursTT)
                                                ? \Carbon\Carbon::parse($user->timekeepings->first()->hoursTT)->format("H:i:s")
                                                : null }}
                                        </td>
                                        <td>{{ $user->timekeepings->first()->keeping > 1
                                                ? 1
                                                : number_format($user->timekeepings->first()->keeping, 2) }}
                                        </td>
                                        <td>
                                            @foreach($user->timekeepings->first()->absence as $absence)
                                                @if($user->timekeepings->first()->weekday != 'T7' && $user->timekeepings->first()->weekday != 'CN')
                                                    <a class="action-col view view-absence"
                                                       style="text-decoration: none">
                                                        {{ $absence->Name }}
                                                        ({{ \Carbon\Carbon::parse($absence->SDate)->format("H:i") }}
                                                        - {{ \Carbon\Carbon::parse($absence->EDate)->format("H:i") }}
                                                        )
                                                    </a>
                                                    <br/>
                                                @endif
                                            @endforeach
                                            @if($user->timekeepings->first()->calendarEvent)
                                                <a class="action-col view" style="text-decoration: none">
                                                    Làm bù
                                                    ({{ $user->timekeepings->first()->calendarEvent->StartDate }})</a>
                                                <br/>
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($user->timekeepings->first()->type) && count($user->timekeepings->first()->type) > 0)
                                                @foreach($user->timekeepings->first()->type as $type)
                                                    <span>{{ $type->Type }}</span><br>
                                                @endforeach
                                            @elseif ($user->timekeepings->first()->id)
                                                <span>@lang('admin.import')</span>
                                            @endif
                                        </td>
                                        @can ("action", $role["Edit"])
                                            <td class="text-center">
                                                <span class="action-col update edit update-timekeeping">
                                                    <i class="fa fa-pencil-square-o"
                                                       aria-hidden="true"></i>
                                                </span>
                                            </td>
                                        @endcan
                                    @else
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            @foreach($user->timekeepings->first()->absence as $absence)
                                                <a class="action-col view view-absence" style="text-decoration: none">
                                                    {{ $absence->Name }}
                                                    ({{ \Carbon\Carbon::parse($absence->SDate)->format("H:i") }}
                                                    - {{ \Carbon\Carbon::parse($absence->EDate)->format("H:i") }})
                                                </a>
                                                <br/>
                                            @endforeach
                                        </td>
                                        <td></td>
                                        @can ("action", $role["Edit"])
                                            <td></td>
                                        @endcan
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script type="text/javascript" defer>
        $(".selectpicker").selectpicker({
            noneSelectedText: 'Chọn nhân viên'
        });
        SetDatePicker($('.date'));
        $('#displayTimekeeping').click(function (e) {
            if ($('#displayTimekeeping').text() == 'Ẩn bảng thống kê') {
                $('#displayTimekeeping').text('Hiện bảng thống kê');
                $('.table-timekeeping-detail').slideToggle();
            } else {
                $('#displayTimekeeping').text('Ẩn bảng thống kê');
                $('.table-timekeeping-detail').slideToggle();
            }
        });
        $(document).ready(function () {
            $(".hover-point").click((e) => {
                $(".selected-tr").removeClass("selected-tr");
                $(e.target).addClass("selected-tr");
            })

            // Export button download file
            $("#export").click(function (event) {
                event.preventDefault();
                const data = $('#timekeeping-search').serialize();
                const url = "{{ route('admin.ExportTimekeepingScheduler') }}";
                const a = document.createElement("a");
                $(a).attr("target", "_blank");
                $(a).attr("href", url + "?" + data);
                $("body").append(a);
                a.click();
                a.remove();
            })
            // View absence
            $(".view-absence").click(function (e) {
                e.preventDefault();
                const date_input = $("#date-input").val();
                const self = $(e.target);
                const title = 'Lý do vắng mặt ngày ' + date_input + ' của ' + $(self).closest("tr").find("td:nth-child(2)").text();
                const data = {
                    date: date_input.split("/").reverse().join("-"),
                    UserID: $(self).closest("tr").attr("data-user-uid"),
                }
                ajaxGetServerWithLoader("{{ route('admin.AbsenceTimekeepingNew') }}", "POST", data, function (data) {
                    $('#popupModal').empty().html(data);
                    $('.modal-title').html(title);
                    $('#modal-absence-list').modal('show');
                })
            })
            // Action button
            $(".update-timekeeping").click(function (e) {
                e.preventDefault();
                const self = $(e.target);
                const date_input = $("#date-input").val();
                const id = $(self).closest("tr").attr("data-item-id");
                if (!id) {
                    return;
                }
                const url = "{{ route('admin.detailTimekeepingNew') }}/" + id;
                const data = {
                    date: date_input.split("/").reverse().join("-"),
                    searchUser: $(self).closest("tr").attr("data-user-uid"),
                }
                ajaxGetServerWithLoader(url, "GET", data, function (data) {
                    const title = 'Sửa chấm công';
                    $('#popupModal').empty().html(data);
                    $('.modal-title').html(title);
                    $('#timeKeeping-info').modal('show');
                })
            })

            // Open modal summary
            $(".th-modal").click(event => {
                const self = event.target;
                let date_input = $("#date-input").val();
                date_input = date_input.split("/").reverse().join("-")
                if ($(self).text() != 0) {
                    const alias = $(self).attr("data-alias");
                    ajaxGetServerWithLoader("{{ route('admin.latecomers') }}" + '/' + alias + '/' + date_input, "GET", null, function (data) {
                        $('#popupModal').empty().html(data);
                        $('#latecomers-modal').modal('show');
                    });
                } else {
                    setTimeout(function () {
                        $(".selected-tr").removeClass("selected-tr");
                    }, 250);
                }
            })
        })
    </script>
@endsection
