<?php echo Form::model($app, [
    'method' => 'put',
    'id' => 'formApp',
    'data-parsley-validate' => 'true'
]); ?>

<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(!empty($app['id'])): ?>
                <?php echo e(Form::hidden('id', $app['id'], ['id' => 'id'])); ?>

            <?php endif; ?>
            <div class="form-group">
                <?php echo Form::label('agentid', '应用id', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('agentid', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'placeholder' => '请输入应用id',
                        'maxlength' => '12'
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('name', '应用名称', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-weixin"></i>
                        </div>
                        <?php echo Form::text('name', null, [
                            'class' => 'form-control',
                            'required' => 'true',
                            'placeholder' => '请输入应用名称（不超过12个汉字）',
                            'maxlength' => '12'
                        ]); ?>

                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('redirect_domain', '企业应用可信域名', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('redirect_domain', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'placeholder' => '请输入企业应用可信域名',
                        'maxlength' => '255'
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('home_url', '主页型应用url', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-link"></i>
                        </div>
                        <?php echo Form::text('home_url', null, [
                            'class' => 'form-control',
                            'required' => 'true',
                            'placeholder' => '请输入主页型应用url',
                            'maxlength' => '255'
                        ]); ?>

                    </div>
                </div>
            </div>
            <?php echo $__env->make('partials.remark', [
                'label' => '应用详情',
                'field' => 'description',
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.enabled', [
                'label' => '打开地理位置上报',
                'id' => 'report_location_flag',
                'value' => $app['report_location_flag'] ?? null,
                'options' => ['是', '否']
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.enabled', [
                'label' => '上报用户进入应用事件',
                'id' => 'isreportenter',
                'value' => $app['isreportenter'] ?? null,
                'options' => ['是', '否']
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
<?php echo Form::close(); ?>