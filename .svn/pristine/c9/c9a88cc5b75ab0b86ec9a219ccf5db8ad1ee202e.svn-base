@extends('admin.layouts.default.app')

@section('content')
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.checkboxes.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.responsive.js') }}"></script>

<style>
    /*.table-scroll th, .table-scroll td {*/
    /*    background: none !important;*/
    /*}*/

    /*.table-striped>tbody>tr:nth-of-type(odd) {*/
    /*    background-color: #cfcfcf !important;*/
    /*}*/
</style>
<section class="content-header">
    <h1 class="page-header">@lang('admin.role.viewRole')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="pull-left">
                <form class="form-inline">
                    <div class="form-group pull-left">
                        <div class="input-group">
                            <select class="selectpicker show-tick show-menu-arrow" id="select" name="alias" data-live-search="true" data-size="5"
                                    data-live-search-placeholder="Search" data-width="180px" data-actions-box="true" tabindex="-98">
                                <option></option>
                                {!! GenHtmlOption($alias, 'alias', 'name', isset($request['alias']) ? $request['alias'] : '') !!}
                            </select>
                        </div>
                    </div>
                    <div class="input-group">
                        <button type="submit" class="btn btn-primary btn-search" id="btn-search" >@lang('admin.btnSearch')</button>
                    </div>
                </form>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            @component('admin.component.table')
                @slot('columnsTable')
                    <tr>
                        <th class="width5">@lang('admin.stt')</th>
                        <th class="width15">@lang('admin.role.nameScreen')</th>
                        <th>@lang('admin.role.isRole')</th>
                        <th>@lang('admin.role.notRole')</th>
                        <th class="width8">@lang('admin.role.function')</th>
                    </tr>
                @endslot
                @slot('dataTable')
                    @foreach($screen as $item)
                        <tr item-alias="{{ $item->alias }}" data-name="{{$item->name}}">
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{!! isset($item->coQuyen) ? $item->coQuyen : '' !!}</td>
                            <td>{!! isset($item->khongQuyen) ? $item->khongQuyen : '' !!}</td>
                            <td class="text-center">
                                <button class="btn btn-default btn-refresh"><span class="action-col refresh" item-id="{{ $item->alias }}"><i class="fa fa-refresh" aria-hidden="true"></i> Xóa</span></button>
                            </td>
                        </tr>
                    @endforeach
                @endslot
                @slot('pageTable')
                @endslot
            @endcomponent
        </div>
    </div>
</section>
<script !src="">
    var ajaxUrl = "{{ route('admin.AbsenceInfo') }}";
    var newTitle = '';
    var updateTitle = '';
    var ALIAS;
    var NEW_TITLE;
    var INDEX;
    var UID;

    $(".selectpicker").selectpicker({
        noneSelectedText : "@lang('admin.role.nameScreen')"
    });

    $(function () {

        //hover btn refresh
        $(".btn-refresh").hover(function(){
            $(this).find('.fa-refresh').addClass("fa-spin");
            $(this).addClass("btn-warning");
        }, function(){
            $(this).find('.fa-refresh').removeClass("fa-spin");
            $(this).removeClass("btn-warning");
        });

        //event click refresh
        $('.refresh').click(function () {
            var itemId  = $(this).attr('item-id');

            console.log(itemId);

            showConfirm('Xóa toàn bộ quyền của màn hình?',
                function(){
                    ajaxGetServerWithLoader("{{ route('admin.refreshRoleScreen') }}?alias="+itemId, 'GET', null, function (data) {
                        if (typeof data.errors !== 'undefined'){
                            showErrors(data.errors);
                            return;
                        }
                        locationPage();
                    });
                }
            );
        });
    });

    // close modal reload page
    $('#popupModal').on('hide.bs.modal', function (e) {
        locationPage();
    });

    $("tbody > tr > td:nth-child(3),tbody > tr > td:nth-child(4)").hover(function(){
        $(this).css("cursor","pointer");
    }, function(){
        $(this).css("cursor","");
    });
</script>
@endsection

