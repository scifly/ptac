<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{!! config('app.name') !!}</title>
    <!-- jquery weui -->
    <link rel="stylesheet" href="{{ URL::asset('css/weui.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('jquery-weui.min.css') }}">
    <!-- swiper -->
    <link rel="stylesheet" href="{{ URL::asset('Scripts') }}">
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
<script src="{{ URL::asset('jquery-weui.min.js') }}"></script>
<!-- swiper -->
<script src="{{ URL::asset('js/plugins/swiper/js/swiper.jquery.min.js') }}"></script>
<script src="{{ URL::asset('js/mobile.js') }}"></script>
</body>
</html>