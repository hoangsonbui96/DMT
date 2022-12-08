@extends('admin.layouts.default.app')
@section('content')
    <style>
        #tblGeneralReport tr td:hover {
            cursor: pointer;
        }

        .text-dark {
            color: black !important;
        }
        .high-lights td{
            background-color: #FF99CC !important ;
        }
        .selected-cell{
            background-color: #c6e2ff !important;
        }
    </style>
    <section class="content-header">
        <h1 class="page-header">@lang('admin.overtime.report')</h1>
    </section>

    <section class="content">
        @if($errors->any())
            <h4 style="color:red;">{{$errors->first()}}</h4>
        @endif
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                @include('admin.includes.daily-general-report-search')
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="box tbl-top">
                    <!-- /.box-header -->
                    <div class="box-body table-responsive no-padding table-scroll">
                        @if(isset($chooseProjects))
                            <table class="table table-bordered table-hover" id="tblGeneralReport">
                                <thead>
                                <tr>
                                    <th class="width12">
                                        <a class="text-dark" href=""
                                           data-link="{{ route('admin.GeneralReports', ['order' => 'full-name', 'type' => $type_reverse]) }}">
                                            @lang('admin.Staffs_name')
                                            @if($type_reverse == "asc" && $order == "full-name")
                                                <i class="fa fa-caret-up"></i>
                                            @else
                                                <i class="fa fa-caret-down"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="width5">
                                        <a class="text-dark" href=""
                                           data-link="{{ route('admin.GeneralReports', ['order' => "total-hours", 'type' => $type_reverse]) }}">
                                            @lang('admin.overtime.total')
                                            @if($type_reverse == "asc" && $order == "total-hours")
                                                <i class="fa fa-caret-up"></i>
                                            @else
                                                <i class="fa fa-caret-down"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="width5">
                                        <a class="text-dark" href=""
                                           data-link="{{ route('admin.GeneralReports', ['order' => "total-hours", 'type' => $type_reverse]) }}">
                                            @lang('admin.overtime.percent')
                                            @if($type_reverse == "asc" && $order == "total-hours")
                                                <i class="fa fa-caret-up"></i>
                                            @else
                                                <i class="fa fa-caret-down"></i>
                                            @endif
                                        </a>
                                    </th>
                                    @foreach($chooseProjects as $project)
                                        <th class="width15">
                                            <a class="text-dark" href=""
                                               data-link="{{ route('admin.GeneralReports', ['order' => $project->id, 'type' => $type_reverse]) }}">
                                                {{ $project->NameVi}}
                                                @if($type_reverse == "asc" && $order == $project->id)
                                                    <i class="fa fa-caret-up"></i>
                                                @else
                                                    <i class="fa fa-caret-down"></i>
                                                @endif
                                            </a>
                                        </th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($group_project_user as $user)
                                    <tr>
                                        <td class="left-important">{{ $user->FullName }}</td>
                                        <td class="right-important">{{ $user->pivot->TotalHours }}</td>
                                        <td class="right-important">
                                            {{ $total_hours != 0 ? number_format($user->pivot->TotalHours * 100 / $total_hours, 2) : 0 }}%
                                        </td>
                                        @foreach($chooseProjects as $project)
                                            <td class="right-important">
                                                {{ isset($user->pivot[$project->id]->TotalHours)
                                                    ? $user->pivot[$project->id]->TotalHours
                                                    : 0 }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr class="high-lights">
                                    <td style="white-space: nowrap; text-align: left">@lang('admin.overtime.total')</td>
                                    <td class="right-important">{{ $total_hours }}</td>
                                    <td class="right-important">{{ $total_hours != 0 ? 100 : 0 }}%</td>
                                    @foreach($chooseProjects as $project)
                                        <td class="right-important">{{ $group_project_user->sum(function ($item) use ($project) {
                                                return isset($item->pivot[$project->id]) ? $item->pivot[$project->id]->TotalHours : 0;
                                        })}}
                                        </td>
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>
                        @endif

                        @if(isset($chooseWorks))
                            <table class="table table-bordered table-hover" id="tblGeneralReport">
                                <thead>
                                <tr>
                                    <th class="width12">
                                        <a class="text-dark" href=""
                                           data-link="{{ route('admin.GeneralReports', ['order' => "full-name", 'type' => $type_reverse, 't' => 'work']) }}">
                                            @lang('admin.Staffs_name')
                                            @if($type_reverse == "asc" && $order == "full-name")
                                                <i class="fa fa-caret-up"></i>
                                            @else
                                                <i class="fa fa-caret-down"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="width5">
                                        <a class="text-dark" href=""
                                           data-link="{{ route('admin.GeneralReports', ['order' => 'total-hours', 'type' => $type_reverse, 't' => 'work']) }}">
                                            @lang('admin.overtime.total')
                                            @if($type_reverse == "asc" && $order == "total-hours")
                                                <i class="fa fa-caret-up"></i>
                                            @else
                                                <i class="fa fa-caret-down"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="width5">
                                        <a class="text-dark" href=""
                                           data-link="{{ route('admin.GeneralReports', ['order' => 'total-hours', 'type' => $type_reverse, 't' => 'work']) }}">
                                            @lang('admin.overtime.percent')
                                            @if($type_reverse == "asc" && $order == "total-hours")
                                                <i class="fa fa-caret-up"></i>
                                            @else
                                                <i class="fa fa-caret-down"></i>
                                            @endif
                                        </a>
                                    </th>
                                    @foreach($chooseWorks as $work)
                                        <th class="width15">
                                            <a class="text-dark" href=""
                                               data-link="{{ route('admin.GeneralReports', ['order' => $work->DataValue, 'type' => $type_reverse, 't' => 'work']) }}">
                                                {{ $work->Name}}
                                                @if($type_reverse == "asc" && $order == $work->DataValue)
                                                    <i class="fa fa-caret-up"></i>
                                                @else
                                                    <i class="fa fa-caret-down"></i>
                                                @endif
                                            </a>
                                        </th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($group_work_user as $user)
                                    <tr>
                                        <td class="left-important">{{ $user->FullName }}</td>
                                        <td class="right-important">{{ $user->pivot->TotalHours }}</td>
                                        <td class="right-important">
                                            {{ $total_hours != 0 ? number_format($user->pivot->TotalHours * 100 / $total_hours, 2) : 0 }}%
                                        </td>
                                        @foreach($chooseWorks as $work)
                                            <td class="right-important">
                                                {{ isset($user->pivot[$work->DataValue]->TotalHours)
                                                    ? $user->pivot[$work->DataValue]->TotalHours
                                                    : 0 }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr class="high-lights">
                                    <td style="white-space: nowrap; text-align: left">@lang('admin.overtime.total')</td>
                                    <td class="right-important">{{ $total_hours }}</td>
                                    <td class="right-important">{{ $total_hours != 0 ? 100 : 0 }}%</td>
                                    @foreach($chooseWorks as $work)
                                        <td class="right-important">{{ $group_work_user->sum(function ($item) use ($work) {
                                            return isset($item->pivot[$work->DataValue]) ? $item->pivot[$work->DataValue]->TotalHours : 0;
                                    })}}
                                        </td>
                                    @endforeach
                                </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        $(function () {
            $('#tblGeneralReport tbody tr td').click(function (e) {
                e.stopPropagation();
                $('.selected-cell').removeClass("selected-cell");
                $('#tblGeneralReport tbody tr td, #tblGeneralReport tbody tr').css("background-color", "rgba(255, 255, 255)");
                // $(this).parent().siblings().find('td:eq(' + $(this).index() + ')').css("background-color", "rgba(255, 99, 132, 0.12)");
                // $(this).siblings().css("background-color", "rgba(255, 99, 132, 0.12)");
                // $(this).css("background-color", "rgba(255, 99, 132, 0.12)");

                // $('#tblGeneralReport tbody tr td, #tblGeneralReport tbody tr').addClass("selected-cell");
                $(this).parent().siblings().find('td:eq(' + $(this).index() + ')').addClass("selected-cell");
                $(this).siblings().addClass("selected-cell");
                $(this).addClass("selected-cell");
            });
        });

        $(window).click(function() {
            $('.selected-cell').removeClass("selected-cell");
        });

        $(document).ready(function () {
            $('a.text-dark').each(function (i, a) {
                $(a).attr("href", $(a).attr("data-link") + "?" + location.search.substr(1));
            })
        })
    </script>
@endsection
