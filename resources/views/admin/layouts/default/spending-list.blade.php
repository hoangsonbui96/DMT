@extends('admin.layouts.default.app')
@section('content')
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.checkboxes.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.responsive.js') }}"></script>
    <script src="{{ asset('js/easy-number-separator.js') }}"></script>
    <style>
        .table.table-bordered th, .table.table-bordered td {
            border: 1px solid #bdb9b9 !important;
            text-align: center;
            vertical-align: middle !important;
            background-color: #fff;
        }
    </style>
    <section class="content-header">
        <h1 class="page-header">
            {{--        Chi tiêu tháng {{ (\Request::get('date')) ? \Request::get('date[0]') - \Request::get('date[1]') : \Carbon\Carbon::now()->format(FOMAT_DISPLAY_MONTH)}}--}}
            @lang('admin.spending.screen')
        </h1>
    </section>
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
    <section class="content">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form class="form-inline" id="form-search" action="" method="">
                    <div class="form-group pull-left margin-r-5">
                        <select class="selectpicker show-tick show-menu-arrow" id="" name="finance_category" data-size="5" tabindex="-98" data-live-search="{{ isset($cats) && count($cats) > 5 ? 'true' : 'false' }}">
                            <option value="">Tất cả danh mục</option>
                            @foreach($cats as $cat)
                                <option
                                    value="{{ $cat->DataValue }}" {{isset($request['finance_category']) && $request['finance_category'] == $cat->DataValue ? 'selected'  : '' }}>
                                    {{ $cat->Name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group pull-left margin-r-5">
                        <select class="selectpicker show-tick show-menu-arrow" id="finance-cat" name="user_spend" data-size="5" tabindex="-98" data-live-search="true">
                            <option value="">Người chi</option>
                            {!! GenHtmlOption($spendingUsers, 'id', 'FullName', isset($request['user_spend']) ? $request['user_spend'] : '') !!}
                        </select>
                    </div>
                    <div class="form-group pull-left margin-r-5">
                        <div class="input-group search date">
                            <input type="text" class="form-control dtpicker" id="s-date" placeholder="Ngày bắt đầu" name="date[]" autocomplete="off"
                                   value="{{ isset($request['date']) ? $request['date'][0] : \Carbon\Carbon::now()->startOfMonth()->format(FOMAT_DISPLAY_DAY) }}">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group pull-left margin-r-5">
                        <div class="input-group search date">
                            <input type="text" class="form-control dtpicker" id="e-date" placeholder="Ngày kết thúc" name="date[]" autocomplete="off"
                                   value="{{ isset($request['date']) ? $request['date'][1] : \Carbon\Carbon::now()->format(FOMAT_DISPLAY_DAY) }}">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group pull-left margin-r-5">
                        <div class="input-group search">
                            <input type="text" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
                        </div>
                    </div>
                    <div class="form-group pull-left">
                        <button type="submit" class="btn btn-primary btn-search"
                                id="btn-search">@lang('admin.btnSearch')</button>
                    </div>

                    <div class="form-group pull-right">
                        @can('action', $add)
                            <button type="button" class="btn btn-primary btn-detail">Thêm chi tiêu</button>
                        @endcan
                        @can('action', $export)
                            <a class="btn btn-success" id="btn-export" data="{{ isset($request['search']) ? $request['search'] : null  }}">@lang('admin.export-excel')</a>
                        @endcan
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 spending">
                @component('admin.component.table')
                    @slot('columnsTable')
                        <tr>
                            <th class="width5">@lang('admin.stt')</th>

                            <th>@lang('admin.spending.categoryName')</th>
                            <th class="width12">@lang('admin.spending.expense')</th>
                             <th class="width12">@lang('admin.spending.user_chi')</th>
                            <th>@lang('admin.spending.description')</th>
                            <th>@lang('admin.note')</th>
                            <th class="sticky-hz" class="width8">@lang('admin.spending.date')</th>
                            <th class="sticky-hz" class="width8">@lang('admin.spending.date_create')</th>
                            @if ($canEdit || $canDelete)
                            <th class="width8">@lang('admin.action')</th>
                            @endif
                        </tr>
                    @endslot
                    @slot('dataTable')
                        @foreach($spendingList as $item)
                            <tr class="even gradeC" data-id="">
                                <td>{{ ++$stt }}</td>

                                <td class="left-important">{{ $item->categoryName }}</td>
                                <td class="left-important">{{ number_format($item->expense,0,".",",") }}</td>
                                 <td class="left-important">{{ $item->FullName }}</td>
                                <td class ="left-important">{{ $item->desc }}</td>
                                <td class="left-important">{{ $item->note }}</td>
                                <th style="font-weight:normal">{{ FomatDateDisplay($item->date, FOMAT_DISPLAY_DAY) }}</th>
                                <th style="font-weight:normal">{{ FomatDateDisplay($item->created_at, FOMAT_DISPLAY_CREATE_DAY) }}</th>
                                @if ($canEdit || $canDelete)
                                <td class="center-important">
                                    @if ($canEdit)
                                        <span class="action-col update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                    @endif
                                    @if ($canDelete)
                                        <span class="action-col delete-one" item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                                    @endif
                                </td>
                                @endcan
                            </tr>
                        @endforeach
                    @endslot
                     @slot('pageTable')
                            {{ $spendingList->appends($query_array)->links() }}
                    @endslot
                @endcomponent
            </div>
        </div>
    </section>
@endsection
@section('js')
    <script type="text/javascript" async>

        var ajaxUrl = "{{ route('admin.spendingDetail') }}";
        var newTitle = 'Thêm chi tiêu';
        var updateTitle = 'Sửa chi tiêu';

        $(function () {
            SetDatePicker($('.date'));
            $(".selectpicker").selectpicker();
            $('#finance-cat').selectpicker();

            $('#btn-export').click(function () {
                ajaxGetServerWithLoader('{{ route('admin.exportExcelSpending') }}', 'GET', $('#form-search').serializeArray(), function (data) {
                    var req = window.location.search;
                    if (typeof data.errors !== 'undefined') {
                        showErrors('Không có dữ liệu!');
                        return;
                    }

                    window.location.href = '{{ route('admin.exportExcelSpending') }}' + req;
                })
            })
        });
    </script>
@endsection
