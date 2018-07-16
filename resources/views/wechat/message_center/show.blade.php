@extends('wechat.layouts.master')
@section('title')
    <title>消息中心</title>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/wechat/message_center/show.css') }}">
@endsection
@section('content')
    <div style="height: 100%;" id="app">
        <div class="weui_tab">
            <div class="weui_tab_bd vux-fix-safari-overflow-scrolling">
                <div class="content homework overflow-homework">
                    <div class="homework-wrap">
                        <div class="artic-head" style="font-size: 15px;">
                            {!! Form::hidden('id', $content['id'], ['id' => 'id']) !!}
                            <span class="artic-title word-warp" style="color:#000">{{ $content['title'] }}</span>
                            <span class="artic-time" style="color:#878787">时间：{{ $content['updated_at'] }}</span>
                        </div>
                        <div class="model-name-layout d-inline">
                            <div class="model-name-left d-inline white-over" style="color:#878787;font-size:15px">
                                <span>发送者：{{ $content['sender'] }}</span>
                            </div>
                        </div>
                        <div class="model-name-layout d-inline">
                            <div class="model-name-left d-inline white-over" style="color:#878787;font-size:15px">
                                <span>接收者数量：{{ $content['recipients'] }}</span>
                            </div>
                        </div>
                        <div class="detail-content">
                            <div class="artic-detail-module">
                                <div class="writing-texts bg-fff">
                                    <div class="wwbw js-wwbw mce-item-table article-message">
                                        <?php
                                        $type = $content['type'];
                                        $message = $type != 'other' ? $content[$type]->{$type} : $content[$type];
                                        ?>
                                        @switch ($type)
                                            @case ('text')
                                                <p>{!! $message->{'content'} !!}</p>
                                                @break
                                            @case ('image')
                                                <p><img alt="" src="/{!! $message->{'path'} !!}" /></p>
                                                @break
                                            @case ('voice')
                                                <p><a href="/{!! $message->{'path'} !!}">点击下载此语音</a></p>
                                                @break
                                            @case ('video')
                                                <p>标题: {!! $message->{'title'} !!}</p>
                                                <p>描述: {!! $message->{'description'} !!}</p>
                                                <video controls>
                                                    <source src="/{!! $message->{'path'} !!}" type="video/mp4">
                                                </video>
                                                @break
                                            @case ('file')
                                                <p><a href="/{!! $content['file']->{'path'} !!}">点击下载此文件</a></p>
                                                @break
                                            @case ('textcard')
                                                <div class="card-content">
                                                    <p class="card-title">{!! $message->{'title'} !!}</p>
                                                    <p class="card-detail">{!! $message->{'description'} !!}</p>
                                                    <a class="card-url" href="{!! $message->{'url'} !!}">
                                                        {!! $message->{'btntxt'} ? $message->{'btntxt'} : '详情' !!}
                                                    </a>
                                                </div>
                                                @break
                                            @case ('mpnews')
                                                @foreach ($message->{'articles'} as $article)
                                                    <div class="mpnews-item">
                                                        <p class="mpnews-title">{!! $article->{'title'} !!}</p>
                                                        <img alt="" src="/{!! $article->{'image_url'} !!}" />
                                                        <p class="mpnews-digest">{!! $article->{'digest'} !!}</p>
                                                        <a class="mpnews-url" href="{!! $article->{'content_source_url'} !!}">阅读全文</a>
                                                    </div>
                                                @endforeach
                                                @break
                                            @case ('sms')
                                                <p>{!! $message->{'sms'} !!}</p>
                                                @break
                                            @default
                                                <p>{!! $message !!}</p>
                                                @break
                                        @endswitch
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="artic-head comment-head" style="position: relative;border-top: 5px solid rgb(251, 249, 254); margin-top:11px;">
                            <span class="artic-title white-over" style="font-size:16px;width:50%;color:#8c8c8c">回复区</span>
                            <div style="clear: both;"></div>
                        </div>
                        <div class="out-layout comment-wrap" style="background-color: #fff;">
                            <div style="padding: 0 11px" class="course-comment">
                                <div class="comment-content">
                                    <div style="padding: 15px 0 0 0; background-color: #fff">
                                        <div class="js-show-comment">
                                            <div class="comment-selfEdit-con">
                                                <div class="edit-input">我来说点什么</div>
                                                <img class="head avatar" src="/img/0.png">
                                            </div>
                                        </div>
                                        <ul class="discuss_list" style="position: relative;">

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--评论模块结束-->
                    </div>
                </div>
                @if(isset($show))
                    <div class="bottom-artic b-top">
                        <div class="bot-edit-wrap" _v-2ee0662a="">
                            <div class="bottom-edit-layout" style="padding:0;height: auto;text-align: center;">
                                {{--@if(!empty($edit))--}}
                                {{--<span class="bottom-icon-box">--}}
                                {{--<span class="icon iconfont icon-bianji c-green"></span>--}}
                                {{--</span>--}}
                                {{--@endif--}}
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
    <div id="mycomment" class="weui-popup__container popup-bottom">
        <div class="weui-popup__overlay"></div>
        <div class="weui-popup__modal comment-content" style="height: 80%;">
            {!! Form::hidden('msl_id', $content['msl_id'], ['id' => 'msl_id']) !!}
            <div style="background-color: #fff;height: 100%;">
                <div class="comment-edit-con">
                    <p style="float:left;width: 20%;" class="close-btn close-popup"><i class="icon iconfont icon-guanbi"></i></p>
                    <p style="font-size: 17px;font-weight: 700;float:left;width: 60%;text-align: center">回复内容</p>
                    <p style="font-size: 16px;width: 20%;float: left;color:#04be02" class="send-btn">发送</p>
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
@endsection
@section('script')
    <script src="{{ asset('/js/wechat/message_center/show.js') }}"></script>
@endsection