@extends('layouts.master')
@section('header')

    微网站管理
@endsection
@section('breadcrumb')
    微网站管理/
@endsection
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="panel">
                <div class="panel-body">
                    <div class="m-web">
                        <div class="m-web-content">
                            <!--轮播-->
                            <div id="myCarousel" class="carousel slide">
                                <!-- 轮播（Carousel）指标 -->
                                <ol class="carousel-indicators">
                                    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                                    <li data-target="#myCarousel" data-slide-to="1"></li>
                                    <li data-target="#myCarousel" data-slide-to="2"></li>
                                </ol>
                                <!-- 轮播（Carousel）项目 -->
                                <div class="carousel-inner">
                                    <div class="item active">
                                        <img src="http://placehold.it/900x500/39CCCC/ffffff&text=I+Love+Bootstrap"
                                             alt="First slide">
                                    </div>
                                    <div class="item">
                                        <img src="http://placehold.it/900x500/3c8dbc/ffffff&text=I+Love+Bootstrap"
                                             alt="Second slide">
                                    </div>
                                    <div class="item">
                                        <img src="http://placehold.it/900x500/f39c12/ffffff&text=I+Love+Bootstrap"
                                             alt="Third slide">
                                    </div>
                                </div>
                                <!-- 轮播（Carousel）导航 -->
                                <a class="left carousel-control" href="#myCarousel"
                                   data-slide="prev">
                                    <span class="fa fa-angle-left"></span>
                                </a>
                                <a class="right carousel-control" href="#myCarousel"
                                   data-slide="next">
                                    <span class="fa fa-angle-right"></span>
                                </a>
                            </div>
                            <!--九宫格-->
                            <div class="item-list">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <a href="javascript:" class="col-xs-4">
                                            <div class="item-icon">
                                                <img src="img/book.png" alt="">
                                            </div>
                                            <p class="item-label">通讯录</p>
                                        </a>
                                        <a href="javascript:" class="col-xs-4">
                                            <div class="item-icon">
                                                <img src="img/book.png" alt="">
                                            </div>
                                            <p class="item-label">通讯录</p>
                                        </a>
                                        <a href="javascript:" class="col-xs-4">
                                            <div class="item-icon">
                                                <img src="img/book.png" alt="">
                                            </div>
                                            <p class="item-label">通讯录</p>
                                        </a>
                                        <a href="javascript:" class="col-xs-4">
                                            <div class="item-icon">
                                                <img src="img/book.png" alt="">
                                            </div>
                                            <p class="item-label">通讯录</p>
                                        </a>
                                        <a href="javascript:" class="col-xs-4">
                                            <div class="item-icon">
                                                <img src="img/book.png" alt="">
                                            </div>
                                            <p class="item-label">通讯录</p>
                                        </a>
                                        <a href="javascript:" class="col-xs-4">
                                            <div class="item-icon">
                                                <img src="img/book.png" alt="">
                                            </div>
                                            <p class="item-label">通讯录</p>
                                        </a>
                                        <a href="javascript:" class="col-xs-4">
                                            <div class="item-icon">
                                                <img src="img/book.png" alt="">
                                            </div>
                                            <p class="item-label">通讯录</p>
                                        </a>
                                        <a href="javascript:" class="col-xs-4">
                                            <div class="item-icon">
                                                <img src="img/book.png" alt="">
                                            </div>
                                            <p class="item-label">通讯录</p>
                                        </a>
                                        <a href="javascript:" class="col-xs-4">
                                            <div class="item-icon">
                                                <img src="img/book.png" alt="">
                                            </div>
                                            <p class="item-label">通讯录</p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!--版权信息-->
                            <footer>
                                <p>Copyright © 2008-2016</p>
                            </footer>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
