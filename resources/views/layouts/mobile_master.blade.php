<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>微网站</title>
    <!-- jquery weui -->
    <link rel="stylesheet" href="{{ URL::asset('css/weui.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/jquery-weui.min.css') }}">
    <!-- 自定义样式 -->
    <link rel="stylesheet" href="{{ URL::asset('css/mobile_main.css') }}">
</head>
<body>
<div class="wrapper">
    @yield('content')
</div>
<!-- jQuery 3 -->
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
<!-- jquery weui -->
<script src="{{ URL::asset('js/jquery-weui.min.js') }}"></script>
</body>
</html>