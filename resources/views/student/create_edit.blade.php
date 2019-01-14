<div class="box box-default box-solid">
    <div class="box-header with-border">
        @include('shared.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <!-- 学生ID -->
            @if (isset($student['id']))
                <!-- 学生ID -->
                {{ Form::hidden('id', $student['id'], ['id' => 'id']) }}
                <!-- 学生UserID -->
                {{ Form::hidden('user_id', $student['user_id'], ['id' => 'user_id']) }}
                <!-- 学生头像 -->
                @include('shared.avatar', ['user' => $student->user])
            @endif
            <!-- 真实姓名 -->
            <div class="form-group">
                {{ Form::label('user[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-child'])
                        {{ Form::text('user[realname]', null, [
                            'class' => 'form-control text-blue',
                            'required' => 'true',
                            'placeholder' => '(请填写真实姓名)',
                            'data-parsley-length' => '[2, 255]',
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 英文名称 -->
            <div class="form-group">
                {{ Form::label('user[english_name]', '英文名', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-language'])
                        {{ Form::text('user[english_name]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(可选)',
                            'type' => 'string',
                            'data-parsley-length' => '[2, 255]',
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 性别 -->
            @include('shared.switch', [
                'id' => 'user[gender]',
                'label' => '性别',
                'value' => $user['gender'] ?? null,
                'options' => ['男', '女']
            ])
            <!-- 手机号码 -->
            @include('shared.mobile')
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
                            'placeholder' => '(可选)',
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 电子邮件 -->
            <div class="form-group">
                {{ Form::label('user[email]', '邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa-envelope-o'])
                        {{ Form::text('user[email]', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(可选)',
                            'type' => 'email',
                            'maxlength' => '255',
                            'data-parsley-type'=>"email"
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 所属年级/班级 -->
            @include('shared.single_select', [
                'label' => '所属年级',
                'id' => 'grade_id',
                'items' => $grades,
                'icon' => 'fa fa-object-group',
            ])
            @include('shared.single_select', [
                'label' => '所属班级',
                'id' => 'class_id',
                'items' => $classes,
                'icon' => 'fa fa-users',
            ])
            <!-- 学号 -->
            <div class="form-group">
                {!! Form::label('student_number', '学号', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>学</strong>
                        </div>
                        {!! Form::text('student_number', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(小写字母与阿拉伯数字)',
                            'data-parsley-type' => 'alphanum',
                            'required' => 'true',
                            'data-parsley-length' => '[2, 32]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 卡号 -->
            <div class="form-group">
                {!! Form::label('card_number', '卡号', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group" style="width: 100%;">
                        <div class="input-group-addon" style="width: 45px;">
                            <strong>卡</strong>
                        </div>
                        {!! Form::text('card_number', null, [
                            'class' => 'form-control text-blue',
                            'placeholder' => '(小写字母与阿拉伯数字)',
                            'required' => 'true',
                            'data-parsley-type' => 'alphanum',
                            'data-parsley-length' => '[2, 32]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 生日 -->
            <div class="form-group">
                {!! Form::label('birthday', '生日', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        @include('shared.icon_addon', ['class' => 'fa fa-calendar'])
                        {!! Form::date('birthday', isset($student) ? date('Y-m-d', strtotime($student->birthday)) : null, [
                            'required' => 'true',
                            'class' => 'form-control text-blue',
                            'data-parsley-type' => 'date',
                        ]) !!}
                    </div>
                </div>
            </div>
            <!-- 是否住校 -->
            @include('shared.switch', [
                'label' => '住校',
                'id' => 'oncampus',
                'value' => $student['oncampus'] ?? null,
                'options' => ['是', '否']
            ])
            <!-- 备注 -->
            @include('shared.remark', [
                'field' => 'remark'
            ])
            <!-- 状态 -->
            @include('shared.switch', [
                'id' => 'user[enabled]',
                'value' => $student['enabled'] ?? null
            ])
        </div>
    </div>
    @include('shared.form_buttons')
</div>
