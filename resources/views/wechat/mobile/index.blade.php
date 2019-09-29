@extends('layouts.wap')
@section('title') 微网站 @endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('mobile') !!}">
@endsection
@section('content')
    <header class="wechat-header">
        <h1 class="wechat-title">
            {!! $wap->school->name !!}
        </h1>
        <p class="wechat-sub-title">微网站</p>
    </header>
    <div class="swiper-container swiper-container-horizontal" style="height: 230px;">
        <div class="swiper-wrapper" style="">
            @foreach($medias as $key => $media)
                <div class="swiper-slide" data-swiper-slide-index="{!! $key !!}">
                    {!! Html::image('../' . $media->path, null) !!}
                </div>
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
        @foreach ($wap->columns as $column)
            <a href="{!! url(session('acronym') . '/mobiles/column?id=' . $column->id) !!}"
               class="weui-grid js_grid"
            >
                <div class=" weui-grid__icon">
                    {!! Html::image('../' . $column->media->path, null) !!}
                </div>
                <p class="weui-grid__label">{!! $column->name !!}</p>
            </a>
        @endforeach
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