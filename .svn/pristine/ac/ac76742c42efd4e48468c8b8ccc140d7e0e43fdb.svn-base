@extends('admin.layouts.default.app')
@section('content')
<section class="content-header">
    <h1 class="page-header" >@lang('admin.overtime.report')</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            @include('admin.includes.overtime-report-search')
            @if($errors->any())
                <h4 style="color:red;">{{$errors->first()}}</h4>
            @endif
            @if(Session::has('success'))
                {!! Session::get('success') !!}
            @endif
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            @component('admin.component.table')
				@slot('columnsTable')
                    <tr>
                        <th class="width5">@lang('admin.stt')</th>
                        <th style="white-space: nowrap;">@lang('admin.user.full_name')</th>
                        @foreach($projects as $project)
                        <th>{{ $project->NameVi}}</th>
                        @endforeach
                        <th>@lang('admin.overtime.total')</th>
                        <th>@lang('admin.overtime.percent')</th>
                    </tr>
                @endslot
				@slot('dataTable')
                    @foreach($userList as $user)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td style="white-space: nowrap; text-align: left">{{ $user->FullName }}</td>
                    @foreach($user->workOnProject as $work)
                        <td>@if($work > 0) {{ $work }}h @else - @endif</td>
                    @endforeach
                        <td>{{ $user->totalOvertime }}h</td>
                        <td>
                        @if($user->totalOvertime > 0)
                            {{ number_format($user->totalOvertime/array_sum($totalOvertimeOnProject)*100, 2) }}
                        @endif
                        </td>
                    </tr>
                    @endforeach
                    @if ($totalOvertimeOnProject != null)
                        <tr>
                            <td style="white-space: nowrap; text-align: left">@lang('admin.overtime.total')</td>
                            <td></td>
                        @foreach($totalOvertimeOnProject as $item)
                            <td>{{ $item }}h</td>
                        @endforeach
                            <td>{{ array_sum($totalOvertimeOnProject) }}h</td>
                            <td>100%</td>
                        </tr>
                    @endif
                @endslot
            @endcomponent
        </div>
    </div>
</section>
@endsection
