@extends('layouts.mobile_master')
@section('content')
    <!--article-->
    <div class="weui-article">
        <h1>{{$article->name}}</h1>
        <!--轮播图-->
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @if(isset($medias) && !empty($medias))
                    @foreach($medias as $k => $v)
                        <div class="swiper-slide">
                            <img src="../../../{{$v->path}}" alt="">
                        </div>
                    @endforeach
                @endif
            </div>
            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>
        </div>
        <br/>
        <!--内容页-->
        <section>
            {!! $article->content !!}
        </section>
    </div>
    <!--footer-->
    <div class="weui-footer">
        <p class="weui-footer__links">
            <a href="javascript:" class="weui-footer__link">底部链接</a>
        </p>
        <p class="weui-footer__text">Copyright © 2008-2016 weui.io</p>
    </div>
@endsection
