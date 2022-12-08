@extends('admin.layouts.default.app')
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
@section('content')
<section class="content-header">
    <h1 class="page-header">@lang('admin.room_report.room_report')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            @include('admin.includes.room-report-search')
            @if($errors->any())
            <h4 style="color:red;">{{$errors->first()}}</h4>
            @endif
            @if(Session::has('success'))
                {!! Session::get('success') !!}
            @endif
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
                @slot('columnsTable')
                    <tr>
                        <th class="width5">@lang('admin.stt')</th>
                        <th>@lang('admin.room_report.time')</th>
                        <th>@lang('admin.room_report.week_work')</th>
                        <th>@lang('admin.room_report.unfinished_work')</th>
                        <th>@lang('admin.room_report.Contents')</th>
                        <th>@lang('admin.room_report.noted')</th>
                        <th>@lang('admin.room_report.DateUpdate')</th>
                        @if ($canEdit || $canDelete)
                            <th class="width8">@lang('admin.action')</th>
                        @endif
                    </tr>
                @endslot
                @slot('dataTable')
                        @foreach($list as $item)
                            <tr class="even gradeC" data-id="10184">
                                <td class="text-center">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->SDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($item->EDate)->format(FOMAT_DISPLAY_DAY) }}</td>
                                <td> {!! nl2br(e($item->week_work)) !!} </td>
                                <td>{!! nl2br(e($item->unfinished_work)) !!}</td>
                                <td> {!! nl2br(e($item->Contents)) !!} </td>
                                <td>{!! nl2br(e($item->noted)) !!} </td>
                                <td>  {{ isset($item->DateUpdate) ? \Carbon\Carbon::parse($item->DateUpdate)->format(FOMAT_DISPLAY_DAY) : ''}} </td>

                                @if ($canEdit || $canDelete)
                                    <td class="text-center">
                                        @can('action', $edit)
                                            <span class="action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                        @endcan
                                        @can('action', $delete)
                                            <span class="action-col update delete delete-one"  item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                                        @endcan

                                    </td>
                                @endif
                            </tr>
                        @endforeach
                @endslot
                @slot('pageTable')
                    {{ $list->appends($query_array)->links() }}
                @endslot
            @endcomponent
        </div>
    </div>
@endsection
@section('js')
<script type="text/javascript" async>
        var ajaxUrl = "{{ route('admin.RoomReportInfo') }}";
        var newTitle = 'Thêm mới báo cáo phòng ban';
        var updateTitle = 'Cập nhật báo cáo phòng ban';
        // $(function () {
        //     $('#btn-export-room_report').click(function (e) {
        //         e.preventDefault();
        //         var search = $(this).attr('data');
        //         window.location.href = '{{ route('export.RoomReport') }}/'+search;
        //     });
        // })
    </script>

@endsection
