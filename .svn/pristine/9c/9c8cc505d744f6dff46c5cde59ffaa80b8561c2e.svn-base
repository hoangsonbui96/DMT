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
    <h1 class="page-header">@lang('admin.interview.interview-jobs')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <form class="form-inline">
                <div class="form-group pull-left margin-r-5">
                    <input type="search" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
                </div>
                <div class="form-group pull-left">
                    <button type="submit" class="btn btn-primary btn-search" id="btn-search">@lang('admin.btnSearch')</button>
                </div>
                <div class="form-group pull-right">
                    <button type="button" class="btn btn-primary btn-detail" id="add_doc">@lang('admin.interview.add-recruitment')</button>
                </div>
                <div class="clearfix"></div>
            </form>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
                @slot('columnsTable')
                    <tr>
                        <th class=width5>@lang('admin.stt')</th>
                        <th><a class="sort-link" data-link="{{ route("admin.InterviewJob") }}/Name/" data-sort="{{ $sort_link }}">@lang('admin.interview.recruitment-name')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.InterviewJob") }}/Description/" data-sort="{{ $sort_link }}">@lang('admin.interview.depiction')</a></th>
                        <th class="width8"><a class="sort-link" data-link="{{ route("admin.InterviewJob") }}/Active/" data-sort="{{ $sort_link }}">@lang('admin.status')</a></th>
                        <th class="width8">@lang('admin.action')</th>
                    </tr>
                @endslot
                @slot('dataTable')
                    @foreach($jobs as $item)
                        <tr class="even gradeC" data-id="">
                            <td class="text-center">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
                            <td class = "left-important">{{ $item->Name }}</td>
                            <td class = "left-important">{!! nl2br(e($item->Description)) !!}</td>
                            <td class = "text-center"><input type="checkbox" class="checkActive" job-id="{{$item->id}}"
                                    {{ (isset($item->Active) && $item->Active == 1) ? 'checked' : ''}} {{$canEdit == false ? 'disabled' : ''}} /></td>
                            <td class="text-center">
                                <span class="action-col update edit inter-view" data-name-id="{{$item->Name}}" item-id="{{ $item->id }}"><i class="fa fa-address-book-o" aria-hidden="true"></i></span>
                                <span class="action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                <span class="action-col update delete delete-one" rmb-id="0" item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                            </td>
                        </tr>
                    @endforeach
                @endslot
                @slot('pageTable')
                        {{ $jobs->appends($query_array)->links() }}
                @endslot
            @endcomponent
            <div id="popupModal"></div>
        </div>
    </div>
</section>
@endsection

@section('js')
    <script type="text/javascript" async>
        var ajaxUrl = "{{ route('admin.JobInfo') }}";
        var ajaxUrl1 = "{{ route('admin.CandidateList') }}";
        var newTitle = 'Thêm công việc tuyển dụng';
        var newTitle1 ='Danh sách ứng viên';
        var updateTitle = 'Sửa yêu cầu tuyển dụng';
        var updateTitle1 = 'Sửa yêu cầu tuyển dụng';

        $('.inter-view').on('click',function () {
            var itemId = $(this).attr('item-id');
            var name = $(this).attr('data-name-id');
            ajaxServer(ajaxUrl1+'/'+itemId, 'GET', '',function (data) {
                $('#popupModal').empty().html(data);
                $('.modal-title').html(newTitle1+' [ '+name+' ] ');
                $('.detail-modal').modal('show');
                $('.loadajax').hide();
            });
        });

        $('.checkActive').on('change',function () {
            var jobsId = $(this).attr('job-id');
            var active = $(this).prop("checked") == true ? 1 : 0;

            ajaxGetServerWithLoader("{{ route('admin.InterCheckboxActive') }}/"+ jobsId +'/'+ active, 'POST');
        });

    </script>
@endsection

