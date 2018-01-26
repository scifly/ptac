@extends('wechat.layouts.master')
@section('title')
	<title>微网站</title>
@endsection
@section('css')
	<link rel="stylesheet" href="{{ asset('/css/wechat/wapsite/module.css') }}">
@endsection
@section('content')
<!--轮播图-->
<div class="multi-role">
	<div class="switchclass-item clearfix">
		<div class="switchclass-head">

			<div class="weui-cell">
				<div class="weui-cell__bd title-name">
					<div style="text-align: center;">{{$module->name}}</div>
				</div>
			</div>

			<!--<input class="title-name" id="classlist" type="text" value="一年级一班" readonly="" data-values="一年级一班">-->
		</div>
	</div>
	<div id="main" style="width: 100%;height: auto;">
		<div class="weui-panel weui-panel_access">
			<div class="weui-panel__bd">
				@if($articles)
					@foreach($articles as $a)
						<a href="/wapsite/article/home?id={{$a->id}}" class="weui-media-box weui-media-box_appmsg">
							<div class="weui-media-box__hd">
								<img class="weui-media-box__thumb" src="../../{{$a->thumbnailmedia->path}}" alt="">
							</div>
							<div class="weui-media-box__bd">
								<h4 class="weui-media-box__title">{{$a->name}}</h4>
								<p class="weui-media-box__desc">时间:{{$a->created_at}}</p>
								<p class="weui-media-box__desc">摘要:{{$a->summary}}</p>
							</div>
						</a>
					@endforeach
				@endif
			</div>
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
