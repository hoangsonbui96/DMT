<?php $__env->startPush('pageJs'); ?>
	<script src="<?php echo e(asset('js/jquery.dataTables.min.js')); ?>"></script>
	<script src="<?php echo e(asset('js/absence.js')); ?>"></script>
	
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<?php
	$canEdit = false;
	$canDelete = false;
	$canAppr = false;
?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('action', $edit)): ?>
	<?php
		$canEdit = true;
	?>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('action', $delete)): ?>
	<?php
		$canDelete = true;
	?>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('action', $appr)): ?>
	<?php
		$canAppr = true;
	?>
<?php endif; ?>

<style>
	/* #add_new_absence { margin: 5px 0; } */
</style>

<section class="content-header">
	<h1 class="page-header"><?php echo app('translator')->get('admin.absence.absence'); ?></h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12">
					<?php echo $__env->make('admin.includes.search-absence', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
				</div>
				
			</div>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<!-- /.box-header -->
			<?php $__env->startComponent('admin.component.table'); ?>
				<?php $__env->slot('columnsTable'); ?>
					<tr>
						<th class="width5"><a class="sort-link" data-link="<?php echo e(route("admin.Absences")); ?>/id/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.stt'); ?></a></th>
						<th class="width12"><a class="sort-link" data-link="<?php echo e(route("admin.Absences")); ?>/UID/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.absence.fullname'); ?></a></th>
						<th class="width8"><a class="sort-link" data-link="<?php echo e(route("admin.Absences")); ?>/SDate/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.absence.start'); ?></a></th>
						<th class="width8"><a class="sort-link" data-link="<?php echo e(route("admin.Absences")); ?>/EDate/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.absence.end'); ?></a></th>
						<th><a class="sort-link" data-link="<?php echo e(route("admin.Absences")); ?>/TotalTimeOff/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.absence.time'); ?></a></th>
						<th class=""><a class="sort-link" data-link="<?php echo e(route("admin.Absences")); ?>/Reason/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.absence.reason'); ?></a></th>
						<th><a class="sort-link" data-link="<?php echo e(route("admin.Absences")); ?>/AbsentDate/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.absence.absentDate'); ?></a></th>
						<th class="width9"><a class="sort-link" data-link="<?php echo e(route("admin.Absences")); ?>/ApprovedDate/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.absence.approvedDate'); ?></a></th>
						<th><a class="sort-link" data-link="<?php echo e(route("admin.Absences")); ?>/Approved/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.absence.approve'); ?></a></th>
						<?php if($canEdit || $canDelete): ?>
							<th class="width5"><?php echo app('translator')->get('admin.action'); ?></th>
						<?php endif; ?>
						<th class="width15"><a class="sort-link" data-link="<?php echo e(route("admin.Absences")); ?>/Remark/" data-sort="<?php echo e($sort_link); ?>"><?php echo app('translator')->get('admin.absence.remark'); ?></a></th>
					</tr>
				<?php $__env->endSlot(); ?>
				<?php $__env->slot('dataTable'); ?>
					<?php $__currentLoopData = $absence; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr class="even gradeC" data-id="">
							<td class="text-center"><?php echo e($sort == 'desc' ? ++$stt : $stt--); ?></td>
							<td class="left-important "><?php echo e($item->FullName); ?></td>
							<td class="text-center"><?php echo e(FomatDateDisplay($item->SDate, FOMAT_DISPLAY_DATE_TIME)); ?></td>
							<td class="text-center"><?php echo e(FomatDateDisplay($item->EDate, FOMAT_DISPLAY_DATE_TIME)); ?></td>
							<td><?php echo e(number_format($item->TotalTimeOff/60, 2)); ?></td>
							<td class = "left-important"><?php echo e('('.$item->Name.')'.' '.$item->Reason); ?></td>
							<td class="text-center"><?php echo e(FomatDateDisplay($item->created_at, FOMAT_DISPLAY_CREATE_DAY)); ?></td>
							<td>
								<?php echo AddSpecial("<br/>", FomatDateDisplay($item->ApprovedDate, FOMAT_DISPLAY_DATE_TIME), e($item->NameUpdateBy)); ?>

							</td>
							<td class = "action-col text-center"><?php echo ApprovedDisplayHtml($item->Approved, '', '', 'data-toggle="tooltip" title="'.$item->Comment.'"'); ?></td>
                            <?php if($canEdit || $canDelete): ?>
                                <td class="text-center">
                                    <?php if($canEdit && ($canAppr || (!$canAppr && $item->Approved != 1 && $item->UID == \Illuminate\Support\Facades\Auth::user()->id))): ?>
                                        <span class="action-col update edit update-one" item-id="<?php echo e($item->id); ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                    <?php endif; ?>
                                    <?php if($canDelete && ($canAppr || (!$canAppr && $item->Approved != 1 && $item->UID == \Illuminate\Support\Facades\Auth::user()->id))): ?>
                                        <span class="action-col update delete delete-one"  item-id="<?php echo e($item->id); ?>"><i class="fa fa-times" aria-hidden="true"></i></span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
							<td class = "left-important" ><?php echo nl2br(e($item->Remark)); ?></td>
						</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php $__env->endSlot(); ?>
				<?php $__env->slot('pageTable'); ?>
					<?php echo e($absence->appends($query_array)->links()); ?>

				<?php $__env->endSlot(); ?>
			<?php echo $__env->renderComponent(); ?>
			<div id="popupModal">
			</div>
		</div>
	</div>
</section>

<script>
	var ajaxUrl = "<?php echo e(route('admin.AbsenceInfo')); ?>";
	var ajaxUrlApr = "<?php echo e(route('admin.AprAbsence')); ?>";
	var newTitle = 'Thêm lịch nghỉ';
	var updateTitle = 'Sửa lịch nghỉ';

	$(function() {
		$('[data-toggle="tooltip"]').tooltip();
	});

	$('.btn-search').click(function () {
		$('#absence-search-form').submit();
	});
	$('#export-absences').click(function (e) {
		e.preventDefault();
		var search = $('input[name=search]').val();
		var uid = $('#select-user option:selected').val();
		var dataValue = $('#select-absentreason option:selected').val();
		var sDate = $('#s-date').val();
		var eDate = $('#e-date').val();

		var url_string = window.location.href;
		var url = new window.URL(url_string);
		var approve = url.searchParams.get('approve');

		var req = '?search='+search+'&UID='+uid+'&MasterDataValue='+dataValue+'&Date[0]='+sDate+'&Date[1]='+eDate+'&approve='+approve;
		ajaxGetServerWithLoader('<?php echo e(route('export.Absences')); ?>'+req,'GET'
			, $('#absence-search-form').serializeArray() ,function (data) {
				if (typeof data.errors !== 'undefined'){
					showErrors(data.errors);
				}else{
					window.location.href = '<?php echo e(route('export.Absences')); ?>'+req;
				}
			});
	});
	$("#add_new_absence").click(function () {
		$('.loadajax').show();
		$.ajax({
			url: ajaxUrl,
			success: function (data) {
				$('#popupModal').empty().html(data);
				$('.modal-title').html(newTitle);
				$('.detail-modal').modal('show');
				$('.loadajax').hide();
			}
		});
	});
</script>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('admin.layouts.default.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\DMT\resources\views/admin/layouts/default/absence/absence.blade.php ENDPATH**/ ?>