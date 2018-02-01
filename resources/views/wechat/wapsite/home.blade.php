@extends('wechat.layouts.master')
@section('title')
<title>微网站</title>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('/css/wechat/wapsite/index.css') }}">
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
					@if($v)
						<div class="swiper-slide" data-swiper-slide-index="{{$k}}"><img src="../{{$v->path}}"></div>
					@endif
				@endforeach
			@endif
      	</div>
      	<div class="swiper-pagination swiper-pagination-bullets"><span class="swiper-pagination-bullet"></span><span class="swiper-pagination-bullet swiper-pagination-bullet-active"></span><span class="swiper-pagination-bullet"></span></div>
    </div>
	
	
	<!--九宫格图标-->
	<div class="weui-grids">
		@if($wapsite)
			@foreach($wapsite->wapSiteModules as $v)
				@if($v)
					<a href="/wapsite/module/home/?id={{$v->id}}" class=" weui-grid js_grid">
						<div class=" weui-grid__icon">
							<img src="../{{$v->media->path}}" alt="">
						</div>
						<p class="weui-grid__label">{{$v->name}}</p>
					</a>
				@endif
			@endforeach
		@endif
	</div>



@endsection
@section('script')
	<script>
		
      $(".swiper-container").swiper({
        	loop: true,
        autoplay: 3000
      });
    
	</script>
@endsection