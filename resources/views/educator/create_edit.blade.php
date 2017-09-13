{!! Form::open([
    'method' => 'post',
    'id' => 'formEducator',
    'class' => 'form-horizontal form-bordered',
    'data-parsley-validate' => 'true'
]) !!}
<div class="box box-widget">
    <div class="box-header with-border">
        @include('partials.form_header')
    </div>
    <div class="box-body">
        <div class="form-horizontal">
            @if (!empty($educator['id']))
                {{ Form::hidden('id', $educator['id'], ['id' => 'id']) }}
            @endif
            <div class="form-group">
                {!! Form::label('user[realname]', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('user[realname]', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入真实姓名)',
                        'required' => 'true',
                        'data-parsley-length' => '[2,10]'
                    ]) !!}
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
                    <label id="user[gender]">
                        <input id="user[gender]" type="radio" name="user[gender]" class="minimal" value="1">
                    </label> 男
                    <label id="user[gender]">
                        <input id="user[gender]" type="radio" name="user[gender]" class="minimal" value="0">
                    </label> 女
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('user[username]', '用户名', [
                    'class' => 'col-sm-3 control-label',
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('user[username]', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入用户名)',
                        'required' => 'true',
                        'data-parsley-length' => '[6,20]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('user[password]', '密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::password('user[password]', [
                        'class' => 'form-control',
                        'placeholder' => '(请输入密码)',
                        'required' => 'true',
                        'minlength' => '8'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('user[password-confirm]', '确认密码', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::password('user[password-confirm]', [
                        'class' => 'form-control',
                        'placeholder' => '(请确认密码)',
                        'required' => 'true',
                        'minlength' => '8'
                    ]) !!}
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
                {!! Form::label('user[email]', '电子邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    {!! Form::text('user[email]', null, [
                        'class' => 'form-control',
                        'placeholder' => '(请输入电子邮件地址)',
                        'data-parsley-length' => '[3,255]'
                    ]) !!}
                </div>
            </div>
            <div class="form-group">
                <label for="mobile[mobile][]" class="col-sm-3 control-label">手机号码</label>
                <div class="col-sm-6">
                    <table class="table-bordered table-responsive" style="width: 100%;">
                        <thead>
                        <tr>
                            {{--<td><label for="mobile[mobile][]">手机号码</label></td>--}}
                            <td>手机号码</td>
                            {{--<td style="text-align: center;"><label for="mobile[isdefault][]">默认</label></td>--}}
                            <td style="text-align: center;">默认</td>
                            {{--<td style="text-align: center;"><label for="mobile[enabled][]">启用</label></td>--}}
                            <td style="text-align: center;">启用</td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($mobiles))
                            @foreach($mobiles as $key => $mobile)
                                <tr>
                                    <td><input class="form-control" name="mobile[mobile][e{{$key}}]" type="text"
                                               placeholder="（请输入手机号码）" value='{{$mobile->mobile}}'>
                                    </td>
                                    <td style="text-align: center;">
                                        <input name="mobile[isdefault]" value="e".{{$key}} type="radio" class="minimal" @if($mobile->isdefault == 1) checked @endif/>
                                    </td>
                                    <td style="text-align: center;">
                                        <input name="mobile[enabled][e{{$key}}]" type="checkbox" class="minimal" @if($mobile->enabled == 1) checked @endif />
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="input-group-btn">
                                            <button class="btn btn-box-tool btn-add" type="button">
                                                <i class="fa fa-plus text-blue"></i>
                                            </button>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td><input class="form-control" name="mobile[mobile][e1]" type="text"
                                           placeholder="（请输入手机号码）"></td>
                                <td style="text-align: center;">
                                    <input name="mobile[isdefault]" value="e1" type="radio" class="minimal">
                                </td>
                                <td style="text-align: center;">
                                    <input name="mobile[enabled][e1]" type="checkbox" class="minimal">
                                </td>
                                <td style="text-align: center;">
                                        <span class="input-group-btn">
                                            <button class="btn btn-box-tool btn-add" type="button">
                                                <i class="fa fa-plus text-blue"></i>
                                            </button>
                                        </span>
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @include('partials.single_select', [
                'label' => '角色',
                'id' => 'user[group_id]',
                'items' => $groups
            ])

            @include('partials.single_select', [
                'label' => '所属学校',
                'id' => 'educator[school_id]',
                'items' => $schools
            ])
            @include('partials.multiple_select', [
                'label' => '所属班级',
                'id' => 'educator[class_ids]',
                'items' => $squads
            ])
            @include('partials.single_select', [
               'label' => '科目',
               'id' => 'educator[subject_id]',
               'items' => $subjects
           ])
            @include('partials.enabled', [
                'label' => '是否启用',
                'id' => 'user[enabled]',
                'value' => isset($educator['enabled']) ? $educator['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
{!! Form::close() !!}


