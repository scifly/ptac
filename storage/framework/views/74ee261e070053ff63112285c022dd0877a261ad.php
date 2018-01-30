<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(!empty($school['id'])): ?>
                <?php echo e(Form::hidden('id', $school['id'], ['id' => 'id'])); ?>

            <?php endif; ?>
            <div class="form-group">
                <?php echo Form::label('name', '名称',[
                    'class' => 'col-sm-3 control-label',
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('name', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'data-parsley-length' => '[6, 255]'
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('address', '地址', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('address', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'data-parsley-length' => '[6, 255]'
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('signature', '签名', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('signature', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'placeholder'=>'签名格式必须为[内容]',
                        'data-parsley-length' => '[2, 7]'
                    ]); ?>

                </div>
            </div>
            <?php echo $__env->make('partials.single_select', [
                'label' => '学校类型',
                'id' => 'school_type_id',
                'items' => $schoolTypes
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.single_select', [
                'label' => '所属企业',
                'id' => 'corp_id',
                'items' => $corps
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php if(isset($school['department_id'])): ?>
                <?php echo Form::hidden('department_id', $school['department_id']); ?>

            <?php endif; ?>
            <?php if(isset($school['menu_id'])): ?>
                <?php echo Form::hidden('menu_id', $school['menu_id']); ?>

            <?php endif; ?>
            <?php echo $__env->make('partials.enabled', [
                'id' => 'enabled',
                'value' => $school['enabled'] ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
