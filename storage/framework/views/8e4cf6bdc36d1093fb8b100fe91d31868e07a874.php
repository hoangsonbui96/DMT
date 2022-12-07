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
									<th scope="col"><?php echo app('translator')->get('admin.stt'); ?></th>
									<th scope="col">Kiểu nghỉ</th>
									<th scope="col"><?php echo app('translator')->get('admin.startTime'); ?></th>
									<th scope="col"><?php echo app('translator')->get('admin.endTime'); ?></th>
									<th scope="col"><?php echo app('translator')->get('admin.times'); ?> (h)</th>
									<th scope="col"><?php echo app('translator')->get('admin.absence.reason'); ?></th>
									<th scope="col"><?php echo app('translator')->get('admin.note'); ?></th>
									<th scope="col"><?php echo app('translator')->get('admin.status'); ?></th>
								</tr>
							</thead>
							<tbody>
							<?php $__currentLoopData = $absenceTimekeeping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<td scope="row"><?php echo e($loop->iteration); ?></td>
									<td class="modal-name"><?php echo e($item->Name); ?></td>
									<td class="modal-stime"><?php echo e($item->STime); ?></td>
									<td class="modal-etime"><?php echo e($item->ETime); ?></td>
									<td class="modal-totaltimeoff"><?php echo e(number_format($item->hours, 2)); ?></td>
									<td class="modal-reason"><?php echo e($item->Reason); ?></td>
									<td class="modal-remark"><?php echo e($item->Remark); ?></td>
									<td class="modal-approved"><?php echo isset($item->Approved) && $item->Approved == 0 ? '<span class="label label-default">Chưa duyệt</label>' : '<span class="label label-success">Đã duyệt</label>'; ?></td>
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo app('translator')->get('admin.btnCancel'); ?></button>
			</div>
		</div>
	</div>
</div>
<?php /**PATH D:\DMT\resources\views/admin/includes/checkin/timekeeping-absence-detail.blade.php ENDPATH**/ ?>