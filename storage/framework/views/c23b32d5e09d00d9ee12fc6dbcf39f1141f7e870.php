<span id="breadcrumb" style="color: #999; font-size: 13px;"><?php echo $breadcrumb; ?></span>
<div class="box-tools pull-right">
    <?php if(!isset($addBtn)): ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('act', $uris['create'])): ?>
            <button id="add-record" type="button" class="btn btn-box-tool">
                <i class="fa fa-plus text-blue"> 新增</i>
            </button>
        <?php endif; ?>
    <?php endif; ?>
    <?php if(isset($buttons)): ?>
        <?php $__currentLoopData = $buttons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('act', $uris[$button['id']])): ?>
                <button id="<?php echo e($button['id']); ?>" type="button" class="btn btn-box-tool">
                    <i class="<?php echo e($button['icon']); ?> text-blue"> <?php echo e($button['label']); ?></i>
                </button>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</div>