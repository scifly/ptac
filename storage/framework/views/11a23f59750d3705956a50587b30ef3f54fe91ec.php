<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(!empty($class['id'])): ?>
                <?php echo e(Form::hidden('id', $class['id'], ['id' => 'id'])); ?>

            <?php endif; ?>
            <div class="form-group">
                <?php echo Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-users"></i>
                        </div>
                    <?php echo Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不超过40个汉字)',
                        'required' => 'true',
                        'data-parsley-length' => '[4, 40]'
                    ]); ?>

                    </div>
                </div>
            </div>
            <?php echo $__env->make('partials.single_select', [
                'label' => '所属年级',
                'id' => 'grade_id',
                'items' => $grades,
                'icon' => 'fa fa-object-group'
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.multiple_select', [
                'label' => '班级主任',
                'id' => 'educator_ids',
                'items' => $educators,
                'selectedItems' => $selectedEducators ?? NULL
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php if(isset($class['department_id'])): ?>
                <?php echo Form::hidden('department_id', $class['department_id']); ?>

            <?php endif; ?>
            <?php echo $__env->make('partials.enabled', [
                'id' => 'enabled',
                'value' => $class['enabled'] ?? NULL
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>