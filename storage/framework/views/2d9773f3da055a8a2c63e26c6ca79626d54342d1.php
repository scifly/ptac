<?php echo Form::open([
    'method' => 'post',
    'id' => 'formCustodian',
    'data-parsley-validate' => 'true'
]); ?>

<?php echo $__env->make('custodian.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>