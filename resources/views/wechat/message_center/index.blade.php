@extends('wechat.layouts.master')
@section('title')
    <title>消息中心</title>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/wechat/message_center/index.css') }}">
@endsection
@section('content')
    <div class="content home">
        <div class="multi-role">
            <div class="switchschool-item clearfix">
                <div class="switchschool-head">
                    <div class="title-name"> 消息中心</div>
                    @if ($canSend)
                        <span class="addworkicon">
							<a class="icon iconfont icon-add c-green" href="{{ url($acronym . '/mc/create') }}"></a>
						</span>
                    @endif
                </div>
            </div>
            <div class="weui-tab">
                <div class="weui-navbar">
                    @if ($canSend)
                        <a class="weui-navbar__item weui-bar__item--on" href="#tab1" data-type="sent">
                            发件箱
                        </a>
                    @endif
                    <a class="weui-navbar__item" href="#tab2" data-type="received">
                        收件箱
                        <span class="received">{{ $count }}</span>
                    </a>
                </div>
                <div class="weui-tab__bd ">
                    <!-- 已发送-->
                    @if ($canSend)
                        <div id="tab1" class="weui-tab__bd-item weui-tab__bd-item--active">
                            <div class="tea-head">
								<span class="tea-select-list-icon">
									<span class="searchicon">
										<a class="icon iconfont icon-search3 c-green open-popup" href="#"
                                           data-target="#search"></a>
									</span>
								</span>
                                <div class="selectlist-layout">
                                    <div class="selectlist-box">
                                    <span class="select-box c-green b-bottom">
                                        全部 <i class="icon iconfont icon-arrLeft-fill"></i>
                                    </span>
                                    </div>
                                </div>
                                <ul class="select-ul" style="display: none;">
                                    <li class="c-green" data-id="0">全部</li>
                                    @foreach ($messageTypes as $key => $vaule)
                                        <li class="c-green" data-id="{{ $key }}"> {{ $vaule }} </li>
                                    @endforeach
                                </ul>
                                <div class="select-container" style="display: none;"></div>
                            </div>
                            <div class="list-layout">
                                @if (sizeof($sent) > 0)
                                    @foreach($sent as $type => $messages)
                                        @foreach($messages as $message)
                                            <div class="table-list list-{{ $type }}">
                                                <div class="line"></div>
                                                <div class="teacher-list-box glayline" id="{{ $message['id'] }}">
                                                    <div class="teacher-work-box">
                                                        <a class="teacher-work-head" style="color:#000" href="#">
                                                            <div class="titleinfo">
                                                                <div class="titleinfo-head">
                                                                    <div class="titleinfo-head-left fl">
                                                                        <div class="title ml12">{{$message['title']}}</div>
                                                                        <div class="title-info ml12">
                                                                            接收者：{{ $message['recipient'] }} ...
                                                                        </div>
                                                                    </div>
                                                                    <span class="worktime">
                                                                        {{ $message['created_at'] }}
                                                                        <span class="info-status green">
                                                                            {{ $message['sent'] ? '已发送' : '未发送' }}
                                                                        </span>
													                </span>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                @else
                                    <div class="weui-loadmore weui-loadmore_line">
                                        <span class="weui-loadmore__tips">暂无数据</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                @endif
                <!-- 已发送结束-->
                    <!--已接收-->
                    <div id="tab2" class="weui-tab__bd-item @if(!$canSend) weui-tab__bd-item--active @endif ">
                        <div class="tea-head">
                            <span class="tea-select-list-icon">
                                <span class="searchicon">
                                    <a class="icon iconfont icon-search3 c-green open-popup" href="#"
                                       data-target="#search"></a>
                                </span>
                            </span>
                            <div class="selectlist-layout">
                                <div class="selectlist-box">
                                    <span class="select-box c-green b-bottom">全部
                                        <i class="icon iconfont icon-arrLeft-fill"></i>
                                    </span>
                                </div>
                            </div>
                            <ul class="select-ul" style="display: none;">
                                <li class="c-green" data-id="0">全部</li>
                                @foreach ($messageTypes as $key => $vaule)
                                    <li class="c-green" data-id="{{ $key }}"> {{ $vaule }}</li>
                                @endforeach
                            </ul>
                            <div class="select-container" style="display: none;"></div>
                        </div>
                        <div class="list-layout">
                            @if( sizeof($received) > 0)
                                @foreach($received as $type => $messages)
                                    @foreach($messages as $message)
                                        <div class="table-list list-{{ $type }}">
                                            <div class="line"></div>
                                            <div class="teacher-list-box glayline" id="{{ $message['id'] }}">
                                                <div class="teacher-work-box">
                                                    <a class="teacher-work-head" style="color:#000" href="#">
                                                        <div class="titleinfo">
                                                            <div class="titleinfo-head">
                                                                <div class="titleinfo-head-left fl">
                                                                    <div class="title ml12">{{ $message['title'] }}</div>
                                                                    <div class="title-info ml12">
                                                                        发送者：{{ $message['sender'] }}
                                                                    </div>
                                                                </div>
                                                                <span class="worktime">
                                                                    {{ $message['created_at'] }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            @else
                                <div class="line"></div>
                                <div class="weui-loadmore weui-loadmore_line">
                                    <span class="weui-loadmore__tips">暂无数据</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!--已接收结束-->
                </div>
            </div>
        </div>
    </div>
@endsection
@section('search')
    <div id="search" class='weui-popup__container'>
        <div class="weui-popup__overlay"></div>
        <div class="weui-popup__modal">
            <div class="weui-search-bar weui-search-bar_focusing" id="searchBar">
                <form class="weui-search-bar__form" action="#">
                    <div class="weui-search-bar__box">
                        <i class="weui-icon-search"></i>
                        <input type="search" class="weui-search-bar__input" id="searchInput" placeholder="请输入搜索内容"
                               required=""/>

                        <a href="#" class="weui-icon-clear" id="searchClear"></a>
                    </div>
                </form>
                <a href="#" class="weui-search-bar__cancel-btn close-popup" id="searchCancel"
                   style="display: block;">取消</a>
            </div>
            <div class="weui-tab__bd-item weui-tab__bd-item--active">
                <div class="weui-tab__bd-item weui-tab__bd-item--active">
                    <div class="list-layout"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('/js/wechat/message_center/index.js') }}"></script>
@endsection