<div class="box box-default box-solid main-form">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (isset($educator))
                {!! Form::hidden('id', $educator['id']) !!}
                {!! Form::hidden('user_id', $educator['user_id']) !!}
                @include('shared.avatar', ['user' => $educator->user])
            @endif
            <!-- 姓名 -->
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
                            'data-parsley-length' => '[2,255]',
                            'required' => 'true',
                        ]) !!}
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
                            'placeholder' => '(最少6个字符)',
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
                                'placeholder' => '(最少8个字符)',
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
                                'placeholder' => '(最少8个字符)',
                                'required' => 'true',
                                'minlength' => '8'
                            ]) !!}
                        </div>
                    </div>
                </div>
            @endif
            <!-- 手机号码 -->
            <div class="form-group">
                {!! Form::label('user[mobile]', '手机号码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-mobile'])
                        {!! Form::text('user[mobile]', null, [
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
                        {!! Form::email('user[email]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(选填。如果手机号码为空，此项必填)',
                            'maxlength' => '255'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 职务 -->
            <div class="form-group">
                {!! Form::label('user[ent_attrs][position]', '职务', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-briefcase'])
                        {!! Form::text('user[ent_attrs][position]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(选填)',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 班级、科目绑定关系 -->
            @include('educator.educator_class')
            <!-- 也是监护人 -->
            @include('shared.switch', [
                'id' => 'singular',
                'value' => isset($educator) ? ($educator->user->custodian ? 0 : 1) : null,
                'label' => '也是监护人',
                'options' => ['否', '是']
            ])
            <!-- 所属部门-->
            <div class="form-group depart">
                {!! Form::label('departmentId', '所属部门 & 标签', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6" style="margin-top: 7px;">
                    <div id="checked-nodes">
                        @foreach($selectedDepartments as $key => $department)
                            <button type="button" class="btn btn-flat"
                                    style="margin-right: 5px; margin-bottom: 5px">
                                <i class="{!! $department['icon'] !!}"></i>
                                {!! $department['text'] !!}
                                <i class="fa fa-close remove-selected" style="margin-left: 5px;"></i>
                                {!! Form::hidden('selectedDepartments[]', $department['id']) !!}
                            </button>
                        @endforeach
                    </div>
                    {!! Form::hidden(
                        'selected-node-ids[]',
                        $selectedDepartmentIds,
                        ['id' => 'selected-node-ids']
                    ) !!}
                    <a id="choose" href="#"><i class="fa fa-sitemap"></i>&nbsp; 选择</a>
                </div>
            </div>
            <!-- 所属标签 -->
            @include('shared.tag.tags')
            <!-- 一卡通 -->
            @include('shared.card')
            <!-- 状态 -->
            @include('shared.switch', [
                'id' => 'user[enabled]',
                'value' => $educator['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>