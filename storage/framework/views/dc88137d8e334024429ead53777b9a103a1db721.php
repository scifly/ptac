<?php echo Form::model($messageType, ['url' => '/message_types/' . $messageType->id, 'method' => 'put', 'id' => 'formMessageType', 'data-parsley-validate' => 'true']); ?>

<?php echo $__env->make('message_type.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>

