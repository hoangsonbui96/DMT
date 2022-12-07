<?php $__env->startPush('pageJs'); ?>
	<script src="<?php echo e(asset('js/jquery.dataTables.min.js')); ?>"></script>
	<script src="<?php echo e(asset('js/absence.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<style>
	/*.table-scroll th, .table-scroll td {*/
	/*	background: none !important;*/
	/*}*/

	/*.table-striped>tbody>tr:nth-of-type(odd) {*/
	/*	background-color: #cfcfcf !important;*/
	/*}*/
</style>

<section class="content-header">
	<h1 class="page-header"><?php echo app('translator')->get('admin.timekeeping.history'); ?></h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12">
					<?php echo $__env->make('admin.includes.checkin.checkin-history-search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
				</div>
			</div>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<!-- /.box-header -->
			<?php $__env->startComponent('admin.component.table'); ?>
				<?php $__env->slot('columnsTable'); ?>
					<tr>
						<th class="width3"><a class="sort-link" data-link="<?php echo e(route("admin.TimekeepingHistory")); ?>/id/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.stt'); ?></a></th>
						<th class="width8"><a class="sort-link" data-link="<?php echo e(route("admin.TimekeepingHistory")); ?>/QRCodeID/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.timekeeping.qr-code'); ?></a></th>
						<th class="width12"><a class="sort-link" data-link="<?php echo e(route("admin.TimekeepingHistory")); ?>/DeviceName/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.timekeeping.device-name'); ?></a></th>
						<th class="width12"><a class="sort-link" data-link="<?php echo e(route("admin.TimekeepingHistory")); ?>/DeviceInfo/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.timekeeping.device-info'); ?></a></th>
						<th class="width12"><a class="sort-link" data-link="<?php echo e(route("admin.TimekeepingHistory")); ?>/OsVersion/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.timekeeping.os-version'); ?></a></th>
						<th class="width8"><a class="sort-link" data-link="<?php echo e(route("admin.TimekeepingHistory")); ?>/Type/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.timekeeping.type'); ?></a></th>
						<th class="width9"><a class="sort-link" data-link="<?php echo e(route("admin.TimekeepingHistory")); ?>/CheckinTime/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.timekeeping.checkin-time'); ?></a></th>
						<th class="width8"><a class="sort-link" data-link="<?php echo e(route("admin.TimekeepingHistory")); ?>/MacAddress/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.timekeeping.mac-address'); ?></a></th>
					</tr>
				<?php $__env->endSlot(); ?>
				<?php $__env->slot('dataTable'); ?>
					<?php $__currentLoopData = $checkin_history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr class="even gradeC" data-id="">
							<td class="text-center"><?php echo e($sort == 'desc' ? ++$stt : $stt--); ?></td>
							<td class="text-center"><?php echo e(isset($item->QRCodeID) && $item->QRCodeID != 0 ? $item->QRCode : ''); ?></td>
							<td class="left-important"><?php echo e($item->DeviceName); ?></td>
							<td class="left-important"><?php echo e($item->DeviceInfo); ?></td>
							<td class="left-important"><?php echo e($item->OsVersion); ?></td>
							<td class="text-center"><?php echo e($item->Type); ?></td>
							<td class="text-center"><?php echo e(FomatDateDisplay($item->CheckinTime, FOMAT_DISPLAY_DATE_TIME)); ?></td>
							<td class="text-center"><?php echo e($item->MacAddress); ?></td>
						</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php $__env->endSlot(); ?>
				<?php $__env->slot('pageTable'); ?>
					<?php echo e($checkin_history->appends($query_array)->links()); ?>

				<?php $__env->endSlot(); ?>
			<?php echo $__env->renderComponent(); ?>
			<div id="popupModal">
			</div>
		</div>
	</div>
</section>

<script>

	$(function() {
		$('[data-toggle="tooltip"]').tooltip();
	});

</script>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('admin.layouts.default.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\DMT\resources\views/admin/layouts/default/checkin/checkin-history.blade.php ENDPATH**/ ?>