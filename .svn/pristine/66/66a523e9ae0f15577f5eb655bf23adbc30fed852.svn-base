@extends('admin.layouts.default.app')
@section('content')
    <style>
        .DR-summary-year .page-header h1 {
            text-transform: uppercase;
            font-size: 30px;
            color: #bb3a01;
            font-weight: bold;
        }

        table th, table td {
            border: 1px solid #bdb9b9 !important;
            text-align: center;
            vertical-align: middle !important;
            background-color: #fff;
        }

        .SummaryMonth table tr th {
            background-color: #dbeef4;
        }

        .tbl-dReport table tr th {
            background-color: #c6e2ff;
        }

        #container {
            min-height: 250px;
            padding: 15px;
            margin-right: auto;
            margin-left: auto;
        }

        .hover-point:hover {
            background: #c6e2ff;
            cursor: pointer;
        }

        .selected-tr {
            background-color: rgb(255, 99, 132) !important;
            cursor: pointer;
            color: white;
        }

        /*td:hover {*/
        /*    background: lightgray;*/
        /*}*/
        /*td[colspan]:hover {*/
        /*    background: lime;*/
        /*}*/
        /*td[colspan] + td[colspan]:hover {*/
        /*    background:turquoise;*/
        /*}*/
        /*td[colspan="2"]:hover {*/
        /*    background: gold;*/
        /*}*/
        /*td[rowspan]:hover {*/
        /*    background: tomato;*/
        /*}*/

        /*.percent-type {*/
        /*    display: none;*/
        /*}*/

        caption {
            text-align: center;
            font-size: 20px;
            font-weight: 600;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <section class="content-header">
        <div class="container-fluid DR-summary-year text-center">
            <div class="page-header">
                <h1>Bảng tổng hợp giờ làm dự án năm {{ $request['year']?$request['year']:$year }}</h1>
                <div>
                    <h3 class="info"><span style="font-weight: bold;"
                                           id="nameSelected"></span><span> - Ngày lập:{{\Carbon\Carbon::now()->format('d/m/Y')}}</span>
                    </h3>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                @if(isset($minYear))
                    <form class="form-inline" id="yearly-report-form">
                        @can('admin', $menu)
                            <div class="form-group pull-left margin-r-5">
                                <div class="input-group">
                                    <select class="selectpicker show-tick show-menu-arrow" id="select-user" name="UID"
                                            data-live-search="true" data-size="5"
                                            data-live-search-placeholder="Search" data-width="220px"
                                            data-actions-box="true" tabindex="-98">
                                        <option value="">@lang('admin.overtime.employer')</option>
                                        {!! GenHtmlOption($users, 'id', 'FullName', isset($request['UID']) ? $request['UID'] : $user) !!}
                                    </select>
                                </div>
                            </div>
                        @endcan
                        <div class="form-group pull-left margin-r-5">
                            <div class="input-group">
                                <select class="selectpicker show-tick show-menu-arrow" id="select-year" name="year"
                                        data-live-search="true" data-size="5"
                                        data-live-search-placeholder="Search" data-width="220px" data-actions-box="true"
                                        tabindex="-98">
                                    <option value="">@lang('admin.daily.choose_year')</option>
                                    @for($i=$minYear; $i<=$maxYear; $i++)
                                        <option
                                            value="{{ $i }}" {{ (isset($request['year']) && $request['year'] == $i) || (!isset($request['year']) && $year == $i) ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="form-group pull-left width12">
                            <div class="input-group">
                                <button type="button" class="btn btn-primary"
                                        id="yearly-report-btn">@lang('admin.btnSearch')</button>
                            </div>
                            <div class="input-group">
                                    <button type="button" class="btn btn-primary" id="toggle-canvas">Ẩn chi tiết</button>
                            </div>
                        </div>
                        <div class="form-group pull-right">
                            @can('action', $export)
                                <div class="input-group">
                                    <input type="submit" class="btn btn-success" id="export-excel"
                                           value="@lang('admin.export-excel')">
                                </div>
                            @endcan
                        </div>
                    </form>
                @endif
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="tbl-top">
                    <div class="box-body tbl-top table-responsive no-padding table-scroll">
                        <div class="table-responsive tbl-dReport">
                            <table class="table table-striped table-bordered table-hover table-user-groups">
                                <caption id="captionAll"></caption>
                                <thead class="thead-default">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <!--                                    Phần trăm-->
                                    <th class="percent-type"><span style="font-size: 18px">&Sigma;</span> mỗi tháng</th>
                                    @for($i = 1; $i <= 12; $i++)
                                        <td class="hover-point percent-type" alias="all-p-1-m"
                                            item-index="{{ $i }}">{{ $statistic_year['TotalYear']['TotalHours'] != 0 ? number_format(100*$statistic_year['T'.$i]['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) + 0 : 0 }}
                                            %
                                            <br>
                                            {{$statistic_year['T'.$i]['TotalHours']}} h
                                        </td>
                                    @endfor
                                    <td class="hover-point percent-type" alias="all-p-all-m"
                                        item-index="13">{{ $statistic_year['TotalYear']['TotalHours'] != 0 ? number_format(100*$statistic_year['TotalYear']['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) + 0 : 0 }}
                                        %
                                        <br>
                                        {{ $statistic_year['TotalYear']['TotalHours'] }} h
                                    </td>
                                    <td class="percent-type"></td>
                                </tr>
                                <tr>
                                    <th>Dự án</th>
                                    @for($i = 1; $i <= 12; $i++)
                                        <th>T{{$i}}</th>
                                    @endfor
                                    <th>Cả năm</th>
                                    <th>Percent</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($projects as $index => $project)
                                    <tr item-id="{{ $project->id }}" item-index="{{$index}}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="name-project">{{ $project->NameVi }}</td>
                                        @for($i = 1; $i <= 12; $i++)
                                            <td class="hover-point" alias="1-p-1-m"
                                                item-index="{{$i}}">{{ $project['T'.$i]['TotalHours'] }}</td>
                                        @endfor
                                        <td class="hover-point" alias="1-p-all-m"
                                            item-index="13">{{ $project->TotalYear['TotalHours'] }}</td>
                                        <td>{{$statistic_year['TotalYear']['TotalHours'] != 0 ?number_format(100*$project->TotalYear['TotalHours']/$statistic_year['TotalYear']['TotalHours'], 2) : 0}}
                                            %
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="myCanvas">
                    <div id="tableDetail" class="tbl-dReport"></div>
                    <canvas id="myChart" height="80" style="margin: 20px 0"></canvas>
                </div>
            </div>
        </div>
    </section>
    <script async>
        const color0 = 'rgba(139,69,19, 0.1)';
        const color1 = 'rgba(253, 197, 139, 0.1)';
        const color2 = 'rgba(55, 113, 142, 0.1)';
        const color3 = 'rgba(255, 0, 0, 0.1)';
        const color4 = 'rgba(104, 170, 85, 0.1)';
        const color5 = 'rgba(153, 102, 255, 0.1)';
        const color6 = 'rgba(134, 190, 218, 0.1)';
        const color7 = 'rgba(0, 0, 200, 0.1)';
        const colorNone = 'rgba(201, 217, 210, 0.1)';

        const colorBorder0 = 'rgb(139,69,19)';
        const colorBorder1 = 'rgb(253, 197, 139)';
        const colorBorder2 = 'rgb(55, 113, 142)';
        const colorBorder3 = 'rgb(255, 0, 0)';
        const colorBorder4 = 'rgb(104, 170, 85)';
        const colorBorder5 = 'rgb(153, 102, 255)';
        const colorBorder6 = 'rgb(134, 190, 218)';
        const colorBorder7 = 'rgb(0,0,200)';
        const colorBorderNone = 'rgb(201, 217, 210)';
        const labels = [];
        for (let i = 0; i < 12; i++) labels[i] = `Tháng ${i+1}`;
        const labelWorks = [];
        const arrColor = [color0, color1, color2, color3, color4, color5, color6, color7];
        const arrColorBorder = [colorBorder0, colorBorder1, colorBorder2, colorBorder3, colorBorder4, colorBorder5, colorBorder6, colorBorder7];
        const dataSetMonth = [
            {
                axis: 'x',
                label: '',
                data: [],
                backgroundColor: arrColor,
                borderColor: arrColorBorder,
                borderWidth: 1
            },
        ];
        const dataValue = @json($masterData);
        const statistic_year = @json($statistic_year);
        const projects = @json($projects);
        const dataSet = [];

        const renderTableDetail = (project_index = null, month_index = null) => {
            const type = month_index === null ? "Cả năm" : `Tháng ${month_index}`;
            let caption = 'Bảng thống kê giờ làm theo đầu việc ';
            if (project_index == null && month_index == null) {
                caption += 'năm {{ $request['year']?$request['year']:$year }}';
            }
            if (project_index == null && month_index != null) {
                caption += `tháng ${month_index}/{{ $request['year']?$request['year']:$year }}`;
            }
            if (project_index != null && month_index == null) {
                caption += `của dự án ${projects[project_index].NameVi} năm {{ $request['year']?$request['year']:$year }}`;
            }
            if (project_index != null && month_index != null) {
                caption += `của dự án ${projects[project_index].NameVi} tháng ${month_index}/{{ $request['year']?$request['year']:$year }}`;
            }
            caption += ` - ${$('#select-user option:selected').html()}`;
            let table_content = ``;
            let type_works = ``;
            const arr_variable = {};
            $(dataValue).each(function (index, item) {
                arr_variable[item.DataValue] = 0;
                type_works += `<th style="background-color: #c6e2ff">${item.Name}</th>`;
            })
            arr_variable["TotalHours"] = 0;
            projects.map(function (item, index) {
                if (month_index === null) {
                    let content_td = ` <td>${index + 1}</td>`;
                    content_td += `<td class="pName">${item.NameVi}</td>`;
                    for (let key in arr_variable) {
                        content_td += `<td>${item["TotalYear"][key]}</td>`;
                    }
                    content_td += `
                    <td>
                        ${statistic_year.TotalYear.TotalHours !== 0
                        ? (100 * item.TotalYear.TotalHours / statistic_year.TotalYear.TotalHours).toFixed(2)
                        : 0} %
                    </td>
                    `;
                    const html_content = `<tr>${content_td}</tr>`;
                    if (project_index == index) {
                        for (let key in arr_variable) {
                            arr_variable[key] += item["TotalYear"][key];
                        }
                        table_content += html_content;
                        return;
                    }
                    if (project_index === null) {
                        for (let key in arr_variable) {
                            arr_variable[key] += item["TotalYear"][key];
                        }
                        table_content += html_content;
                    }
                } else {
                    const key_month = `T${month_index}`;
                    let content_td = ` <td>${index + 1}</td>`;
                    content_td += `<td class="pName">${item.NameVi}</td>`;
                    for (let key in arr_variable) {
                        content_td += `<td>${item[[key_month]][key]}</td>`;
                    }
                    content_td += `
                    <td>
                        ${statistic_year[key_month].TotalHours !== 0
                        ? (100 * item[key_month].TotalHours / statistic_year[key_month].TotalHours).toFixed(2)
                        : 0} %
                    </td>
                    `;
                    const html_content = `<tr>${content_td}</tr>`;
                    if (project_index == index) {
                        for (let key in arr_variable) {
                            arr_variable[key] += item[key_month][key];
                        }

                        table_content += html_content;
                        return;
                    }
                    if (project_index === null) {
                        for (let key in arr_variable) {
                            arr_variable[key] += item[key_month][key];
                        }
                        table_content += html_content;
                    }
                }
            })
            let td_percent = ``;
            let td_hours = ``;
            for (let key in arr_variable) {
                td_percent += `
                    <td>
                        ${arr_variable["TotalHours"] !== 0
                        ? (100 * arr_variable[key] / arr_variable["TotalHours"]).toFixed(2)
                        : 0} %
                    </td>`;
                td_hours += `
                    <td ${key === "TotalHours" ? "style='background-color: #c6e2ff'" : ""}>
                        ${(arr_variable[key]).toFixed(2)}
                    </td>
                `;
            }
            return `
                <div class="table-responsive" style="margin-top: 20px">
                    <table class="table table-striped table-bordered table-hover table-user-groups">
                        <caption>${caption}</caption>
                        <thead class="thead-default">
                            <tr>
                                <th style="background-color: #c6e2ff" rowspan="2">No</th>
                                <th style="background-color: #c6e2ff">Tổng(%)</th>
                                ${td_percent}
                                <th rowspan="2" style="background-color: #c6e2ff">Percent</th>
                            </tr>
                            <tr>
                                <th style="background-color: #c6e2ff">Dự án</th>
                                ${type_works}
                                <th style="background-color: #c6e2ff">${type}</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${table_content}
                        <tr>
                            <td class=""></td>
                            <th style="background-color: #c6e2ff"> Tổng(h)</th>
                            ${td_hours}
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            `;
        }

        $(dataValue).each(function (index, item) {
            const data = {
                label: item.Name,
                data: [],
                backgroundColor: colorNone,
                borderColor: colorBorderNone,
                borderWidth: 1
            }
            switch (index) {
                case 0:
                    data.backgroundColor = color0;
                    data.borderColor = colorBorder0;
                    break;
                case 1:
                    data.backgroundColor = color1;
                    data.borderColor = colorBorder1;
                    break;
                case 2:
                    data.backgroundColor = color2;
                    data.borderColor = colorBorder2;
                    break;
                case 3:
                    data.backgroundColor = color3;
                    data.borderColor = colorBorder3;
                    break;
                case 4:
                    data.backgroundColor = color4;
                    data.borderColor = colorBorder4;
                    break;
                case 5:
                    data.backgroundColor = color5;
                    data.borderColor = colorBorder5;
                    break;
                case 6:
                    data.backgroundColor = color6;
                    data.borderColor = colorBorder6;
                    break;
                case 7:
                    data.backgroundColor = color7;
                    data.borderColor = colorBorder7;
                    break;
                default:
                    break;
            }
            dataSet.push(data);
            labelWorks.push(item.Name);
        })

        const drawChart = () => {
            const data = {
                labels: labels,
                datasets: dataSet
            };
            const config = {
                type: 'bar',
                data,
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: ``
                        }
                    },
                    responsive: true,
                    interaction: {
                        intersect: false,
                    },
                    scales: {
                        x: {
                            display: true,
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Giờ làm(h)'
                            },
                        }
                    }
                }
            };
            return new Chart($('#myChart'), config);
        }

        const updateChart = (chart, labels, dataset, title, legend = true) => {
            chart.options.plugins.title.text = title;
            chart.options.plugins.legend.display = legend;
            chart.data.labels = labels;
            chart.data.datasets = dataset;
            chart.update();
        }

        const update1pAllM = (myChart, self) => {
            const index = $(self).closest("tr").attr("item-index");
            const project = projects[index];
            const data = {};
            for (let i = 1; i <= 12; i++) {
                const key = `T${i}`;
                const data_month_i = project[key];
                for (let key in data_month_i) {
                    if (key === "TotalHours") {
                        continue;
                    }
                    if (!data.hasOwnProperty(key)) {
                        data[key] = [data_month_i[key]];
                    } else {
                        data[key].push(data_month_i[key]);
                    }
                }
            }
            const data_arr = Object.values(data);
            $(dataSet).each(function (index, el) {
                el["data"] = data_arr[index];
            })
            const title = `Tổng hợp giờ làm dự án ${$($(self).closest("tr")).find("td:nth-child(2)").text()} năm {{ $request['year']?$request['year']:$year }} - ${$('#select-user option:selected').html()}`
            $('#tableDetail').html(renderTableDetail(index));
            updateChart(myChart, labels, dataSet, title);
        }

        const updateAllPAllM = (myChart) => {
            const data_year = [];
            $(projects).each(function (index, project) {
                const data = {};
                for (let i = 1; i <= 12; i++) {
                    const key = `T${i}`;
                    const data_month_i = project[key];
                    for (let key in data_month_i) {
                        if (key === "TotalHours") {
                            continue;
                        }
                        if (!data.hasOwnProperty(key)) {
                            data[key] = [data_month_i[key]];
                        } else {
                            data[key].push(data_month_i[key]);
                        }
                    }
                }
                let data_new = Object.values(data);
                if (data_year.length === 0)
                    data_year.push(data_new);
                else {
                    const data_old = data_year[0];
                    const m = data_old.length;
                    const n = data_old[0].length;
                    for (let i = 0; i < m; i++) {
                        for (let j = 0; j < n; j++) {
                            data_old[i][j] += data_new[i][j];
                        }
                    }
                }
            })
            const [data] = data_year;
            $(dataSet).each(function (index, el) {
                el["data"] = data[index];
            })
            const title = `Tổng hợp giờ làm các dự án năm {{ $request['year']?$request['year']:$year }} - ${$('#select-user option:selected').html()}`;
            $('#tableDetail').html(renderTableDetail());
            updateChart(myChart, labels, dataSet, title);

        }

        const update1p1M = (myChart, self) => {
            const index_project = $(self).closest("tr").attr("item-index");
            const index = $(self).attr("item-index");
            const project = projects[index_project];
            const data = {};
            const data_month_i = project[`T${index}`];
            for (let key in data_month_i) {
                if (key === "TotalHours") {
                    continue;
                }
                if (!data.hasOwnProperty(key)) {
                    data[key] = [data_month_i[key]];
                } else {
                    data[key].push(data_month_i[key]);
                }
            }
            dataSetMonth[0].data = Object.values(data).map(function (item) {
                return item[0];
            });
            dataSetMonth[0].label = labels[index - 1];
            const title = `Tổng hợp giờ làm dự án ${$($(self).closest("tr")).find("td:nth-child(2)").text()} ${index}/{{ $request['year']?$request['year']:$year }} - ${$('#select-user option:selected').html()}`;
            $('#tableDetail').html(renderTableDetail(index_project, index));
            updateChart(myChart, labelWorks, dataSetMonth, title, false);
        }

        const updateAllP1M = (myChart, self) => {
            const index = $(self).attr("item-index");
            const key = `T${index}`;
            const data = {};
            $(projects).each(function (index, project) {
                const data_month_i = project[key];
                for (let key in data_month_i) {
                    if (key === "TotalHours") {
                        continue;
                    }
                    if (!data.hasOwnProperty(key)) {
                        data[key] = [data_month_i[key]];
                    } else {
                        data[key] = [data[key][0] + data_month_i[key]];
                    }
                }
            })
            dataSetMonth[0].data = Object.values(data).map(function (item) {
                return item[0];
            });
            dataSetMonth[0].label = labels[index - 1];
            const title = `Tổng hợp giờ làm các dự án ${index}/{{ $request['year']?$request['year']:$year }} - ${$('#select-user option:selected').html()}`;
            $('#tableDetail').html(renderTableDetail(null, index));
            updateChart(myChart, labelWorks, dataSetMonth, title, false);
        }

        $(function () {
            $('#toggle-canvas').click(function () {
                let text = $(this).text();
                $(this).text(text === "Hiện chi tiết" ? "Ẩn chi tiết" : "Hiện chi tiết");
                $(".myCanvas").toggle('show');
                // $(".page-header").toggle("show")
            })
            $('.switch-btn').click(function () {
                $(".percent-type").toggle('show');
                $(".hours-type").toggle("hide")
            })

            //Add attr
            let hover = $(".hover-point");
            $(hover).attr("title", "Chi tiết");

            $(".selectpicker").selectpicker();

            $('#yearly-report-btn').click(function () {
                $('#yearly-report-form').submit();
            });

            $('#nameSelected').append($('#select-user option:selected').html());
            $('#captionAll').html(`Bảng thống kê tổng hợp giờ làm theo tháng năm {{ $request['year']?$request['year']:$year }} - ${$('#select-user option:selected').html()}`);
            $('#export-excel').click(function (e) {
                e.preventDefault();
                let req = '?UID=' + $('#select-user option:selected').val() + '&year=' + $('#select-year option:selected').val();
                let a = document.createElement('a');
                a.target = '_blank';
                a.href = "{{ route('admin.exportYearlyReport') }}" + req;
                a.click();
                {{--ajaxGetServerWithLoader("{{ route('admin.exportYearlyReport') }}", "GET", $('#yearly-report-form').serializeArray(),--}}
                {{--    function (data) {--}}
                {{--        // if (typeof data.errors !== 'undefined') {--}}
                {{--        //     showErrors(data.errors);--}}
                {{--        //     return;--}}
                {{--        // }--}}
                {{--        --}}{{--                        window.open("{{ route('admin.exportYearlyReport') }}?" + req, "_blank");--}}
                {{--            window.location.href = "{{ route('admin.exportYearlyReport') }}" + req;--}}
                {{--    });--}}
            });
            // Draw chart
            const myChart = drawChart();
            updateAllPAllM(myChart);
            $(hover).click(function () {
                const self = this;
                $(".selected-tr").removeClass("selected-tr");
                $(self).addClass("selected-tr");
                $(self).closest("tr").find("td.name-project, th.percent-type").addClass("selected-tr");
                const thead = $(self).closest("tr").parent().parent().find("thead");
                $(thead).find(`tr:nth-child(2)>th:nth-child(${parseInt($(self).attr("item-index")) + 1})`).addClass("selected-tr");
                const alias = $(self).attr("alias");
                switch (alias) {
                    case "1-p-all-m":
                        update1pAllM(myChart, $(self))
                        break;
                    case "1-p-1-m":
                        update1p1M(myChart, $(self))
                        break;
                    case "all-p-1-m":
                        updateAllP1M(myChart, $(self));
                        break;
                    case "all-p-all-m":
                        updateAllPAllM(myChart);
                        break;
                }
            })
        })
    </script>
@endsection
