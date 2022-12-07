@extends('admin.layouts.default.app')

@section('content')
<style>
    .tbl-top {
        margin-top: 20px;
    }
    .table.table-bordered th,
    .table.table-bordered td {
        border: 1px solid #bdb9b9 !important;
        vertical-align: middle !important;
        background-color: #fff;
    }
</style>
<section class="content-header">
    <h1 class="page-header">@lang('document::admin.list-document')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <form class="form-inline">
                <div class="form-group search pull-left margin-r-5">
                    <input type="text" class="form-control" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
                </div>
                <div class="form-group pull-left margin-r-5">
                    <button type="submit" class="btn btn-primary btn-search" id="btn-search">@lang('admin.btnSearch')</button>
                </div>
                <div>
                    @can('action', $add)
                        <button type="button" class="btn btn-primary btn-detail" id="add_doc">@lang('document::admin.document.add-doc')</button>
                    @endcan
                </div>
            </form>
            <div class="clearfix"></div>
        </div>

        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
                @slot('columnsTable')
                    <tr>
                        <th class="width5"><a class="sort-link" data-link="{{ route("admin.DocumentList") }}/id/" data-sort="{{ $sort_link }}">@lang('admin.stt')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.DocumentList") }}/dType/" data-sort="{{ $sort_link }}">@lang('document::admin.document.dType')</a></th>
                        <th><a class="sort-link" data-link="{{ route("admin.DocumentList") }}/dName/" data-sort="{{ $sort_link }}">@lang('document::admin.document.dName')</a></th>
                        <th class="width12">Mô tả</th>
                        <th class="width12"><a class="sort-link" data-link="{{ route("admin.DocumentList") }}/created_at/" data-sort="{{ $sort_link }}">@lang('document::admin.document.create_at')</a></th>
                        <th class="width12"><a class="sort-link" data-link="{{ route("admin.DocumentList") }}/byUser/" data-sort="{{ $sort_link }}">@lang('document::admin.document.byUser')</a></th>
                        <th class="width8">@lang('admin.action')</th>
                    </tr>
                @endslot
                @slot('dataTable')
                    @foreach($document as $item)
                        <tr class="even gradeC" data-id="">
                            <td class="text-center">{{ $sort == 'desc' ? ++$stt : $stt-- }}</td>
                            <td class ="left-important">{{ $item->Name }}</td>
                            <td class ="left-important">{{ $item->dName }}</td>
                            <td class ="left-important">{{ $item->dDescription }}</td>
                            <td class="text-center">{{ FomatDateDisplay($item->created_at, FOMAT_DISPLAY_DAY) }}</td>
                            <td>{!! nl2br(e($item->FullName)) !!}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.document.signedUrl', [$item->id,'admin.document.show_document']) }}" class="popup{{ $item->id }}"></a>
                                <span class="action-col show_document" item-id="{{ $item->id }}" path-file="@if(empty($item->dUrl)){{ $item->fileName }} @else {{ $item->dUrl }} @endif"
                                    type-upload="{{ $item->typeUpload }}" dName={{ $item->dName }}><i class="fa fa-eye" aria-hidden="true"></i></span>
                                @can('action', $edit)
                                    <span class="action-col update edit update-one" item-id="{{ $item->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                @endcan
                                @can('action', $delete)
                                    <span class="action-col update delete delete-one"  item-id="{{ $item->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                @endslot
                @slot('pageTable')
                    {{ $document->appends($query_array)->links() }}
                @endslot
            @endcomponent
        </div>
        <div id="popupModal"></div>
    </div>
</section>
@endsection

@section('js')
<script type="text/javascript" async>
    var ajaxUrl = "{{ route('admin.DocumentDetail') }}";
    var newTitle = 'Thêm tài liệu';
    var updateTitle = 'Sửa tài liệu';
</script>
<script>
    var urlCheckDocument = "{{ route('admin.document.checkDocument') }}";
        $(document).on('click', '.show_document', function(e) {
            $('.loadajax').show();
            let id_document = $(this).attr('item-id');
            let path_file = $(this).attr('path-file');
            let type_upload = $(this).attr('type-upload');
            let dName = $(this).attr('dName');
            let formData = new FormData();
            formData.append('path_file', path_file);
            formData.append('type_upload', type_upload);
            let classes = '.popup' + id_document;
            let create_signed_url = $(classes).attr('href');
            $.ajax({
                url: create_signed_url,
                type: 'GET',
                success: function(url_show_document) {
                    jQuery.ajax({
                        type: "post",
                        url: urlCheckDocument,
                        data: formData,
                        contentType: false,
                        processData: false,
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(res) {
                            if ($.isEmptyObject(res.errors)) {
                                var windowName = dName;
                                var width = 1000;
                                var height = 650;
                                var left = (screen.width - width) / 2;
                                var top = (screen.height - height) / 2;
                                window.open(url_show_document,
                                    windowName, "width=" + width + ",height=" + height +
                                    ",left=" + left + ",right=" + left + ",top=" + top);
                            } else {
                                showErrors(res.errors);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            if (jqXHR.status == 408) {
                                showErrors('Không tìm thấy file');
                                return;
                            }
                            showErrors('Không tìm thấy file');
                        }
                    }).always(function(jqXHR, textStatus) {
                        $('.loadajax').hide();
                    });
                },
                error: function() {
                    alert('Không tìm thấy file');
                }
            });

        })
</script>
@endsection

