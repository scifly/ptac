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

                    <div class="detail-content">
                        <div class="artic-detail-module">
                            <div class="writing-texts bg-fff">
                                <div class="wwbw js-wwbw mce-item-table article-message">
                                    {!! $message->content !!}
                                </div>
                            </div>
                        </div>
                    </div>
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
								<span class="icon iconfont icon-lajixiang c-green"></span>
							</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="{{ asset('/js/wechat/message_center/show.js') }}"></script>
@endsection