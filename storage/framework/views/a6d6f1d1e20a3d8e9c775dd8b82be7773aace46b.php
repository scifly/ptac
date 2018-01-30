<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(!empty($major['id'])): ?>
                <?php echo e(Form::hidden('id', $major['id'], ['id' => 'id'])); ?>

            <?php endif; ?>
            <div class="form-group">
                <?php echo Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-graduation-cap"></i>
                        </div>
                    <?php echo Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过40个汉字)',
                        'required' => 'true',
                        'data-parsley-length' => '[4, 40]',
                    ]); ?>

                    </div>
                </div>
            </div>
            <?php echo $__env->make('partials.multiple_select', [
                'label' => '包含科目',
                'id' => 'subject_ids',
                'items' => $subjects,
                'selectedItems' => $selectedSubjects ?? null,
                'icon' => 'fa fa-book'
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.remark', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.enabled', [
                'id' => 'enabled',
                'value' => $major['enabled'] ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
