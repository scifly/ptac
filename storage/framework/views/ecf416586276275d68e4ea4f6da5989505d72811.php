<?php echo Form::model($school, ['url' => '/schools/' . $school->id, 'method' => 'put', 'id' => 'formSchool']); ?>

<?php echo $__env->make('school.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>

