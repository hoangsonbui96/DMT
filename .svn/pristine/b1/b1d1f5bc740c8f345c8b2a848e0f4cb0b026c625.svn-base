@extends('admin.layouts.default.app')
@push('pageJs')
    {{--    <script src="{{ asset('js/jquery.number.min.js') }}"></script>--}}
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>

@endpush
@push('pageCss')
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">

@endpush
@section('content')
    <div id="container">
        <div class="group-top">
            <div class="col-lg-12">
                <h1 class="page-header">@lang('admin.daily.daily_report') - {{ $user->FullName }}</h1>

            </div>
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-8 col-sm-8 col-xs-8">
                    @include('admin.includes.daily-report-search')
                </div>
                <div class="col-md-4 col-sm-4 col-xs-4">
                    <div class="add-dReport">
                        <form action="">
                            @can('action',$add)
                                <button type="button" class="btn btn-primary btn-detail" id="add_daily">@lang('admin.daily.add_daily')</button>
                                <button type="button" class="btn btn-primary" id="add_daily_one">@lang('admin.daily.add_daily_one')</button>
                            @endcan
                        </form>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>

        <div class="table-responsive SummaryMonth" style="display: none;">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th>ToW%</th>
                    @foreach($masterData as $data)
                        <?php $key = $data->DataValue ?>
                        <td>{{ $total->totalHours > 0 ? number_format($total->$key/$total->totalHours*100, 2) : 0 }}%</td>
                    @endforeach
                    <td></td>
                    <th rowspan="2">Project Precent</th>
                </tr>
                <tr>
                    <th>Project</th>
                    @foreach($masterData as $data)
                        <th>{{ $data->Name }}</th>
                    @endforeach
                    {{--                    <th>Read Document</th>--}}
                    {{--                    <th>Code</th>--}}
                    {{--                    <th>Test</th>--}}
                    {{--                    <th>FixBug</th>--}}
                    {{--                    <th>Study</th>--}}
                    {{--                    <th>Management</th>--}}
                    {{--                    <th>Support</th>--}}
                    {{--                    <th>Others</th>--}}
                    <th>Sum</th>
                </tr>
                </thead>
                <tbody>
                @foreach($total as $item)
                    <tr>
                        <td style="font-weight: bold">1</td>
                        <td class="pName">{{ $item->NameVi }}</td>
                        @foreach($masterData as $data)
                            <?php $key = $data->DataValue ?>
                            <td>{{ $item->$key+0 }}</td>
                        @endforeach
                        <td>{{ $item->totalHours }}</td>
                        <td>{{ $total->totalHours > 0 ? number_format($item->totalHours/$total->totalHours*100, 2) : 0 }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="td-last" colspan="8"></td>
                    <th>Total</th>
                    <th>{{ $total->totalHours }}</th>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="table-responsive tbl-dReport">
            <table width="100%" class="table table-striped table-bordered table-hover table-user-groups">
                <thead class="thead-default">
                <tr>
                    <th rowspan="2">ID</th>
                    <th rowspan="2"><a href="">@lang('admin.daily.Date')</a></th>
                    <th rowspan="2"><a href="">@lang('admin.daily.Project')</a></th>
                    <th rowspan="2"><a href="">@lang('admin.daily.Screen_Name')</a></th>
                    <th rowspan="2"><a href="">@lang('admin.daily.Type_Of_Work')</a></th>
                    <th rowspan="2" style="width: 20%"><a href="">@lang('admin.daily.Content')</a></th>
                    <th rowspan="2"><a href="">@lang('admin.daily.Working_Time')</a></th>
                    <th rowspan="1" colspan="2">@lang('admin.daily.Progress')</th>
                    <th rowspan="2" style="width: 10%"><a href="">@lang('admin.daily.Note')</a></th>
                    <th rowspan="2"><a href="">@lang('admin.daily.Date_Create')</a></th>
                    <th rowspan="2">@lang('admin.action')</th>
                </tr>

                <tr>
                    <th rowspan="1" colspan="1"><a href="">@lang('admin.daily.Progressing')</a></th>
                    <th rowspan="1" colspan="1"><a href="">@lang('admin.daily.Delay')</a></th>
                </tr>
                </thead>
                <tbody>
                @foreach($dailyReports as $dailyReport)
                    <tr class="even gradeC" data-id="">
                        <td>{{ $dailyReport->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($dailyReport->Date)->format('d/m/Y') }}</td>
                        <td>{{ $dailyReport->NameVi }}</td>
                        <td>{{ $dailyReport->ScreenName }}</td>
                        <td>{{ $dailyReport->Name }}</td>
                        <td>{{ $dailyReport->Contents }}</td>
                        <td>{{ $dailyReport->WorkingTime }}</td>
                        <td>{{ $dailyReport->Progressing.' %'}}</td>
                        <td>{{ $dailyReport->Delay }}</td>
                        <td>{{ $dailyReport->Note }}</td>
                        <td>{{ \Carbon\Carbon::parse($dailyReport->DateCreate)->format('d/m/Y') }}</td>
                        <td style="width: 120px;">
                            @can('action',$edit)
                                <span class="update edit update-one" item-id="{{ $dailyReport->id }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                            @endcan
                            @can('action',$delete)
                                <span class="update delete delete-one"  item-id="{{ $dailyReport->id }}"><i class="fa fa-times" aria-hidden="true"></i></span>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{--            {{ $absence_report->appends($query_array)->links() }}--}}
        </div>

        <div id="popupModal">
        </div>

    </div>
    <script>
        var ajaxUrl = "{{ route('admin.DailyInfo') }}";
        var newTitle = 'Thêm báo cáo';
        var updateTitle = 'Sửa báo cáo';

        $("#add_daily_one").click(function () {
            // $('#user-form')[0].reset();
            // $('.loadajax').show();
            $.ajax({
                url: "{{ route('admin.DailyInfoOne') }}",
                success: function (data) {
                    $('#popupModal').empty().html(data);
                    $('.modal-title').html(newTitle);
                    // $('#user-form')[0].reset();
                    $('.detail-modal').modal('show');
                    $('.loadajax').hide();
                }
            });
        });
        $(function () {
            $('.btn-show-summary').click(function () {
                var html = $(this).html();
                // console.log(html);
                if(html == 'Hiện tổng hợp báo cáo') html = 'Ẩn tổng hợp báo cáo';
                else html = 'Hiện tổng hợp báo cáo';
                $(this).html(html);
                $('.SummaryMonth').toggle('show');
            });
        });
    </script>
@endsection
