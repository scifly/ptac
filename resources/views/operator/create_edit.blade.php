<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <!-- 用户id -->
            @if (!empty($user['id']))
                {{ Form::hidden('id', $user['id'], ['id' => 'id']) }}
                @include('partials.avatar')
            @endif
            <!-- 用户名 -->
            <div class="form-group">
                {!! Form::label('user[username]', '用户名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-user'])
                        {!! Form::text('user[username]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(用户名不能为空)',
                            'required' => 'true',
                            'data-parsley-length' => '[6, 255]'
                        ]) !!}
                    </div>
                </div>
            </div>
            @if (!isset($user['id']))
                <!-- 密码 -->
                <div class="form-group">
                    {!! Form::label('user[password]', '密码', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        <div class="input-group">
                            @include('partials.icon_addon', ['class' => 'fa-lock'])
                            {{ Form::password('user[password]', [
                                'id' => 'password',
                                'class' => 'form-control text-blue',
                                'required' => 'true'
                            ]) }}
                        </div>
                    </div>
                </div>
                <!-- 确认密码 -->
                <div class="form-group">
                    {!! Form::label('user[password_confirmation]', '确认密码', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        <div class="input-group">
                            @include('partials.icon_addon', ['class' => 'fa-lock'])
                            {{ Form::password('user[password_confirmation]', [
                                'id' => 'password_confirm',
                                'class' => 'form-control text-blue',
                                'required' => 'true',
                                'data-parsley-equalto' => '#password'
                            ]) }}
                        </div>
                    </div>
                </div>
            @endif
            <!-- 角色 -->
            @include('partials.single_select', [
                'label' => '角色',
                'id' => 'user[group_id]',
                'items' => $groups,
                'icon' => 'fa fa-meh-o'
            ])
            <!-- 所属企业 -->
            @if (isset($corps))
                @include('partials.single_select', [
                    'id' => 'corp_id',
                    'label' => '所属企业',
                    'items' => $corps,
                    'icon' => 'fa fa-weixin text-green',
                    'divId' => 'corp'
                ])
            @else
                <div id="corp" class="form-group" style="display: none;">
                    {!! Form::label('corp_id', '所属企业', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        <div class="input-group">
                            @include('partials.icon_addon', [
                                'class' => 'fa-weixin text-green'
                            ])
                        </div>
                    </div>
                </div>
            @endif
            <!-- 所属学校 -->
            @if (isset($schools))
                @include('partials.single_select', [
                    'id' => 'school_id',
                    'label' => '所属学校',
                    'items' => $schools,
                    'icon' => 'fa fa-university text-purple',
                    'divId' => 'school'
                ])
            @else
                <div id="school" class="form-group" style="display: none;">
                    {!! Form::label('corp_id', '所属学校', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        <div class="input-group">
                            @include('partials.icon_addon', [
                                'class' => 'fa-university text-purple'
                            ])
                        </div>
                    </div>
                </div>
            @endif
            <!-- 真实姓名 -->
            <div class="form-group">
                {!! Form::label('user[realname]', '真实姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-user-o'])
                        {!! Form::text('user[realname]', null, [
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
                {!! Form::label('user[english_name]', '英文名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-language'])
                        {!! Form::text('user[english_name]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(可选)',
                            'data-parsley-length' => '[2, 64]',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 性别 -->
            @include('partials.enabled', [
                'id' => 'user[gender]',
                'label' => '性别',
                'value' => $user['gender'] ?? null,
                'options' => ['男', '女']
            ])
            <!-- 手机号码 -->
            @include('partials.mobile')
            <!-- 电子邮箱 -->
            <div class="form-group">
                {{ Form::label('user[email]', '邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-envelope-o'])
                        {{ Form::text('user[email]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入电子邮件地址, 可选)',
                            'type' => 'email',
                            'maxlength' => '255',
                            'data-parsley-type'=>"email"
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 座机号码 -->
            <div class="form-group">
                {{ Form::label('user[telephone]', '座机', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('partials.icon_addon', ['class' => 'fa-phone'])
                        {{ Form::text('user[telephone]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入座机号码, 可选)',
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 状态 -->
            @include('partials.enabled', [
                'id' => 'user[enabled]',
                'value' => $user['enabled'] ?? null
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>