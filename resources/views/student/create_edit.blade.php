<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($student['id']))
                {{ Form::hidden('id', $student['id'], ['id' => 'id']) }}
            @endif

                @if (!empty($student['user_id']))
                    {{ Form::hidden('user_id', $student['user_id'], ['id' => 'user_id']) }}
                @endif

                <div class="form-group">
                    {{ Form::label('user[realname]', '姓名', [
                        'class' => 'col-sm-3 control-label'
                    ]) }}
                    <div class="col-sm-6">
                        {{ Form::text('user[realname]', null, [
                            'class' => 'form-control',
                            'required' => 'true',
                            'placeholder' => '(请填写真实姓名)',
                            'data-parsley-length' => '[2, 255]'
                        ]) }}
                    </div>
                </div>
                <div class="form-group">
                    {{ Form::label('user[english_name]', '英文名', [
                        'class' => 'col-sm-3 control-label'
                    ]) }}
                    <div class="col-sm-6">
                        {{ Form::text('user[english_name]', null, [
                            'class' => 'form-control',
                            'placeholder' => '请填写英文名(可选)',
                            'data-parsley-type' => 'alphanum',
                            'data-parsley-length' => '[2, 255]'
                        ]) }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="user[gender]" class="col-sm-3 control-label">性别</label>
                    <div class="col-sm-6">
                        {!! Form::radio('user[gender]', '1', true) !!}
                        {!! Form::label('user[gender]', '男') !!}
                        {!! Form::radio('user[gender]', '0') !!}
                        {!! Form::label('user[gender]', '女') !!}
                    </div>
                </div>
                <div class="form-group">
                    {{ Form::label('mobile[mobile]', '手机', [
                        'class' => 'col-sm-3 control-label'
                    ]) }}
                    <div class="col-sm-6">
                        {{ Form::text('mobile[mobile]', null, [
                            'class' => 'form-control',
                            'placeholder' => '(请输入手机号码)',
                            'required' => 'true',
                            'type' => 'number',
                            'data-parsley-length' => '[11, 11]'
                        ]) }}
                    </div>
                </div>
                <div class="form-group">
                    {{ Form::label('user[telephone]', '座机', [
                        'class' => 'col-sm-3 control-label'
                    ]) }}
                    <div class="col-sm-6">
                        {{ Form::text('user[telephone]', null, [
                            'class' => 'form-control',
                            'placeholder' => '请输入座机号码(可选}',
                        ]) }}
                    </div>
                </div>
                <div class="form-group">
                    {{ Form::label('user[email]', '邮箱', [
                        'class' => 'col-sm-3 control-label'
                    ]) }}
                    <div class="col-sm-6">
                        {{ Form::text('user[email]', null, [
                            'class' => 'form-control',
                            'placeholder' => '(请输入电子邮件地址)',
                            'required' => 'true',
                            'type' => 'email',
                            'maxlength' => '255'
                        ]) }}
                    </div>
                </div>
                @include('partials.multiple_select', [
                    'label' => '所属部门',
                    'id' => 'department_ids',
                    'items' => $departments,
                    'selectedItems' => isset($selectedDepartments) ? $selectedDepartments : NULL
                ])
                @include('partials.single_select', [
                    'label' => '角色',
                    'id' => 'user[group_id]',
                    'items' => $groups,
                ])
                @include('partials.multiple_select', [
                'label' => '监护人',
                'id' => 'student_ids',
                'items' => $students,
                'selectedItems' => isset($selectedStudents) ? $selectedStudents : NULL
             ])
            @include('partials.single_select', [
                'label' => '班级名称',
                'id' => 'student[class_id]',
                'items' => $class
            ])
            <div class="form-group">
                {!! Form::label('student[student_number]', '学号', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('student[student_number]', null, [
                        'class' => 'form-control',
                        'placeholder' => '小写字母与阿拉伯数字',
                        'data-parsley-type' => 'alphanum',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 32]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('student[card_number]', '卡号', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('student[card_number]', null, [
                        'class' => 'form-control',
                        'placeholder' => '小写字母与阿拉伯数字',
                        'required' => 'true',
                        'data-parsley-type' => 'alphanum',
                        'data-parsley-length' => '[2, 32]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('student[birthday]', '生日', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('student[birthday]', null, [
                        'class' => 'form-control',
                        'placeholder' => '生日格式为2000-08-12形式',
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('student[remark]', '备注', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('student[remark]', null, [
                        'class' => 'form-control',
                        'placeholder' => '备注',
                        'required' => 'true',
                        'data-parsley-length' => '[2, 32]'
                    ]) !!}
                </div>
            </div>
            @include('partials.enabled', [
                'label' => '是否住校',
                'id' => 'student[oncampus]',
                'value' => isset($student['oncampus']) ? $student['oncampus'] : NULL, 
            ])
            @include('partials.enabled', [
                'label' => '是否启用',
                'id' => 'user[enabled]',
                'value' => isset($student['enabled']) ? $student['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
