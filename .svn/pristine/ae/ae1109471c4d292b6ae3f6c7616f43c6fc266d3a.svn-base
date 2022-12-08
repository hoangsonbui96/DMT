<table class="table table-striped table-bordered table-hover data-table">
    <thead class="thead-default">
    	<tr>
    		<td colspan="10">
    			@lang('admin.daily.daily_report') tháng {{
	            (\Request::get('time')) ? \Request::get('time') : \Carbon\Carbon::now()->format(FOMAT_DISPLAY_MONTH)
	        }} - {{  $user->FullName }}
    		</td>
    	</tr>
    	<tr>
	    	<th class="width3pt">@lang('admin.stt')</th>
			<th class="width5 sticky-hz">@lang('admin.daily.Date')</th>
			<th>@lang('admin.daily.Project')</th>
			<th>@lang('admin.daily.Screen_Name')</th>
			<th>@lang('admin.daily.Type_Of_Work')</th>
			<th >@lang('admin.contents')</th>
			<th class ="width3" >@lang('admin.daily.Working_Time')</th>
			<th class="width5">@lang('admin.daily.progressing')</th>
			<th >@lang('admin.daily.Note')</th>
			<th class="width5">@lang('admin.daily.Date_Create')</th>
		</tr>
    </thead>
    <tbody>
    	@foreach($dailyReports as $dailyReport)
	    	<tr class="even gradeC" data-id="">
				<td>{{ $loop->iteration }}</td>
				<th style="font-weight:normal">{{ FomatDateDisplay($dailyReport->Date, FOMAT_DISPLAY_DAY) }}</th>
				<td class ="left-important"> {!! nl2br(e($dailyReport->NameVi)) !!}</td>
				<td class="left-important">{!! nl2br(e($dailyReport->ScreenName)) !!}</td>
				<td class="left-important">{{ $dailyReport->Name }}</td>
				<td class="left-important">{!! nl2br(e($dailyReport->Contents)) !!}</td>
				<td>{{ $dailyReport->WorkingTime }}</td>
				<td>{{ $dailyReport->Progressing.' %'}}</td>
				<td class="left-important">{!! nl2br(e($dailyReport->Note)) !!}</td>
				<td>{{ FomatDateDisplay($dailyReport->created_at, FOMAT_DISPLAY_DATE_TIME) }}</td>
			</tr>
    	@endforeach
    	<tr>
            <td></td>
            <td colspan="5">Tổng</td>
            <td>=SUM(G3:G{{$intLoopTmp}})</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>