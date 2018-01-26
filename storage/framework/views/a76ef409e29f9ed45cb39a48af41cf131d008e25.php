<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(!empty($messageType['id'])): ?>
                <?php echo e(Form::hidden('id', $messageType['id'], ['id' => 'id'])); ?>

            <?php endif; ?>
            <div class="form-group">
                <?php echo Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过40个汉字)',
                        'required' => 'true',
                        'data-parsley-lenght' => '[4, 40]'
                    ]); ?>

                </div>
            </div>
            <?php echo $__env->make('partials.remark', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.enabled', [
                'id' => 'enabled',
                'value' => $messageType['enabled'] ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
