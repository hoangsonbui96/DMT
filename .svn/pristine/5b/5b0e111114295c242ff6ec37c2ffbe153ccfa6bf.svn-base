@extends('admin.layouts.default.app')
@section('content')
<section class="content-header">
    <h1 class="page-header">@lang('admin.equipment.handover')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            @include('admin.includes.equipment-history-search')
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
                @slot('columnsTable')
                    <tr>
                        <th class="width5">@lang('admin.stt')</th>
                        <th><a class="sort-link" data-link="{{ route("admin.EquipmentHistories") }}/code/" data-sort="{{ $sort_link }}">@lang('admin.equipment.code')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.EquipmentHistories") }}/name/" data-sort="{{ $sort_link }}">@lang('admin.equipment.name')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.EquipmentHistories") }}/type_name/" data-sort="{{ $sort_link }}">@lang('admin.equipment.type_name')</a></th>
                         <th><a class="sort-link" data-link="{{ route("admin.EquipmentHistories") }}/old_user_owner/" data-sort="{{ $sort_link }}">@lang('admin.equipment.old_owner')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.EquipmentHistories") }}/user_owner/" data-sort="{{ $sort_link }}">@lang('admin.equipment.receive_owner')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.EquipmentHistories") }}/created_user/" data-sort="{{ $sort_link }}">@lang('admin.equipment.created_user')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.EquipmentHistories") }}/status_id/" data-sort="{{ $sort_link }}">@lang('admin.equipment.current_status')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.EquipmentHistories") }}/deal_date/" data-sort="{{ $sort_link }}">@lang('admin.equipment.deal_date')</a></th>

                    </tr>
                @endslot
                @slot('dataTable')
                    <?php $id = isset($query_array['page']) ? ($query_array['page']-1)*$recordPerPage : 0 ; ?>
                    @foreach($list as $item)
                        <?php $id = $id+1; ?>
                        <tr class="even gradeC">
                            <td class="text-center"><?php echo $id ?></td>
                            <td class="left-important">{{ $item->code }}</td>
                            <td class="left-important">{{ $item->eqName }}</td>
                            <td class="left-important">{{ $item->eqTypeName }}</td>
                            <td class="center-important">{{ $item->oldOwnerName }}</td>
                            <td class="left-important">{{ $item->ownerName }}</td>
                            <td class="left-important">{{ $item->created_user_name }}</td>
                            <td>{{ $item->current_status }}</td>
                            <td>{{FomatDateDisplay($item->deal_date, FOMAT_DISPLAY_DAY)}}</td>
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
        var ajaxUrl = "{{ route('admin.EquipmentHistoryDetail') }}";
        var newTitle = '@lang('admin.equipment.handover')';
        var updateTitle = '@lang('admin.equipment.update_add')';
        var copyTitle = '@lang('admin.equipment.copy_add')';
    </script>
@endsection
