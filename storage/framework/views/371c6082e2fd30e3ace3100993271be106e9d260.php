<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(!empty($exam['id'])): ?>
                <?php echo e(Form::hidden('id', $exam['id'], ['id' => 'id'])); ?>

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
                        'data-parsley-length' => '[4, 40]'
                    ]); ?>

                </div>
            </div>
            <?php echo $__env->make('partials.single_select', [
                'label' => '所属考试类型',
                'id' => 'exam_type_id',
                'items' => $examtypes
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.multiple_select', [
                'label' => '所属班级',
                'id' => 'class_ids',
                'items' => $classes,
                'selectedItems' => isset($selectedClasses) ? $selectedClasses : []
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.multiple_select', [
                'label' => '科目',
                'id' => 'subject_ids',
                'items' => $subjects,
                'selectedItems' => isset($selectedSubjects) ? $selectedSubjects : []
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="form-group">
                <?php echo Form::label('max_scores', '科目满分', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('max_scores', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过10个数字)',
                        'required' => 'true',
                        'type' => "number",
                        'data-parsley-range' => '[100,150]',
                        'data-parsley-length' => '[1, 10]'
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('pass_scores', '科目及格分数', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('pass_scores', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过10个数字)',
                        'required' => 'true',
                        'type' => "number",
                        'data-parsley-range' => '[60,90]',
                        'data-parsley-length' => '[1, 10]'
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('start_date', '考试开始日期', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::date('start_date', null, [
                        'class' => 'form-control',
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('end_date', '考试结束日期', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::date('end_date', null, [
                        'class' => 'form-control',
                    ]); ?>

                </div>
            </div>
            <?php echo $__env->make('partials.remark', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.enabled', [
                'id' => 'enabled',
                'value' => $exam['enabled'] ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
