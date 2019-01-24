<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <!-- 用户id -->
            @if (!empty($operator['id']))
                {{ Form::hidden('id', $operator['id'], ['id' => 'id']) }}
                @include('shared.avatar')
            @endif
            <!-- 用户名 -->
            <div class="form-group">
                {!! Form::label('operator[username]', '用户名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-user'])
                        {!! Form::text('operator[username]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(用户名不能为空)',
                            'required' => 'true',
                            'data-parsley-length' => '[6, 255]'
                        ]) !!}
                    </div>
                </div>
            </div>
            @if (!isset($operator['id']))
                <!-- 密码 -->
                <div class="form-group">
                    {!! Form::label('operator[password]', '密码', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        <div class="input-group">
                            @include('shared.icon_addon', ['class' => 'fa-lock'])
                            {{ Form::password('operator[password]', [
                                'id' => 'password',
                                'class' => 'form-control text-blue',
                                'required' => 'true'
                            ]) }}
                        </div>
                    </div>
                </div>
                <!-- 确认密码 -->
                <div class="form-group">
                    {!! Form::label('operator[password_confirmation]', '确认密码', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        <div class="input-group">
                            @include('shared.icon_addon', ['class' => 'fa-lock'])
                            {{ Form::password('operator[password_confirmation]', [
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
            @include('shared.single_select', [
                'label' => '角色',
                'id' => 'operator[group_id]',
                'items' => $groups,
                'icon' => 'fa fa-meh-o'
            ])
            <!-- 所属企业 -->
            @if (isset($corps))
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
            @if (isset($schools))
                @include('shared.single_select', [
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
                            @include('shared.icon_addon', [
                                'class' => 'fa-university text-purple'
                            ])
                        </div>
                    </div>
                </div>
            @endif
            <!-- 真实姓名 -->
            <div class="form-group">
                {!! Form::label('operator[realname]', '真实姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-user-o'])
                        {!! Form::text('operator[realname]', null, [
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
                {!! Form::label('operator[english_name]', '英文名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-language'])
                        {!! Form::text('operator[english_name]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(可选)',
                            'data-parsley-length' => '[2, 64]',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 性别 -->
            @include('shared.switch', [
                'id' => 'operator[gender]',
                'label' => '性别',
                'value' => $operator['gender'] ?? null,
                'options' => ['男', '女']
            ])
            <!-- 手机号码 -->
            @include('shared.mobile')
            <!-- 电子邮箱 -->
            <div class="form-group">
                {{ Form::label('operator[email]', '邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-envelope-o'])
                        {{ Form::text('operator[email]', null, [
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
                {{ Form::label('operator[telephone]', '座机', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-phone'])
                        {{ Form::text('operator[telephone]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入座机号码, 可选)',
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 状态 -->
            @include('shared.switch', [
                'id' => 'operator[enabled]',
                'value' => $operator['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>