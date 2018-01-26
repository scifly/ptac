<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(!empty($subjectModules['id'])): ?>
                <?php echo Form::hidden('id', $subjectModules['id'], ['id' => 'id']); ?>

            <?php endif; ?>
            <div class="form-group">
                <?php echo Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '不能超过20个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 20]'
                    ]); ?>

                </div>
            </div>
            <?php echo $__env->make('partials.single_select', [
                'label' => '所属科目',
                'id' => 'subject_id',
                'items' => $subjects
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="form-group">
                <?php echo Form::label('weight', '次分类权重', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('weight', null, [
                        'class' => 'form-control',
                        'placeholder' => '次分类权重是数字',
                        'required' => 'true',
                        'type' => 'number',
                        'data-parsley-min' => '101'
                    ]); ?>

                </div>
            </div>
            <?php echo $__env->make('partials.enabled', [
                'id' => 'enabled',
                'value' => $subjectModules['enabled'] ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
