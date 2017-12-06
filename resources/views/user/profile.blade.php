{!! Form::model($user, [ 'method' => 'put', 'id' => 'formUser', 'data-parsley-validate' => 'true']) !!}
<div class="box box-default box-solid">
    {{--<div class="box-header with-border">--}}
        {{--@include('partials.form_header')--}}
    {{--</div>--}}
    <div class="box-body">
        <div class="form-horizontal">

            @if (isset($user['user_id']))
                {{ Form::hidden('user_id', $user['user_id'], ['id' => 'user_id']) }}
            @endif

            <div class="form-group">
                {!! Form::label('realname', '姓名', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-user"></i>
                        </div>
                        {!! Form::text('realname', null, [
                            'class' => 'form-control',
                            'placeholder' => '(请输入真实姓名)',
                            'required' => 'true',
                            'data-parsley-length' => '[2,10]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('english_name', '英文名', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-language"></i>
                        </div>
                        {{ Form::text('english_name', null, [
                            'class' => 'form-control',
                            'placeholder' => '请填写英文名(可选)',
                            'data-parsley-type' => 'alphanum',
                            'data-parsley-length' => '[2, 255]'
                        ]) }}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('wechatid', '微信号', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-weixin"></i>
                        </div>
                        {{ Form::text('wechatid', null, [
                            'class' => 'form-control',
                            'data-parsley-type' => 'alphanum',
                            'data-parsley-length' => '[2, 255]'
                        ]) }}
                    </div>
                </div>
            </div>
            <!-- 性别 -->
            @include('partials.enabled', [
                'id' => 'gender',
                'label' => '性别',
                'value' => isset($user->gender) ? $user->gender : null,
                'options' => ['男', '女']
            ])

            <div class="form-group">
                {!! Form::label('username', '用户名', [
                    'class' => 'col-sm-3 control-label',
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-user-o"></i>
                        </div>
                        {!! Form::text('username', null, [
                            'class' => 'form-control',
                            'placeholder' => '(请输入用户名)',
                            'required' => 'true',
                            'data-parsley-length' => '[6,20]'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {{ Form::label('telephone', '座机', [
                    'class' => 'col-sm-3 control-label'
                ]) }}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-phone"></i>
                        </div>
                        {{ Form::text('telephone', null, [
                            'class' => 'form-control',
                            'placeholder' => '请输入座机号码(可选}',
                        ]) }}
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('email', '电子邮箱', [
                    'class' => 'col-sm-3 control-label'
                ]) !!}
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class= "fa fa-envelope-o"></i>
                        </div>
                        {!! Form::email('email', null, [
                            'class' => 'form-control',
                            'placeholder' => '(请输入电子邮件地址)',

                        ]) !!}
                    </div>
                </div>
            </div>
            @include('partials.mobile')

            @include('partials.enabled', [
                'id' => 'enabled',
                'value' => isset($user['enabled']) ? $user['enabled'] : NULL
            ])
        </div>
    </div>
    @include('partials.form_buttons')
</div>
{!! Form::close() !!}


