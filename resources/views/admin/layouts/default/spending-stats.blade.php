@extends('admin.layouts.default.app')
@section('content')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('js/dataTables.checkboxes.min.js') }}"></script>
<script src="{{ asset('js/dataTables.responsive.js') }}"></script>
<style>
    .table.table-bordered th, .table.table-bordered td {
        border: 1px solid #bdb9b9 !important;
        text-align: center;
        vertical-align: middle !important;
        background-color: #fff;
    }
    #form-search div{
        margin-right: 5px;
    }

</style>
<section class="content-header left232 daily-header top49">
    <h1 class="page-header">Biểu đồ Chi tiêu
        {{
            \Request::get('year_start') && \Request::get('year_start') != \Request::get('year_end') ? \Request::get('year_start')." - " : ''
        }}
        {{
            \Request::get('year_end') ? \Request::get('year_end') : \Carbon\Carbon::now()->year
        }}
    </h1>
</section>
<section class="content" style="margin-top:35px;">
	<div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12">
        <form class="form-inline" id="form-search" action="" method="">
            <div class="form-group pull-left">
                <select class="selectpicker show-tick show-menu-arrow" id="finance-cat" name="finance_category[]" tabindex="-98" data-live-search="true" multiple>
                    <option value="" {{isset($request['finance_category']) && in_array('', $request['finance_category']) ? 'selected'  : '' }}>Tổng</option>
                    @foreach($cats as $cat)
                    <option value="{{ $cat->DataValue }}" {{isset($request['finance_category']) && in_array($cat->DataValue, $request['finance_category']) ? 'selected'  : '' }}>{{ $cat->Name }}</option>
                    @endforeach
                </select>
            </div>
            {{-- <div class="form-group pull-left">
                <select class="selectpicker show-tick show-menu-arrow" name="user_spend" tabindex="-98" data-live-search="true">
                    <option value="">Người chi</option>
                    @foreach($spendingUsers as $user)
                    <option value="{{ $user->user_spend }}" {{isset($request['user_spend']) && $request['user_spend'] == $user->user_spend ? 'selected'  : '' }}>{{ $user->FullName }}</option>
                    @endforeach
                </select>
            </div> --}}
            <div class="form-group pull-left">
                <div class="input-group search">
                    <input type="text" class="form-control" id="date-input" name="year_start" value="{{!isset($request['year_start']) ? Carbon\Carbon::now()->format('Y') : $request['year_start'] }}" autocomplete="off">
                </div>
            </div>
            <div class="form-group pull-left">
                <div class="input-group search">
                    <input type="text" class="form-control" id="date-input2" name="year_end" value="{{!isset($request['year_end']) ? Carbon\Carbon::now()->format('Y') : $request['year_end'] }}" autocomplete="off">
                </div>
            </div>
            <div class="form-group pull-left">
                <button type="submit" class="btn btn-primary btn-search" id="btn-search">@lang('admin.btnSearch')</button>
            </div>


            <div class="clearfix"></div>
        </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 spending">
            <div id="finance-stats" style="height: 500px;">
                {{-- <div class="loadding"><img src="{{asset('images/loading.svg')}}" height="40" class="rotate2"></div> --}}
            </div>
		</div>
    </div>
