<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($custodian['id']))
                {{ Form::hidden('id', $custodian['id'], ['id' => 'id']) }}
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
                {!! Form::label('user.userid', '帐号', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {{ Form::text('user.user_id', null, [
                        'class' => 'form-control',
                        'required' => 'true',
                        'disabled' => isset($custodian['userid']) ? 'true' : 'false',
                        'placeholder' => '(成员唯一标识, 设定后不可更改)',
                        'data-parsley-type' => 'alphanum',
                        'data-parsley-length' => '[2, 255]'
                    ]) }}
                </div>
            </div>
            <div class="form-group">
                <label for="user[gender]" class="col-sm-3 control-label">性别</label>
                <div class="col-sm-6">
                    <label for="user[gender]">
                        男 <input id="user[gender]" type="radio" name="user[gender]" checked class="minimal">
                    </label>
                    <label for="user[gender]">
                        女 <input id="user[gender]" type="radio" name="user[gender]" class="minimal">
                    </label>
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
            <div class="form-group">
                {{ Form::label('group_id', '角色', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    {{ $group['remark'] }}{{ Form::hidden('group_id', $group['id']) }}
                </div>
            </div>
            @include('partials.enabled', [
                'label' => '是否启用',
                'id' => 'enabled',
                'value' => isset($custodian['enabled']) ? $custodian['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
