@extends('layouts.wap')
@section('title') 微网站 @endsection
@section('css')
	<link rel="stylesheet" href="{!! asset('mobile') !!}">
@endsection
@section('content')
	<!--轮播图-->
	<div style="height: 100%;" id="app">
		<div class="weui_tab">
			<div class="weui_tab_bd vux-fix-safari-overflow-scrolling">
				<div class="content homework overflow-homework">
					<div class="homework-wrap">
						<div class="artic-head" style="font-size: 15px;">
							<span class="artic-title word-warp" style="color:#000" >{!! $article->name !!}</span>
							<span class="artic-time" style="color:#878787">{!! $article->created_at !!}</span>
						</div>
						<div class="model-name-layout d-inline"> 
							<div class="model-name-left d-inline white-over" style="color:#878787;font-size:15px"> 
								<span>摘要</span> 
							</div>
						</div>
						<div class="swiper-container swiper-container-horizontal" style="height: 200px;">
					      	<div class="swiper-wrapper" style="">
								@if ($medias)
									@foreach($medias as $key => $media)
										<div class="swiper-slide" data-swiper-slide-index="{!! $key !!}">
											<img src="../../{!! $media->path !!}" alt="">
										</div>
									@endforeach
								@endif
					      	</div>
					      	<div class="swiper-pagination swiper-pagination-bullets">
								<span class="swiper-pagination-bullet"></span>
								<span class="swiper-pagination-bullet swiper-pagination-bullet-active"></span>
								<span class="swiper-pagination-bullet"></span>
							</div>
					    </div>
						<div class="detail-content">
							<div class="artic-detail-module"> 
								<div class="writing-texts bg-fff"> 
									<div class="wwbw js-wwbw mce-item-table article-message"> 
										{!! $article->content !!}
									</div> 
								</div> 
							</div> 
						</div>
					</div>
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
