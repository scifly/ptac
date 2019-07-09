<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
{{--    <meta name="csrf-token" content="{{ csrf_token() }}" id="csrf_token">--}}
    {!! Html::meta('csrf-token', csrf_token(), ['id' => 'csrf_token']) !!}
    <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
    <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.options.cluster') }}">
    <title>{!! config('app.name') !!}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    {!! Html::style('css/bootstrap.min.css') !!}
    {!! Html::style('css/font-awesome.min.css') !!}
    {!! Html::style('css/ionicons.min.css') !!}
    {!! Html::style('js/plugins/gritter/css/jquery.gritter.css', ['id' => 'cip']) !!}
    {!! Html::style('js/plugins/parsley/parsley.css') !!}
    {!! Html::style('css/AdminLTE.min.css') !!}
    {!! Html::style('css/skins/_all-skins.min.css') !!}
    {!! Html::style('css/page.css') !!}
    <link rel="shortcut icon" href="{{ URL::asset('favicon.ico') }}">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Google Font -->
    {!! Html::style('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic') !!}
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
<!-- 页面头部 -->
@include('shared.site_header')
<!-- 页面左侧边栏 -->
@include('shared.site_main_sidebar')
<!-- 页面内容 -->
<div class="content-wrapper">
@include('shared.site_content_header')
<div class="content clearfix">
    @include('shared.site_content')
</div>
</div>
<!-- 页面尾部 -->
@include('shared.site_footer')
@include('home.profile')
</div>
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
<script src="{{ URL::asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ URL::asset('js/pusher.min.js') }}"></script>
<script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('js/adminlte.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/gritter/js/jquery.gritter.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/parsley/parsley.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/parsley/i18n/zh_cn.js') }}"></script>
<script src="{{ URL::asset('js/plugins/parsley/i18n/zh_cn.extra.js') }}"></script>
<script src="{{ URL::asset('js/shared/plugins.js') }}"></script>
<script src="{{ URL::asset('js/shared/page.js') }}"></script>
</body>
</html>
