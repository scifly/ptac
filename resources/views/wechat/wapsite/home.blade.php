@extends('wechat.layouts.master')
@section('title')
<title>微网站</title>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('/css/wechat/message_center/index.css') }}">
@endsection
@section('content')
	<!--轮播图-->
	<div class="switchclass-item clearfix">
		<div class="switchclass-head"> 
			
			<div class="weui-cell">
		        <div class="weui-cell__bd title-name">
		          	<div>标题</div>
		        </div>
		    </div>
			
		</div>
	</div>
	<div class="swiper-container swiper-container-horizontal" style="height: 230px;">
      	<div class="swiper-wrapper" style="">
			@if($medias)
				@foreach($medias as $k => $v)
					<div class="swiper-slide" data-swiper-slide-index="{{$k}}"><img src="../{{$v->path}}"></div>
				@endforeach
			@endif
      	</div>
      	<div class="swiper-pagination swiper-pagination-bullets"><span class="swiper-pagination-bullet"></span><span class="swiper-pagination-bullet swiper-pagination-bullet-active"></span><span class="swiper-pagination-bullet"></span></div>
    </div>
	
	
	<!--九宫格图标-->
	<div class="weui-grids">
		@foreach($wapsite->wapSiteModules as $v)
			<a href="javascript:" class="col-xs-4">
				<div class="item-icon">
					<img src="../../{{$v->media->path}}" alt="">
				</div>
				<p class="item-label">{{$v->name}}</p>
			</a>
		@endforeach
	</div>



@endsection
@section('script')
@endsection
	<script>
		
      $(".swiper-container").swiper({
        loop: true,
        autoplay: 3000
      });
    
	</script>
