@extends('admin.layouts.default.app')

@push('pageJs')
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/absence.js') }}"></script>
@endpush

@section('content')

    @php
        $canEdit = false;
        $canDelete = false;
    @endphp

    @can('action', $edit)
        @php
            $canEdit = true;
        @endphp
    @endcan

    @can('action', $delete)
        @php
            $canDelete = true;
        @endphp
    @endcan

    <style>
        /* #add_new_absence { margin: 5px 0; } */
        /*.table-scroll th, .table-scroll td {*/
        /*    background: none !important;*/
        /*}*/

        /*.table-striped>tbody>tr:nth-of-type(odd) {*/
        /*    background-color: #cfcfcf !important;*/
        /*}*/
    </style>

    <section class="content-header">
        <h1 class="page-header">@lang('admin.working-schedule.header')</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        @include('admin.includes.work.working-schedule-search')
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <!-- /.box-header -->
                @component('admin.component.table')
                    @slot('columnsTable')
                        <tr>
                            <th class="width3"><a class="sort-link" data-link="{{ route("admin.WorkingSchedule") }}/id/"
                                                  data-sort="{{ $sort_link }}">@lang('admin.stt')</a></th>
                            <th class="width12"><a class="sort-link"
                                                   data-link="{{ route("admin.WorkingSchedule") }}/AssignID/"
                                                   data-sort="{{ $sort_link }}">@lang('admin.working-schedule.assign-id')</a>
                            </th>
                            <th class="width8"><a class="sort-link"
                                                  data-link="{{ route("admin.WorkingSchedule") }}/Date/"
                                                  data-sort="{{ $sort_link }}">@lang('admin.day')</a></th>
                            {{--						<th class="width8"><a class="sort-link" data-link="{{ route("admin.WorkingSchedule") }}/TimeWorking/" data-sort="{{ $sort_link }}">@lang('admin.working-schedule.time-working')</a></th>--}}
                            <th class="width8"><a class="sort-link"
                                                  data-link="{{ route("admin.WorkingSchedule") }}/STime/"
                                                  data-sort="{{ $sort_link }}">@lang('admin.working-schedule.sdate')</a>
                            </th>
                            <th class="width8"><a class="sort-link"
                                                  data-link="{{ route("admin.WorkingSchedule") }}/ETime/"
                                                  data-sort="{{ $sort_link }}">@lang('admin.working-schedule.edate')</a>
                            </th>
                            {{--						<th class="width8"><a class="sort-link" data-link="{{ route("admin.WorkingSchedule") }}/Date/" data-sort="{{ $sort_link }}">@lang('admin.endDate')</a></th>--}}
                            <th class="width15"><a class="sort-link"
                                                   data-link="{{ route("admin.WorkingSchedule") }}/Content/"
                                                   data-sort="{{ $sort_link }}">@lang('admin.working-schedule.content')</a>
                            </th>
                            <th class="width15"><a class="sort-link"
                                                   data-link="{{ route("admin.WorkingSchedule") }}/Address/"
                                                   data-sort="{{ $sort_link }}">@lang('admin.partner.address')</a></th>
                            <th class="width9"><a class="sort-link"
                                                  data-link="{{ route("admin.WorkingSchedule") }}/UserID/"
                                                  data-sort="{{ $sort_link }}">@lang('admin.working-schedule.user-id')</a>
                            </th>
                            <th class="width9"><a class="sort-link"
                                                  data-link="{{ route("admin.WorkingSchedule") }}/updated_at/"
                                                  data-sort="{{ $sort_link }}">@lang('admin.working-schedule.updated_at')</a>
                            </th>
                            @if ($canEdit || $canDelete)
                                <th class="width5">@lang('admin.action')</th>
                            @endif
                            <th class="width9"><a class="sort-link"
                                                  data-link="{{ route("admin.WorkingSchedule") }}/Note/"
                                                  data-sort="{{ $sort_link }}">@lang('admin.working-schedule.note')</a>
                            </th>
                        </tr>
                    @endslot
                    @slot('dataTable')
                        @foreach($working_schedule as $item)
                            <tr class="even gradeC" data-id="">
                                <td class="text-center">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
                                <td class="text-left">
                                    @if(isset($item->AssignID) && $item->AssignID == '0')
                                        @lang('admin.working-schedule.all-user')
                                    @elseif(isset($item->AssignID) && $item->AssignID != '0')
                                        {!! \App\Http\Controllers\Admin\Work\WorkingScheduleController::getListAssignUser($item->AssignID, $users) !!}
                                    @endif
                                </td>
                                <td class="text-center">{{ FomatDateDisplay($item->Date, FOMAT_DISPLAY_DAY) }}</td>
                                <td class="text-center">{{FomatDateDisplay($item->STime, FOMAT_DISPLAY_TIME)}}</td>
                                <td class="text-center">{{FomatDateDisplay($item->ETime, FOMAT_DISPLAY_TIME)}}</td>
                                <td class="left-important" style="word-break: break-word;">{{ $item->Content }}</td>
                                <td class="left-important" style="word-break: break-word;">
                                @if(isset($item->in_out) && $item->in_out == 0)
                                    {{App\Room::find($item->roomsID)->Name}}
                                @elseif(isset($item->in_out) && $item->in_out == 1)
                                    {{ $item->Address}}
                                @endif
                                <!-- @if(isset($item->Address)||isset($item->roomsID))
                                    {{ $item->Address ? $item->Address : App\Room::find($item->roomsID)->Name}}
                                @endif -->
                                </td>
                                <td class="action-col text-left">{{ isset($item->UserID) && $item->UserID != 0 ? App\User::find($item->UserID)->FullName : '' }}</td>
                                <td class="text-center">{{FomatDateDisplay($item->updated_at, FOMAT_DISPLAY_CREATE_DAY)}}</td>
                                @if ($canEdit || $canDelete)
                                    <td class="text-center">
                                        @if ($canEdit)
                                            <span class="action-col update edit update-one" item-id="{{ $item->id }}"><i
                                                    class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                        @endif
                                        @if ($canDelete)
                                            <span class="action-col update delete delete-one" item-id="{{ $item->id }}"><i
                                                    class="fa fa-times" aria-hidden="true"></i></span>
                                        @endif
                                    </td>
                                @endif
                                <td class="left-important" style="word-break: break-word;">{{ $item->Note }}</td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('pageTable')
                        {{ $working_schedule->appends($query_array)->links() }}
                    @endslot
                @endcomponent
                <div id="popupModal">
                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript">
        workingsheduleIot = true;

        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

        $('.btn-search').click(function () {
            $('#absence-search-form').submit();
        });

        $(document).ready(function () {
            let member_td = $("table[name='table']").find("tbody>tr>td:nth-child(2)");
            $(member_td).each(function (index, element) {
                let usernames = $(element).find(".td_user");
                $(usernames).each(function (i, e) {
                    if (i > 2)
                        $(e).addClass("hide");
                });
                if (usernames.length > 3) {
                    $(element).append(`<a href="javascript:void(0)" class="show-more">xem thêm...</a>`);
                }
            });

            $(".show-more").click(function (event) {
                event.preventDefault();
                let td = $(event.target).closest("td");
                let hide = $(td).find(".hide");
                if (hide.length !== 0) {
                    $(td).find(".hide").removeClass("hide");
                    $(event.target).text("ẩn bớt");
                } else {
                    let usernames = $(td).find(".td_user");
                    $(usernames).each(function (i, e) {
                        if (i > 2)
                            $(e).addClass("hide");
                        $(event.target).text("xem thêm...");
                    })
                }
            })
        })
    </script>
@endsection



