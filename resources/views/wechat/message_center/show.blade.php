@extends('wechat.layouts.master')
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
                        <input id="id" name="id" value="{{ $message->id }}" hidden>
                        <span class="artic-title word-warp" style="color:#000">{{ $message->title }}</span>
                        <span class="artic-time" style="color:#878787">时间：{{ $message->updated_at }}</span>
                    </div>
                    <div class="model-name-layout d-inline">
                        <div class="model-name-left d-inline white-over" style="color:#878787;font-size:15px">
                            <span>发送者：{{ $message->user->realname }}</span>
                        </div>
                    </div>
                    <div class="model-name-layout d-inline">
                        <div class="model-name-left d-inline white-over" style="color:#878787;font-size:15px">
                            <span>接收者数量：{{ $message->messageSendinglogs->recipient_count }}</span>
                        </div>
                    </div>
                    <div class="detail-content">
                        <div class="artic-detail-module">
                            <div class="writing-texts bg-fff">
                                <div class="wwbw js-wwbw mce-item-table article-message">
                                    {!! $message->content !!}
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
                                            <img class="head avatar" src="http://wx.qlogo.cn/mmopen/QRbxqI6kLPHxib7JwKrqic1OX7RXqcLpVLibOwsz2MMFG81C26nk22ljrvvBx89GZdZnRtQhvgK6XjvjDqPMFZ0DDc0PGyv25Zia/0">
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
        <input name="msl_id" id="msl_id" value="{{ $message->messageSendinglogs->id }}" hidden/>
        <div style="background-color: #fff;height: 100%;">
            <div class="comment-edit-con">
                <p style="float:left;width: 20%;" class="close-btn close-popup"><i class="icon iconfont icon-guanbi"></i></p>
                <p style="font-size: 17px;font-weight: 700;float:left;width: 60%;text-align: center">回复内容</p>
                <p style="font-size: 16px;width: 20%;float: left;color:#04be02" class="send-btn">发送</p>
            </div>
            <div class="weui_cells vux-no-group-title">
                <div class="weui_cell js-textarea-val" style="font-size: 16px">
                    <div class="weui_cell_bd weui_cell_primary">
                        <textarea class="weui_textarea" spellcheck="false" placeholder="请输入评论..." rows="3" cols="30" maxlength="100" style="height: 150px;"></textarea>
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