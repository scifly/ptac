<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(!empty($subject['id'])): ?>
                <?php echo e(Form::hidden('id', $subject['id'], ['id' => 'id'])); ?>

            <?php endif; ?>
            <div class="form-group">
                <?php echo Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-book"></i>
                        </div>
                        <?php echo Form::text('name', null, [
                            'class' => 'form-control',
                            'placeholder' => '不能超过20个汉字',
                            'required' => 'true',
                            'data-parsley-length' => '[2, 20]'
                        ]); ?>

                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('max_score', '最高分', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-hand-o-up"></i>
                        </div>
                    <?php echo Form::text('max_score', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过6个数字含小数点)',
                        'required' => 'true',
                        'type' => 'number',
                        'data-parsley-range' => '[100,150]',
                        'data-parsley-length' => '[3, 6]'
                    ]); ?>

                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('pass_score', '及格分', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-hand-o-down"></i>
                        </div>
                    <?php echo Form::text('pass_score', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过5个数字含小数点)',
                        'required' => 'true',
                        'data-parsley-min' => '60',
                        'data-parsley-max' => '90',
                        'type' => 'number',
                        'data-parsley-length' => '[2, 5]'
                    ]); ?>

                    </div>
                </div>
            </div>
            <?php echo $__env->make('partials.multiple_select', [
                'label' => '所属年级',
                'id' => 'grade_ids',
                'items' => $grades,
                'icon' => 'fa fa-object-group',
                'selectedItems' => $selectedGrades ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.multiple_select', [
                'label' => '包含专业',
                'id' => 'major_ids',
                'items' => $majors,
                'icon' => 'fa fa-graduation-cap',
                'selectedItems' => $selectedMajors ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.enabled', [
                'label' => '是否为副科',
                'id' => 'isaux',
                'options' => ['是', '否'],
                'value' => $subject['isaux'] ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.enabled', [
                'id' => 'enabled',
                'value' => $subject['enabled'] ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
