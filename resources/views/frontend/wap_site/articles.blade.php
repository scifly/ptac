@extends('layouts.mobile_master')
@section('content')
    <!--article-->
    <div class="weui-article">
        <h1>大标题标题标题</h1>
        <!--轮播图-->
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="../img/1.jpg" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="../img/2.jpg" alt="">
                </div>
                <div class="swiper-slide">
                    <img src="../img/3.jpg" alt="">
                </div>
            </div>
            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>
        </div>
        <!--内容页-->
        <section>
            <p>
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                consequat.
            </p>
            <p>
                <img src="../img/1.jpg" alt="">
                <img src="../img/2.jpg" alt="">
            </p>
            <p>
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                consequat.
            </p>
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
