<?php echo Form::model($menu, [
    'method' => 'put',
    'id' => 'formMenu',
    'class' => 'form-horizontal form-borderd',
    'data-parsley-validate' => 'true'
]); ?>

<?php echo $__env->make('menu.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>