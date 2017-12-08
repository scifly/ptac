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
        .box.box-solid.box-default > .box-header {
            background-color: #f4f4f4;
        }

        .box.box-solid.box-default {
            border: 1px solid #f4f4f4;
        }
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
            {!! Form::open([
            'method' => 'post',
            'id' => 'formUser',
            'class' => 'form-horizontal form-bordered',
            'data-parsley-validate' => 'true'
            ]) !!}
        <div class="box box-default box-solid">
            <div class="box-body">
                <div class="form-horizontal">

                    @if (isset($user['id']))
                        {{ Form::hidden('user_id', $user['id'], ['id' => 'user_id']) }}
                    @endif
                        <div class="form-group">
                            {!! Form::label('password', '请输入原密码', [
                                'class' => 'col-sm-3 control-label'
                            ]) !!}
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </div>
                                    {!! Form::password('password', [
                                        'class' => 'form-control',
                                        'placeholder' => '(请输入密码)',
                                        'required' => 'true',
                                        'minlength' => '6'
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    <div class="form-group">
                        {!! Form::label('password', '请输入新密码', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-lock"></i>
                                </div>
                                {!! Form::password('password', [
                                    'class' => 'form-control',
                                    'placeholder' => '(请输入密码)',
                                    'required' => 'true',
                                    'minlength' => '6'
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('password', '请确认新密码', [
                            'class' => 'col-sm-3 control-label'
                        ]) !!}
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-lock"></i>
                                </div>
                                {!! Form::password('password', [
                                    'class' => 'form-control',
                                    'placeholder' => '(请确认密码)',
                                    'required' => 'true',
                                    'minlength' => '6'
                                ]) !!}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @include('partials.form_buttons')
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
<script src="{{ URL::asset($js) }}"></script>
</body>
</html>



