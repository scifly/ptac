{!! Form::open([
    'method' => 'post',
    'id' => 'formOperator',
    'class' => 'form-horizontal form-bordered',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('User[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('User[realname]', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入真实姓名)',
                        'required' => 'true',
                        'data-parsley-length' => '[2,10]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="User[gender]" class="col-sm-3 control-label">性别</label>
                <div class="col-sm-6">
                    <label id="User[gender]">
                        <input id="User[gender]" type="radio" name="User[gender]" class="minimal">
                    </label> 男
                    <label id="User[gender]">
                        <input id="User[gender]" type="radio" name="User[gender]" class="minimal">
                    </label> 女
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('User[username]', '用户名', [
                    'class' => 'col-sm-3 control-label',
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('User[username]', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入用户名)',
                        'required' => 'true',
                        'data-parsley-length' => '[6,20]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('User[password]', '密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::password('User[password]', [
                        'class' => 'form-control',
                        'placeholder' => '(请输入密码)',
                        'required' => 'true',
                        'minlength' => '8'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('User[password-confirm]', '确认密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::password('User[password-confirm]', [
                        'class' => 'form-control',
                        'placeholder' => '(请确认密码)',
                        'required' => 'true',
                        'minlength' => '8'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('User[email]', '电子邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('User[email]', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入电子邮件地址)',
                        'data-parsley-length' => '[3,255]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="Mobile[mobile][]" class="col-sm-3 control-label">手机号码</label>
                <div class="col-sm-6">
                    <table class="table-bordered table-responsive" style="width: 100%;">
                        <thead>
                        <tr>
                            <td><label for="Mobile[mobile][]">手机号码</label></td>
                            <td style="text-align: center;"><label for="Mobile[isdefault][]">默认</label></td>
                            <td style="text-align: center;"><label for="Mobile[enabled][]">启用</label></td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><input class="form-control" name="Mobile[mobile][]" type="text" placeholder="(请输入手机号码)">
                            </td>
                            <td style="text-align: center;">
                                <label for="Mobile[isdefault][]">
                                    <input name="Mobile[isdefault][]" type="radio" id="Mobile[isdefault][]"
                                           class="minimal">
                                </label>
                            </td>
                            <td style="text-align: center;">
                                <label for="Mobile[enabled][]">
                                    <input name="Mobile[enabled][]" type="checkbox" id="Mobile[enabled][]"
                                           class="minimal">
                                </label>
                            </td>
                            <td style="text-align: center;">
                                <span class="input-group-btn">
                                    <button class="btn btn-box-tool btn-add" type="button">
                                        <i class="fa fa-plus text-blue"></i>
                                    </button>
                                </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '角色',
                'id' => 'Operator[group_id]',
                'items' => $groups
            ])
            @include('partials.single_select', [
                'label' => '所属公司',
                'id' => 'Operator[company_id]',
                'items' => $companies
            ])
            @include('partials.multiple_select', [
                'label' => '可管理的学校',
                'id' => 'Operator[school_ids]',
                'items' => $schools,
                'selectedItems' => NULL
            ])
            @include('partials.enabled', [
                'label' => '是否启用',
                'id' => 'User[enabled]',
                'value' => NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
{!! Form::close() !!}