@extends('admin.layouts.default.app')
@php 
    $canApprove = false;
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

@can('action', $approve)
    @php
        $canApprove = true;
    @endphp
@endcan
@push('pageCss')
    <!--<link rel="stylesheet" href="{{ asset('css/overtimeWork.css') }}">-->
@endpush
@section('content')
<section class="content-header">
    <h1 class="page-header">@lang('admin.equipment.registration')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            @include('admin.includes.equipment-registration-search')
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
                @slot('columnsTable')
                    <tr>
                        <th class="width12">@lang('admin.equipment.register_code')</th>
                        <th><a class="sort-link" data-link="{{ route("admin.EquipmentRegistrations") }}/user_id/" data-sort="{{ $sort_link }}">@lang('admin.equipment.register_user')</a></th>
                        <th class="width10"><a class="sort-link" data-link="{{ route("admin.EquipmentRegistrations") }}/created_at/" data-sort="{{ $sort_link }}">@lang('admin.equipment.created_at')</a></th>
                        <th class="width10"><a class="sort-link" data-link="{{ route("admin.EquipmentRegistrations") }}/updated_at/" data-sort="{{ $sort_link }}">@lang('admin.equipment.updated_at')</a></th>
                        <th class="width10"><a class="sort-link" data-link="{{ route("admin.EquipmentRegistrations") }}/requests/" data-sort="{{ $sort_link }}">@lang('admin.equipment.requests')</a></th>
                        <th class="width10"><a class="sort-link" data-link="{{ route("admin.EquipmentRegistrations") }}/processed_requests/" data-sort="{{ $sort_link }}">@lang('admin.equipment.Approved')</a>
                        </th>
                        <th class="width10"><a class="sort-link" data-link="{{ route("admin.EquipmentRegistrations") }}/rejected_requests/" data-sort="{{ $sort_link }}">@lang('admin.equipment.rejected_requests')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.EquipmentRegistrations") }}/status/" data-sort="{{ $sort_link }}">@lang('admin.equipment.status')</a></th>
                        @if ($canEdit || $canDelete || $canApprove)
                        <th class="width8">@lang('admin.action')</th>
                        @endif

                    </tr>
                @endslot
                @slot('dataTable')
                    @foreach($list as $item)
                    <tr class="even gradeC" data-id="10184">
                        <td class="text-center">{{ $item->id }}</td>
                        <td>{{ $item->user_id }}</td>
                        <td class="text-center">{{ FomatDateDisplay($item->created_at, FOMAT_DISPLAY_CREATE_DAY)}}</td>
                        <td class="text-center">{{ FomatDateDisplay($item->updated_at, FOMAT_DISPLAY_CREATE_DAY)}}</td>
                        <td class="text-center">{{ $item->requests }}</td>
                        <td class="text-center">{{ $item->processed_requests }}</td>
                        <td class="text-center">{{ $item->rejected_requests }}</td>
                        <td class="text-center">{!! $item->status == 0 ? "<span class='label label-default'>Chưa xử lý</span>" : "<span class='label label-success'>Đã xử lý</span>" !!}</td>
                        @if ($canEdit || $canDelete || $canApprove)
                        <td class="text-center">
                            @if ($canApprove)
                                <span class="action-col update edit btn-reg-approve" item-id="{{ $item->id }}">{!! $item->status == 0 ? "<i class='fa fa-check'></i>" : "<i class='fa fa-file-text-o'></i>" !!} </span>
                            @endif
                            @if ($canEdit)
                            @if($item->status == 0)
                            <span class="action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                            @endif
                            @endif
                            @if ($canDelete)
                            @if($item->status == 0)
                            <span class="action-col update delete delete-one"  item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                            @endif
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
        </div>
    </div>
</section>
@endsection
@section('js')
    <script type="text/javascript" async>
        var ajaxUrl = "{{ route('admin.EquipmentRegistrationDetail') }}";
        var newTitle = '@lang('admin.equipment.add_registration_form')';
        var updateTitle = '@lang('admin.equipment.update_registration_form')';
        var copyTitle = 'Sao chép thiết bị';
        
        $(function () {
            $(".btn-reg-approve").click(function () {
                
                var itemId = $(this).attr('item-id');
                
                ajaxGetServerWithLoader(
                    '{{ route('admin.EquipmentRegistrationApprove') }}/' + itemId,
                    'GET', null, function (data) {
                        $('#popupModal').empty().html(data);
                        $('.modal-title').html('Duyệt đơn đăng ký thay đổi thiết bị');
                        // $('#user-form')[0].reset();
                        $('.detail-modal').modal('show');
                    }
                );
            });
        });
    </script>
@endsection
