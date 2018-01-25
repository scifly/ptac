<?php echo Form::open([
    'action'=>'GroupController@store',
    'method' => 'post',
    'id' => 'formGroup',
    'data-parsley-validate' => 'true'
]); ?>

<?php echo $__env->make('group.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>