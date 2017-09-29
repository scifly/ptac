@extends('layouts.master')
@section('header')
    <h1>添加新年级</h1>
@endsection
@section('content')

    <div class="row">
        <div class="col-xs-12">
            <!--微网站管理-->
            <div class="panel">
                <div class="panel-body">
                    <div class="m-web">
                        <div class="m-web-content">
                            <!--轮播-->
                            <div id="myCarousel" class="carousel slide">
                                <!-- 轮播（Carousel）指标 -->
                                <ol class="carousel-indicators">
                                    @foreach($medias as $k => $v)
                                            <li data-target="#myCarousel" data-slide-to="{{$k}}" @if($k==0) class="active" @endif></li>
                                    @endforeach
                                </ol>
                                <!-- 轮播（Carousel）项目 -->
                                <div class="carousel-inner">
                                    @foreach($medias as $k => $v)

                                        @if($k==0)
                                            <div class="item active">
                                                <a href="http://www.baidu.com">
                                                    <img src="../../{{$v->path}}"
                                                         alt="First slide">
                                                </a>
                                            </div>
                                        @else
                                            <div class="item">
                                                <a href="http://www.baidu.com">
                                                    <img src="../../{{$v->path}}"
                                                         alt="First slide">
                                                </a>
                                            </div>
                                        @endif
                                    @endforeach

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
                                        @foreach($wapsite->wapsitemodule as $v)
                                            <a href="javascript:" class="col-xs-4">
                                                <div class="item-icon">
                                                    <img src="../../{{$v->media->path}}" alt="">
                                                </div>
                                                <p class="item-label">{{$v->name}}</p>
                                            </a>
                                        @endforeach


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
