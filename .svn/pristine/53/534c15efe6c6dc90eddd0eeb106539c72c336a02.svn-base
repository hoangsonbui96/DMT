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
    <h1 class="page-header">@lang('admin.equipment.select_type')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <form class="form-inline" id ="meeting-search-form">
                <div class="form-group pull-left margin-r-5">
                    <div class="input-group search">
                        <input type="text" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
                    </div>
                </div>
                <div class="form-group pull-left margin-r-5">
                    <button type="button" class="btn btn-primary btn-search" id="btn-search-meeting">@lang('admin.btnSearch') </button>
                </div>
                <div class="form-group">
                    @can('action',$add)
                    <button type="button" class="btn btn-primary btn-detail" id="add-new-room-btn">@lang('admin.equipment.add_type')</button>
                    @endcan
                </div>
                <div class="clearfix"></div>
            </form>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
                @slot('columnsTable')
                    <tr>
                        <th class="width5">@lang('admin.stt')</th>
                        <th class ="width10"><a class="sort-link" data-link="{{ route("admin.EquipmentType") }}/type_id/" data-sort="{{ $sort_link }}">@lang('admin.equipment.type_id')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.EquipmentType") }}/type_name/" data-sort="{{ $sort_link }}">@lang('admin.equipment.type_name')</a></th>
                        <th class ="width12"><a class="sort-link" data-link="{{ route("admin.EquipmentType") }}/created_user/" data-sort="{{ $sort_link }}">@lang('admin.equipment.created_user')</a></th>
                        <th class ="width12"><a class="sort-link" data-link="{{ route("admin.EquipmentType") }}/created_at/" data-sort="{{ $sort_link }}">@lang('admin.equipment.created_at')</a></th>
                        <th>@lang('admin.equipment.note')</th>
                        @if ($canEdit || $canDelete)
                        <th class="width8">@lang('admin.action')</th>
                        @endif

                    </tr>
                @endslot
                @slot('dataTable')
                    <?php $id = isset($query_array['page']) ? ($query_array['page'] - 1) * $recordPerPage : 0; ?>
                    @foreach($list as $item)
                    <?php $id = $id + 1; ?>
                    <tr class="even gradeC" data-id="10184">
                        <td class="text-center"><?php echo $id ?></td>
                        <td>{{ $item->type_id }}</td>
                        <td>{{ $item->type_name }}</td>
                        <td>{{ $item->created_user }}</td>
                        <td>{{FomatDateDisplay($item->created_at, FOMAT_DISPLAY_CREATE_DAY) }}</td>
                        <td>{!! nl2br(e($item->note)) !!}</td>
                        @if($canEdit || $canDelete)
                        <td class="text-center">
                            @if($canEdit)
                            <span class="action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                            @endif
                            @if($canDelete)
                            <span class="action-col update delete delete-one"  item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @endforeach
                @endslot
                @slot('pageTable')
                    {{ $list->appends($query_array)->links() }}
                @endslot
            @endcomponent
            <!-- /.box -->
        </div>
    </div>
</section>
@endsection
@section('js')
<script type="text/javascript" async>
    var ajaxUrl = "{{ route('admin.EquipmentTypeDetail') }}";
    var newTitle = '@lang('admin.equipment.add_type')';
    var updateTitle = '@lang('admin.equipment.update_type')';
    $(function () {
        $('.btn-search').click(function () {
            $('#meeting-search-form').submit();
        });
    })
</script>
@endsection
