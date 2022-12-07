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

@push('pageJs')
	<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('js/absence.js') }}"></script>
@endpush
@section('content')
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
    <h1 class="page-header">@lang('admin.listPosition.header')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <form class="form-inline">
                <div class="form-group">
                    <select class="selectpicker show-tick show-menu-arrow" id="select-datakey" name="dataKey" data-live-search="true"
                            data-size="5" data-live-search-placeholder="Search" data-width="220px" data-actions-box="true" tabindex="-98">
                        {!! GenHtmlOption($groupDataKey, 'DataKey', 'TypeName',isset($request['dataKey'])? $request['dataKey'] : '') !!}
                    </select>
                </div>
                <div class="form-group ">
                    <!-- <input type="hidden" class="form-control" name="DataKey"  value="{{ isset($request['dataKey'])? $request['dataKey'] : '' }}" required> -->
                    <div class="input-group search">
                        <input type="search" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
                    </div>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-primary btn-search" id="btn-search-meeting">@lang('admin.btnSearch')</button>
                    @if($canEdit)
                    <button type="button" class="btn btn-primary add-new-room-btn" id="add-new-room-btn" data-value="{{ isset($request['datakey']) ? $request['datakey'] : null }}" data-valuetext="">@lang('admin.listPosition.addPosition')</button>
                    @endif
                </div>
                <div class="form-group pull-right">
                    <div class="input-group" id="area-btn">
                        @if($canEdit)
                        <button type="button" class="btn btn-primary add-new-group-positon-btn" id="add-new-group-positon-btn">@lang('admin.listPosition.add')</button>
                        @endif
                    </div>
                </div>
                <div class="clearfix"></div>
            </form>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
            @slot('columnsTable')
            <tr>
                <!-- <th class="width5">STT</th> -->
                <th class="width15" id='DataValue'><a class="sort-link" data-link="{{ route('admin.ListPosition') }}/DataValue/" data-sort="{{ $sort_link }}">@lang('admin.listPosition.dataValue')</a></th>
                <th id='Name' class="width12"><a class="sort-link" data-link="{{ route('admin.ListPosition') }}" data-sort="">@lang('admin.listPosition.name')</a></th>
                <th id='ListUserName'><a class="sort-link" data-link="{{ route('admin.ListPosition') }}" data-sort="">@lang('admin.listPosition.listUserName')</a></th>
                <th id='Level' class="width12"><a class="sort-link" data-link="{{ route('admin.ListPosition') }}/Level/" data-sort="{{ $sort_link }}">@lang('admin.listPosition.level')</a></th>
                <th id='DataDescription' class="" style="width: 20em;"><a class="sort-link" data-link="{{ route('admin.ListPosition') }}" data-sort="">@lang('admin.listPosition.description')</a></th>
                @if ($canEdit || $canDelete)
                <th class="width8">@lang('admin.action')</th>
                @endif

            </tr>
            @endslot
            @slot('dataTable')
            @foreach($listPosition as $item)
            <tr class="even gradeC" data-id="10184">
                <!-- <td class="text-center">{{ $item->id }}</td> -->
                <td class="text-center">{{ $item->DataValue }}</td>
                <td class="text-center">{{ $item->Name }}</td>

                <td class="text-center">{!! nl2br(e($item->ListUserName)) !!}</td>

                <td class="text-center">{{ $item->Level }}</td>
                <td class="text-center">{!! nl2br(e($item->DataDescription)) !!}</td>
                @if ($canEdit || $canDelete)
                <td class="text-center">
                    @if ($canEdit)
                    <span class="action-col update edit update-master-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                    @endif
                    @if ($canDelete)
                    <span class="action-col update delete delete-master-one" item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                    @endif
                </td>
                @endif
            </tr>
            @endforeach
            @endslot
            @slot('pageTable')
            {{ $listPosition->links() }}
            @endslot
            @endcomponent
            <!-- /.box -->
        </div>
    </div>
</section>
<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
    $('.btn-search').click(function() {
        $('#absence-search-form').submit();
    });
</script>
@endsection
@section('js')
<script type="text/javascript" async>
    var title = $('#select-datakey option:selected').text();
    var ajaxUrl = "{{ route('admin.ListPositionItem') }}";
    var ajaxUrl1 = "{{ route('admin.AddGroupPosition') }}";
    var newTitle = 'Thêm chức vụ ( ' + title + ' )';
    var updateTitle = 'Cập nhật chức vụ ( ' + title + ' )';
    $('#add-new-room-btn').attr('data-value', $('#select-datakey option[selected]').val());
    $('#add-new-room-btn').attr('data-valuetext', $('#select-datakey option[selected]').text());
    $('.btn-search').click(function() {
        $('.form-inline').submit();
    });
    $(".add-new-room-btn").click(function() {
        // $('#user-form')[0].reset();
        $('.loadajax').show();
        $.ajax({
            data: {
                'DataKey': $('#select-datakey option[selected]').val()
            },
            url: ajaxUrl,
            success: function(data) {
                $('#popupModal').empty().html(data);
                $('.modal-title').html(newTitle);
                // $('#user-form')[0].reset();
                $('.detail-modal').modal('show');
                $('.loadajax').hide();
            },
            error: function(data) {
                showErrors(data.responseJSON.error);
                $('.loadajax').hide();
            }
        });
    });
    $(".add-new-group-positon-btn").click(function() {
        // $('#user-form')[0].reset();
        $('.loadajax').show();
        $.ajax({
            data: {
                // 'DataKey': $('#select-datakey option[selected]').val()
            },
            url: ajaxUrl1,
            success: function(data) {
                $('#popupModal').empty().html(data);
                $('.modal-title').html(newTitle);
                // $('#user-form')[0].reset();
                $('.detail-modal').modal('show');
                $('.loadajax').hide();
            },
            error: function(data) {
                showErrors(data.responseJSON.error);
                $('.loadajax').hide();
            }
        });
    });
    $('.update-master-one').click(function(e) {
        e.preventDefault();
        ajaxGetServerWithLoader(genUrlGet([
            ajaxUrl, '/', $(this).attr('item-id')
        ]), 'GET', null, function(data) {
            if (typeof data.errors !== 'undefined') {
                $('.loadajax').hide();
                showErrors(data.errors[0]);
            } else {
                $('.loadajax').hide();
                $('#popupModal').empty().html(data);
                $('.modal-title').html(updateTitle);
                $('.detail-modal').modal('show');
            }
        });
    });
    $('.delete-master-one').click(function(e) {
        e.preventDefault();
        var obj = $(this);
        showConfirm(confirmMsg, function() {

            var keyId = '';

            if (hasAttr(obj, 'item-id')) {
                keyId = obj.attr('item-id');
            } else if (hasAttr(obj, 'user-id')) {
                keyId = obj.attr('user-id');
            }

            if (StringIsNullOrEmpty(keyId)) return;

            ajaxGetServerWithLoader(genUrlGet([
                ajaxUrl, '/', keyId, '/del'
            ]), 'GET', null, function(data) {
                if (typeof data.errors !== 'undefined') {
                    $('.loadajax').hide();
                    showErrors(data.errors[0]);
                } else {
                    $('.loadajax').hide();
                    if (data == 1) locationPage();
                }
            });
        });
    });
</script>
@endsection