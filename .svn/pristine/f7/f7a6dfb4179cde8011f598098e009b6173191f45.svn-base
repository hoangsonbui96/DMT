<div class="modal fade" id="modal-absence-list">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modal-date"></h4>
			</div>
			<div class="modal-body">
				<div class="box">
					<div class="box-body table-responsive no-padding">
						<table class="table table-bordered table-hover" id="tbl-absent">
							<thead class="thead-default">
								<tr>
									<th scope="col">@lang('admin.stt')</th>
									<th scope="col">Kiểu nghỉ</th>
									<th scope="col">@lang('admin.startTime')</th>
									<th scope="col">@lang('admin.endTime')</th>
									<th scope="col">@lang('admin.times') (h)</th>
									<th scope="col">@lang('admin.absence.reason')</th>
									<th scope="col">@lang('admin.note')</th>
									<th scope="col">@lang('admin.status')</th>
								</tr>
							</thead>
							<tbody>
							@foreach($absenceTimekeeping as $item)
								<tr>
									<td scope="row">{{ $loop->iteration }}</td>
									<td class="modal-name">{{ $item->Name }}</td>
									<td class="modal-stime">{{ $item->STime }}</td>
									<td class="modal-etime">{{ $item->ETime }}</td>
									<td class="modal-totaltimeoff">{{ number_format($item->hours, 2) }}</td>
									<td class="modal-reason">{{ $item->Reason }}</td>
									<td class="modal-remark">{{ $item->Remark }}</td>
									<td class="modal-approved">{!! isset($item->Approved) && $item->Approved == 0 ? '<span class="label label-default">Chưa duyệt</label>' : '<span class="label label-success">Đã duyệt</label>' !!}</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">@lang('admin.btnCancel')</button>
			</div>
		</div>
	</div>
</div>
