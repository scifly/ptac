<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(isset($educator['id'])): ?>
                <?php echo e(Form::hidden('id', $educator['id'], ['id' => 'id'])); ?>

            <?php endif; ?>
            <?php if(isset($educator['user_id'])): ?>
                <?php echo e(Form::hidden('user_id', $educator['user_id'], ['id' => 'user_id'])); ?>

            <?php endif; ?>
            <!-- 真实姓名 -->
            <div class="form-group">
                <?php echo Form::label('user[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-user"></i>
                        </div>
                        <?php echo Form::text('user[realname]', null, [
                            'class' => 'form-control',
                            'placeholder' => '(请输入真实姓名)',
                            'required' => 'true',
                            'data-parsley-length' => '[2,10]'
                        ]); ?>

                    </div>
                </div>
            </div>
            <!-- 英文名 -->
            <div class="form-group">
                <?php echo e(Form::label('user[english_name]', '英文名', [
                    'class' => 'col-sm-3 control-label'
                ])); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-language"></i>
                        </div>
                        <?php echo e(Form::text('user[english_name]', null, [
                            'class' => 'form-control',
                            'placeholder' => '请填写英文名(可选)',
                            'data-parsley-type' => 'alphanum',
                            'data-parsley-length' => '[2, 255]'
                        ])); ?>

                    </div>
                </div>
            </div>
            <!-- 微信号 -->
            <div class="form-group">
                <?php echo e(Form::label('user[wechatid]', '微信号', [
                    'class' => 'col-sm-3 control-label'
                ])); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-weixin"></i>
                        </div>
                        <?php echo e(Form::text('user[wechatid]', null, [
                            'class' => 'form-control',
                            'data-parsley-type' => 'alphanum',
                            'data-parsley-length' => '[2, 255]'
                        ])); ?>

                    </div>
                </div>
            </div>
            <!-- 性别 -->
            <?php echo $__env->make('partials.enabled', [
                'id' => 'user[gender]',
                'label' => '性别',
                'value' => $educator->user->gender ?? null,
                'options' => ['男', '女']
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <!-- 角色 -->
            <?php echo $__env->make('partials.single_select', [
                'label' => '角色',
                'id' => 'user[group_id]',
                'items' => $groups,
                'icon' => 'fa fa-meh-o'
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <!-- 用户名 -->
            <div class="form-group">
                <?php echo Form::label('user[username]', '用户名', [
                    'class' => 'col-sm-3 control-label',
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-user-o"></i>
                        </div>
                        <?php echo Form::text('user[username]', null, [
                            'class' => 'form-control',
                            'placeholder' => '(请输入用户名)',
                            'required' => 'true',
                            'data-parsley-length' => '[6,30]'
                        ]); ?>

                    </div>
                </div>
            </div>
            <?php if(!isset($educator['id'])): ?>
                <!-- 密码 -->
                <div class="form-group">
                    <?php echo Form::label('user[password]', '密码', [
                        'class' => 'col-sm-3 control-label'
                    ]); ?>

                    <div class="col-sm-6">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class= "fa fa-lock"></i>
                            </div>
                            <?php echo Form::password('user[password]', [
                                'class' => 'form-control',
                                'placeholder' => '(请输入密码)',
                                'required' => 'true',
                                'minlength' => '8'
                            ]); ?>

                        </div>
                    </div>
                </div>
                <!-- 确认密码 -->
                <div class="form-group">
                    <?php echo Form::label('user[password_confirmation]', '确认密码', [
                        'class' => 'col-sm-3 control-label'
                    ]); ?>

                    <div class="col-sm-6">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class= "fa fa-lock"></i>
                            </div>
                            <?php echo Form::password('user[password_confirmation]', [
                                'class' => 'form-control',
                                'placeholder' => '(请确认密码)',
                                'required' => 'true',
                                'minlength' => '8'
                            ]); ?>

                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <!-- 座机号码 -->
            <div class="form-group">
                <?php echo e(Form::label('user[telephone]', '座机', [
                    'class' => 'col-sm-3 control-label'
                ])); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-phone"></i>
                        </div>
                        <?php echo e(Form::text('user[telephone]', null, [
                            'class' => 'form-control',
                            'placeholder' => '请输入座机号码(可选}',
                        ])); ?>

                    </div>
                </div>
            </div>
            <!-- 电子邮箱 -->
            <div class="form-group">
                <?php echo Form::label('user[email]', '电子邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-envelope-o"></i>
                        </div>
                        <?php echo Form::email('user[email]', null, [
                            'class' => 'form-control',
                            'placeholder' => '(请输入电子邮件地址)',
                        ]); ?>

                    </div>
                </div>
            </div>
            <!-- 手机号码 -->
            <?php echo $__env->make('partials.mobile', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <!-- 所属班级 -->
            <?php echo $__env->make('educator.class_subject', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <!-- 所属部门 -->
            <div class="form-group depart">
                <?php echo Form::label('departmentId', '所属部门', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div id="department-nodes-checked">
                        <?php if(isset($selectedDepartments)): ?>
                            <?php $__currentLoopData = $selectedDepartments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button" class="btn btn-flat" style="margin-right: 5px;margin-bottom: 5px">
                                    <i class="<?php echo e($department['icon']); ?>"></i>
                                    <?php echo e($department['text']); ?>

                                    <i class="fa fa-close close-selected"></i>
                                    <input type="hidden" name="selectedDepartments[]" value="<?php echo e($department['id']); ?>"/>
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                    <?php if(isset($selectedDepartmentIds)): ?>
                        <input type="hidden" id="selectedDepartmentIds" value="<?php echo e($selectedDepartmentIds); ?>"/>
                    <?php else: ?>
                        <input type="hidden" id="selectedDepartmentIds" value=""/>
                    <?php endif; ?>
                    <a id="add-department" class="btn btn-primary" style="margin-bottom: 5px">修改</a>
                </div>
            </div>
            <!-- 所属教职员工组 -->
            <?php echo $__env->make('partials.multiple_select', [
               'label' => '所属组',
               'id' => 'educator[team_id]',
               'items' => $teams,
               'selectedItems' => $selectedTeams ?? []
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <!-- 状态 -->
            <?php echo $__env->make('partials.enabled', [
                'id' => 'user[enabled]',
                'value' => $educator['enabled'] ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>

