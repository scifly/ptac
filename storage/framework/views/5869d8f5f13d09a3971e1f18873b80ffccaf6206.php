<?php echo Form::model($exam, ['url' => '/exams/' . $exam->id,  'method' => 'put', 'id' => 'formExam', 'data-parsley-validate' => 'true']); ?>

<?php echo $__env->make('exam.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>

