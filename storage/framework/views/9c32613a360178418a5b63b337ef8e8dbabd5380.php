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
    .selected {
        border: 2px solid #3c8dbc;border-radius: 5px;
    }
    .mainClass {
        border: 0.5px solid gray;border-radius: 5px;
    }
    .new-main {
        border: 2px solid #3c8dbc !important;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .custom-file-input {
        display: flex;
        width: 100% !important;
        height:80px;margin-top: 10px;
        border-radius: 10px;
    }
    .main {
        padding: 5px 0;
    }
    /* .form-control {
        height:40px;
        border-radius: 10px;
        padding: 12px 20px;
        margin: 8px 0;
    } */
    .table.table-bordered th, .table.table-bordered td {
        border: 1px solid #bdb9b9 !important;
        text-align: center;
        vertical-align: middle !important;
        background-color: #fff;
    }

    .flex-row {
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        align-content: center;
        justify-content: center;
        align-items: center;
    }

    .SummaryMonth .table.table-bordered tr th { background-color: #dbeef4; }
    .tbl-dReport .table.table-bordered tr th { background-color: #c6e2ff; }
	.tbl-top { margin-top: 0px; }

</style>
<section class="content-header left232 daily-header top49">
    <h1 class="page-header">Yêu cầu thực hiện công việc</h1>
</section>
<section class="content">
    <div class="col-lg-8 col-md-8 col-sm-8 daily-content top100" style="padding: 0">
        <?php echo $__env->make('taskrequest::task-request-search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
     <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: 85px; padding:0">
        <?php $__env->startComponent('admin.component.table'); ?>
				<?php $__env->slot('columnsTable'); ?>
					<tr>
						<th class="width3pt"><?php echo app('translator')->get('admin.stt'); ?></th>
						<th>Người yêu cầu</th>
						<th>Yêu cầu lúc</th>
						<th>Nội dung yêu cầu</th>
						<th>Dự án</th>
                        <th>Người nhận</th>
						<th>Nội dung phản hồi</th>
                        <th>Phản hồi lúc</th>
                        <?php if($canEdit || $canDelete): ?>
							<th class="width5pt"><?php echo app('translator')->get('admin.action'); ?></th>
						<?php endif; ?>
					</tr>
				<?php $__env->endSlot(); ?>
                <?php $__env->slot('dataTable'); ?>
					<?php $__currentLoopData = $task_request_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $task_request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr class="<?php echo e(($task_request->needResponse == true) ? 'info' : ''); ?>">
							<td><?php echo e(($task_request_list->currentPage() - 1) * $task_request_list->perPage() + $index+1); ?></td>
                            <td class ="left-important"> <?php echo e(\App\User::find($task_request->requestUserID)->FullName); ?></td>
                            <td class ="center-important"> <?php echo e(FomatDateDisplay($task_request->requestTime, "d/m/Y H:i")); ?></td>
                            <td class ="left-important"> <?php echo nl2br(e($task_request->sumaryContent)); ?></td>
                            <td class ="left-important"> <?php echo e(\App\Project::find($task_request->projectID)->NameVi); ?></td>
                            <td class ="left-important"> <?php echo e(\App\User::find($task_request->receiveUserID)->FullName); ?></td>

                            <?php if($task_request->responseContent != null): ?>
                                <td class ="center-important ">
                                    <span class="label label-success data-toggle=">Đã Phản Hồi</span>
                                </td>
                            <?php else: ?>
                                <td class ="center-important ">
                                    <span class="label label-danger data-toggle=">Chưa Phản Hồi</span>
                                </td>
                            <?php endif; ?>

                            <?php if($task_request->responseTime != null): ?>
                                <td class ="center-important"> <?php echo e(FomatDateDisplay($task_request->responseTime, "d/m/Y H:i")); ?></td>
                            <?php else: ?>
                                <td class ="center-important"> - </td>
                            <?php endif; ?>

                            <?php if($canEdit || $canDelete): ?>
								<td class="text-center">
                                    <?php if($canEdit): ?>
                                        <span class="action-col update edit review-request-task" item-id="<?php echo e($task_request->id); ?>">
                                            <i class="fa fa-eye" aria-hidden="true" title="Chi tiết"></i></span>
                                        <?php if($task_request->DeleteorEdit == true): ?>
                                            <span class="action-col update edit update-request-task" item-id="<?php echo e($task_request->id); ?>">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true" title="Chỉnh sửa"></i></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if($canDelete): ?>
                                        <?php if($task_request->DeleteorEdit == true): ?>
                                            <span class="action-col update delete delete-request-task"  item-id="<?php echo e($task_request->id); ?>">
                                                <i class="fa fa-times" aria-hidden="true" title="Xóa"></i></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
								</td>
							<?php endif; ?>
						</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php $__env->endSlot(); ?>
                <?php $__env->slot('pageTable'); ?>
                    <?php echo e($task_request_list->links()); ?>

                <?php $__env->endSlot(); ?>
			<?php echo $__env->renderComponent(); ?>
     </div>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
<script src="<?php echo e(asset('js/ckeditor/ckeditor.js')); ?>"></script>
<script type="text/javascript" async>
    let orderNumber = 0;
    let temp = 0;
    let listEditor = [];

    var UrlUpdateRequestTask = "<?php echo e(route('admin.TaskRequestDetail')); ?>";
    var UrlReviewRequestTask = "<?php echo e(route('admin.TaskRequestReview')); ?>";

    const actionMain = (item) => {
        $(this).find(".mainClass").addClass('new-main');

        let parent = $('.new-main');
        let parent_offset = $(parent).offset();

        $("#control-tab").remove();

        html = `<div id="control-tab" class = "column" style="
                margin-top: 5px;
                height: 90px;
                width: 45px;
                border-radius: 5px;
                border: 1px solid gray;
                padding: 1px;
                align-items: center;text-align: center;
                display: flex;
                flex-direction: column;
                justify-content: space-around;
                margin-left:2px;z-index: 99;right: 4%;position: absolute;" class="col-sm-1">
                <i class="fa fa-plus-circle fa-2x" aria-hidden="true" onclick="addProject('b${item}')"></i>
				<i class="fa fa-trash fa-2x" aria-hidden="true" onclick="removeForm('b${item}',this)"></i>
            </div>`;

        $("#b"+item).append(html);

        $('.main').click(function (e) {
            $("#control-tab").remove();
            var b = $(this).attr('data-id');
            $("#"+b).append(html);
            $('.fa.fa-plus-circle.fa-2x').remove();
            $('.fa.fa-trash.fa-2x').remove();
            $('#control-tab').append('<i class="fa fa-plus-circle fa-2x" aria-hidden="true" onclick="addProject(`'+b+'`)"></i>');
			$('#control-tab').append('<i class="fa fa-trash fa-2x" aria-hidden="true" onclick="removeForm(`'+b+'`,this)"></i>');
            $('.new-main').removeClass('new-main');
            $(this).find(".mainClass").addClass('new-main');
            let parent = $('.new-main');
            let parent_offset = $(parent).offset();
        })
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.default.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\DMT\Modules/TaskRequest\Resources/views/task-request.blade.php ENDPATH**/ ?>