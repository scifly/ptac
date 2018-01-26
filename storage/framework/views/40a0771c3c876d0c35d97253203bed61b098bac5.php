<link rel="stylesheet" href="<?php echo e(URL::asset('js/plugins/parsley/parsley.css')); ?>">
<?php echo Form::model(Auth::user(), [ 'method' => 'PUT', 'id' => 'formUser', 'data-parsley-validate' => 'true']); ?>

<section class="content clearfix">
    <?php echo $__env->make('partials.modal_dialog', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="col-lg-12">
        <div class="nav-tabs-custom">
            <div class="box box-default box-solid">
                <div class="box-header with-border">
                    <span id="breadcrumb" style="color: #999; font-size: 13px;">用户中心/修改个人信息</span>
                </div>
                <div class="box-body">
                    <div class="form-horizontal">

                        <?php echo e(Form::hidden('id', Auth::user()->id, ['id' => 'id'])); ?>

                        <div class="form-group">
                            <?php echo Form::label('avatar_url', '头像', [
                                'class' => 'col-sm-3 control-label',
                                'style' =>'line-height:80px'
                            ]); ?>

                            <div class="col-sm-6">
                                <div class="input-group">
                                    <?php if(Auth::user()->avatar_url): ?>
                                        <img src="<?php echo e(Auth::user()->avatar_url); ?>" style="height: 80px;border-radius: 40px;">
                                        <?php else: ?>
                                        <img src="<?php echo e(asset('img/user2-160x160.jpg')); ?>" style="height: 80px;border-radius: 40px;">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo Form::label('realname', '姓名', [
                                'class' => 'col-sm-3 control-label'
                            ]); ?>

                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <?php echo Form::text('realname', null, [
                                        'class' => 'form-control',
                                        'placeholder' => '(请输入真实姓名)',
                                        'required' => 'true',
                                        'data-parsley-length' => '[2,10]',
                                        'disabled' => 'true'
                                    ]); ?>

                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo Form::label('username', '用户名', [
                                'class' => 'col-sm-3 control-label',
                            ]); ?>

                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user-o"></i>
                                    </div>
                                    <?php echo Form::text('username', null, [
                                        'class' => 'form-control',
                                        'placeholder' => '(请输入用户名)',
                                        'required' => 'true',
                                        'readonly' => 'true',
                                        'data-parsley-length' => '[6,20]'
                                    ]); ?>

                                    <a class="edit_input"
                                       style="position: absolute;top: 0;right: -25px;line-height:34px" title="编辑"
                                       href="#">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php echo e(Form::label('english_name', '英文名', [
                                'class' => 'col-sm-3 control-label'
                            ])); ?>

                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-language"></i>
                                    </div>
                                    <?php echo e(Form::text('english_name', null, [
                                        'class' => 'form-control',
                                        'placeholder' => '请填写英文名(可选)',
                                        'data-parsley-type' => 'alphanum',
                                        'data-parsley-length' => '[2, 64]',
                                        'readonly'=> 'true',
                                    ])); ?>

                                    <a class="edit_input"
                                       style="position: absolute;top: 0;right: -25px;line-height:34px" title="编辑"
                                       href="#">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo e(Form::label('wechatid', '微信号', [
                                'class' => 'col-sm-3 control-label'
                            ])); ?>

                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-weixin"></i>
                                    </div>
                                    <?php echo e(Form::text('wechatid', null, [
                                        'class' => 'form-control',
                                        'readonly' => 'true',
                                        'data-parsley-type' => 'alphanum',
                                        'data-parsley-length' => '[2, 255]'
                                    ])); ?>

                                    <a class="edit_input"
                                       style="position: absolute;top: 0;right: -25px;line-height:34px" title="编辑"
                                       href="#">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- 性别 -->
                        <?php echo $__env->make('partials.enabled', [
                            'id' => 'gender',
                            'label' => '性别',
                            'value' => $user->gender ?? null,
                            'options' => ['男', '女']
                        ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                        <div class="form-group">
                            <?php echo e(Form::label('telephone', '座机', [
                                'class' => 'col-sm-3 control-label'
                            ])); ?>

                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                    <?php echo e(Form::text('telephone', null, [
                                        'class' => 'form-control',
                                        'placeholder' => '请输入座机号码(可选}',
                                       'readonly' => 'true',
                                    ])); ?>

                                    <a class="edit_input"
                                       style="position: absolute;top: 0;right: -25px;line-height:34px" title="编辑"
                                       href="#">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo Form::label('email', '电子邮箱', [
                                'class' => 'col-sm-3 control-label'
                            ]); ?>

                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-envelope-o"></i>
                                    </div>
                                    <?php echo Form::email('email', null, [
                                        'class' => 'form-control',
                                        'placeholder' => '(请输入电子邮件地址)',
                                        'readonly' => 'true',
                                    ]); ?>

                                    <a class="edit_input"
                                       style="position: absolute;top: 0;right: -25px;line-height:34px" title="编辑"
                                       href="#">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php echo Form::close(); ?>


<script src="<?php echo e(URL::asset('js/jquery.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/plugins/parsley/parsley.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/plugins/parsley/i18n/zh_cn.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/plugins/parsley/i18n/zh_cn.extra.js')); ?>"></script>
<script src="<?php echo e(URL::asset('js/plugins/gritter/js/jquery.gritter.js')); ?>"></script>
<?php if(isset($profile)): ?>
    <script src="<?php echo e(URL::asset($profile)); ?>"></script>
<?php endif; ?>


