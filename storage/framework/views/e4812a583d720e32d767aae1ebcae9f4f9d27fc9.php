<?php echo Form::open([
    'method' => 'post',
    'id' => 'formSemester',
    'data-parsley-validate' => 'true'
]); ?>

<?php echo $__env->make('semester.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>