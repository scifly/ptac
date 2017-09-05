<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($custodian['id']))
                {{ Form::hidden('id', $custodian['id'], ['id' => 'id']) }}
            @endif
            @if (!empty($custodian['user_id']))
                 {{ Form::hidden('user_id', $custodian['user_id'], ['id' => 'user_id']) }}
            @endif
                {{--<div class="form-group">--}}
                    {{--{{ Form::label('user[username]', '用户名', [--}}
                        {{--'class' => 'col-sm-3 control-label'--}}
                    {{--]) }}--}}
                    {{--<div class="col-sm-6">--}}
                        {{--{{ Form::text('user[username]', null, [--}}
                            {{--'class' => 'form-control',--}}
                            {{--'required' => 'true',--}}
                            {{--'placeholder' => '(请填写用户名)',--}}
                            {{--'data-parsley-length' => '[2, 255]'--}}
                        {{--]) }}--}}
                    {{--</div>--}}
                {{--</div>--}}
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
            {{--</div>--}}
            {{--<div class="form-group">--}}
                {{--<label for="user[gender]" class="col-sm-3 control-label">性别</label>--}}
                {{--<div class="col-sm-6">--}}
                    {{--<label for="user[gender]">--}}
                        {{--男 <input id="user[gender]" type="radio" name="user[gender]" checked class="minimal">--}}
                    {{--</label>--}}
                    {{--<label for="user[gender]">--}}
                        {{--女 <input id="user[gender]" type="radio" name="user[gender]" class="minimal">--}}
                    {{--</label>--}}
                {{--</div>--}}
            {{--</div>--}}
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
               'label' => '包含学生',
               'id' => 'student_ids',
               'items' => $students,
               'selectedItems' => isset($selectedStudents) ? $selectedStudents : NULL
           ])

                {{--<div class="form-group addInput">--}}
                    {{--{{ Form::label('expiry', '服务到期时间', [--}}
                    {{--'class' => 'col-sm-3 control-label'--}}
                    {{--]) }}--}}
                    {{--<div class="col-sm-6">--}}
                        {{--<div class="input-group date">--}}
                            {{--<input type="text" class="form-control pull-right" id="datepicker">--}}
                            {{--<div class="input-group-addon btn btn-success btn-add">--}}
                                {{--<i class="glyphicon glyphicon-plus"></i>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}

                <div class="form-group">
                    {{ Form::label('expiry', '服务到期时间', [
                    'class' => 'col-sm-3 control-label'
                    ]) }}
                    <div class="col-sm-6">
                        {!! Form::text('expiry', null, [
                            'class' => 'form-control expiry-date',
                        ]) !!}
                    </div>
                </div>
                {{--<div class="form-group">--}}
                    {{--{{ Form::label('user[realname]', '姓名', [--}}
                        {{--'class' => 'col-sm-3 control-label'--}}
                    {{--]) }}--}}
                    {{--<div class="col-sm-6">--}}
                        {{--{{ Form::text('user[realname]', null, [--}}
                            {{--'class' => 'form-control',--}}
                            {{--'required' => 'true',--}}
                            {{--'placeholder' => '(请填写真实姓名)',--}}
                            {{--'data-parsley-length' => '[2, 255]'--}}
                        {{--]) }}--}}
                    {{--</div>--}}
                {{--</div>--}}
            @include('partials.enabled', [
                'label' => '是否启用',
                'id' => 'user[enabled]',
                'value' => isset($user['user']['enabled']) ? $user['user']['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
