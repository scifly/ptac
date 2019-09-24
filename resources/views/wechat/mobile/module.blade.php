@extends('layouts.wap')
@section('title') 微网站 @endsection
@section('css')
	<link rel="stylesheet" href="{!! asset('mobile') !!}">
@endsection
@section('content')
	<header class="wechat-header">
		<h1 class="wechat-title">{!! $module->name !!}</h1>
		<p class="wechat-sub-title"></p>
	</header>
	<div id="main" style="width: 100%;height: auto;">
		<div class="weui-panel weui-panel_access">
			<div class="weui-panel__bd">
				@foreach($articles as $article)
					<a href="{!! url(session('acronym') . '/mobiles/article?id=' . $article->id) !!}"
					   class="weui-media-box weui-media-box_appmsg"
					>
						<div class="weui-media-box__hd">
							<img class="weui-media-box__thumb"
								 src="../../{!! $article->thumbnailmedia->path !!}"
								 alt=""
							>
						</div>
						<div class="weui-media-box__bd">
							<h4 class="weui-media-box__title">{!! $article->name !!}</h4>
							<p class="weui-media-box__desc">时间：{!! $article->created_at !!}</p>
							<p class="weui-media-box__desc">摘要：{!! $article->summary !!}</p>
						</div>
					</a>
				@endforeach
			</div>
		</div>
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