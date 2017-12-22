@extends('wechat.layouts.master')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/wechat/message_center/index.css') }}">
@endsection
@section('content')
    <div class="content home">
        <div class="multi-role">
            <div class="switchschool-item clearfix">
                <div class="switchschool-head">
                    <div class="title-name"> 消息中心</div>
                    <span class="addworkicon">
							<a class="icon iconfont icon-add c-green" href="{{url('message_create')}}/"></a>
						</span>
                </div>
            </div>
            <div class="weui-tab">
                <div class="weui-navbar">
                    <a class="weui-navbar__item weui-bar__item--on" href="#tab1">
                        已发送
                    </a>
                    <a class="weui-navbar__item" href="#tab2">
                        已接收<span
                                style="display: inline-block;height: 18px;line-height:18px;font-weight:700;margin-left:10px;width: 20px;border-radius: 50%;background-color:red !important;color: #fff;">1</span>
                    </a>
                </div>
                <div class="weui-tab__bd ">
                    <!-- 已发送-->
                    <div id="tab1" class="weui-tab__bd-item weui-tab__bd-item--active">
                        <div class="tea-head">
								<span class="tea-select-list-icon"> 
									<span class="searchicon"> 
										<a class="icon iconfont icon-search3 c-green open-popup" href="javascript:;"
                                           data-target="#search"></a>
									</span> 
									
								</span>

                            <div class="selectlist-layout">
                                <div class="selectlist-box">
                                    <span class="select-box c-green b-bottom">作业 <i
                                                class="icon iconfont icon-arrLeft-fill"></i> </span>
                                </div>
                            </div>
                            <ul class="select-ul" style="display: none;">
                                <li class="c-green"> 作业</li>
                                <li class=""> 草稿</li>
                            </ul>
                            <div class="select-container" style="display: none;"></div>
                        </div>

                        <div class="list-layout">
                            @if(sizeof($sendMessages) != 0)
                                @foreach($sendMessages as $s)
                                    <div class="line"></div>
                                    <div class="teacher-list-box glayline" id="{{$s->id}}">
                                        <div class="teacher-work-box">
                                            <a class="teacher-work-head" style="color:#000" href="javascript:">
                                                <div class="titleinfo">
                                                    <div class="titleinfo-head">
                                                        <div class="titleinfo-head-left fl">
                                                            <div class="title ml12">{{$s->title}}</div>
                                                            <div class="title-info ml12">{{$s->r_user_id}}</div>
                                                        </div>
                                                        <span class="worktime">
														{{substr($s->created_at,0,-8)}}
                                                            @if($s->sent == 1)
                                                                <span class="info-status green">已发送</span>
                                                            @else
                                                                <span class="info-status green">未发送</span>
                                                            @endif
													</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                                @else
                                <div class="weui-loadmore weui-loadmore_line">
                                    <span class="weui-loadmore__tips">暂无数据</span>
                                </div>
                            @endif

                        </div>
                    </div>
                    <!-- 已发送结束-->

                    <!--已接收-->
                    <div id="tab2" class="weui-tab__bd-item">
                        <div class="tea-head">
								<span class="tea-select-list-icon"> 
									<span class="searchicon"> 
										<a class="icon iconfont icon-search3 c-green open-popup" href="javascript:;"
                                           data-target="#search"></a>
									</span> 
								</span>
                            <div class="selectlist-layout">
                                <div class="selectlist-box">
                                    <span class="select-box c-green b-bottom">作业 <i
                                                class="icon iconfont icon-arrLeft-fill"></i> </span>
                                </div>
                            </div>
                            <ul class="select-ul" style="display: none;">
                                <li class="c-green"> 作业</li>
                                <li class=""> 草稿</li>
                            </ul>
                            <div class="select-container" style="display: none;"></div>
                        </div>

                        <div class="list-layout">

                            @if( sizeof($receiveMessages) != 0)
                                @foreach($receiveMessages as $r)
                                    <div class="line"></div>
                                    <div class="teacher-list-box glayline" id="{{$r->id}}">
                                        <div class="teacher-work-box">
                                            <a class="teacher-work-head" style="color:#000" href="javascript:">
                                                <div class="titleinfo">
                                                    <div class="titleinfo-head">
                                                        <div class="titleinfo-head-left fl">
                                                            <div class="title ml12">{{$r->title}}</div>
                                                            <div class="title-info ml12">{{$r->s_user_id}}</div>
                                                        </div>
                                                        <span class="worktime">{{substr($r->created_at,0,-8)}}</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
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
                               required="">
                        <a href="javascript:" class="weui-icon-clear" id="searchClear"></a>
                    </div>
                </form>
                <a href="javascript:" class="weui-search-bar__cancel-btn close-popup" id="searchCancel"
                   style="display: block;">取消</a>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
       
    </script>
@endsection
