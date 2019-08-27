<!DOCTYPE html>
<html lang="en">
<head>
    {!! Html::meta(null, null, ['charset' => 'utf-8']) !!}
    {!! Html::meta('viewport', 'width=device-width,initial-scale=1,user-scalable=0') !!}
    {!! Html::meta('csrf-token', csrf_token()) !!}
    {!! Html::meta('pusher-key', config('broadcasting.connections.pusher.key')) !!}
    {!! Html::meta('pusher-cluster', config('broadcasting.connections.pusher.options.cluster')) !!}
    <title>@yield('title')</title>
    {!! Html::style('/css/wechat/weui.min.css') !!}
    {!! Html::style('/css/wechat/jquery-weui.min.css') !!}
    {!! Html::style('/css/wechat/iconfont.css') !!}
    {!! Html::style('/css/wechat/wechat.css') !!}
    @yield('css')
</head>
<body ontouchstart>
{!! Form::hidden('member_id', $userid, ['id' => 'member_id']) !!}
<div style="height: 100%; overflow: scroll;" id="app">
    @yield('content')
</div>
@yield('search')
{!! Html::script('/js/jquery.min.js') !!}
{!! Html::script('/js/pusher.min.js') !!}
{!! Html::script('/js/plugins/echarts/echarts.common.min.js') !!}
{!! Html::script('/js/wechat/fastclick.js') !!}
{!! Html::script('/js/wechat/jquery-weui.min.js') !!}
{!! Html::script('/js/wechat/swiper.js') !!}
{!! Html::script('/js/wechat/jweixin.js') !!}
{!! Html::script('/js/wechat/wap.js') !!}
@yield('script')
</body>
</html>