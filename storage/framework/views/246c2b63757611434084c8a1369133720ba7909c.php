<span id="breadcrumb" style="color: #999; font-size: 13px;"><?php echo $breadcrumb; ?></span>
<div class="box-tools pull-right">
    <?php if(isset($buttons)): ?>
        <?php $__currentLoopData = $buttons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $button['html']; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
    <?php if(!isset($show)): ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('act', $uris['index'])): ?>
            <button id="record-list" type="button" class="btn btn-box-tool">
                <i class="fa fa-mail-reply text-blue"> 返回列表</i>
            </button>
        <?php endif; ?>
    <?php endif; ?>
</div>