<?php echo Form::open(['url' => '/message_types', 'method' => 'post', 'id' => 'formMessageType', 'data-parsley-validate' => 'true' ]); ?>

<?php echo $__env->make('message_type.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>

