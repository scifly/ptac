<?php echo Form::model($group, ['url'=>'groups/update/'.$group->id,'method' => 'put', 'id' => 'formGroup']); ?>

<?php echo $__env->make('group.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>

