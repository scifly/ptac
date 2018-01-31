<?php if(isset($reset)): ?>
    <script src="<?php echo e(URL::asset($reset)); ?>"></script>
<?php endif; ?>

<?php echo Form::open([
    'method' => 'post',
    'id' => 'formUser',
    'class' => 'form-horizontal form-bordered',
    'data-parsley-validate' => 'true'
]); ?>

<section class="content clearfix">
    <div class="col-lg-12">
        <div class="nav-tabs-custom">
            <div class="box box-default box-solid">
                <div class="box-header with-border">
                    <span id="breadcrumb" style="color: #999; font-size: 13px;">用户中心/重置密码</span>

                </div>
                <div class="box-body">
                    <div class="form-horizontal">
                        <?php if(isset($user['id'])): ?>
                            <?php echo e(Form::hidden('user_id', $user['id'], ['id' => 'user_id'])); ?>

                        <?php endif; ?>
                        <div class="form-group">
                            <?php echo Form::label('password', '请输入原密码', [
                                'class' => 'col-sm-3 control-label'
                            ]); ?>

                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </div>
                                    <?php echo Form::password('password', [
                                        'class' => 'form-control',
                                        'placeholder' => '(请输入密码)',
                                        'required' => 'true',
                                        'minlength' => '6'
                                    ]); ?>

                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo Form::label('pwd1', '请输入新密码', [
                                'class' => 'col-sm-3 control-label'
                            ]); ?>

                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </div>
                                    <?php echo Form::password('password', [
                                        'class' => 'form-control',
                                        'placeholder' => '(请输入密码)',
                                        'required' => 'true',
                                        'minlength' => '6',
                                    ]); ?>

                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo Form::label('password', '请确认新密码', [
                                'class' => 'col-sm-3 control-label'
                            ]); ?>

                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </div>
                                    <?php echo Form::password('password', [
                                        'class' => 'form-control',
                                        'placeholder' => '(请确认密码)',
                                        'required' => 'true',
                                        'minlength' => '6',
                                        'data-parsley-equalto' => ''


                                    ]); ?>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <?php echo $__env->make('partials.form_overlay', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <div class="box-footer">
                    
                    <div class="form-group">
                        <div class="col-sm-3 col-sm-offset-3">
                            <?php echo Form::submit('保存', ['class' => 'btn btn-primary pull-left', 'id' => 'reset']); ?>

                            <?php echo Form::reset('重置', ['class' => 'btn btn-default pull-right', 'id' => 'cancel']); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php echo Form::close(); ?>



