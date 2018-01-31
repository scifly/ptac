<?php echo $__env->make('partials.site_content_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<!--content-->
<section class="content clearfix">
    <?php echo $__env->make('partials.modal_dialog', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    
    <?php if(!empty($tabs)): ?>
        <div class="col-lg-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li <?php if($tab['active']): ?> class="active" <?php endif; ?>>
                            <a href="#<?php echo e($tab['id']); ?>"
                               data-toggle="tab"
                               data-uri="<?php echo e($tab['url']); ?>"
                               class="tab <?php if($tab['active']): ?> text-blue <?php else: ?> text-gray <?php endif; ?>"
                            >
                                <?php if(isset($tab['icon'])): ?>
                                    <i class="<?php echo e($tab['icon']); ?>"></i>
                                <?php endif; ?>
                                <?php echo e($tab['name']); ?>

                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <div class="tab-content">
                    <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="<?php if($tab['active']): ?> active <?php endif; ?> tab-pane card"
                             id="<?php echo e($tab['id']); ?>"></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php echo $content; ?>

    <?php endif; ?>
</section>