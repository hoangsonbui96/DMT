@extends('admin.layouts.default.app')
<meta name="viewport" content="width=device-width, initial-scale=1">
@push('pageCss')
    <link rel="stylesheet" href="https://docs.dhtmlx.com/gantt/codebase/dhtmlxgantt.css?v=6.0.0">
@endpush
@push('pageJs')
    <script src="https://docs.dhtmlx.com/gantt/codebase/dhtmlxgantt.js?v=6.0.0"></script>
@endpush


@section('content')
    <section class="content-header">
        <div class="page-header">
            <h1>Tiến độ dự án: {{$project->NameVi}}</h1>
        
            <a href="{{ route('admin.ProjectManager') }}" class="btn btn-primary pull-right" {{-- onclick="comeBack()" --}}>
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
            </a>
        </div>
    </section>

    <section class="content">
        {{-- <input type=button value="Zoom In" onclick="gantt.ext.zoom.zoomIn();">
        <input type=button value="Zoom Out" onclick="gantt.ext.zoom.zoomOut();">
        <input type=button value="Reinitialize Gantt" onclick="gantt.init('gantt_here')"> --}}

        <div id="gantt_here" style='width:100%; height:100%;overflow-y: scroll;'></div>
    </section>
@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            let projectId = {{ $project->id }}
            $.ajax({
                url: "{{ route('admin.getProgress') }}",
                type: 'post',
                data: {
                    projectId: projectId
                },
                success: function(res) {
                    res.tasks.forEach(task => {
                        if (task.isJob) {
                            task.id = 'j_' + task.id;
                        }
                    });
                    gantt.parse(res)
                }
            })
        });
        var zoomConfig = {
            levels: [{
                    name: "hour",
                    scale_height: 27,
                    min_column_width: 15,
                    scales: [{
                            unit: "day",
                            format: "%d"
                        },
                        {
                            unit: "hour",
                            format: "%H"
                        },
                    ]
                },
                {
                    name: "day",
                    scale_height: 27,
                    min_column_width: 80,
                    scales: [{
                        unit: "day",
                        step: 1,
                        format: "%d/%m"
                    }]
                },
                {
                    name: "week",
                    scale_height: 50,
                    min_column_width: 50,
                    scales: [{
                            unit: "week",
                            step: 1,
                            format: function(date) {
                                var dateToStr = gantt.date.date_to_str("%d/%m");
                                var endDate = gantt.date.add(date, -6, "day");
                                var weekNum = gantt.date.date_to_str("%W")(date);
                                return "Tuần " + weekNum + ", " + dateToStr(date) + " - " + dateToStr(
                                    endDate);
                            }
                        },
                        {
                            unit: "day",
                            step: 1,
                            format: "%d/%m"
                        }
                    ]
                },
                {
                    name: "month",
                    scale_height: 50,
                    min_column_width: 120,
                    scales: [{
                            unit: "month",
                            format: "%m/%Y"
                        },
                        {
                            unit: "week",
                            format: "Tuần %W"
                        }
                    ]
                },
                {
                    name: "quarter",
                    height: 50,
                    min_column_width: 90,
                    scales: [{
                            unit: "quarter",
                            step: 1,
                            format: function(date) {
                                var dateToStr = gantt.date.date_to_str("%m/%Y");
                                var endDate = gantt.date.add(gantt.date.add(date, 3, "month"), -1, "day");
                                return dateToStr(date) + " - " + dateToStr(endDate);
                            }
                        },
                        {
                            unit: "month",
                            step: 1,
                            format: "%m/%Y"
                        },
                    ]
                },
                {
                    name: "year",
                    scale_height: 50,
                    min_column_width: 30,
                    scales: [{
                        unit: "year",
                        step: 1,
                        format: "%Y"
                    }]
                }
            ],
            useKey: "ctrlKey",
            trigger: "wheel",
            element: function() {
                return gantt.$root.querySelector(".gantt_task");
            }
        };

        gantt.ext.zoom.init(zoomConfig);

        gantt.templates.grid_file = function(item) {
            if (!item.isJob) {
                return "<div class='gantt_tree_icon gantt_file'></div>";
            } else {
                return "<div class='gantt_tree_icon gantt_folder_open'></div>";
            }
        };

        gantt.templates.grid_folder = function(item) {
            if (item.isJob) {
                return `
                <div 
                class='gantt_tree_icon gantt_folder_${item.$open ? "open" : "closed"}'>
                </div>
                `;
            }
        };

        gantt.config.date_format = "%Y-%m-%d %H:%i:%s";
        gantt.config.date_grid = "%d/%m/%Y";
        gantt.config.columns = [{
                name: "text",
                label: "Tên Công việc",
                tree: true,
                width: '*'
            },
            // {
            //     name: "start_date",
            //     label: "Bắt đầu",
            //     align: "center",
            // },
            // {
            //     name: "Duration",
            //     label: "Số giờ làm",
            //     align: "center",
            //     template: function(obj) {
            //         if (obj.Duration != 0) return obj.Duration
            //         if (obj.Duration === 0) return ""
            //     },
            // },
            {
                name: "Progress",
                label: "Tiến độ",
                align: "center",
                template: function(obj) {
                    if (obj.Progress && obj.Progress != 0) return obj.Progress + ' %'
                    return ""
                }
            },
        ];

        gantt.templates.task_duration = function(start, end, task) {
            return task.Duration;
        };

        gantt.templates.task_text = function(start, end, task) {
            let workedTime = task.WorkedTime ?? 0;
            let duration = task.Duration ?? 0;
            return `<span style='color:black; background-color:${task.color}' tittle='Số giờ đã thực hiện trên số giờ dự kiến'>${workedTime}/${duration}</span>`
        };

        gantt.templates.tooltip_date_format = function(date) {
            var formatFunc = gantt.date.date_to_str("%H:%i %d/%m/%Y");
            return formatFunc(date);
        };

        gantt.templates.tooltip_text = function(start, end, task) {
            StartDate = gantt.templates.tooltip_date_format(start);
            EndDate = gantt.templates.tooltip_date_format(end);
            if (task.unscheduled) {
                StartDate = 'Chưa có'
                EndDate = 'Chưa có'
            }
            return `${task.isJob ? `<b>Job: </b> ` : `<b>Task: </b>`}${task.text} <br/>
                    <b>Bắt đầu: </b>${StartDate} <br/>
                    <b>Kết thúc: </b>${EndDate} <br/>
                    <b>Số giờ đã thực hiện: </b>${task.WorkedTime ?? 0} <br/>
                    <b>Số giờ dự kiến: </b>${task.Duration ?? 0}
                    ${task.Progress ? `<br/><b>Tiến độ: </b>${task.Progress}%` : ''}
                    `;
        };

        // gantt.templates.progress_text = function(start, end, task) {
        //     return `<span style="display:flex;text-align:left;padding-left: 5px;color:#fff">${task.Progress}% </span>`
        // };

        gantt.config.show_unscheduled = true;
        gantt.config.open_tree_initially = true;
        gantt.plugins({
            tooltip: true
        });
        gantt.config.readonly = true;
        gantt.ext.zoom.setLevel("day");

        gantt.config.prevent_default_scroll = true;

        gantt.init("gantt_here");

    </script>
@endsection
