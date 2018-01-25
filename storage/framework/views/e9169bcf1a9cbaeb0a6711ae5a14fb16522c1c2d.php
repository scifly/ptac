<?php echo Form::model($major, ['method' => 'put', 'id' => 'formMajor']); ?>

<?php echo $__env->make('major.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>