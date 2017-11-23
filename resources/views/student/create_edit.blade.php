{!! Form::open([
    'method' => 'post',
    'id' => 'formStudent',
    'class' => 'form-horizontal form-bordered',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-default box-solid">
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
                        'data-parsley-length' => '[2, 30]'
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
                        'type' => 'string',
                        'data-parsley-length' => '[2, 255]'
                    ]) }}
                </div>
            </div>
            @include('partials.enabled', [
                'id' => 'user[gender]',
                'label' => '性别',
                'value' => isset($user['gender']) ? $user['gender'] : null,
                'options' => ['男', '女']
            ])
            @include('partials.mobile')
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
                        'maxlength' => '255',
                        'data-parsley-type'=>"email"
                    ]) }}
                </div>
            </div>
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
                        'required' => 'true',
                        'class' => 'form-control',
                        'placeholder' => '生日格式为2000-08-12形式',
                    ]) !!}

                </div>
            </div>
            @include('partials.enabled', [
                'label' => '是否住校',
                'id' => 'student[oncampus]',
                'value' => isset($student['oncampus']) ? $student['oncampus'] : NULL,
                'options' => ['住校', '走读']
            ])
            @include('partials.remark')
            @include('partials.enabled', [
                'id' => 'user[enabled]',
                'value' => isset($student['enabled']) ? $student['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
