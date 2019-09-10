@extends('layouts.wap')
@section('title') 消息中心 @endsection
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
                            {!! Form::hidden('id', $message->id, ['id' => 'id']) !!}
                            <span class="artic-title word-warp">{!! $message->title !!}</span>
                            <span class="artic-time">时间：{!! $content['updated_at'] !!}</span>
                        </div>
                        <div class="model-name-layout d-inline">
                            <div class="model-name-left d-inline white-over">
                                <span>发送者：{!! $content['sender'] !!}</span>
                            </div>
                        </div>
                        <div class="model-name-layout d-inline">
                            <div class="model-name-left d-inline white-over">
                                <span>接收者数量：{!! $content['recipients'] !!}</span>
                            </div>
                        </div>
                        <div class="detail-content">
                            <div class="artic-detail-module">
                                <div class="writing-texts bg-fff">
                                    <div class="wwbw js-wwbw mce-item-table article-message">
                                        <?php
                                        $type = $content['type'];
                                        $msg = $type != 'other' ? $content[$type]->{$type} : $content[$type];
                                        ?>
                                        @switch ($type)
                                            @case ('text')
                                                <p>{!! $msg->{'content'} !!}</p>
                                                @break
                                            @case ('image')
                                                <p><img alt="" src="/{!! $msg->{'path'} !!}" /></p>
                                                @break
                                            @case ('voice')
                                                <p><a href="/{!! $msg->{'path'} !!}">点击下载此语音</a></p>
                                                @break
                                            @case ('video')
                                                <p>标题: {!! $msg->{'title'} !!}</p>
                                                <p>描述: {!! $msg->{'description'} !!}</p>
                                                <video controls>
                                                    <source src="/{!! $msg->{'path'} !!}" type="video/mp4">
                                                </video>
                                                @break
                                            @case ('file')
                                                <p><a href="/{!! $content['file']->{'path'} !!}">点击下载此文件</a></p>
                                                @break
                                            @case ('textcard')
                                                <div class="card-content">
                                                    <p class="card-title">{!! $msg->{'title'} !!}</p>
                                                    <p class="card-detail">{!! $msg->{'description'} !!}</p>
                                                    <a class="card-url" href="{!! $msg->{'url'} !!}">
                                                        {!! $msg->{'btntxt'} ? $msg->{'btntxt'} : '详情' !!}
                                                    </a>
                                                </div>
                                                @break
                                            @case ('mpnews')
                                                @foreach ($msg->{'articles'} as $article)
                                                    <div class="mpnews-item">
                                                        <p class="mpnews-title">{!! $article->{'title'} !!}</p>
                                                        <img alt="" src="/{!! $article->{'image_url'} !!}" />
                                                        <p class="mpnews-digest">{!! $article->{'digest'} !!}</p>
                                                        <a class="mpnews-url" href="{!! $article->{'content_source_url'} !!}">阅读全文</a>
                                                    </div>
                                                @endforeach
                                                @break
                                            @case ('sms')
                                                <p>{!! $msg->{'sms'} !!}</p>
                                                @break
                                            @default
                                                <p>{!! $msg !!}</p>
                                                @break
                                        @endswitch
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection