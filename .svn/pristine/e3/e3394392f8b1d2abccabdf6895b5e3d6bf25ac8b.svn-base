@extends('admin.layouts.default.app')

@section('content')
<section class="content-header">
    <h1 class="page-header">@lang('admin.equipment.management')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            @include('admin.includes.equipment-search')
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
                @slot('columnsTable')
                    <tr>
                        <th class="width5 text-center">@lang('admin.stt')</th>
                        <th class="left-important"><a class="sort-link" data-link="{{ route("admin.Equipment") }}/code/" data-sort="{{ $sort_link }}">@lang('admin.equipment.code')</a></th>
                        <th class="left-important"><a class="sort-link" data-link="{{ route("admin.Equipment") }}/name/" data-sort="{{ $sort_link }}">@lang('admin.equipment.name')</a></th>
                        <th class="left-important"><a class="sort-link" data-link="{{ route("admin.Equipment") }}/type_id/" data-sort="{{ $sort_link }}">@lang('admin.equipment.type_name')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.Equipment") }}/status_id/" data-sort="{{ $sort_link }}">@lang('admin.equipment.status')</a></th>
                        <th class ="width12"><a class="sort-link" data-link="{{ route("admin.Equipment") }}/period_date/" data-sort="{{ $sort_link }}">@lang('admin.equipment.period_date')</a></th>
                        <th class ="width12"><a class="sort-link" data-link="{{ route("admin.Equipment") }}/serial_number/" data-sort="{{ $sort_link }}">@lang('admin.equipment.serial_number')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.Equipment") }}/info/" data-sort="{{ $sort_link }}">@lang('admin.equipment.info')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.Equipment") }}/room_id/" data-sort="{{ $sort_link }}">@lang('admin.equipment.room_id')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.Equipment") }}/user_owner/" data-sort="{{ $sort_link }}">@lang('admin.equipment.select_register')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.Equipment") }}/deal_date/" data-sort="{{ $sort_link }}">@lang('admin.equipment.deal_date')</a></th>
                        {{-- <th style="width: 60px;"><a class="sort-link">Bảo hành bảo trì</a></th> --}}
                        <th style="width: 120px;">@lang('admin.action')</th>
                    </tr>
                @endslot
                @slot('dataTable')
                    <?php $id = isset($query_array['page']) ? ($query_array['page']-1)*$recordPerPage : 0 ; ?>
                    @foreach($list as $item)
                        <?php $id = $id+1; ?>
                        <tr class="even gradeC" data-id="10184">
                            <td class="text-center"><?php echo $id ?></td>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->name }}</td>
                            @php
                                $checktype = false;
                            @endphp
                            @foreach($eqTypes as $eqType)
                                @if($eqType->type_id == $item->type_id)
                                    <td>{{ $eqType->type_name }}</td>
                                    @php
                                        $checktype = true;
                                    @endphp
                                @endif
                            @endforeach
                            @if($item->type_id != '' && !$checktype)
                                <td></td>
                            @endif
                            <td>{{ $item->status }}</td>
                            <td>{{ isset($item->period_date)? FomatDateDisplay($item->period_date, FOMAT_DISPLAY_DAY) :''}}</td>
                            <td>{{ $item->serial_number }}</td>
                            <td>{{ $item->info }}</td>
                            <td>{{ $item->room }}</td>
                            @if($item->ownerName == '' && $item->user_owner != 0)
                                <td>@lang('admin.equipment.Office')</td>
                            @elseif($item->ownerName == '' && $item->user_owner == 0)
                                <td>@lang('admin.equipment.store')</td>
                            @else
                                <td>{{ $item->ownerName}}</td>
                            @endif
                            <td> {{FomatDateDisplay($item->deal_date, FOMAT_DISPLAY_DAY)}}</td>
                            {{-- <td >
                                <span class="update "  id='view-eqnote' item-id="{{ $item->id }}"><i class="fa fa-file-text-o"></i></span> --}}
                            {{-- @can('action', $edit)
                             <span class="update "  id='edit-eqnote' item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                            @endcan --}}
                            {{-- </td>  --}}
                            <td class="text-center">
                                <span class=" action-col update edit btn-history-device" item-id="{{ $item->id }}"><i class="fa fa-history" data-toggle="tooltip" data-placement="top" title="View History" item-id="{{ $item->id }}"></i></span>
                                @can('action', $edit)
                                    <span class=" action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                @endcan
                                @can('admin', $menu)
                                    @can('action',$add)
                                        <span class=" action-col update edit copy-one" item-id="{{ $item->id }}"><i class="fa fa-copy"></i></span>
                                    @endcan
                                @endcan
                                @can('admin', $menu)
                                    @can('action', $delete)
                                        <span class=" action-col update delete delete-one"  item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                                    @endcan
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
</section>
@endsection
@section('js')
    <script type="text/javascript" async>
        var ajaxUrl = "{{ route('admin.EquipmentInfo') }}";
        var newTitle = '@lang('admin.equipment.add')';
        var updateTitle = '@lang('admin.equipment.update_add')';
        var copyTitle = '@lang('admin.equipment.copy_add')';
        $(".fa-history").click(function(){
            // $('#user-form')[0].reset();
            $('.loadajax').show();
            var unApproveUrl = "{{ route('admin.EquipmentStatusHistories') }}/"+$(this).attr("item-id");
            ajaxServer(unApproveUrl, 'get',null, function (data) {
                $('#popupModal').empty().html(data);
                    $('.modal-title').html("Lịch sử thiết bị");
                    $('.detail-modal').modal('show');
                    $('.loadajax').hide();
            });
        });
        $(document).on('click', '#view-eqnote',function () {
            var itemId = $(this).attr('item-id');
            ajaxUrlview = "{{ route('admin.Maintenance') }}";
            ajaxServer(ajaxUrlview+'/'+itemId, 'get',null, function (data) {
                $('#popupModal').empty().html(data);
                    $('#popupModal').empty().html(data);
                    $('.detail-modal').modal('show');
            });
        });
        $(document).on('click', '#edit-eqnote',function () {
            var itemId = $(this).attr('item-id');
            ajaxUrlup = "{{ route('admin.MaintenanceInfo') }}";
             ajaxServer(ajaxUrlup+'/'+itemId, 'get',null, function (data) {
                $('#popupModal').empty().html(data);
                    $('#popupModal').empty().html(data);
                    $('.detail-modal').modal('show');
            });
        });
    </script>
@endsection
