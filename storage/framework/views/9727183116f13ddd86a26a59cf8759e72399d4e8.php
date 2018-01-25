<?php echo Form::open([ 'method' => 'post', 'id' => 'formOperator','data-parsley-validate' => 'true']); ?>

<?php echo $__env->make('operator.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>

<?php echo $__env->make('educator.department_tree', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
