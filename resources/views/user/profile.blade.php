<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf_token" content="{{ csrf_token() }}" id="csrf_token">
    <title>家校通</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ URL::asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/gritter/css/jquery.gritter.css') }}" id="cip">
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/parsley/parsley.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/skins/_all-skins.min.css') }}">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <style>
        .box.box-solid.box-default>.box-header { background-color: #f4f4f4; }
        .box.box-solid.box-default { border: 1px solid #f4f4f4; }
    </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <!-- 页面头部 -->
@include('partials.site_header')
<!-- 页面左侧边栏 -->
@include('partials.site_main_sidebar')
<!-- 页面内容 -->
    <div class="content-wrapper">
        <div class="box-header with-border">

        </div>
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
                        'value' => $user->gender ?? null,
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


                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <!-- 页面尾部 -->
    @include('partials.site_footer')
</div>
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('js/adminlte.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/gritter/js/jquery.gritter.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/parsley/parsley.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/parsley/i18n/zh_cn.js') }}"></script>
<script src="{{ URL::asset('js/plugins/parsley/i18n/zh_cn.extra.js') }}"></script>
{{--<script src="{{ URL::asset($js) }}"></script>--}}
</body>
</html>

