<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <!-- 学生ID -->
        <?php if(!empty($student['id'])): ?>
            <!-- 学生ID -->
            <?php echo e(Form::hidden('id', $student['id'], ['id' => 'id'])); ?>

        <?php endif; ?>
        <!-- 学生UserID -->
        <?php if(!empty($student['user_id'])): ?>
            <!-- 学生UserID -->
            <?php echo e(Form::hidden('user_id', $student['user_id'], ['id' => 'user_id'])); ?>

        <?php endif; ?>
        <!-- 学生姓名 -->
            <!-- 真实姓名 -->
            <div class="form-group">
                <?php echo e(Form::label('user[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ])); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-child"></i>
                        </div>
                        <?php echo e(Form::text('user[realname]', null, [
                            'class' => 'form-control',
                            'required' => 'true',
                            'placeholder' => '(请填写真实姓名)',
                            'data-parsley-length' => '[2, 30]'
                        ])); ?>

                    </div>
                </div>
            </div>
            <!-- 英文名称 -->
            <div class="form-group">
                <?php echo e(Form::label('user[english_name]', '英文名', [
                    'class' => 'col-sm-3 control-label'
                ])); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-language"></i>
                        </div>
                        <?php echo e(Form::text('user[english_name]', null, [
                            'class' => 'form-control',
                            'placeholder' => '请填写英文名(可选)',
                            'type' => 'string',
                            'data-parsley-length' => '[2, 255]',
                            'data-parsley-type' => 'alphanum',
                        ])); ?>

                    </div>
                </div>
            </div>
            <!-- 性别 -->
        <?php echo $__env->make('partials.enabled', [
            'id' => 'user[gender]',
            'label' => '性别',
            'value' => $user['gender'] ?? null,
            'options' => ['男', '女']
        ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <!-- 手机号码 -->
        <?php echo $__env->make('partials.mobile', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <!-- 座机号码 -->
            <div class="form-group">
                <?php echo e(Form::label('user[telephone]', '座机', [
                    'class' => 'col-sm-3 control-label'
                ])); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-phone"></i>
                        </div>
                        <?php echo e(Form::text('user[telephone]', null, [
                            'class' => 'form-control',
                            'placeholder' => '请输入座机号码(可选}',
                        ])); ?>

                    </div>
                </div>
            </div>
            <!-- 电子邮件 -->
            <div class="form-group">
                <?php echo e(Form::label('user[email]', '邮箱', [
                    'class' => 'col-sm-3 control-label'
                ])); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-envelope-o"></i>
                        </div>
                        <?php echo e(Form::text('user[email]', null, [
                            'class' => 'form-control',
                            'placeholder' => '(请输入电子邮件地址)',
                            'required' => 'true',
                            'type' => 'email',
                            'maxlength' => '255',
                            'data-parsley-type'=>"email"
                        ])); ?>

                    </div>
                </div>
            </div>
            <!-- 所属年级 -->
            <?php if(isset($grades)): ?>
                <?php if(count($grades) > 1): ?>
                    <?php echo $__env->make('partials.single_select', [
                        'label' => '所属年级',
                        'id' => 'grade_id',
                        'items' => $grades,
                        'icon' => 'fa fa-object-group',
                    ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php else: ?>
                    <div class="form-group">
                        <?php echo e(Form::label('class_id', '所属年级', [
                            'class' => 'col-sm-3 control-label'
                        ])); ?>

                        <div class="col-sm-6" style="margin-top: 7px;">
                            <i class="fa fa-object-group"></i>&nbsp;<?php echo e($grades[array_keys($grades)[0]]); ?>

                            <?php echo e(Form::hidden('grade_id', array_keys($grades)[0], ['id' => 'grade_id'])); ?>

                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <!-- 所属班级 -->
            <div class="form-group">
                <?php echo Form::label('class_id', '所属班级', [
                    'class' => 'col-sm-3 control-label',
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-users"></i>
                        </div>
                        <?php echo Form::select('class_id', $classes, null, [
                            'class' => 'form-control select2',
                            'id' => 'classId',
                            'style' => 'width: 100%;'
                        ]); ?>

                    </div>
                </div>
            </div>
            <!-- 学号 -->
            <div class="form-group">
                <?php echo Form::label('student_number', '学号', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('student_number', null, [
                        'class' => 'form-control',
                        'placeholder' => '小写字母与阿拉伯数字',
                        'data-parsley-type' => 'alphanum',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 32]'
                    ]); ?>

                </div>
            </div>
            <!-- 卡号 -->
            <div class="form-group">
                <?php echo Form::label('card_number', '卡号', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('card_number', null, [
                        'class' => 'form-control',
                        'placeholder' => '小写字母与阿拉伯数字',
                        'required' => 'true',
                        'data-parsley-type' => 'alphanum',
                        'data-parsley-length' => '[2, 32]'
                    ]); ?>

                </div>
            </div>
            <!-- 生日 -->
            <div class="form-group">
                <?php echo Form::label('birthday', '生日', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo Form::date('birthday', null, [
                            'required' => 'true',
                            'class' => 'form-control',
                            'data-parsley-type' => 'date',
                        ]); ?>

                    </div>
                </div>
            </div>
            <!-- 是否住校 -->
            <?php echo $__env->make('partials.enabled', [
                'label' => '住校',
                'id' => 'oncampus',
                'value' => $student['oncampus'] ?? null,
                'options' => ['是', '否']
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <!-- 备注 -->
            <?php echo $__env->make('partials.remark', [
                'field' => 'user[remark]'
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <!-- 状态 -->
            <?php echo $__env->make('partials.enabled', [
                'id' => 'user[enabled]',
                'value' => $student['enabled'] ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
