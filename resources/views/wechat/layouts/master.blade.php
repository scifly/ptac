<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <meta name="csrf_token" content="{{ csrf_token() }}" id="csrf_token">
    <title>WeUI</title>
    <link rel="stylesheet" href="{{ asset('/css/weui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/jquery-weui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/wechat/icon/iconfont.css') }}">
    @yield('css')
</head>
<body ontouchstart>
<div style="height: 100%;" id="app">
    @yield('content')
</div>
@yield('search')

<script src="{{ asset('/js/jquery.min.js') }}"></script>
<script src="{{ asset('/js/fastclick.js') }}"></script>
<script>
    $(function () {
        FastClick.attach(document.body);
    });
</script>
<script src="{{ asset('/js/jquery-weui.min.js') }}"></script>
@yield('script')
</body>
</html>
