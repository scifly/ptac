<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(!empty($action['id'])): ?>
                <?php echo e(Form::hidden('id', $action['id'], ['id' => 'id'])); ?>

            <?php endif; ?>
            <div class="form-group">
                <?php echo Form::label('name', 'Action名称', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入功能名称)',
                        'required' => 'true',
                        'maxlength' => '80'
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('method', '方法名称', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('method', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入方法名称)',
                        'required' => 'true',
                        'maxlength' => '255',
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('route', '路由', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('route', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入路由)',
                        'required' => 'true',
                        'maxlength' => '255',
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('controller', '控制器名称',[
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('controller', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入控制器名称)',
                        'required' => 'true',
                        'maxlength' => '255'
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('view', 'view路径', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('view', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入view路径)',
                        'maxlength' => '255'
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('js', 'js文件路径', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('js', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入js文件路径)',
                        'maxlength' => '255'
                    ]); ?>

                </div>
            </div>
            <?php echo $__env->make('partials.multiple_select', [
                'label' => 'HTTP请求类型',
                'id' => 'action_type_ids',
                'items' => $actionTypes,
                'selectedItems' => $selectedActionTypes ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.remark', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.enabled', [
                'id' => 'enabled',
                'value' => $action['enabled'] ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>