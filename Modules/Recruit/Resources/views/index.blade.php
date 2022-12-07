@extends('admin.layouts.default.app')
@section('content')
<section class="content-header">
    <h1 class="page-header">Danh sách công việc</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <form class="form-inline">
                <div class="form-group pull-left margin-r-5">
                    <input type="search" class="form-control" placeholder=""
                        name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
                </div>
                <div class="form-group pull-left margin-r-5">
                    <div class="input-group search date" id="">
                        <input type="text" class="form-control" id="date-input" name="StartTime" value=""
                            placeholder="Thời gian bắt đầu">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group pull-left margin-r-5">
                    <div class="input-group search date" id="">
                        <input type="text" class="form-control" id="date-input_end" name="StartTime" value=""
                            placeholder="Thời gian kết thúc">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-search margin-r-5"
                        id="btn-search">@lang('admin.btnSearch')</button>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-primary"
                        id="add_job">@lang('admin.interview.add-recruitment')</button>
                </div>

                <div class="clearfix"></div>
            </form>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
            @slot('columnsTable')
            <tr>
                <th>@lang('admin.stt')</th>
                <th>@lang('admin.interview.name-job')</th>
                <th>@lang('admin.interview.depiction')</th>
                <th>@lang('admin.interview.recruitment-time')</th>
                <th>@lang('admin.interview.name-inter')</th>
                <th>@lang('admin.status')</th>
                <th>@lang('admin.action')</th>
            </tr>
            @endslot
            @slot('dataTable')
            <tr class="even gradeC" data-id="">
                <td class="text-center">1</td>
                <td class="text-center">Test 01</td>
                <td class="text-center">Không có dữ liệu</td>
                <td class="text-center">20/6/2022 15:30</td>
                <td class="text-center">
                    <p>Trung Dũng</p>
                    <p>Huy Hùng</p>
                </td>
                <td class="text-center"><input type="checkbox" class="checkActive" </td>
                <td class="text-center">
                    <span class="action-col update edit inter-view" data-name-id="" item-id=""><i
                            class="fa fa-address-book-o" aria-hidden="true"></i></span>
                    <span class="action-col update edit update-one" item-id=""><i class="fa fa-pencil-square-o"
                            aria-hidden="true"></i></span>
                    <span class="action-col update delete delete-one" rmb-id="0" item-id=""><i class="fa fa-times"
                            aria-hidden="true"></i></span>
                </td>
            </tr>
            <tr class="even gradeC" data-id="">
                <td class="text-center">2</td>
                <td class="text-center">Test 01</td>
                <td class="text-center">Không có dữ liệu</td>
                <td class="text-center">20/6/2022 15:30</td>
                <td class="text-center">
                    <p>Trung Dũng</p>
                    <p>Huy Hùng</p>
                </td>
                <td class="text-center"><input type="checkbox" class="checkActive" </td>
                <td class="text-center">
                    <span class="action-col update edit inter-view" data-name-id="" item-id=""><i
                            class="fa fa-address-book-o" aria-hidden="true"></i></span>
                    <span class="action-col update edit update-one" item-id=""><i class="fa fa-pencil-square-o"
                            aria-hidden="true"></i></span>
                    <span class="action-col update delete delete-one" rmb-id="0" item-id=""><i class="fa fa-times"
                            aria-hidden="true"></i></span>
                </td>
            </tr>
            <tr class="even gradeC" data-id="">
                <td class="text-center">3</td>
                <td class="text-center">Test 01</td>
                <td class="text-center">Không có dữ liệu</td>
                <td class="text-center">20/6/2022 15:30</td>
                <td class="text-center">
                    <p>Trung Dũng</p>
                    <p>Huy Hùng</p>
                </td>
                <td class="text-center"><input type="checkbox" class="checkActive" </td>
                <td class="text-center">
                    <span class="action-col update edit inter-view" data-name-id="" item-id=""><i
                            class="fa fa-address-book-o" aria-hidden="true"></i></span>
                    <span class="action-col update edit update-one" item-id=""><i class="fa fa-pencil-square-o"
                            aria-hidden="true"></i></span>
                    <span class="action-col update delete delete-one" rmb-id="0" item-id=""><i class="fa fa-times"
                            aria-hidden="true"></i></span>
                </td>
            </tr>
            @endslot
            @slot('pageTable')
            {{-- {{ $jobs->appends($query_array)->links() }} --}}
            @endslot
            @endcomponent
            <div id="popupModal"></div>
        </div>
    </div>
</section>
@endsection
