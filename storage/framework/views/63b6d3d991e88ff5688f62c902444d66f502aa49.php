<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <?php if(isset($operator['id'])): ?>
                <?php echo e(Form::hidden('id', $operator['id'], ['id' => 'id'])); ?>

            <?php endif; ?>
            <?php if(isset($operator['user_id'])): ?>
                <?php echo e(Form::hidden('user_id', $operator['user_id'], ['id' => 'user_id'])); ?>

            <?php endif; ?>
            <div class="form-group">
                <?php echo Form::label('user[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('user[realname]', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入真实姓名)',
                        'required' => 'true',
                        'data-parsley-length' => '[2,10]'
                    ]); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo e(Form::label('user[english_name]', '英文名', [
                    'class' => 'col-sm-3 control-label'
                ])); ?>

                <div class="col-sm-6">
                    <?php echo e(Form::text('user[english_name]', null, [
                        'class' => 'form-control',
                        'placeholder' => '请填写英文名(可选)',
                        'data-parsley-type' => 'alphanum',
                        'data-parsley-length' => '[2, 255]'
                    ])); ?>

                </div>
            </div>
            <?php echo $__env->make('partials.enabled', [
                'label' => '性别',
                'id' => 'user[gender]',
                'value' => $operator->user->gender ?? null,
                'options' => ['男', '女']
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php echo $__env->make('partials.single_select', [
                 'label' => '角色',
                 'id' => 'user[group_id]',
                 'items' => $groups
             ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php if(!isset($operator)): ?>
                <?php echo Form::hidden('role', $role, ['id' => 'role']); ?>


                <?php switch($role):
                    case ('运营'): ?>
                        <?php echo $__env->make('partials.single_select', [
                            'label' => '所属企业',
                            'id' => 'corp_id',
                            'items' => $corps,
                            'divId' => 'corps'
                        ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php echo $__env->make('partials.single_select', [
                            'label' => '所属学校',
                            'id' => 'school_id',
                            'items' => $schools,
                            'divId' => 'schools'
                        ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php break; ?>
                    <?php case ('企业'): ?>
                        <?php echo Form::hidden('root_id', $rootId, ['id' => 'root_id']); ?>

                        <?php echo $__env->make('partials.single_select', [
                            'label' => '所属学校',
                            'id' => 'school_id',
                            'items' => $schools,
                            'divId' => 'schools'
                        ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php break; ?>
                    <?php case ('学校'): ?>
                        <?php echo Form::hidden('root_id', $rootId, ['id' => 'root_id']); ?>

                        <?php break; ?>
                    <?php default: ?> <?php break; ?>
                <?php endswitch; ?>
            <?php endif; ?>
            <div class="form-group">
                <?php echo Form::label('user[username]', '用户名', [
                    'class' => 'col-sm-3 control-label',
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::text('user[username]', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入用户名)',
                        'required' => 'true',
                        'data-parsley-length' => '[6,20]'
                    ]); ?>

                </div>
            </div>
            <?php if( !isset($operator['id'])): ?>
                <div class="form-group">
                    <?php echo Form::label('user[password]', '密码', [
                        'class' => 'col-sm-3 control-label'
                    ]); ?>

                    <div class="col-sm-6">
                        <?php echo Form::password('user[password]', [
                            'class' => 'form-control',
                            'placeholder' => '(请输入密码)',
                            'required' => 'true',
                            'minlength' => '8'
                        ]); ?>

                    </div>
                </div>
                <div class="form-group">
                    <?php echo Form::label('user[password_confirmation]', '确认密码', [
                        'class' => 'col-sm-3 control-label'
                    ]); ?>

                    <div class="col-sm-6">
                        <?php echo Form::password('user[password_confirmation]', [
                            'class' => 'form-control',
                            'placeholder' => '(请确认密码)',
                            'required' => 'true',
                            'minlength' => '8'
                        ]); ?>

                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <?php echo e(Form::label('user[telephone]', '座机', [
                    'class' => 'col-sm-3 control-label'
                ])); ?>

                <div class="col-sm-6">
                    <?php echo e(Form::text('user[telephone]', null, [
                        'class' => 'form-control',
                        'placeholder' => '请输入座机号码(可选}',
                    ])); ?>

                </div>
            </div>
            <div class="form-group">
                <?php echo Form::label('user[email]', '电子邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <?php echo Form::email('user[email]', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入电子邮件地址)',
                    ]); ?>

                </div>
            </div>
            <?php echo $__env->make('partials.mobile', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="form-group">
                <?php echo Form::label('departmentId', '所属部门', [
                    'class' => 'col-sm-3 control-label'
                ]); ?>

                <div class="col-sm-6">
                    <div id="department-nodes-checked">
                        <?php if(isset($selectedDepartments)): ?>
                            <?php $__currentLoopData = $selectedDepartments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button" class="btn btn-flat" style="margin: 0 5px 5px 0;">
                                    <i class="<?php echo e($department['icon']); ?>"></i>
                                    <?php echo e($department['text']); ?>

                                    <i class="fa fa-close close-selected"></i>
                                    <input type="hidden"
                                           name="selectedDepartments[]"
                                           value="<?php echo e($department['id']); ?>"
                                    />
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                    <?php if(isset($selectedDepartmentIds)): ?>
                        <input type="hidden" id="selectedDepartmentIds"
                               value="<?php echo e($selectedDepartmentIds); ?>"
                        />
                    <?php else: ?>
                        <input type="hidden" id="selectedDepartmentIds" value=""/>
                    <?php endif; ?>
                    <a id="add-department" class="btn btn-primary" style="margin-bottom: 5px;">修改</a>
                </div>
            </div>
            <?php echo $__env->make('partials.enabled', [
                'id' => 'user[enabled]',
                'value' => $operator->user->enabled ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>