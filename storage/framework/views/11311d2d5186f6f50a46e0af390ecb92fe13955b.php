<?php echo Form::model($tab, [
    'method' => 'put',
    'id' => 'formTab',
    'url' => 'tabs/update/' . $tab['id']
]); ?>

<?php echo $__env->make('tab.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>