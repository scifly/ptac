<?php echo Form::model($eas, [
    'method' => 'put',
    'id' => 'formEducatorAttendanceSetting',
    'data-parsley-validate' => 'true'
]); ?>

<?php echo $__env->make('educator_attendance_setting.create_edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo Form::close(); ?>

