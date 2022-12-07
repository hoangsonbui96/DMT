<div class="modal draggable fade in detail-modal" id="user-info" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-lg ui-draggable">
		<!-- Modal content-->
        <div class="modal-content drag">
			<div class="modal-header ui-draggable-handle" style="cursor: move;">
                <button type="button" class="close" data-dismiss="modal" id="close-user-form">×</button>
                <h4 class="modal-title">@lang('admin.calendar.detailed-calendar-date')
					@foreach($CalendarsDay as $item)
					{{-- @if(\Carbon\Carbon::parse($item->EndDate)->equalTo(\Carbon\Carbon::parse($item->StartDate))) --}}
					{{ \Carbon\Carbon::parse($item->EndDate)->equalTo(\Carbon\Carbon::parse($item->StartDate))? FomatDateDisplay($item->EndDate, FOMAT_DISPLAY_DAY) : FomatDateDisplay($item->StartDate, FOMAT_DISPLAY_DAY)." đến ".FomatDateDisplay($item->EndDate, FOMAT_DISPLAY_DAY)}}
					{{-- @endif --}}
					@endforeach
                </h4>
            </div>
            <div class="modal-body">
				<div class="save-errors"></div>
				<form class="form-horizontal detail-form" action="" method="POST">
					@csrf
					<div class="row" style="margin: 5px;">
					@component('admin.component.table')
					@slot('columnsTable')
						<tr>
							<th>@lang('admin.times')</th>
							<th>@lang('admin.calendar.work')</th>
							<th>@lang('admin.calendar.ingredient')</th>
							<th>@lang('admin.calendar.chair')</th>
							<!-- <th>Ghi chú</th> -->
						</tr>
					@endslot
					@slot('dataTable')
						@foreach($CalendarsDay as $item)
						{{-- @if(\Carbon\Carbon::parse($day)->gte(\Carbon\Carbon::parse($item->StartDate)) && \Carbon\Carbon::parse($day)->lte(\Carbon\Carbon::parse($item->EndDate))) --}}
							<tr>
								@if(null != $item->MeetingTimeFrom)
								<td>{{ FomatDateDisplay($item->MeetingTimeFrom, 'H:i')}} - {{FomatDateDisplay($item->MeetingTimeTo, 'H:i')}}</td>
								@else
								<td>Cả ngày</td>
								@endif
								<td>{{ $item->Content }}</td>
								@php
								$array= explode(",",$item->Participant);
								@endphp
								<td id='Participant'>
								@foreach($array as $id)
									@foreach($User as $user)
										@if(($user->id == $id ) && (null != $id))
											{{ $user->FullName }} ,
										@endif
									@endforeach
								@endforeach
								</td>
								@if((null == $item->MeetingHostID))
									<td></td>
								@endif

								@foreach($User as $user)
									@if(($user->id == $item->MeetingHostID) && (null != $item->MeetingHostID))
										<td>{{ $user->FullName }}</td>
									@endif
								@endforeach
								<!-- <td></td> -->
							</tr>
						{{-- @endif --}}
						@endforeach
					@endslot
					@endcomponent
					</div>
				</form>
            </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-sm" data-dismiss="modal" id="cancel">@lang('admin.btnCancel')</button>
			</div>
        </div>
	</div>
</div>
