@extends('admin.layouts.default.app')

@section('content')
<section class="content-header">
    <h1 class="page-header">@lang('admin.menu.list_menu')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
                @slot('columnsTable')
                    <tr>
                        <th class="width5">@lang('admin.stt')</th>
                        <th class="">@lang('admin.menu.menu_lv1')</th>
                        <th class="width8">@lang('admin.menu.icon')</th>
                        <th class="width8">@lang('admin.menu.gps')</th>
                        <th class="">@lang('admin.menu.menu_lv2')</th>
                    </tr>
                @endslot
                @slot('dataTable')
                    @foreach($array as $key => $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td ><a href="javascript:void(0)" class="update-one" item-id="{{ $item['parent']->id }}">@lang('menu.'.$item['parent']->LangKey)</a></td>
                            <td class="text-center"><i class="{{ $item['parent']->FontAwesome }}"></i></td>
                            <td class="text-center">{{ $item['parent']->Order }}</td>
                            <td>
                                @if(count($item['chill']) > 0)
                                    <ul>
                                        @foreach($item['chill'] as $value)
                                            <li><a href="javascript:void(0)" class="menu-child" data-id="{{$value->id}}">@lang('menu.'.$value->LangKey)</a></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endslot
                @slot('pageTable')
                @endslot
            @endcomponent
        </div>
    </div>
    <div class="modal draggable fade in detail-modal1" id="" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-sm ui-draggable">

            <!-- Modal content-->
            <div class="modal-content drag">
                <div class="modal-header ui-draggable-handle" style="cursor: move;">
                    <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                    <h4 class="modal-title">@lang('admin.menu.addLanguage')</h4>
                </div>
                <form class="detail-form" role="form" action="" method="POST" id="formLang" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="save-errors"></div>
                        {{ csrf_field() }}
                        <div class="box-body">
                            <div class="form-group">
                                <label>@lang('admin.menu.nameShort')<sup class="text-red">*</sup>:</label>
                                <input type="text" class="form-control" name="nameShort" maxlength="10" value="" required>
                            </div>
                            <div class="form-group">
                                <label>@lang('admin.menu.imgLang')<sup class="text-red">*</sup>:</label>
                                <input type="file" name="image" id="img">
                            </div>
                            <div class="form-group">
                                <label>@lang('admin.menu.fileLang')<sup class="text-red">*</sup>:</label>
                                <input type="file" name="files[]" id="thumbnail" multiple>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
                    <button type="button" class="btn btn-primary btn-sm" id="save-form">@lang('admin.btnSave')</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
    <script type="text/javascript" async>
        var ajaxUrl = "{{ route('admin.EditMenu') }}";
        var newTitle = 'Thêm lịch nghỉ';
        var updateTitle = 'Sửa menu';

        $('.menu-child').click(function () {
            $(this).attr('data-id');
            ajaxGetServerWithLoader("{{ route('admin.EditMenu') }}/"+ $(this).attr('data-id'), "GET", null, function(data) {
                $('#popupModal').empty().html(data);
                $('.modal-title').html(updateTitle);
                $('.detail-modal').modal('show');
            });
        });
    </script>
@endsection

