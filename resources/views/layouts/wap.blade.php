<!DOCTYPE html>
<html lang="en">
<head>
    {!! Html::meta(null, null, ['charset' => 'utf-8']) !!}
    {!! Html::meta('viewport', 'width=device-width,initial-scale=1,user-scalable=0') !!}
    {!! Html::meta(null, 'IE=edge', ['http-equiv' => 'X-UA-Compatible']) !!}
    {!! Html::meta('csrf-token', csrf_token()) !!}
    {!! Html::meta('pusher-key', config('broadcasting.connections.pusher.key')) !!}
    {!! Html::meta('pusher-cluster', config('broadcasting.connections.pusher.options.cluster')) !!}
    <title>@yield('title')</title>
    {!! Html::style('/css/bootstrap.min.css') !!}
    {!! Html::style('/css/nifty.min.css') !!}
    {!! Html::style('/js/plugins/pace/pace.min.css') !!}
    {!! Html::script('/js/plugins/pace/pace.min.js') !!}
    @yield('css')
</head>
<body ontouchstart>
{!! Form::hidden('member_id', $userid ?? null, ['id' => 'member_id']) !!}
@yield('content')
{!! Html::script('/js/jquery.min.js') !!}
{!! Html::script('/js/pusher.min.js') !!}
{!! Html::script('/js/bootstrap.min.js') !!}
{!! Html::script('/js/nifty.min.js') !!}
{!! Html::script('/js/wechat/fastclick.js') !!}
{!! Html::script('/js/wechat/swiper.js') !!}
{!! Html::script('/js/wechat/jweixin.js') !!}
{!! Html::script('/js/wechat/wap.js') !!}
@yield('script')
</body>
</html>