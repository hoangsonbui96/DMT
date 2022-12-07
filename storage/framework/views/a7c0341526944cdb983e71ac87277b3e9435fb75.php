<form class="form-inline" id="checkin-history-search-form">
	<div class="form-group pull-left">
		<div class="input-group search">
			<input type="text" class="form-control" placeholder="<?php echo app('translator')->get('admin.search-placeholder'); ?>" name="search" value="<?php echo e(isset($request['search']) ? $request['search'] : null); ?>">
		</div>
	</div>

	<div class="form-group pull-left margin-r-5 date" id="date">
		<div class="input-group search date">
			<input type="text" class="form-control" id="date-input" name="time" value="<?php echo e(!isset($request['time']) ? Carbon\Carbon::now()->format('m/Y') : $request['time']); ?>" >
			<span class="input-group-addon">
				<span class="glyphicon glyphicon-calendar"></span>
			</span>
		</div>
	</div>
	<div class="form-group pull-left">
		<select class="selectpicker show-tick show-menu-arrow" id="select-type" name="type" data-size="5" data-width="220px" data-actions-box="true" tabindex="-98">
			<option value="">Tất cả</option>
			<?php $__currentLoopData = $type_checkin; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<option value="<?php echo e($type->Type); ?>" <?php echo e(isset($request['type']) && $request['type'] == $type->Type ? 'selected' : ''); ?>><?php echo e($type->Type); ?></option>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</select>
	</div>

	<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin', $menu)): ?>
	<div class="form-group pull-left">
		<select class="selectpicker show-tick show-menu-arrow user-custom" id="select-user" name="UserID" data-live-search="true" data-live-search-placeholder="Search" data-size="5" tabindex="-98">
			<?php echo GenHtmlOption($users, 'id', 'FullName', isset($request['UserID']) ? $request['UserID'] : \Illuminate\Support\Facades\Auth::user()->id); ?>

		</select>
	</div>
	<?php endif; ?>

	<div class="form-group pull-left">
		<div class="input-group">
			<button type="button" class="btn btn-primary" id="btn-search-checkin-history"><?php echo app('translator')->get('admin.btnSearch'); ?></button>
		</div>
	</div>
</form>

<style>
	#checkin-history-search-form .form-group:not(:last-child), #area-btn button:not(:last-child) { margin-right: 3px; }
</style>

<script>
	$(".selectpicker").selectpicker({
		noneSelectedText : "<?php echo app('translator')->get('admin.timekeeping.type'); ?>"
	});
	SetMothPicker($('#date'));
	$('#btn-search-checkin-history').click(function () {
		var date = moment($('#date-input').val(),'MM/YYYY').format('YYYYMM');

		$('#checkin-history-search-form').submit();
	});
</script>
<?php /**PATH D:\DMT\resources\views/admin/includes/checkin/checkin-history-search.blade.php ENDPATH**/ ?>