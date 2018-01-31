<?php echo Form::model($examType, ['url' => '/exam_types/' . $examType->id, 'method' => 'put', 'id' => 'formExamType', 'data-parsley-validate' => 'true']); ?>

<?php echo $__env->make('exam_type.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>

