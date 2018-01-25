<?php echo $__env->make('partials.form_overlay', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<div class="box-footer">
    
    <div class="form-group">
        <div class="col-sm-3 col-sm-offset-3">
            <?php echo Form::submit('保存', ['class' => 'btn btn-primary pull-left', 'id' => 'save']); ?>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('act', $uris['index'])): ?>
                <?php echo Form::reset('取消', ['class' => 'btn btn-default pull-right', 'id' => 'cancel']); ?>

            <?php endif; ?>
        </div>
    </div>
</div>