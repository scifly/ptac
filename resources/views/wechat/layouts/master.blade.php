<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>WeUI</title>
	<link rel="stylesheet" href="{{ asset('/css/weui.min.css') }}">
	<link rel="stylesheet" href="{{ asset('/css/jquery-weui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/ionicons.min.css') }}">
	@yield('css')
</head>
<body ontouchstart>
	<div style="height: 100%;" id="app">
		@yield('content')
	</div>
	{{--<div id="search" class='weui-popup__container'>--}}
    	{{--<div class="weui-popup__overlay"></div>--}}
    	{{--<div class="weui-popup__modal">--}}
			{{--<div class="weui-search-bar weui-search-bar_focusing" id="searchBar">--}}
	      		{{--<form class="weui-search-bar__form" action="#">--}}
			        {{--<div class="weui-search-bar__box">--}}
			          {{--<i class="weui-icon-search"></i>--}}
			          {{--<input type="search" class="weui-search-bar__input" id="searchInput" placeholder="请输入搜索内容" required="">--}}
			          {{--<a href="javascript:" class="weui-icon-clear" id="searchClear"></a>--}}
			        {{--</div>--}}
	      		{{--</form>--}}
	      		{{--<a href="javascript:" class="weui-search-bar__cancel-btn close-popup" id="searchCancel" style="display: block;">取消</a>--}}
	    	{{--</div>--}}
      	{{--</div>--}}
    {{--</div>--}}
    <script src="{{ asset('/js/jquery.min.js') }}"></script>
	<script src="{{ asset('/js/fastclick.js') }}"></script>
	<script>
		$(function() {
			FastClick.attach(document.body);
		});
	</script>
	<script src="{{ asset('/js/jquery-weui.min.js') }}"></script>
	@yield('script')
</body>
</html>
