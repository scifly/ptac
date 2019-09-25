<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <!-- 用户id -->
            @if (isset($operator))
                {!! Form::hidden('id', $operator['id']) !!}
                @include('shared.avatar', ['user' => $operator])
            @endif
            <!-- 用户名 -->
            <div class="form-group">
                {!! Form::label('username', '用户名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-user'])
                        {!! Form::text('username', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(用户名不能为空)',
                            'required' => 'true',
                            'data-parsley-length' => '[5, 255]'
                        ]) !!}
                    </div>
                </div>
            </div>
            @if (!isset($operator['id']))
                <!-- 密码 -->
                <div class="form-group">
                    {!! Form::label('password', '密码', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        <div class="input-group">
                            @include('shared.icon_addon', ['class' => 'fa-lock'])
                            {!! Form::password('password', [
                                'id' => 'password',
                                'class' => 'form-control text-blue',
                                'required' => 'true'
                            ]) !!}
                        </div>
                    </div>
                </div>
                <!-- 确认密码 -->
                <div class="form-group">
                    {!! Form::label('password_confirmation', '确认密码', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        <div class="input-group">
                            @include('shared.icon_addon', ['class' => 'fa-lock'])
                            {!! Form::password('password_confirmation', [
                                'id' => 'password_confirmation',
                                'class' => 'form-control text-blue',
                                'required' => 'true',
                                'data-parsley-equalto' => '#password'
                            ]) !!}
                        </div>
                    </div>
                </div>
            @endif
            <!-- 角色 -->
            @include('shared.single_select', [
                'label' => '角色',
                'id' => 'group_id',
                'items' => $groups,
                'icon' => 'fa fa-meh-o'
            ])
            <!-- 所属企业 -->
            @if ($corps->isNotEmpty())
                @include('shared.single_select', [
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
                            @include('shared.icon_addon', [
                                'class' => 'fa-weixin text-green'
                            ])
                        </div>
                    </div>
                </div>
            @endif
            <!-- 所属学校 -->
            @if ($schools->isNotEmpty())
                @include('shared.single_select', [
                    'id' => 'school_id',
                    'label' => '所属学校',
                    'items' => $schools,
                    'icon' => 'fa fa-university text-purple',
                    'divId' => 'school'
                ])
            @else
                <div id="school" class="form-group" style="display: none;">
                    {!! Form::label('school_id', '所属学校', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        <div class="input-group">
                            @include('shared.icon_addon', [
                                'class' => 'fa-university text-purple'
                            ])
                        </div>
                    </div>
                </div>
            @endif
            <!-- 姓名 -->
            <div class="form-group">
                {!! Form::label('realname', '真实姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-user-o'])
                        {!! Form::text('realname', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(不超过60个汉字)',
                            'required' => 'true',
                            'data-parsley-length' => '[2, 60]',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 性别 -->
            @include('shared.switch', [
                'id' => 'gender',
                'label' => '性别',
                'value' => $operator['gender'] ?? null,
                'options' => ['男', '女']
            ])
            <!-- 手机号码 -->
            <div class="form-group">
                {!! Form::label('user[mobile]', '手机号码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-mobile'])
                        {!! Form::text('mobile', null, [
                            'type' => 'number',
                            'class' => 'form-control text-blue',
                            'data-parsley-length' => '[11,11]',
                            'data-parsley-pattern' => '/^1[3456789]\d{9}$/',
                            'placeholder' => '(选填。如果邮箱为空，此项必填)'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 电子邮箱 -->
            <div class="form-group">
                {!! Form::label('user[email]', '邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-envelope-o'])
                        {!! Form::text('email', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(选填。如果手机号码为空，此项必填)',
                            'type' => 'email',
                            'maxlength' => '255',
                        ]) !!}
                    </div>
                </div>
            </div>
            @include('shared.card')
            <!-- 状态 -->
            @include('shared.switch', ['value' => $operator['enabled'] ?? null])
        </div>
    </div>
    @include('shared.form_buttons')
</div>