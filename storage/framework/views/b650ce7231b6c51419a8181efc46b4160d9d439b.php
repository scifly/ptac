<?php echo Form::model($custodian, ['method' => 'put', 'id' => 'formCustodian','data-parsley-validate' => 'true']); ?>

<?php echo $__env->make('custodian.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>

<?php echo $__env->make('custodian.department_tree', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>