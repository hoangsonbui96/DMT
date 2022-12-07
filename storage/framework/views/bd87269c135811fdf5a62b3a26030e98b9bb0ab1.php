<?php $__env->startSection('content'); ?>
<?php
    $canEdit = false;
    $canDelete = false;
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
<style>
	.table.table-bordered th,
	.table.table-bordered td {
		border: 1px solid #bdb9b9 !important;
		text-align: center;
		vertical-align: middle !important;
		background-color: #fff;
	}

	.SummaryMonth .table.table-bordered tr th {
		background-color: #dbeef4;
	}

	.tbl-dReport .table.table-bordered tr th {
		background-color: #c6e2ff;
	}

	.tbl-top {
		margin-top: 0;
	}
</style>
<section class="content-header left232 daily-header top49">
	<h1 class="page-header"><?php echo app('translator')->get('admin.daily.daily_report'); ?> tháng <?php echo e((\Request::get('time')) ? \Request::get('time') : \Carbon\Carbon::now()->format(FOMAT_DISPLAY_MONTH)); ?> - <?php echo e($user->FullName); ?></h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 daily-content top100">
			<?php echo $__env->make('admin.includes.daily-report-search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12 marginTop90">
			<div class="table-responsive SummaryMonth" style="display: none;">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th rowspan="2">No</th>
							<th>ToW%</th>
							<?php $__currentLoopData = $masterData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php $key = $data->DataValue; ?>
							<td><?php echo e($total->totalHours > 0 ? number_format($total->$key/$total->totalHours*100, 2) : 0); ?>%</td>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<td></td>
							<th rowspan="2">Project Percent</th>
						</tr>
						<tr>
							<th><?php echo app('translator')->get('admin.daily.Project'); ?></th>
							<?php $__currentLoopData = $masterData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<th><?php echo e($data->Name); ?></th>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<th>Sum</th>
						</tr>
					</thead>
					<tbody>
						<?php $__currentLoopData = $total; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td style="font-weight: bold"><?php echo e($loop->iteration); ?></td>
							<td class="pName"><?php echo e($item->NameVi); ?></td>
							<?php $__currentLoopData = $masterData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php $key = $data->DataValue ?>
							<td><?php echo e($item->$key); ?></td>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<td><?php echo e($item->totalHours); ?></td>
							<td><?php echo e($total->totalHours > 0 ? number_format($item->totalHours/$total->totalHours*100, 2) :
								0); ?></td>
						</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td class="td-last" colspan="<?php echo e(count($masterData) + 1); ?>"></td>
							<th>Total</th>
							<th><?php echo e($total->totalHours); ?></th>
							<td></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-md-12 col-sm-12 col-xs-12">
			<?php $__env->startComponent('admin.component.table'); ?>
			<?php $__env->slot('columnsTable'); ?>
			<tr>
				<th class="width3pt"><?php echo app('translator')->get('admin.stt'); ?></th>
				<th class="width5 sticky-hz"><?php echo app('translator')->get('admin.daily.Date'); ?></th>
				<th><?php echo app('translator')->get('admin.daily.Project'); ?></th>
				<th><?php echo app('translator')->get('admin.daily.Screen_Name'); ?></th>
				<th><?php echo app('translator')->get('admin.daily.Type_Of_Work'); ?></th>
				<th><?php echo app('translator')->get('admin.contents'); ?></th>
				<th class="width3"><?php echo app('translator')->get('admin.daily.Working_Time'); ?></th>
				<th class="width5"><?php echo app('translator')->get('admin.daily.progressing'); ?></th>
				<th><?php echo app('translator')->get('admin.daily.Note'); ?></th>
				<th class="width5"><?php echo app('translator')->get('admin.daily.Date_Create'); ?></th>
				<th><?php echo app('translator')->get('admin.daily.Status'); ?></th>
				<?php if($canEdit || $canDelete): ?>
				<th class="width5pt"><?php echo app('translator')->get('admin.action'); ?></th>
				<?php endif; ?>
			</tr>
			<?php $__env->endSlot(); ?>
			<?php $__env->slot('dataTable'); ?>
			<?php if(count($dailyReports) != 0): ?>
			<?php $__currentLoopData = $dailyReports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $dailyReport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr class="even gradeC" data-id="">
				<td><?php echo e($loop->iteration); ?></td>
				<th style="font-weight:normal"><?php echo e(FomatDateDisplay($dailyReport->Date, FOMAT_DISPLAY_DAY)); ?></th>
				<td class="left-important"> <?php echo nl2br(e($dailyReport->NameVi)); ?></td>
				<td class="left-important"><?php echo nl2br(e($dailyReport->ScreenName)); ?></td>
				<td class="left-important"><?php echo e($dailyReport->Name); ?></td>
				<td class="left-important"><?php echo nl2br(e($dailyReport->Contents)); ?></td>
				<td><?php echo e($dailyReport->WorkingTime); ?></td>
				<td><?php echo e($dailyReport->Progressing.' %'); ?></td>
				<td class="left-important"><?php echo nl2br(e($dailyReport->Note)); ?></td>
				<td><?php echo e(FomatDateDisplay($dailyReport->created_at, FOMAT_DISPLAY_DATE_TIME)); ?></td>
				<td>
					<?php if($dailyReport->Status !== null): ?>
					<?php switch($dailyReport->Status):
					case ('0'): ?>
					<span class="label label-info data-toggle="><?php echo app('translator')->get('admin.daily.Need Approve'); ?></span>
					<?php break; ?>
					<?php case (1): ?>
					<a href="" class="report-issue" data-target="#denyModal" data-toggle="modal"
						issue-value="<?php echo e(isset($dailyReport->Issue) ? $dailyReport->Issue : null); ?>" item-id="<?php echo e($dailyReport->id); ?>"><span
							class="label label-danger data-toggle="><?php echo app('translator')->get('admin.daily.Rewrite'); ?></span></a>
					<?php break; ?>
					<?php case (2): ?>
					<?php if(isset($dailyReport->Issue)): ?>
					<a href="" class="report-issue" data-target="#denyModal" data-toggle="modal"
						issue-value="<?php echo e($dailyReport->Issue); ?>" item-id="<?php echo e($dailyReport->id); ?>"
						item-status="<?php echo e($dailyReport->Status); ?>" onclick="setIssue()"><span
							class="label label-warning data-toggle="><?php echo app('translator')->get('admin.daily.Approved'); ?></span></a>
					<?php else: ?>
					<span class="label label-success data-toggle="><?php echo app('translator')->get('admin.daily.Approved'); ?></span>
					<?php endif; ?>
					<?php break; ?>
					<?php default: ?>
					<?php endswitch; ?>
					<?php else: ?> 
					<?php endif; ?>
				</td>
				<?php if($canEdit || $canDelete): ?>
				<td class="text-center">
					<?php if($dailyReport->TypeReport != 2): ?>
					<?php if($canEdit): ?>
					<span class="action-col update edit update-one" item-id="<?php echo e($dailyReport->id); ?>"><?php if(
						$dailyReport->Status != 2): ?>
						<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
						<?php else: ?>
						<i class="fa fa-eye" aria-hidden="true"></i>
						<?php endif; ?>
					</span>
					<?php endif; ?>
					<?php if($canDelete): ?>
					<?php if(
					$dailyReport->Status != 2): ?>
					<span class="action-col update delete delete-one" item-id="<?php echo e($dailyReport->id); ?>"><i
							class="fa fa-times" aria-hidden="true"></i></span>
					<?php endif; ?>
					<?php endif; ?>
					<?php endif; ?>
				</td>
				<?php endif; ?>
			</tr>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			<?php endif; ?>

			<?php $__env->endSlot(); ?>
			
			<?php echo $__env->renderComponent(); ?>
			<div class="modal draggable fade in denyModal" id="denyModal" role="dialog" data-backdrop="static">
				<div class="modal-dialog modal-lg ui-draggable">
					<div class="modal-content drag">
						<div class="modal-header ui-draggable-handle" style="cursor: move;">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title"><?php echo app('translator')->get('admin.daily.Deny Report'); ?></h4>
						</div>
						<div class="modal-body">
							<textarea class="form-control" name="issue" id="issue" rows="10" readonly></textarea>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default"
								data-dismiss="modal"><?php echo app('translator')->get('admin.daily.Close'); ?></button>

							<button type="button" id="edit-report"
								class="btn btn-primary action-col update edit update-one"
								item-id=""><?php echo app('translator')->get('admin.daily.Edit Report'); ?></button>
						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
		</div>
	</div>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
<script type="text/javascript" async>
	var ajaxUrl = "<?php echo e(route('admin.DailyInfo')); ?>";
    var newTitle = 'Thêm báo cáo';
    var updateTitle = 'Sửa báo cáo';
    var nameSelected = '';

    $(function () {
		$('.modal-dialog').draggable({
       		handle: ".modal-header"
    	});
        if($("#select-user option:selected").text() != ''){
            nameSelected = '[' + $("#select-user option:selected").text() + ']';
        }

        $("#add_daily").click(function () {
            var reqId = $(this).attr('req');
            ProcessAddDaily(ajaxUrl + '?reqId=' + reqId, reqId, function(data) {
                $('#req-id').val(reqId);
            });
        });

        
        
        
        
        
        
    });

    function ProcessAddDaily(urlPost, reqId, callback) {
        ajaxGetServerWithLoader(urlPost, 'GET', null, function (data) {
            $('#popupModal').empty().html(data);
            $('.modal-title').html(newTitle + ' ' + nameSelected);
            $('.detail-modal').modal('show');

            if (IsFunction(callback)) {
                callback(data);
            }
        });
    }

	$('.report-issue').click(function(){
		id = $(this).attr('item-id');
		let issue = $(this).attr('issue-value');
		status = $(this).attr('item-status');
		if(issue == ''){
			issue = 'Người duyệt không có yêu cầu gì đặc biệt'
		}
		if(status == 2){
			$('#edit-report').hide();
		}else{
			$('#edit-report').show();
		}
		console.log(issue);
		$('#issue').html(issue);
		$('#edit-report').attr('item-id',id);
	});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.default.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\DMT\resources\views/admin/layouts/default/daily-report.blade.php ENDPATH**/ ?>