<div class="box box-default box-solid">
    <div class="box-header with-border">
        <?php echo $__env->make('partials.form_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <!-- 监护人ID -->
        <?php if(!empty($custodian['id'])): ?>
            <?php echo e(Form::hidden('id', $custodian['id'], ['id' => 'id'])); ?>

        <?php endif; ?>
        <!-- 监护人UserID -->
        <?php if(!empty($custodian['user_id'])): ?>
            <?php echo e(Form::hidden('user_id', $custodian['user_id'], ['id' => 'user_id'])); ?>

        <?php endif; ?>
        <!-- 监护人姓名 -->
            <div class="form-group">
                <?php echo e(Form::label('user[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ])); ?>

                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </div>
                        <?php echo e(Form::text('user[realname]', null, [
                            'class' => 'form-control',
                            'required' => 'true',
                            'placeholder' => '(请填写真实姓名)',
                            'data-parsley-length' => '[2, 255]'
                        ])); ?>

                    </div>
                </div>
            </div>
            <!-- 监护人英文名 -->
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
                            'data-parsley-length' => '[2, 255]'
                        ])); ?>

                    </div>
                </div>
            </div>
            <!-- 监护人性别 -->
        <?php echo $__env->make('partials.enabled', [
            'label' => '性别',
            'id' => 'user[gender]',
            'value' => $custodian->user->gender ?? null,
            'options' => ['男', '女']
        ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <!-- 监护人手机列表 -->
        <?php echo $__env->make('partials.mobile', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <!-- 监护人座机号码 -->
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
            <!-- 监护人电子邮件地址 -->
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
                            'maxlength' => '255',
                            'data-parsley-type'=>"email"
                        ])); ?>

                    </div>
                </div>
            </div>
            <!-- 被监护人列表 -->
            <div class="form-group">
                <label class="col-sm-3 control-label">被监护人</label>
                <div class="col-sm-6" style="padding-top: 3px;">
                    <div style="display: block; overflow-x: auto; clear: both; width: 100%; margin-top: 10px;">
                        <table class="table table-striped table-bordered table-hover table-condensed"
                               style="white-space: nowrap; width: 100%;">
                            <thead>
                            <tr class="bg-info">
                                <th>学生</th>
                                <th>学号</th>
                                <th>监护人关系</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody id="tBody">
                            <?php if(!empty($pupils)): ?>
                                <?php $__currentLoopData = $pupils; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $pupil): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <input type="hidden" value="<?php echo e($pupil->student_id); ?>" name="student_ids[<?php echo e($key); ?>]"
                                               id="student_ids">
                                        <td><?php echo e($pupil->student->user->realname); ?></td>
                                        <td><?php echo e($pupil->student->student_number); ?></td>
                                        <td>
                                            <input type="text" name="relationships[<?php echo e($key); ?>]" id="" readonly
                                                   class="no-border" style="background: none"
                                                   value="<?php echo e($pupil->relationship); ?>">
                                        </td>
                                        <td>
                                            <a href="javascript:" class="delete">
                                                <i class="fa fa-trash-o text-blue"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                    <button id="add-pupil" class="btn btn-box-tool" type="button">
                        <i class="fa fa-user-plus text-blue">&nbsp;新增</i>
                    </button>
                    
                </div>
            </div>
            <!-- 监护人角色 -->
        <?php echo Form::hidden('user[group_id]', $groupId); ?>

        <!-- 监护人状态 -->
            <?php echo $__env->make('partials.enabled', [
                'id' => 'user[enabled]',
                'value' => $custodian->user->enabled ?? null
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
    </div>
    <?php echo $__env->make('partials.form_buttons', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
<!-- 添加被监护人 -->
<div class="modal fade" id="pupils">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">被监护人</h4>
            </div>
            <div class="modal-body with-border">
                <div class="form-horizontal">
                    <!-- 所属学校 -->
                        <?php if(isset($schools)): ?>
                            <?php echo $__env->make('partials.single_select', [
                                    'id' => 'schoolId',
                                    'label' => '所属学校',
                                    'items' => $schools,
                                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                        <?php endif; ?>
                    <!-- 所属年级 -->
                        <?php if(isset($grades)): ?>
                            <?php echo $__env->make('partials.single_select', [
                                    'id' => 'gradeId',
                                    'label' => '所属年级',
                                    'items' => $grades
                                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                        <?php endif; ?>
                    <!-- 所属班级 -->
                        <?php if(isset($classes)): ?>
                            <?php echo $__env->make('partials.single_select', [
                                    'id' => 'classId',
                                    'label' => '所属班级',
                                    'items' => $classes
                                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                        <?php endif; ?>
                    <!-- 学生列表 -->
                        <?php if(isset($students)): ?>
                            <?php echo $__env->make('partials.single_select', [
                                    'id' => 'studentId',
                                    'label' => '被监护人',
                                    'items' => $students
                                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                        <?php endif; ?>
                    
                    <div class="form-group">
                        

                        
                        <?php echo e(Form::label('relationship', '监护关系', [
                                'class' => 'control-label col-sm-3'
                            ])); ?>

                        <div class="col-sm-6">
                            <?php echo e(Form::text('relationship', null, [
                                'id' => 'relationship',
                                'require' => 'true'
                            ])); ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-sm btn-white" data-dismiss="modal">取消</a>
                <a id="confirm-bind" href="javascript:" class="btn btn-sm btn-success">确定</a>
            </div>
        </div>
    </div>
</div>
