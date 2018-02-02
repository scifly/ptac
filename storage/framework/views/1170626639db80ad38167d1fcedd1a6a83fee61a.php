<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(!empty($sas['id'])): ?>
                <?php echo e(Form::hidden('id', $sas['id'], ['id' => 'id'])); ?>

            <?php endif; ?>
            <div class="form-group">
                <?php echo Form::label('name', '名称', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('name', null, [
                        'class' => 'form-control',
                        'placeholder' => '不能超过60个汉字',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 60]',
                    ]); ?>

                </div>
            </div>
            <?php echo $__env->make('partials.single_select', [
                 'label' => '所属年级',
                 'id' => 'grade_id',
                 'items' => $grades,
                 'icon' => 'fa fa-object-group'
             ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.single_select', [
                'label' => '所属学期',
                'id' => 'semester_id',
                'items' => $semesters,
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.single_select', [
                'label' => '星期几',
                'id' => 'day',
                'items' => $days,
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="form-group">
                <?php echo Form::label('start', '起始时间', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        <?php echo Form::text('start', null, [
                            'class' => 'form-control start-time',
                            'required' => 'true',
                            'data-parsley-start' => '.end-time',
                            'placeholder' => '(不得大于等于结束时间)'
                        ]); ?>

                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('end', '结束时间', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        <?php echo Form::text('end', null, [
                            'class' => 'form-control end-time',
                            'required' => 'true',
                            'data-parsley-end' => '.start-time',
                            'placeholder' => '(不得小于等于开始时间)'
                        ]); ?>

                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('msg_template', '考勤消息模板', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('msg_template', '尊敬的{name}家长, 你的孩子于{time}在校打卡, 打卡规则：{rule}, 状态：{status}', [
                        'class' => 'form-control',
                        'placeholder' => '消息模板必须为字符',
                        'required' => 'true',
                        'type' => 'integer',
                        'data-parsley-length' => '[2, 255]'
                    ]); ?>

                </div>
            </div>
            <?php echo $__env->make('partials.enabled', [
                'label' => '是否为公用',
                'id' => 'ispublic',
                'value' => $sas['ispublic'] ?? null,
                'options' => ['是', '否']                
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->make('partials.enabled', [
              'label' => '进或出',
              'id' => 'inorout',
              'value' => $sas['ispublic'] ?? null,
              'options' => ['进', '出']
          ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
