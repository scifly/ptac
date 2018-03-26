<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <!-- 用户id -->
            @if (!empty($user['id']))
                {{ Form::hidden('id', $user['id'], ['id' => 'id']) }}
            @endif
            <!-- 用户名 -->
            <div class="form-group">
                {!! Form::label('username', '用户名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-user'])
                        {!! Form::text('username', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(用户名不能为空)',
                            'required' => 'true',
                            'data-parsley-length' => '[6, 255]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 密码 -->
            <div class="form-group">
                {!! Form::label('password', '密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-lock'])
                        {{ Form::password('password', [
                            'id' => 'password',
                            'class' => 'form-control text-blue',
                            'required' => 'true'
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 确认密码 -->
            <div class="form-group">
                {!! Form::label('password-confirm', '确认密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-lock'])
                        {{ Form::password('password-confirm', [
                            'id' => 'password-confirm',
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'data-parsley-equalto' => '#password'
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 角色 -->
            @include('partials.single_select', [
                'label' => '角色',
                'id' => 'group_id',
                'items' => $groups,
                'icon' => 'fa fa-meh-o'
            ])
            <!-- 所属企业 -->
            <div id="corp-id" class="form-group" style="display: none;">
                {!! Form::label('corp_id', '所属企业', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-weixin'])
                    </div>
                </div>
            </div>
            <!-- 所属学校 -->
            <div id="school-id" class="form-group" style="display: none;">
                {!! Form::label('corp_id', '所属学校', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-university'])
                    </div>
                </div>
            </div>
            <!-- 真实姓名 -->
            <div class="form-group">
                {!! Form::label('realname', '真实姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-user-o'])
                        {!! Form::text('realname', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不超过60个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[2, 60]',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 英文名 -->
            <div class="form-group">
                {!! Form::label('english_name', '英文名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-language'])
                        {!! Form::text('english_name', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(可选)',
                            'data-parsley-length' => '[2, 64]',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 性别 -->
            @include('partials.enabled', [
                'id' => 'gender',
                'label' => '性别',
                'value' => $user['gender'] ?? null,
                'options' => ['男', '女']
            ])
            <!-- 手机号码 -->
            @include('partials.mobile')
            <!-- 电子邮箱 -->
            <div class="form-group">
                {{ Form::label('email', '邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-envelope-o'])
                        {{ Form::text('email', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入电子邮件地址)',
                            'required' => 'true',
                            'type' => 'email',
                            'maxlength' => '255',
                            'data-parsley-type'=>"email"
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 座机号码 -->
            <div class="form-group">
                {{ Form::label('telephone', '座机', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-phone'])
                        {{ Form::text('telephone', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '请输入座机号码(可选}',
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 微信号 -->
            <div class="form-group">
                {!! Form::label('wechatid', '微信号', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('wechatid', null, [
                        'class' => 'form-control text-blue',
                        'placeholder' => '(小写字母和数字)',
                        'required' => 'true',
                        'data-parsley-type' => 'alphanum',
                        'maxlength' => '255'
                    ]) !!}
                </div>
            </div>
            <!-- 状态 -->
            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => $user['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>