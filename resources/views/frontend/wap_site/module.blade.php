@extends('layouts.mobile_master')
@section('content')
    <!--list-->
    <div class="weui-panel__bd">

        @foreach($articles as $v)
            <a href="/wsm_articles/detail/{{$v->id}}" class="weui-media-box weui-media-box_appmsg">
                <div class="weui-media-box__hd">
                    <img class="weui-media-box__thumb"
                         src="../../{{$v->thumbnailmedia->path}}"
                         alt="">
                </div>
                <div class="weui-media-box__bd">
                    <h4 class="weui-media-box__title">{{$v->name}}</h4>
                    <p class="weui-media-box__desc">{{$v->summary}}</p>
                </div>
            </a>
        @endforeach


    </div>
    <!--footer-->
    <div class="weui-footer">
        <p class="weui-footer__links">
            <a href="javascript:" class="weui-footer__link">底部链接</a>
        </p>
        <p class="weui-footer__text">Copyright © 2008-2016 weui.io</p>
    </div>
@endsection
