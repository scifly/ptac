<!DOCTYPE html>
<html lang="en">
<head>
    {!! Html::meta(null, null, ['charset' => 'utf-8']) !!}
    {!! Html::meta(null, 'IE=edge', ['http-equiv' => 'X-UA-Compatible']) !!}
    {!! Html::meta('csrf-token', csrf_token(), ['id' => 'csrf_token']) !!}
    {!! Html::meta('pusher-key', config('broadcasting.connections.pusher.key')) !!}
    {!! Html::meta('pusher-cluster', config('broadcasting.connections.pusher.options.cluster')) !!}
    <title>{!! config('app.name') !!}</title>
    <!-- Tell the browser to be responsive to screen width -->
    {!! Html::meta('viewport', 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no') !!}
    {!! Html::style('css/bootstrap.min.css') !!}
    {!! Html::style('css/font-awesome.min.css') !!}
    {!! Html::style('css/ionicons.min.css') !!}
    {!! Html::style('js/plugins/gritter/css/jquery.gritter.css', ['id' => 'cip']) !!}
    {!! Html::style('js/plugins/parsley/parsley.css') !!}
    {!! Html::style('css/AdminLTE.min.css') !!}
    {!! Html::style('css/skins/_all-skins.min.css') !!}
    {!! Html::style('css/page.css') !!}
    {!! Html::link('favicon.ico') !!}
    <!--[if lt IE 9]>
        {!! Html::script('https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js') !!}
        {!! Html::script('https://oss.maxcdn.com/respond/1.4.2/respond.min.js') !!}
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
{!! Html::script('js/jquery.min.js') !!}
{!! Html::script('js/jquery-ui.min.js') !!}
{!! Html::script('js/pusher.min.js') !!}
{!! Html::script('js/bootstrap.min.js') !!}
{!! Html::script('js/adminlte.min.js') !!}
{!! Html::script('js/plugins/gritter/js/jquery.gritter.min.js') !!}
{!! Html::script('js/plugins/parsley/parsley.min.js') !!}
{!! Html::script('js/plugins/parsley/i18n/zh_cn.js') !!}
{!! Html::script('js/plugins/parsley/i18n/zh_cn.extra.js') !!}
{!! Html::script('js/shared/plugins.js') !!}
{!! Html::script('js/shared/page.js') !!}
</body>
</html>
