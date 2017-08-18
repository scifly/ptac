@extends('layouts.mobile_master')
@section('content')
    <!--轮播图-->
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <img src="../img/banner01.jpg" alt="轮播">
            </div>
            <div class="swiper-slide">
                <img src="../img/banner02.jpg" alt="轮播">
            </div>
            <div class="swiper-slide">
                <img src="../img/banner03.jpg" alt="轮播">
            </div>
        </div>
        <!-- If we need pagination -->
        <div class="swiper-pagination"></div>
    </div>
    <!--九宫格-->
    <div class="weui-grids">
        <a href="" class="weui-grid js_grid">
            <div class="weui-grid__icon">
                <img src="../img/icon_nav_button.png" alt="Button">
            </div>
            <p class="weui-grid__label">
                Button
            </p>
        </a>
        <a href="" class="weui-grid js_grid">
            <div class="weui-grid__icon">
                <img src="../img/icon_nav_button.png" alt="Button">
            </div>
            <p class="weui-grid__label">
                List
            </p>
        </a>
        <a href="" class="weui-grid js_grid">
            <div class="weui-grid__icon">
                <img src="../img/icon_nav_button.png" alt="Button">
            </div>
            <p class="weui-grid__label">
                Form
            </p>
        </a>
        <a href="" class="weui-grid js_grid">
            <div class="weui-grid__icon">
                <img src="../img/icon_nav_button.png" alt="Button">
            </div>
            <p class="weui-grid__label">
                List
            </p>
        </a>
        <a href="" class="weui-grid js_grid">
            <div class="weui-grid__icon">
                <img src="../img/icon_nav_button.png" alt="Button">
            </div>
            <p class="weui-grid__label">
                List
            </p>
        </a>
        <a href="" class="weui-grid js_grid">
            <div class="weui-grid__icon">
                <img src="../img/icon_nav_button.png" alt="Button">
            </div>
            <p class="weui-grid__label">
                List
            </p>
        </a>
        <a href="" class="weui-grid js_grid">
            <div class="weui-grid__icon">
                <img src="../img/icon_nav_button.png" alt="Button">
            </div>
            <p class="weui-grid__label">
                List
            </p>
        </a>
        <a href="" class="weui-grid js_grid">
            <div class="weui-grid__icon">
                <img src="../img/icon_nav_button.png" alt="Button">
            </div>
            <p class="weui-grid__label">
                List
            </p>
        </a>
        <a href="" class="weui-grid js_grid">
            <div class="weui-grid__icon">
                <img src="../img/icon_nav_button.png" alt="Button">
            </div>
            <p class="weui-grid__label">
                List
            </p>
        </a>
    </div>
    <!-- footer -->
    <div class="weui-footer weui-footer_fixed-bottom">
        <p class="weui-footer__links">
            <a href="javascript:" class="weui-footer__link">底部链接</a>
        </p>
        <p class="weui-footer__text">Copyright © 2008-2016 weui.io</p>
    </div>
@endsection
