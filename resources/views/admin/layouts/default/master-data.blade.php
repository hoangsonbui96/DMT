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
    <h1 class="page-header">@lang('admin.masterdata.management')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <form class="form-inline">
                <div class="form-group">
                    <select class="selectpicker show-tick show-menu-arrow" id="select-datakey" name="datakey" data-live-search="true"
                            data-size="5" data-live-search-placeholder="Search" data-width="220px" data-actions-box="true" tabindex="-98">
                        {!! GenHtmlOption($groupDatakey, 'DataKey', 'TypeName',isset($request['datakey'])? $request['datakey'] : '') !!}
                    </select>
                </div>
                <div class="form-group ">
                    <div class="input-group search">
                        <input type="search" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
                    </div>
                </div>
                <div class="form-group ">
                    <button type="button" class="btn btn-primary btn-search" id="btn-search-meeting">@lang('admin.btnSearch')</button>
                    @if($canEdit)
                        <button type="button" class="btn btn-primary btn-detail" id="add-new-room-btn" data-value="{{ isset($request['datakey']) ? $request['datakey'] : null }}" data-valuetext="">@lang('admin.masterdata.add')</button>
                    @endif
                </div>
                <div class="clearfix"></div>
            </form>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
                @slot('columnsTable')
                        <tr>
                            <!-- <th class="width5">STT</th> -->
                            <th class="width10" id ='DataValue'><a class="sort-link"  data-link="{{ route("admin.MasterData") }}/DataValue/" data-sort="{{ $sort_link }}">@lang('admin.masterdata.data_value')</a></th>
                            <th id ='Name'><a class="sort-link"  data-link="{{ route("admin.MasterData") }}/Name/" data-sort="{{ $sort_link }}">@lang('admin.masterdata.name')</a></th>
                            <th id ='DataDescription'><a class="sort-link" data-link="{{ route("admin.MasterData") }}/DataDescription/" data-sort="{{ $sort_link }}">@lang('admin.masterdata.description')</a></th>
                            @if ($canEdit || $canDelete)
                                <th class="width8">@lang('admin.action')</th>
                            @endif

                        </tr>
                       @endslot
                @slot('dataTable')
                        @foreach($masterDatas as $item)
                            <tr class="even gradeC" data-id="10184">
                                <!-- <td class="text-center">{{ $item->id }}</td> -->
                                <td class="text-center">{{ $item->DataValue }}</td>
                                <td class="left-important">{{ $item->Name }}</td>
                                <td class="left-important">{!! nl2br(e($item->DataDescription)) !!}</td>
                                @if ($canEdit || $canDelete)
                                <td class="text-center">
                                    @if ($canEdit)
                                    <span class="action-col update edit update-master-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                    @endif
                                    @if ($canDelete)
                                    <span class="action-col update delete delete-master-one"  item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                                    @endif
                                </td>
                                @endif
                            </tr>
                        @endforeach
                        @endslot
                @slot('pageTable')
                    {{ $masterDatas->appends($query_array)->links() }}
                @endslot
            @endcomponent
            <!-- /.box -->

        </div>
    </div>
</section>
@endsection
@section('js')
    <script type="text/javascript" async>
        var title = $('#select-datakey option:selected').text();
        var ajaxUrl = "{{ route('admin.MasterDataItem') }}";
        var newTitle = 'Thêm master data ( '+title+' )';
        var updateTitle = 'Cập nhật master data ( '+title+' )';
        $('#add-new-room-btn').attr('data-valuetext',$('#select-datakey option[selected]').text());
        $('.btn-search').click(function () {
            $('.form-inline').submit();
        });
        if($('#select-datakey').val()=='SK'){
            $('#add-new-room-btn').addClass('hidden');
        }
        $('.update-master-one').click(function (e) {
            e.preventDefault();
            ajaxGetServerWithLoader(genUrlGet([
                ajaxUrl, '/', $(this).attr('item-id')
            ]), 'GET', null, function(data) {
                if (typeof data.errors !== 'undefined'){
                    $('.loadajax').hide();
                    showErrors(data.errors[0]);
                }else{
                    $('.loadajax').hide();
                    $('#popupModal').empty().html(data);
                    $('.modal-title').html(updateTitle);
                    $('.detail-modal').modal('show');
                }
            });
        });
        $('.delete-master-one').click(function (e) {
        e.preventDefault();
        var obj = $(this);
        showConfirm(confirmMsg, function () {

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
                if (typeof data.errors !== 'undefined'){
                    $('.loadajax').hide();
                    showErrors(data.errors[0]);
                }else{
                    $('.loadajax').hide();
                    if(data == 1) locationPage();
                }
            });
        });
    });
    $(function () {
        if($('#select-datakey option:selected').val() == 'WT'){
            $('#Name a').remove();
            $('#DataDescription a').remove();
            $('#Name').append('Giờ bắt đầu');
            $('#DataDescription').append('Giờ kết thúc');
        }
    });
    </script>
@endsection
