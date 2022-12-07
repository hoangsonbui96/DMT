@extends('admin.layouts.default.app')
@push('pageCss')
    <style>
        #page-wrapper{
            /* background: #0079bf;
            overflow-x: scroll; */
        }
        .page-header{
            /* color: white;
            border: none; */
        }
    </style>
@endpush
@push('pageJs')

@endpush
@section('content')
    <div id="container">
        <div class="group-top">
            <div class="col-lg-12">
                <h1 class="page-header">@lang('admin.task.management')</h1>
            </div>
            {{-- <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-8 col-sm-12 col-xs-12">

                </div>
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="add-dReport">
                        <form action="">
                            <button type="button" class="btn btn-primary btn-detail" id="add-new-room-btn">@lang('admin.masterdata.add')</button>
                        </form>
                    </div>
                </div>
                <div class="clear"></div>
            </div> --}}
        </div>
        <div class="row">
            <div class="col-sm-12">

                <div class="pull-right">
                    <div id="dataTables-user_filter" class="dataTables_filter">
                        <label>
                            <form>
                                <input type="search" class="form-control input-sm" placeholder="@lang('admin.search-placeholder')" name="search" value="{{ isset($request['search']) ? $request['search'] : null }}">
                                <input type='submit' value='Search' style="display: none"/>
                            </form>
                        </label>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div class="work-management row">
            @foreach($list as $item)
                <div class="col-md-3 work-table-grid" data-id="{{ $item->id }}">
                    <div class="work-table-w1"><div class="project-header">{{ $item->name }}</div></div>

                </div>
            @endforeach
            <div class="col-md-3 new-work-table">
                <div class="work-table-w1">
                    <span class="wt-new-title">Tạo bảng mới</span>
                    <form class="form-new-work-table form-horizontal" style="display:none;">
                        <div class="form-group">
                            {{-- <label class="col-md-4">
                                Dự án:
                            </label> --}}
                            <div class="col-md-12">
                                <select name="project_id" class="form-control selectpicker col-md-9">
                                    {{-- <option value="0">Không thuộc dự án nào</option> --}}
                                    @foreach($projects as $item)
                                    <option value="{{ $item->id }}">{{ $item->NameVi }}</option>
                                    @endforeach
                                </select>
                            </div>


                        </div>
                        <div class="form-group">
                            {{-- <label class="col-md-4">
                                Tên bảng:
                            </label> --}}
                            <div class="col-md-12">
                                <textarea name="name" class="form-control"></textarea>
                            </div>

                        </div>

                        <div class="form-group" style="text-align: right;">
                            <div class="col-md-12" style="padding: 0px 16px;">
                                <button class="btn btn-success btn-add-work-table">Thêm bảng mới</button>
                                <button class="btn btn-danger">Hủy</button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
            {{-- {{ $list->appends($query_array)->links() }} --}}
        </div>

        <div id="popupModal">

        </div>

    </div>
    <script>
        var ajaxUrl = "{{ route('admin.MasterDataItem') }}";
        var newTitle = 'Thêm phòng mới';
        var updateTitle = 'Cập nhật phòng';
        $(function(){
            $(".selectpicker").selectpicker();
            $(document).on('click', ".btn-danger", function(e) {
                e.preventDefault();
                // $(".form-new-work-list").slideUp();
                $(this).closest('form').animate({ height: 'toggle', opacity: 'toggle' }, 'fast');

            });
            $(document).on('click', ".new-work-table", function(e) {
                var target = $(e.target);
                if(!target.is('.btn-danger') && !target.is('.form-new-work-table .form-group') && !target.is('.form-new-work-table textarea') && !target.is('.form-new-work-table select')){
                    // $(".form-new-work-table").slideUp("fast");
                    $(this).closest('div').find('form').animate({ height: 'toggle', opacity: 'toggle' }, 'fast');
                }

            });
        });
        $(".work-table-grid").click(function(){
            window.location.href = "{{ route('admin.TasksInProject') }}/"+$(this).attr('data-id');
        });
        $(document).on('click', ".btn-add-work-table", function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.TaskNewWorkTable') }}",
                data: $(".form-new-work-table").serializeArray(),
                type: "post",
                success: function (data) {
                    if (typeof data.errors !== 'undefined'){
                        // $('.loadajax').hide();

                        showErrors(data.errors);
                    }else{
                        console.log(data);
                        window.location.reload();
                    }

                },
                fail: function (error) {
                    console.log(error);
                }
            });
        });
    </script>
@endsection
