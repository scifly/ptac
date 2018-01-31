<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(!empty($score['id'])): ?>
                <?php echo e(Form::hidden('id', $score['id'], ['id' => 'id'])); ?>

            <?php endif; ?>
            <?php echo $__env->make('partials.single_select', [
                    'label' => '考试名称',
                    'id' => 'exam_id',
                    'items' => $exams
                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.single_select', [
               'label' => '科目名称',
               'id' => 'subject_id',
               'items' => $subjects
           ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.single_select', [
                'label' => '学号',
                'id' => 'student_id',
                'items' => $students
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="form-group">
                <?php echo Form::label('score', '分数', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('score', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过5个数字含小数点)',
                        'required' => 'true',
                        'type' => "number",
                        'maxlength' => '5',
                    ]); ?>

                </div>
            </div>
            <?php echo $__env->make('partials.enabled', [
                'id' => 'enabled',
                'value' => $score['enabled'] ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
