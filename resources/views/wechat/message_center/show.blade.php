@extends('layouts.wap')
@section('title')
    <title>消息中心</title>
@endsection
@section('css')
    <link rel="stylesheet" href="{!! asset('/css/wechat/message_center/show.css') !!}">
@endsection
@section('content')
    <div id="app">
        <div class="weui_tab">
            <div class="weui_tab_bd vux-fix-safari-overflow-scrolling">
                <div class="content homework overflow-homework">
                    <div class="homework-wrap">
                        <div class="artic-head">
                            {!! Form::hidden('id', $msg->{'id'}) !!}
                            <span class="artic-title word-warp">{!! $msg->{'title'} !!}</span>
                            <span class="artic-time">时间：{!! $detail['updated_at'] !!}</span>
                        </div>
                        <div class="model-name-layout d-inline">
                            <div class="model-name-left d-inline white-over">
                                <span>发送者：{!! $detail['sender'] !!}</span>
                            </div>
                        </div>
                        <div class="model-name-layout d-inline">
                            <div class="model-name-left d-inline white-over">
                                <span>接收者数量：{!! $detail['recipients'] !!}</span>
                            </div>
                        </div>
                        <div class="detail-content">
                            <div class="artic-detail-module">
                                <div class="writing-texts bg-fff">
                                    <div class="wwbw js-wwbw mce-item-table article-message">
                                        @switch ($detail['type'])
                                            @case ('text')
                                                <p>{!! $content['content'] ?? ($content['text'] ?? '') !!}</p>
                                                @break
                                            @case ('image')
                                                <p><img alt="" src="/{!! $content['path'] !!}"/></p>
                                                @break
                                            @case ('voice')
                                                <p><a href="/{!! $content['path'] !!}">点击下载此语音</a></p>
                                                @break
                                            @case ('video')
                                                <p>标题: {!! $content['title'] !!}</p>
                                                <p>描述: {!! $content['description'] !!}</p>
                                                <video controls>
                                                    <source src="/{!! $content['path'] !!}" type="video/mp4">
                                                </video>
                                                @break
                                            @case ('file')
                                                <p><a href="/{!! $content['path'] !!}">点击下载此文件</a></p>
                                                @break
                                            @case ('textcard')
                                                <div class="card-content">
                                                    <p class="card-title">{!! $content['title'] !!}</p>
                                                    <p class="card-detail">{!! $content['description'] !!}</p>
                                                    <a class="card-url" href="{!! $content['url'] !!}">
                                                        {!! $content['btntxt'] ? $content['btntxt'] : '详情' !!}
                                                    </a>
                                                </div>
                                                @break
                                            @case ('mpnews')
                                                @foreach ($content['articles'] as $article)
                                                    <div class="mpnews-item">
                                                        <p class="mpnews-title">{!! $article['title'] !!}</p>
                                                        <img alt="" src="/{!! $article['image_url'] !!}"/>
                                                        <p class="mpnews-digest">{!! $article['digest'] !!}</p>
                                                        <a class="mpnews-url" href="{!! $article['content_source_url'] !!}">阅读全文</a>
                                                    </div>
                                                @endforeach
                                                @break
                                            @case ('sms')
                                                <p>{!! $content !!}</p>
                                                @break
                                            @default
                                                <p>{!! $content !!}</p>
                                                @break
                                        @endswitch
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if (isset($replies))
                            <div class="artic-head comment-head">
                                <span class="artic-title white-over">回复区</span>
                                <div style="clear: both;"></div>
                            </div>
                            <div class="out-layout comment-wrap">
                                <div class="course-comment">
                                    <div class="comment-content">
                                        <div class="comment-content-container">
                                            <div class="js-show-comment">
                                                <div class="comment-selfEdit-con">
                                                    <div class="edit-input">我来说点什么</div>
                                                    <img class="head avatar" src="{!! asset('img/0.png') !!}" alt="">
                                                </div>
                                            </div>
                                            <ul class="discuss_list">
                                                @include('wechat.message_center.replies', [
                                                    'replies' => $replies
                                                ])
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @if (isset($replies))
                    <div class="bottom-artic b-top">
                        <div class="bot-edit-wrap">
                            <div class="bottom-edit-layout">
                                <span class="bottom-icon-box">
                                    <span class="icon iconfont icon-lajixiang c-green delete-message"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @if (isset($replies))
        <div id="mycomment" class="weui-popup__container popup-bottom">
            <div class="weui-popup__overlay"></div>
            <div class="weui-popup__modal comment-content">
                {!! Form::hidden('msl_id', $msg->{'msl_id'}, ['id' => 'msl_id']) !!}
                <div style="background-color: #fff;height: 100%;">
                    <div class="comment-edit-con">
                        <p class="close-btn close-popup">
                            <i class="icon iconfont icon-guanbi"></i>
                        </p>
                        <p class="reply-content">回复内容</p>
                        <p class="send-btn">发送</p>
                    </div>
                    <div class="weui_cells vux-no-group-title">
                        <div class="weui_cell js-textarea-val" style="font-size: 16px">
                            <div class="weui_cell_bd weui_cell_primary">
                                {!! Form::textarea('comment', null, [
                                    'class' => 'weui_textarea',
                                    'spellcheck' => 'false',
                                    'placeholder' => '请输入评论...',
                                    'rows' => 3,
                                    'cols' => 30,
                                    'maxlength' => 100,
                                    'style' => 'height: 150px;'
                                ]) !!}
                                <div class="weui_textarea_counter">
                                    <span>0</span>/100
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('script')
    @if (isset($replies))
        <script src="{!! asset('/js/wechat/message_center/show.js') !!}"></script>
    @endif
@endsection