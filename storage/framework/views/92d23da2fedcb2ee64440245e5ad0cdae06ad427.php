<?php echo Form::open([
    'method' => 'post',
    'id' => 'formStudent',
    'data-parsley-validate' => 'true'
]); ?>

<?php echo $__env->make('student.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>

<?php echo $__env->make('student.department_tree', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>