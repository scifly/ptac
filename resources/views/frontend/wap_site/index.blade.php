@extends('layouts.mobile_master')
@section('content')
    <!--轮播图-->
    <div class="swiper-container">
        <div class="swiper-wrapper">
            @if(isset($medias) && !empty($medias))
                @foreach($medias as $k => $v)
                    <div class="swiper-slide">
                        <img src="../../{{$v->path}}" alt="轮播">
                    </div>
                @endforeach
            @endif
        </div>
        <!-- If we need pagination -->
        <div class="swiper-pagination"></div>
    </div>
    <!--九宫格-->
    <div class="weui-grids">
        @if(isset($wapsite->wapSiteModules) && !empty($wapsite->wapSiteModules))
            @foreach($wapsite->wapSiteModules as $v)

                <a href="../wap_site_modules/webindex/{{$v->id}}" class="weui-grid js_grid">
                    <div class="weui-grid__icon">
                        <img src="../../{{$v->media->path}}" alt="Button">
                    </div>
                    <p class="weui-grid__label">
                        {{$v->name}}
                    </p>
                </a>
            @endforeach
        @endif

    </div>
    <!-- footer -->
    <div class="weui-footer weui-footer_fixed-bottom">
        <p class="weui-footer__links">
            <a href="javascript:" class="weui-footer__link">底部链接</a>
        </p>
        <p class="weui-footer__text">Copyright © 2008-2016 weui.io</p>
    </div>
@endsection
