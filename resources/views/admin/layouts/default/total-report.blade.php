@extends('admin.layouts.default.app')

@push('pageJs')
	<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
@endpush

@section('content')
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.checkboxes.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.responsive.js') }}"></script>
<section class="content-header">
	<h1 class="page-header">@lang('admin.total_report.screen_name')</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
            <form id="frmTotalReportSearch" class="form-inline" method="GET">
                {{-- <div class="form-group select-user">
                    <div class="btn-group bootstrap-select show-tick show-menu-arrow" id="action-select-user">
                        <select class="selectpicker show-tick show-menu-arrow" id="select-user" name="UserID" data-live-search="true" data-size="5" data-live-search-placeholder="Search" data-actions-box="true" tabindex="-98">
                            <option value="">@lang('admin.chooseUser')</option>
                            {!! GenHtmlOption($selectUser, 'id', 'FullName', isset($request['UserID']) ? $request['UserID'] : Auth::user()->id) !!}
                        </select>
                    </div>
                </div> --}}

                {{-- <div class="form-group">
                    <div class="input-group search date">
                        <input type="text" class="form-control dtpicker" id="s-date" placeholder="@lang('admin.StartDate')" name="date[]"
                                value="{{ isset($request['date'] ) ? $request['date'][0] : \Carbon\Carbon::now()->startOfMonth()->format(FOMAT_DISPLAY_DAY) }}">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                    </div>
                </div> --}}

                <div class="form-group pull-left margin-r-5">
                    <div class="input-group search date cMonth">
                        <input type="text" class="form-control dtpicker" id="c-month" placeholder="@lang('admin.month')" name="month"
                                value="{{ isset($request['month'] ) ? $request['month'] : \Carbon\Carbon::now()->format(FOMAT_DISPLAY_MONTH) }}">
                        <div class="input-group-addon">
                            <span class="glyphicon glyphicon-th"></span>
                        </div>
                    </div>

                </div>
                <div class="form-group pull-left">
                    <button type="button" class=" btn btn-primary btn-search" id="btn-search-tr">@lang('admin.btnSearch')</button>
                    {{-- @can('action', $export)
                        <a class="btn btn-success" id="export-tr">Export Excel</a>
                    @endcan --}}
                </div>
                <div class="form-group pull-right">
                    <div class="input-group">
                        <button type="button" class="btn btn-success" id="setting">Cài đặt</button>
                    </div>
                </div>
            </form>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12" style="margin-top:15px">
            <div class="table-responsive tbl-dReport">
                <table width="100%" class="table table-striped table-bordered table-hover table-user-groups" style="background:white;">
                    <thead class="thead-default">
                        <tr>
                            <th>@lang('admin.staff')</th>
                            <th>@lang('admin.total_report.date_not_write')</th>
                            <th>@lang('admin.total_report.write_daily_late')</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="report-data"></tbody>
                </table>
                <div id="dot-loader">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
		</div>

        <div id="myModal">

        </div>
	</div>
</section>
<style>
    td{
        text-align: center;
    }
    #dot-loader
    {
        position: relative;
        margin: 2rem auto;
        width: 15rem;
    }
    #dot-loader div
    {
        width: 15px;
        height: 15px;
        background: #333;
        border-radius: 50%;
        display: inline-block;
        animation: slideDotLoader 1s infinite;
    }
    #dot-loader div:nth-child(1){ animation-delay: .1s; }
    #dot-loader div:nth-child(2){ animation-delay: .2s; }
    #dot-loader div:nth-child(3){ animation-delay: .3s; }
    #dot-loader div:nth-child(4){ animation-delay: .4s; }
    #dot-loader div:nth-child(5){ animation-delay: .5s; }
    @keyframes slideDotLoader {
        0% { transform: scale(1); }
        50% { opacity: .3; transform: scale(2); }
        100% { transform: scale(1); }
    }
</style>
<script>
    var newTitle = 'Danh sách nhân viên không cần viết báo cáo';
    var arrayID = [];
    $(function(){
        window.onbeforeunload = confirmExit;
        SetMothPicker($('.cMonth'));
        $('.modal-dialog').draggable({
            handle: ".modal-header"
        });

        $('#btn-search-tr').click(function () {
			$('#frmTotalReportSearch').submit();
        });
        var arr = {{ $users }};
        if(arr.length == 0){
            $('#dot-loader').hide();
            $('#report-data').append('');
        } else {
            var request;
            function appendReportStatus(index){
                request = $.ajax({
                    url: "{{ route('ajax.getUserReportStatus') }}",
                    type: 'post',
                    data: {'userId' : arr[index], 'month': $('#c-month').val()},
                    success: function (result) {
                        let total = result.data[0].length + result.data[1].length;
                        var html = '<tr>';
                        html += '<td class="left-important">'+result.FullName+'</td>';
                        html += '<td>'+result.data[0]+' (<b>'+result.data[0].length+'</b>)</td>';
                        html += '<td>'+result.data[1]+' (<b>'+result.data[1].length+'</b>)</td>';
                        html += '<td>'+total+'</td>';
                        html += '</tr>';
                        $('#report-data').append(html);
                        if(index == arr.length - 1){
                            $('#dot-loader').hide();
                        }
                        if(index< arr.length -1){
                            appendReportStatus(index + 1);
                        }
                    },
                    fail: function (error) {
                        console.log(error);
                    }
                });
            }
            appendReportStatus(0);
        }

        //click cài đặt
        $('#setting').click(function () {
            ajaxGetServerWithLoader("{{route('admin.TotalReportDetail')}}", "POST", null, function (data) {
                $('#myModal').empty().html(data);
                setDataTable('tableUserNotWriteDaily', 10);
                $('.modal-title').html(newTitle);
                $('.detail-modal').modal('show');
            });
        });

        function confirmExit()
        {
           request.abort();
        }
    });
</script>
@endsection
