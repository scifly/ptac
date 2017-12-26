@extends('wechat.layouts.master')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/wechat/message_center/create.css') }}">
@endsection
@section('content')
		<div class="msg-send-wrap">
			<div class="scui-choose js-scui-choose-container3 js-scui-choose scui-form-group">
				<label class="scui-control-label mr4">作业发布对象</label> 
				<div id="homeWorkChoose" class="choose-results js-choose-results"> <!-- /* eslint-disable */  -->
					
				</div> 
				<span class="icons-choose choose-icon js-choose-icon">
					<a class="icon iconfont icon-add c-green open-popup" href="javascript:;" data-target="#choose"></a>
				</span>
			</div>
			
			<div class="mt5px msg-send-bg b-bottom hw-title">
				<div class="weui-cell">
					<div class="weui-cell__bd">
						<input id="title" name="title" type="text" placeholder="信息名称" maxlength="30" @if(isset($message)) value="{{ $message->title }}" @else value="" @endif  class="weui-input fs18 one-line title">
					</div>
				</div>
			</div>
			
			<div class="msg-send-conwrap msg-send-bg">
				<div contenteditable="true" id="emojiInput" class="wangEditor-mobile-txt">@if(isset($message)) {!! $message->content !!}@endif</div>
			</div>
			<div class="msg-send-conicon msg-send-bg b-top">
				<ul class="weui-flex">
					<li class="weui-flex__item addImg">
						<i class="icon iconfont icon-tupian placeholder fs15 c-999"></i>
						 <input id="uploaderInput" class="weui-uploader__input js_file" type="file" accept="image/*" multiple="multiple">
                    </li>
				</ul>
			</div>
			<div class="weui-cell weui-cell_switch b-top weui-cells_form mt5px msg-send-bg">
				<div class="weui-cell__bd">定时发送</div> 
				<div class="weui-cell__ft">
					<input type="checkbox" title="开启评论" name="openCom" class="weui-switch"></div>
			</div>
			
			<div class="hw-time b-top" style="display: none;">
				<div class="weui-cell msg-send-bg">
					<div class="weui-cell__hd">
						<label for="" class="weui-label">发送日期</label>
					</div> 
					<div class="weui-cell__bd">
						<input id="time" name="time" readonly="readonly" type="text" placeholder="请选择日期" class="weui-input ma_expect_date" data-toggle='datetime-picker'>
					</div>
				</div>
			</div>
			
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
			                <li data-id="0" class="js-choose-breadcrumb-li active"><a>全部</a></li>
			            </ol>
			        </div>
					
					<div class="choose-items js-choose-items">
						<div class="weui-cells weui-cells_checkbox" style="padding-bottom: 60px;">
					      	<label class="weui-cell weui-check__label" id="item-1" data-item="1" data-uid="1">
					        	<div class="weui-cell__hd">
					          		<input type="checkbox" class="weui-check choose-item-btn" name="checkbox" >
					          	<i class="weui-icon-checked"></i>
					        	</div>
					        	<div class="weui-cell__bd">
					          		<img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjjG5UGo8OES72Sw7U1CJYHXEkg1UlGkono5lDEiaZeBFlw/64" class="js-go-detail lazy" width="75" height="75">
					          		<span class="contacts-text">王小飞</span>
					        	</div>
					     	</label>
					     	<label class="weui-cell weui-check__label" id="item-2" data-item="2" data-uid="2">
					        	<div class="weui-cell__hd">
					          		<input type="checkbox" class="weui-check choose-item-btn" name="checkbox" >
					          	<i class="weui-icon-checked"></i>
					        	</div>
					        	<div class="weui-cell__bd">
					          		<img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjgYesvoOibygyRfgukxHDouo6ovRRicAKOphkKd0Licg3I2w/64" class="js-go-detail lazy" width="75" height="75">
					          		<span class="contacts-text">于娜</span>
					        	</div>
					     	</label>
					     	<label class="weui-cell weui-check__label" id="item-3" data-item="3" data-uid="3">
					        	<div class="weui-cell__hd">
					          		<input type="checkbox" class="weui-check choose-item-btn" name="checkbox" >
					          	<i class="weui-icon-checked"></i>
					        	</div>
					        	<div class="weui-cell__bd">
					          		<img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjjG5UGo8OES72Sw7U1CJYHXEkg1UlGkono5lDEiaZeBFlw/64" class="js-go-detail lazy" width="75" height="75">
					          		<span class="contacts-text">王小飞</span>
					        	</div>
					     	</label>
					     	<label class="weui-cell weui-check__label" id="item-4" data-item="4" data-uid="4">
					        	<div class="weui-cell__hd">
					          		<input type="checkbox" class="weui-check choose-item-btn" name="checkbox" >
					          	<i class="weui-icon-checked"></i>
					        	</div>
					        	<div class="weui-cell__bd">
					          		<img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjgYesvoOibygyRfgukxHDouo6ovRRicAKOphkKd0Licg3I2w/64" class="js-go-detail lazy" width="75" height="75">
					          		<span class="contacts-text">于娜</span>
					        	</div>
					     	</label>
					     	<label class="weui-cell weui-check__label" id="item-5" data-item="5" data-uid="5">
					        	<div class="weui-cell__hd">
					          		<input type="checkbox" class="weui-check choose-item-btn" name="checkbox" >
					          	<i class="weui-icon-checked"></i>
					        	</div>
					        	<div class="weui-cell__bd">
					          		<img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjjG5UGo8OES72Sw7U1CJYHXEkg1UlGkono5lDEiaZeBFlw/64" class="js-go-detail lazy" width="75" height="75">
					          		<span class="contacts-text">王小飞</span>
					        	</div>
					     	</label>
					     	<label class="weui-cell weui-check__label" id="item-6" data-item="6" data-uid="6">
					        	<div class="weui-cell__hd">
					          		<input type="checkbox" class="weui-check choose-item-btn" name="checkbox" >
					          	<i class="weui-icon-checked"></i>
					        	</div>
					        	<div class="weui-cell__bd">
					          		<img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjjG5UGo8OES72Sw7U1CJYHXEkg1UlGkono5lDEiaZeBFlw/64" class="js-go-detail lazy" width="75" height="75">
					          		<span class="contacts-text">王小飞</span>
					        	</div>
					     	</label>
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
					已选0名用户
					</span>
			    </div>
			</div>
      	</div>
    </div>
@endsection
@section('script')
    <script src="{{asset('/js/wechat/message_center/create.js')}}"></script>
@endsection