<?php if($paginator->hasPages()): ?>
    <nav>
        <ul class="pagination">
            
                <li class="" id="previousPage">
                    <a class="page-link" rel="prev" aria-label="<?php echo app('translator')->get('pagination.previous'); ?>">&lsaquo;</a>
                </li>

            
            <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                <?php if(is_string($element)): ?>
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link"><?php echo e($element); ?></span></li>
                <?php endif; ?>

                
                <?php if(is_array($element)): ?>
                    <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $paginator->currentPage()): ?>
                            <li class="page-item active" aria-current="page" page-nums=<?php echo e($page); ?>>
                                <a class="page-link" page-nums=<?php echo e($page); ?>><?php echo e($page); ?></a>
                            </li>
                        <?php else: ?>
                            <li class="page-item" page-nums=<?php echo e($page); ?>>
                                <a class="page-link" id="page<?php echo e($page); ?>" href="<?php echo e($url); ?>" page-nums=<?php echo e($page); ?>><?php echo e($page); ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            
                <li class="" id="nextPage">
                    <a class="page-link">&rsaquo;</a>
                </li>
            
        </ul>
    </nav>
<?php endif; ?>
<?php /**PATH D:\DMT\resources\views/vendor/pagination/bootstrap-4.blade.php ENDPATH**/ ?>