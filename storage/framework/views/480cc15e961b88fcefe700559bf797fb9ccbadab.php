<?php echo Form::model($class, [
    'method' => 'put',
    'id' => 'formSquad',
    'data-parsley-validate' => 'true'
]); ?>

<?php echo $__env->make('class.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>

