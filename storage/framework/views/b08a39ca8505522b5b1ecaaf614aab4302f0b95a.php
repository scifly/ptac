<?php echo Form::open(['url' => '/schools', 'method' => 'post', 'id' => 'formSchool']); ?>

<?php echo $__env->make('school.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>