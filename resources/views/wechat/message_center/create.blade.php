@extends('wechat.layouts.master')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/wechat/message_center/create.css') }}">
@endsection
@section('content')
		<div class="msg-send-wrap">
			<div class="scui-choose js-scui-choose-container3 js-scui-choose scui-form-group">
				<label class="scui-control-label mr4">发布对象</label>
				<div id="homeWorkChoose" class="choose-results js-choose-results"> <!-- /* eslint-disable */  -->
					
				</div> 
				<span class="icons-choose choose-icon js-choose-icon">
					<a class="icon iconfont icon-add c-green open-popup" href="javascript:" data-target="#choose"></a>
				</span>
			</div>

			<div class="weui-cell" style="background-color: #fff;">
				<div class="weui-cell__hd"><label for="name" class="weui-label">信息类型</label></div>
				<div class="weui-cell__bd">
					<input class="weui-input" id="type" type="text" value="文本" readonly="" data-values="text">
				</div>
			</div>
            <div style="height: 5px;"></div>

			<div class="mt5px msg-send-bg b-bottom hw-title">
				<div class="weui-cell">
					<div class="weui-cell__bd js-title">
						<input id="title" name="title" type="text" placeholder="标题" maxlength="30" @if(isset($message)) value="{{ $message->title }}" @else value="" @endif  class="weui-input fs18 one-line title">
					</div>
				</div>
			</div>
			
			<div class="msg-send-conwrap msg-send-bg js-content js-content-item">
				<div contenteditable="true" id="emojiInput" class="wangEditor-mobile-txt">@if(isset($message)) {!! $message->content !!}@endif</div>
			</div>

			<div class="msg-send-conicon msg-send-bg b-top js-upload-img js-content-item" style="display: none">
				<ul class="weui-flex">
					<li class="weui-flex__item addImg">
						<i class="icon iconfont icon-tupian placeholder fs15 c-999"></i>
						 <input id="uploaderInput" class="weui-uploader__input js_file" type="file" accept="image/*" multiple="multiple">
                    </li>
				</ul>
			</div>
            <div style="height: 5px;"></div>

            <div class="weui-cells weui-cells_form js-mpnews-cover js-content-item" style="margin: 0;display: none;">
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <div class="weui-uploader">
                            <div class="weui-uploader__hd">
                                <p class="weui-uploader__title">封面上传</p>
                            </div>
                            <div class="weui-uploader__bd" id="cover" style="width: 100%">
                                <div class="weui-uploader__input-box" >
                                    <input id="pic-url" onchange="upload_cover()" class="weui-uploader__input pic-url" type="file" accept="image/*" multiple="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="weui-cells weui-cells_form js-image js-content-item" style="margin: 0;display: none;">
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <div class="weui-uploader">
                            <div class="weui-uploader__hd">
                                <p class="weui-uploader__title">图片上传</p>
                            </div>
                            <div class="weui-uploader__bd">

                                <div class="weui-uploader__input-box">
                                    <input id="upload_image" class="weui-uploader__input" type="file" accept="image/*" multiple="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="weui-cells weui-cells_form js-video js-content-item" style="margin: 0;display: none;">
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <div class="weui-uploader">
                            <div class="weui-uploader__hd">
                                <p class="weui-uploader__title">视频上传</p>
                            </div>
                            <div class="weui-uploader__bd">
                                <div class="weui-uploader__input-box">
                                    <input id="upload_video" class="weui-uploader__input" type="file" accept="video/mp4" multiple="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class=" msg-send-bg b-bottom hw-title js-content_source_url js-content-item" style="display: none;">
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <input type="text" placeholder="原文链接(选填)" maxlength="30" class="weui-input one-line title">
                    </div>
                </div>
            </div>

            <div class="msg-send-bg b-bottom hw-title js-author js-content-item" style="display: none;">
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <input type="text" placeholder="作者(选填)" maxlength="10" class="weui-input one-line title">
                    </div>
                </div>
            </div>

            <div class="msg-send-bg b-bottom hw-title js-description js-content-item" style="display: none;">
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <input type="text" id="description-video" placeholder="描述" maxlength="30" class="weui-input one-line title" value="">
                    </div>
                </div>
            </div>

			{{--<div class="weui-cell weui-cell_switch b-top weui-cells_form mt5px msg-send-bg">--}}
				{{--<div class="weui-cell__bd">定时发送</div> --}}
				{{--<div class="weui-cell__ft">--}}
					{{--<input type="checkbox" title="开启评论" name="openCom" class="weui-switch"></div>--}}
			{{--</div>--}}
			{{----}}
			{{--<div class="hw-time b-top" style="display: none;">--}}
				{{--<div class="weui-cell msg-send-bg">--}}
					{{--<div class="weui-cell__hd">--}}
						{{--<label for="" class="weui-label">发送日期</label>--}}
					{{--</div> --}}
					{{--<div class="weui-cell__bd">--}}
						{{--<input id="time" name="time" readonly="readonly" type="text" placeholder="请选择日期" class="weui-input ma_expect_date" data-toggle='datetime-picker'>--}}
					{{--</div>--}}
				{{--</div>--}}
			{{--</div>--}}
			
			<div class="weui-flex mt5px">
				<div class="weui-flex__item">
					<div class="placeholder msg-send-btn" style="padding: 15px;">
						<a href="javascript:" class="weui-btn weui-btn_primary release">发布信息</a>
					</div>
				</div>
			</div>
			
		</div>

		<div id="choose" class='weui-popup__container'>
			<div class="weui-popup__overlay"></div>
			<div class="weui-popup__modal">
				<div class="choose-container js-scui-choose-layer">
					<div class="choose-container-fixed">
						<div class="choose-header js-choose-header">
							<div class="choose-header-result js-choose-header-result">


							</div>
							<div class="common-left-search">
								<i class="icon iconfont icon-search3 search-logo icons2x-search"></i>
								<input type="text" name="search" class="js-search-input" placeholder="搜索">
							</div>
						</div>

						<div class="choose-breadcrumb js-choose-breadcrumb">
							<ol class="breadcrumb js-choose-breadcrumb-ol">
								<li data-id="{{ $department->id }}" class="js-choose-breadcrumb-li headclick"><a>{{ $department->name }}</a></li>
							</ol>
						</div>

						<div class="choose-items js-choose-items">
							<div class="weui-cells weui-cells_checkbox" style="padding-bottom: 60px;">

								<div class="air-choose-group">

                                    @foreach($departments as $department)
									<div class="air-choose-item" style="position: relative;">
										<label class="weui-cell weui-check__label" id="group-{{ $department->id }}" data-item="{{ $department->id }}" data-uid="{{ $department->id }}" data-type="group">
											<div class="weui-cell__hd">
												<input type="checkbox" class="weui-check choose-item-btn" name="checkbox" >
												<i class="weui-icon-checked"></i>
											</div>
											<div class="weui-cell__bd">
												<img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjjG5UGo8OES72Sw7U1CJYHXEkg1UlGkono5lDEiaZeBFlw/64" style="border-radius: 0;" class="js-go-detail lazy" width="75" height="75">
												<span class="contacts-text">{{ $department->name }}</span>
											</div>
										</label>
										<a class="icon iconfont icon-jiantouyou show-group" style="position:absolute;top: 0;right:0;height: 55px;line-height:55px;z-index: 1;width: 30px;"></a>
									</div>
                                    @endforeach

                                    @foreach($users as $user)
									<div class="air-choose-item" style="position: relative;">
										<label class="weui-cell weui-check__label" id="person-{{ $user->id }}" data-item="{{ $user->id }}" data-uid="{{ $user->id }}" data-type="person">
											<div class="weui-cell__hd">
												<input type="checkbox" class="weui-check choose-item-btn" name="checkbox">
												<i class="weui-icon-checked"></i>
											</div>
											<div class="weui-cell__bd">
												<img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjgYesvoOibygyRfgukxHDouo6ovRRicAKOphkKd0Licg3I2w/64" class="js-go-detail lazy" width="75" height="75">
												<span class="contacts-text">{{ $user->realname }}</span>
											</div>
										</label>
									</div>
                                    @endforeach
								</div>
							</div>
							<div style="height: 40px;"></div>
						</div>

					</div>


					<div class="choose-footer js-choose-footer">

						<div class="weui-cells weui-cells_checkbox">
							<label class="weui-cell weui-check__label">
								<div class="weui-cell__hd">
									<input type="checkbox" id="checkall" class="weui-check" name="checkedall" >
									<i class="weui-icon-checked"></i>
								</div>
								<div class="weui-cell__bd">
									<p>全选</p>
								</div>
							</label>

						</div>

						<span class="scui-pull-right js-choose-sure def-color choose-enable" id="choose-btn-ok">确定<i class="expand"></i></span>
						<span class="js-choose-num choose-num"><!--  eslint-disable -->
					已选0个分组,0名用户


					</span>
					</div>

				</div>

			</div>
		</div>
        <div id="upload-wait" style="display:none;position: fixed;top: 0;height: 100%;width: 100%;z-index:999;background-color: #000;opacity: 0.4">
            <div class="weui-loadmore" style="margin-top: 50%;">
                <i class="weui-loading"></i>
                <span class="weui-loadmore__tips">正在上传</span>
            </div>
        </div>

@endsection
@section('script')
    <script src="{{asset('/js/wechat/message_center/create.js')}}"></script>
@endsection