</section>
@endsection
@section('js')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js?sensor=false"></script>
<script type="text/javascript" async>

    var ajaxUrl = "{{ route('admin.spendingDetail') }}";
    var newTitle = 'Thêm chi tiêu';
    var updateTitle = 'Sửa chi tiêu';
    var days = 10;
    var loadClicks = false;
    var loadStats = false;
    $(function () {
        $(".selectpicker").selectpicker({
            noneSelectedText : "@lang('admin.spending.categoryName')"
        });

        $('#date-input, #date-input2').datetimepicker({
            format: 'Y',
            // minDate: '{{ $minDate }}',
            // maxDate: 'now'
        });

        //chart
        google.load("visualization", "1", {packages:["corechart"]});

        var startYear = {{ !isset($request['year_start']) ? Carbon\Carbon::now()->format('Y') : $request['year_start'] }};
        var endYear = {{ !isset($request['year_end']) ? Carbon\Carbon::now()->format('Y') : $request['year_end'] }};
        var cat = '{{ isset($request['finance_category']) && count($request['finance_category'])  ? implode(',',$request['finance_category']) : implode(',', ['all']) }}';
        var user = {{ isset($request['user_spend']) ? $request['user_spend']  : 0 }};
        var loadClicks = true;
        var totalChartType = {{ !isset($request['finance_category']) ? $cats->count() + 1 : count($request['finance_category']) }};
        google.setOnLoadCallback(financeStats);
        function financeStats() {

            var jsonData = $.ajax({
                url: '{{ route("admin.postStats") }}',
                type: 'post',
                data: {startYear: startYear, endYear: endYear, cat: cat, user: user},
                dataType: "json",
                async: false
            }).responseText;
            jsonData = JSON.parse(jsonData);

            for(var j = 0; j < jsonData[0].length; j++){
                try {
                    jsonData[0][j] = JSON.parse(jsonData[0][j]);

                } catch (error) {
                    // console.log(error);
                }
            }
            // console.log(jsonData);
            var data = google.visualization.arrayToDataTable(jsonData);
            var chart = new google.visualization.ComboChart(document.getElementById('finance-stats'));
            var columns = [];
            var series = {};
            var options = {
                chartArea: {width: '86%', height: '70%', top:20},
                fontSize: ['13'],
                seriesType: 'bars',
                // series: {5: {type: 'line'}}
                series: series,
                legend: { position: 'bottom' },
                annotations: {
                    textStyle: {
                        color: 'black',
                        fontSize: 11,
                    },
                    alwaysOutside: true
                },
            };
            $( window ).resize(function() {
                chart.draw(data, options);
            }).trigger('resize');

            for (var i = 0; i < data.getNumberOfColumns(); i++) {
                columns.push(i);
                if (i > 0) {
                    series[i - 1] = {};
                }
            }
            console.log(columns.length);
            google.visualization.events.addListener(chart, 'select', function () {
                var sel = chart.getSelection();
                // if selection length is 0, deselected an element
                if (sel.length > 0) {
                    // if row is undefined, clicked on the legend
                    if (sel[0].row === null) {
                        var col = sel[0].column;
                        if(columns[col] == col) {
                            // hide the data series
                            columns[col] = {
                                label: data.getColumnLabel(col),
                                type: data.getColumnType(col),
                                calc: function () {
                                    return null;
                                }
                            };
                            // grey out the legend entry
                            if(startYear < endYear){
                                if(col < columns.length - 2*totalChartType){
                                    series[col - 1].color = '#CCCCCC';
                                }
                                for(var j = 0; j < totalChartType; j++){
                                    if(col == columns.length - 2*totalChartType +2*j)
                                    series[columns.length - 2*totalChartType + j -1].color = '#CCCCCC';
                                }
                            }else{
                                series[col - 1].color = '#CCCCCC';
                            }

                        }
                        else{
                            // show the data series
                            columns[col] = col;
                            if(startYear < endYear){
                                if(col < columns.length - 2*totalChartType){
                                    series[col - 1].color = null;
                                }
                                for(var j = 0; j < totalChartType; j++){
                                    if(col == columns.length - 2*totalChartType +2*j)
                                    series[columns.length - 2*totalChartType + j -1].color = null;
                                }
                            }else{
                                series[col - 1].color = null;
                            }

                            // series[col - 1].color = null;
                        }
                        // console.log(series)
                        var view = new google.visualization.DataView(data);
                        view.setColumns(columns);
                        chart.draw(view, options);
                    }
                }
            });
        }

    });
</script>
@endsection
