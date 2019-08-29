<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{!! config('app.name') !!} | 登录</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css')  }}">
    <link rel="stylesheet" href="{{ URL::asset('js/plugins/gritter/css/jquery.gritter.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{asset('js/plugins/icheck/all.css')}}">
    <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        @media (max-width: 768px) {
            .main {
                width:90% !important;
                margin-top:20px !important;
            }
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="main" style="margin:15% auto;width: 360px">
    <div class="box box-success">
        <div class="box-header with-border">
            <b>{!! config('app.name') !!}</b>
        </div>
        <!-- /.login-logo -->
        <div class="box-body">
            {{--<p class="login-box-msg">请登录</p>--}}
            <form  method="post">
                {!! csrf_field() !!}
                <div class="form-group has-feedback">
                    {!! Form::text('input', null, [
                        'id' => 'input',
                        'placeholder' => '(用户名/邮箱/手机号码)',
                        'class' => 'form-control'
                    ]) !!}
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    {!! Form::password('password', [
                        'id' => 'password',
                        'class' => 'form-control',
                        'placeholder' => '(密码)'
                    ]) !!}
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            {!! Form::checkbox('remember', 0, null, ['id' => 'remember']) !!}
                            {!! Form::label('remember', '记住我', [
                                'style' => 'vertical-align: middle; margin-left: 5px;',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-xs-4">
                        {!! Form::button(
                            Html::tag('i', ' 登录', ['class' => 'fa fa-sign-in']),
                            [
                                'id' => 'signin', 'onclick' => 'void(0)',
                                'class' => 'btn btn-primary btn-block btn-flat'
                            ]
                        ) !!}
                    </div>
                </div>
            </form>
            {{--<a href="{{Url('password/reset')}}">忘记密码</a><br>--}}
        </div>
        @include('shared.form_overlay')
    </div>
</div>
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('js/adminlte.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js')  }}"></script>
<script src="{{ asset('js/auth/login.js')  }}"></script>
<script src="{{ URL::asset('js/plugins/icheck/icheck.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/gritter/js/jquery.gritter.min.js') }}"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>
