@extends('admin.layouts.default.app')
@push('pageJs')
    <!--<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>-->
@endpush
@section('content')
    <section class="content-header">
        <h1 class="page-header">@lang('event::admin.event.management')</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <form class="form-inline">
                    <div class="form-group ">
                        <div class="input-group search">
                            <input type="search" class="form-control" placeholder="@lang('event::admin.event.name')"
                                   name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group search date">
                            <input type="text" class="form-control datepicker" id="s-date"
                                   placeholder="@lang('admin.startDate')" name="Date[]" autocomplete="off"
                                   value="{{ isset($request['Date']) ? $request['Date'][0] : '' }}">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group search date">
                            <input type="text" class="form-control datepicker" id="e-date"
                                   placeholder="@lang('admin.endDate')" name="Date[]" autocomplete="off"
                                   value="{{ isset($request['Date']) ? $request['Date'][1] : '' }}">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-search"
                                id="btn-search-absence">@lang('admin.btnSearch')</button>
                        @can('action', $add)
                            <button type="button" class="btn btn-primary btn-detail"
                                    id="add-new-room-btn">@lang('event::admin.event.add')</button>
                        @endcan
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                @component('admin.component.table')
                    @slot('columnsTable')
                        <tr>
                            <th class="width5pt">@lang('admin.stt')</th>
                            <th><a class="sort-link" data-link="{{ route("admin.Events") }}/Name/"
                                   data-sort="{{ $sort_link }}">@lang("event::admin.event.name")</a></th>
                            <th class="width10"><a class="sort-link" data-link="{{ route("admin.Events") }}/SDate/"
                                                   data-sort="{{ $sort_link }}">@lang('event::admin.event.start_date')</a></th>
                            <th class="width10"><a class="sort-link" data-link="{{ route("admin.Events") }}/EDate/"
                                                   data-sort="{{ $sort_link }}">@lang('event::admin.event.end_date')</a></th>
                            <th class="width10"><a class="sort-link" data-link="{{ route("admin.Events") }}/created_at/"
                                                   data-sort="{{ $sort_link }}">@lang('event::admin.event.created_date')</a>
                            </th>
                            <th class="width12"><a class="sort-link" data-link="{{ route("admin.Events") }}/CreateUID/"
                                                   data-sort="{{ $sort_link }}">@lang('event::admin.event.created_by')</a></th>
                            <th class="width9">@lang('admin.action')</th>
                        </tr>
                    @endslot
                    @slot('dataTable')
                        @php $id = isset($query_array['page']) ? ($query_array['page']-1) * $recordPerPage : 0 ; @endphp
                        @foreach($list as $item)
                            @php $id += 1; @endphp
                            <tr class="even gradeC" data-id="10184">
                                <td class="text-center">{{ $id }}</td>
                                <td>{{ $item->Name }}</td>
                                <td class="text-center">{{ FomatDateDisplay($item->SDate, FOMAT_DISPLAY_DAY) }}</td>
                                <td class="text-center">{{ FomatDateDisplay($item->EDate, FOMAT_DISPLAY_DAY) }}</td>
                                <td class="text-center">{{ FomatDateDisplay($item->created_at, FOMAT_DISPLAY_CREATE_DAY) }}</td>
                                <td>{{ $item->CreateUID }}</td>
                                <td class="text-center">
                                    @can('action',$vote)
                                        @if($item->SDate <= Carbon\Carbon::now()->toDatestring() && $item->EDate >= Carbon\Carbon::now()->toDatestring() && $item->Status == 1)
                                            <span class="action-col update edit open-vote" data-qid="{{ $item->id }}"><i
                                                    class=""
                                                    style="border: 1px solid red; padding: 0px 4px; border-radius: 6px; color: red;">Vote</i></span>
                                        @endif
                                    @endcan
                                    @can('action',$stats)
                                        <span class="action-col update edit view-one-1" item-id="{{ $item->id }}"><i
                                                class="fa fa-pie-chart"></i></span>
                                    @endcan
                                    @can('action',$edit)
                                        <span class="action-col update edit update-one" item-id="{{ $item->id }}"><i
                                                class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                    @endcan
                                    @can('action',$delete)
                                        <span class="action-col update delete delete-one" item-id="{{ $item->id }}"><i
                                                class="fa fa-times" aria-hidden="true"></i></span>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @endslot
                    @slot('pageTable')
                        {{ $list->appends($query_array)->links() }}
                    @endslot
                @endcomponent
            </div>
        </div>
        <div id="modal-vote"></div>
        <script>
            var voteUrl = '{{ route('admin.EventVote') }}';
        </script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js?sensor=false"></script>
    </section>
@endsection
@section ('js')
    <script type="text/javascript" async>
        var ajaxUrl = "{{ route('admin.EventDetail') }}";
        var resultUrl = "{{ route('admin.EventResult') }}";
        var resultTitle = 'Kết quả sự kiện';
        var newTitle = 'Thêm sự kiện';
        var updateTitle = 'Cập nhật sự kiện';

        $(function () {
            SetDatePicker($('.date'));
            $('.view-one-1').click(function () {
                var itemId = $(this).attr('item-id');
                ajaxGetServerWithLoader(resultUrl + '/' + itemId, 'GET', null, function (data) {
                    $('#popupModal').empty().html(data);
                    $('.modal-title').html(resultTitle);
                    $('.detail-modal').modal('show');
                });
            });
        });
    </script>
@endsection
