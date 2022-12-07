@extends('admin.layouts.default.app')

@push('pageJs')
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/absence.js') }}"></script>
@endpush

@section('content')
    <section class="content-header">
        <h1 class="page-header">@lang('admin.absence.absence_list_approve')</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form class="form-inline">
                    <div class="input-group pull-left margin-r-5">
                        <input type="text" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
                    </div>
                    <div class="input-group">
                        <button type="submit" class="btn btn-primary btn-search" id="btn-search" >@lang('admin.btnSearch')</button>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
                @component('admin.component.table')
                    @slot('columnsTable')
                        <tr>
                            <th class="width5"><a class="sort-link" data-link="{{ route("admin.AbsencesListApprove") }}/id/" data-sort="{{ $sort_link }}">@lang('admin.stt')</a></th>
                            <th><a class="sort-link" data-link="{{ route("admin.AbsencesListApprove") }}/UID/" data-sort="{{ $sort_link }}">@lang('admin.absence.fullname')</a></th>
                            <th><a class="sort-link" data-link="{{ route("admin.AbsencesListApprove") }}/Rooms/" data-sort="{{ $sort_link }}">@lang('admin.absence.room')</a></th>
                            <th class="width12"><a class="sort-link" data-link="{{ route("admin.AbsencesListApprove") }}/SDate/" data-sort="{{ $sort_link }}">@lang('admin.absence.start')</a></th>
                            <th class="width12"><a class="sort-link" data-link="{{ route("admin.AbsencesListApprove") }}/EDate/" data-sort="{{ $sort_link }}">@lang('admin.absence.end')</a></th>
                            <th class="width8"><a class="sort-link" data-link="{{ route("admin.AbsencesListApprove") }}/TotalTimeOff/" data-sort="{{ $sort_link }}">@lang('admin.absence.time')</a></th>
                            <th><a class="sort-link" data-link="{{ route("admin.AbsencesListApprove") }}/Reason/" data-sort="{{ $sort_link }}">@lang('admin.absence.reason')</a></th>
                            <th><a class="sort-link" data-link="{{ route("admin.AbsencesListApprove") }}/Remark/" data-sort="{{ $sort_link }}">@lang('admin.absence.remark')</a></th>
                            <th class="width8"><a class="sort-link" data-link="{{ route("admin.AbsencesListApprove") }}/AbsentDate/" data-sort="{{ $sort_link }}">@lang('admin.absence.absentDate')</a></th>
                            @can('action', $app)
                            <th class="width8">@lang('admin.approved')</th>
                            @endcan
                        </tr>
                    @endslot
                    @slot('dataTable')
                        @foreach($absence as $item)
                            <tr class="even gradeC" data-id="">
                                <td class="text-center">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
                                <td class="left-important">{{ $item->FullName }}</td>
                                <td class="left-important">{{ $item->RoomName }}</td>
                                <td class="text-center">{{ FomatDateDisplay($item->SDate, FOMAT_DISPLAY_DATE_TIME) }}</td>
                                <td class="text-center">{{ FomatDateDisplay($item->EDate, FOMAT_DISPLAY_DATE_TIME) }}</td>
                                <td >{{ number_format($item->TotalTimeOff/60, 2) }}</td>
                                <td class="left-important">{{ '('.$item->Name.')'.' '.$item->Reason }}</td>
                                <td class="left-important">{!! nl2br(e($item->Remark)) !!}</td>
                                <td class="text-center">{{ FomatDateDisplay($item->AbsentDate, FOMAT_DISPLAY_DAY) }}</td>
                                @can('action', $app)
                                <td class="text-center width8">
                                    @foreach($checkRequestManager as $check)
                                        @if($item->Approved == 0 && ($item->id == $check->id))
                                            <button class="action-col btn btn-success btn-sm btnApr" id="btnApr" id-apr="{{ $item->id }}" type="submit"><i class="fa fa-check" aria-hidden="true"></i></button>
                                            <button class="action-col btn btn-danger btn-sm btnDel" id="btnDel" id-apr="{{ $item->id }}" type="submit"><i class="fa fa-times" aria-hidden="true"></i></button>
                                        @endif
                                    @endforeach
                                </td>
                                @endcan
                            </tr>
                        @endforeach
                    @endslot
                    @slot('pageTable')
                        {{ $absence->appends($query_array)->links() }}
                    @endslot
                @endcomponent
                <div id="popupModal"></div>
            </div>
        </div>
    </section>
@endsection
@section('js')
    <script type="text/javascript" async>

        var ApproveUrl = "{{ route('admin.AprAbsence') }}";
        var unApproveUrl = "{{ route('admin.UnApprove') }}";

        $('.btnApr').click(function () {
            var itemId = $(this).attr('id-apr');
            showConfirm('Bạn có chắc chắn duyệt?',
                function () {
                    ajaxGetServerWithLoader(ApproveUrl + '/' + itemId, 'GET', null, function (data) {
                        if (typeof data.errors !== 'undefined') {
                            showErrors(data.errors);
                            return;
                        }
                        showSuccess(data.success);
                        locationPage();
                    });
                }
            );
        });

        $('.btnDel').click(function () {
            var itemId = $(this).attr('id-apr');
            ajaxServer(unApproveUrl, 'GET', null, function (data) {
                $('#popupModal').empty().html(data);
                $('#req-id').val(itemId);
                $('.detail-modal').modal('show');
            })
        });

    </script>
@endsection



