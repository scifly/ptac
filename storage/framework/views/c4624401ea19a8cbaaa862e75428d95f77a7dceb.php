<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(isset($semester['id'])): ?>
                <?php echo Form::hidden('id', $semester['id'], ['id' => 'id']); ?>

            <?php endif; ?>
            <div class="form-group">
                <?php echo Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '(不得超过20个汉字)',
                        'required' => 'true',
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('start_date', '起始日期', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo Form::text('start_date', null, [
                            'class' => 'form-control pull-right start_date',
                            'placeholder' => '(请选择起始日期)',
                            'required' => 'true',
                        ]); ?>

                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('end_date', '结束日期', [
                    'class' => 'col-sm-3 control-label']); ?>

                <div class="col-sm-6">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo Form::text('end_date', null, [
                            'class' => 'form-control pull-right start_date',
                            'placeholder' => '(请选择结束日期)',
                            'required' => 'true',
                        ]); ?>

                    </div>
                </div>
            </div>
            <?php echo $__env->make('partials.remark', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.enabled', [
                'id' => 'enabled',
                'value' => $semester['enabled'] ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
