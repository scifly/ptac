@extends('layouts.wap')
@section('title') 微网站 @endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('/css/wechat/mobile_site/index.css') !!}">
@endsection
@section('content')
    <header class="wechat-header">
        <h1 class="wechat-title">
            {!! $wapsite->school->name !!}
        </h1>
        <p class="wechat-sub-title">微网站</p>
    </header>
    <div class="swiper-container swiper-container-horizontal" style="height: 230px;">
        <div class="swiper-wrapper" style="">
            @foreach($medias as $key => $media)
                @if ($media)
                    <div class="swiper-slide" data-swiper-slide-index="{!! $key !!}">
                        <img src="../{!! $media->path !!}" alt="">
                    </div>
                @endif
            @endforeach
        </div>
        <div class="swiper-pagination swiper-pagination-bullets">
            <span class="swiper-pagination-bullet"></span>
            <span class="swiper-pagination-bullet swiper-pagination-bullet-active"></span>
            <span class="swiper-pagination-bullet"></span>
        </div>
    </div>
    <!--九宫格图标-->
    <div class="weui-grids">
        @if ($wapsite)
            @foreach ($wapsite->wapSiteModules as $module)
                @if ($module)
                    <a href="{!! url(session('acronym') . '/mobile_sites/module?id=' . $module->id) !!}" class=" weui-grid js_grid">
                        <div class=" weui-grid__icon">
                            <img src="../{!! $module->media->path !!}" alt="">
                        </div>
                        <p class="weui-grid__label">{!! $module->name !!}</p>
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
            autoplay: 3000,
            autoplayDisableOnInteraction: false,
        });
    </script>
@endsection