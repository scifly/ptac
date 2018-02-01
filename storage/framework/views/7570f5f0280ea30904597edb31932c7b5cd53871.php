<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(!empty($corp['id'])): ?>
                <?php echo e(Form::hidden('id', $corp['id'], ['id' => 'id'])); ?>

            <?php endif; ?>
            <div class="form-group">
                <?php echo Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-weixin"></i>
                        </div>
                        <?php echo Form::text('name', null, [
                            'class' => 'form-control',
                            'placeholder' => '(不超过60个汉字)',
                            'required' => 'true',
                            'minlength' => '3',
                        ]); ?>

                    </div>
                </div>
            </div>
            <?php echo $__env->make('partials.single_select', [
                'label' => '所属运营者',
                'id' => 'company_id',
                'items' => $companies,
                'icon' => 'fa fa-building'
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="form-group">
                <?php echo Form::label('corpid', '企业号ID', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('corpid', null, [
                        'class' => 'form-control',
                        'placeholder' => '(18个小写字母与阿拉伯数字)',
                        'required' => 'true',
                        'data-parsley-type' => 'alphanum',
                        'data-parsley-length' => '[18, 18]'
                    ]); ?>

                </div>
            </div>
            <?php if(isset($corp['department_id'])): ?>
                <?php echo Form::hidden('department_id', $corp['department_id']); ?>

            <?php endif; ?>
            <?php if(isset($corp['menu_id'])): ?>
                <?php echo Form::hidden('menu_id', $corp['menu_id']); ?>

            <?php endif; ?>
            <?php echo $__env->make('partials.enabled', [
                'id' => 'enabled',
                'value' => $corp['enabled'] ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
