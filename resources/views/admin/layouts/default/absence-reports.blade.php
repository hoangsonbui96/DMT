@extends('admin.layouts.default.app')

@section('content')
<style>
    .table th, .table td {
        border: 1px solid #bdb9b9 !important;
        text-align: center;
        vertical-align: middle !important;
        background-color: #fff;
    }

    .SummaryMonth .table tr th {
        background-color: #dbeef4;
    }

    .tbl-dReport .table tr th {
        background-color: #c6e2ff;
    }
</style>
<section class="content-header">
    <h1 class="page-header">@lang('admin.absence.absence-reports')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 margin-bottom">
                @include('admin.includes.absence-report-search')
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="table-responsive tbl-dReport">
                <table class="table">
                    <thead class="thead-default">
                        <tr>
                            <th class="width3" rowspan="2">@lang('admin.stt')</th>
                            <th rowspan="2">@lang('admin.user.full_name')</th>
                            <th colspan=" {{ count($master_datas) }}" rowspan="1">@lang('admin.absences')</th>
                            <th rowspan="2">@lang('admin.sum')</th>
                            <th rowspan="2">@lang('admin.action')</th>
                        </tr>
                        <tr>
                            @foreach($master_datas as $master_data)
                                <th rowspan="1" colspan="1">{{ $master_data->Name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absence_report as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class = "left-important"    >{{ $item->FullName }}</td>
                                @foreach($item->hours as $key => $value)
                                    <td>@if($value > 0){{ number_format($value/60, 1) }}h <br>({{ $item->times[$key] }} lượt) @else - @endif</td>
                                @endforeach
                                <td>{{ number_format(array_sum($item->hours)/60, 2) }}h <br>
                                    ({{ array_sum($item->times) }} lượt)
                                </td>
                                <td>
                                    <a href="{{ route('admin.Absences') }}?UID={{ $item->UID }}" class="btn btn-default">@lang('admin.view')</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="popupModal"></div>
        </div>
    </div>
</section>
@endsection
@section('js')
    <script type="text/javascript" async>
        var sDate = '';
        var eDate = '';

        $(function () {
            if($('#s-date input:text').val() != ''){
                sDate = $('#s-date').val();
            }
            if($('#e-date input:text').val() != ''){
                eDate = $('#e-date').val();
            }

            $('.table tbody tr td:last-child a.btn-default').each(function(i, e) {
                let valueAttr = $(this).attr('href');
                $(this).attr('href', valueAttr+'&Date%5B%5D='+sDate+'&Date%5B%5D='+eDate+'&approve=2');
            });
            $('#AbRportExport').click(function (e) {
                e.preventDefault();
                var uid = $('#select-user option:selected').val();
                var sDate = $('#s-date').val();
                var eDate = $('#e-date').val();
                ajaxGetServerWithLoader('{{ route('export.AbsencesReport') }}','GET'
                    , $('#absenceReport-search-form').serializeArray(),function (data) {
                        if (typeof data.errors !== 'undefined'){
                            showErrors(data.errors);
                        }else{
                            window.location.href = '{{ route('export.AbsencesReport') }}?UID='+uid+'&date[0]='+sDate+'&date[1]='+eDate;
                        }
                    });
            });
        })
    </script>
@endsection

