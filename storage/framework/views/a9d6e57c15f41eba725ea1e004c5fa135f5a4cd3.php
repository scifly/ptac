<div class="form-group" <?php if(isset($divId)): ?> id="<?php echo e($divId); ?>" <?php endif; ?>>
    <?php echo Form::label($id, $label, [
        'class' => 'col-sm-3 control-label',
    ]); ?>

    <div class="col-sm-6">
        <div class="input-group">
            <div class="input-group-addon">
                <i class="<?php if(isset($icon)): ?> <?php echo e($icon); ?> <?php else: ?> fa fa-list <?php endif; ?>"></i>
            </div>
            <?php echo Form::select($id, $items, null, [
                'class' => 'form-control select2',
                'style' => 'width: 100%;'
            ]); ?>

        </div>
    </div>
</div>