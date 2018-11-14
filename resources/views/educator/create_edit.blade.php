<div class="box box-default box-solid main-form">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($educator['id']))
                {{ Form::hidden('id', $educator['id'], ['id' => 'id']) }}
                {{ Form::hidden('user_id', $educator['user_id'], ['id' => 'user_id']) }}
                @include('shared.avatar', ['user' => $educator->user])
            @endif
            <!-- 真实姓名 -->
            <div class="form-group">
                {!! Form::label('user[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-user'])
                        {!! Form::text('user[realname]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入真实姓名)',
                            'required' => 'true',
                            'data-parsley-length' => '[2,255]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 英文名 -->
            <div class="form-group">
                {{ Form::label('user[english_name]', '英文名', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-language'])
                        {{ Form::text('user[english_name]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请填写英文名, 可选)',
                            'data-parsley-length' => '[2, 255]'
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 性别 -->
            @include('shared.switch', [
                'id' => 'user[gender]',
                'label' => '性别',
                'value' => $educator->user->gender ?? null,
                'options' => ['男', '女']
            ])
            <!-- 角色 -->
            @include('shared.single_select', [
                'label' => '角色',
                'id' => 'user[group_id]',
                'items' => $groups,
                'icon' => 'fa fa-meh-o'
            ])
            <!-- 用户名 -->
            <div class="form-group">
                {!! Form::label('user[username]', '用户名', [
                    'class' => 'col-sm-3 control-label',
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-user-o'])
                        {!! Form::text('user[username]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入用户名)',
                            'required' => 'true',
                            'data-parsley-length' => '[6,30]'
                        ]) !!}
                    </div>
                </div>
            </div>
            @if (!isset($educator['id']))
                <!-- 密码 -->
                <div class="form-group">
                    {!! Form::label('user[password]', '密码', [
                        'class' => 'col-sm-3 control-label'
                    ]) !!}
                    <div class="col-sm-6">
                        <div class="input-group">
                            @include('shared.icon_addon', ['class' => 'fa-lock'])
                            {!! Form::password('user[password]', [
                                'class' => 'form-control text-blue',
                                'placeholder' => '(请输入密码)',
                                'required' => 'true',
                                'minlength' => '8'
                            ]) !!}
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
                            @include('shared.icon_addon', ['class' => 'fa-lock'])
                            {!! Form::password('user[password_confirmation]', [
                                'class' => 'form-control text-blue',
                                'placeholder' => '(请确认密码)',
                                'required' => 'true',
                                'minlength' => '8'
                            ]) !!}
                        </div>
                    </div>
                </div>
            @endif
            <!-- 座机号码 -->
            <div class="form-group">
                {{ Form::label('user[telephone]', '座机', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-phone'])
                        {{ Form::text('user[telephone]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入座机号码, 可选)',
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 电子邮箱 -->
            <div class="form-group">
                {!! Form::label('user[email]', '电子邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-envelope-o'])
                        {!! Form::email('user[email]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入电子邮件地址)',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 职务 -->
            <div class="form-group">
                {!! Form::label('user[position]', '职务', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-briefcase'])
                        {!! Form::text('user[position]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(请输入职务)',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 手机号码 -->
            @include('shared.mobile')
            <!-- 班级、科目绑定关系 -->
            @include('educator.educator_class')
            <!-- 也是监护人 -->
            @include('shared.switch', [
                'id' => 'singular',
                'value' => isset($educator) ? ($educator->user->custodian ? 0 : 1) : null,
                'label' => '也是监护人',
                'options' => ['否', '是']
            ])
            <!-- 所属部门 -->
            <div class="form-group depart">
                {!! Form::label('departmentId', '所属部门', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6" style="margin-top: 7px;">
                    <div id="checked-nodes">
                        @if (isset($selectedDepartments))
                            @foreach($selectedDepartments as $key => $department)
                                <button type="button" class="btn btn-flat"
                                        style="margin-right: 5px; margin-bottom: 5px">
                                    <i class="{{ $department['icon'] }}"></i>
                                    {{ $department['text'] }}
                                    <i class="fa fa-close remove-selected" style="margin-left: 5px;"></i>
                                    <input type="hidden" name="selectedDepartments[]" value="{{ $department['id'] }}"/>
                                </button>
                            @endforeach
                        @endif
                    </div>
                    <input type="hidden" id="selected-node-ids" value="{{ $selectedDepartmentIds ?? '' }}"/>
                    <a id="choose" href="#"><i class="fa fa-sitemap"></i>&nbsp; 选择</a>
                </div>
            </div>
            <!-- 状态 -->
            @include('shared.switch', [
                'id' => 'user[enabled]',
                'value' => $educator['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>