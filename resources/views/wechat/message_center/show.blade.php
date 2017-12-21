<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>WeUI</title>
	<link rel="stylesheet" href="{{ asset('/css/weui.min.css') }}">
	<link rel="stylesheet" href="{{ asset('/css/jquery-weui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/ionicons.min.css') }}">
    <style>
body, html {
    height: 100%;
    width: 100%;
    overflow-x: hidden;
}
body{
	margin: 0;
	padding:0;
	background-color: #fff;
	font-family: "微软雅黑";
}
.weui_tab {
    position: relative;
    height: 100%;
}
div.weui_tab_bd {
    padding-bottom: 50px;
}
.multi-role {
    background: #fff;
    position: relative;
}
.multi-role .switchschool-item {
    padding: 5px 10px;
    padding-right: 0;
    display: -webkit-box;
    line-height: 30px;
    border-bottom: 5px solid #f8f8f8;
    text-align: center;
}
.multi-role .switchschool-item .switchschool-title{
	-webkit-box-flex: 1;position: relative;
} 
.title-name{
	font-size: 16px; 
	color: #686868; 
	width: 100%;
	display: inline-block;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis; 
	float: left;
}
.switchschool-head{
	width: 100%;
}
.switchschool-head .addworkicon{
	position: absolute;
	right: 12px;
	width:40px;
}
.tea-head {
    width: 100%;
    height: 38px;
    position: relative;
    z-index: 10;
}
.tea-select-list-icon {
    position: absolute;
    z-index: 101;
    right: 12px;
    height: 38px;
    line-height: 38px;
    
}
.switchschool-title .addworkicon a, .tea-select-list-icon .searchicon a{
    display: inline-block;
    font-size:18px;
    width: 40px;
    text-align: center;
}
.selectlist-layout {
    font-size:16px;
    position: relative;
    z-index: 100;
}
.selectlist-box {
    position: relative;
    z-index: 20;
}
.selectlist-box .select-box {
    display: inline-block;
    width: 100%;
    line-height: 40px;
    background-color: #fff;
    text-align: center;
    z-index: 6;
}
.select-ul {
    background: #fff;
    position: absolute;
    text-align: center;
    width: 100%;
    z-index: 100;
    top: 38px;
}
.select-ul li {
    height: 41px;
    line-height:41px;
    position: relative;
}
.select-ul li:after {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    border-top: 1px solid #ececec;
    width: 100%;
    height: 1px;
    -webkit-transform: scaleY(0.5);
    transform: scaleY(0.5);
    -webkit-transform-origin: 0 0;
    transform-origin: 0 0;
    z-index: 0;
}
.b-bottom:after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    border-bottom: 1px solid #ececec;
    width: 100%;
    height: 1px;
    -webkit-transform: scaleY(0.5);
    transform: scaleY(0.5);
    -webkit-transform-origin: 0 0;
    transform-origin: 0 0;
    z-index: 0;
}
.select-container {
    position: fixed;
    top: 88px;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 99;
    background: rgba(0,0,0,.5);
}
.c-green {
    color: #1aad19;
}
#search .weui-popup__modal{
	background: #fff;
}
.weui-navbar{
	z-index: 9;
	background-color: #fff;
}
.weui-navbar__item{
	padding:7px 10px;
	border: none;
}
.weui-navbar__item:after{
	border-right:0;
}
.weui-navbar:after{
	border-bottom:0 ;
}
.weui-navbar + .weui-tab__bd {
    padding-top: 40px;
}
.weui-navbar__item.weui-bar__item--on{
	background-color: #fff;
	color:#1aad19;
}

.teacher-list-box, .teacher-work-box {
    width: 100%;
    position: relative;
    z-index: 0;
}
.teacher-work-box .titleinfo {
    height:91px;
    position: relative;
}
.titleinfo .titleinfo-head-left {
    max-width: 75%;
    height:65px;
    float: left;
}
.titleinfo .worktime {
    /*width: 20%;*/
    height:100%;
    text-align: right;
    margin-top: 15px;
    margin-right: 14px;
    font-size: 12px;
    color: #999;
    position: absolute;
    right: 0;
    z-index: 0;
}
.worktime .info-status {
	position: absolute;bottom: 30px;right: 0;pointer-events: none;padding: 0 10px;
}
.worktime .info-status.green {
	color: green;
}
.worktime .info-status.red {
	color: red;
}
.titleinfo .titleinfo-head-left .title, .titleinfo .titleinfo-head-left .title-info {
    vertical-align: top;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}
.titleinfo .titleinfo-head-left .title {
    font-size: 16px;
    line-height:17px;
    margin-top: 17px;
}
.ml12 {
    margin-left:14px;
}
.titleinfo .titleinfo-head-left .title-info {
    font-size: 15px;
    line-height: 16px;
    color: #999;
    margin-top: 11px;
    position: absolute;
    bottom: 15px;
}
.line {
    width: 100%;
    height: 10px;
    background: #f8f8f8;
}
.line:first-child {
    width: 0;
    height: 0;
}
    </style>
</head>
<body ontouchstart>
	<div style="height: 100%;" id="app">
		<div class="content home">
			<div class="multi-role">
				<div class="switchschool-item clearfix">
					<div class="switchschool-head"> 
						<div class="title-name" > 消息中心 </div>
						<span class="addworkicon"> 
							<a class="icon iconfont icon-add c-green" href="add.html"></a> 
						</span> 
					</div>
				</div>
				<div class="weui-tab">
					<div class="weui-navbar">
				        <a class="weui-navbar__item weui-bar__item--on" href="#tab1">
				          	已发送
				        </a>
				        <a class="weui-navbar__item" href="#tab2">
				         	 已接收<span style="display: inline-block;height: 18px;line-height:18px;font-weight:700;margin-left:10px;width: 20px;border-radius: 50%;background-color:red !important;color: #fff;">1</span>
				        </a>
			      	</div>
			      	<div class="weui-tab__bd ">
			      		
			      		<div id="tab1" class="weui-tab__bd-item weui-tab__bd-item--active">
				      		<div class="tea-head">
								<span class="tea-select-list-icon"> 
									<span class="searchicon"> 
										<a class="icon iconfont icon-search3 c-green open-popup" href="javascript:" data-target="#search"></a>
									</span> 
									
								</span>
								
								<div class="selectlist-layout">
									<div class="selectlist-box">
										<span class="select-box c-green b-bottom">作业 <i class="icon iconfont icon-arrLeft-fill"></i> </span>
									</div>
								</div>
								<ul class="select-ul" style="display: none;"> 
									<li class="c-green"> 作业 </li>
									<li class=""> 草稿 </li>
								</ul>
								<div class="select-container" style="display: none;"></div>
							</div>
							
							<div class="list-layout">
								<div class="line"></div>
								<div class="teacher-list-box glayline">
									<div class="teacher-work-box">
										<a class="teacher-work-head" style="color:#000" href="javascript:"> 
											<div class="titleinfo"> 
												<div class="titleinfo-head"> 
													<div class="titleinfo-head-left fl">  
														<div class="title ml12">修图作业</div> 
														<div class="title-info ml12">学生 - c2016级3班等26人</div> 
													</div> 
													<span class="worktime">
														2017-12-18 16:36
														<span class="info-status green">已发送</span>
													</span> 
												</div> 
											</div> 
										</a>
									</div>
								</div>
								
								<div class="line"></div>
								<div class="teacher-list-box glayline">
									<div class="teacher-work-box">
										<a class="teacher-work-head" style="color:#000" href="javascript:"> 
											<div class="titleinfo"> 
												<div class="titleinfo-head"> 
													<div class="titleinfo-head-left fl">  
														<div class="title ml12">修图作业</div> 
														<div class="title-info ml12">学生 - c2016级3班等26人</div> 
													</div> 
													<span class="worktime">
														2017-12-18 16:36
														<span class="info-status green">已发送</span>
													</span> 
												</div> 
											</div> 
										</a>
									</div>
								</div>
								
								<div class="line"></div>
								<div class="teacher-list-box glayline">
									<div class="teacher-work-box">
										<a class="teacher-work-head" style="color:#000" href="javascript:"> 
											<div class="titleinfo"> 
												<div class="titleinfo-head"> 
													<div class="titleinfo-head-left fl">  
														<div class="title ml12">修图作业</div> 
														<div class="title-info ml12">学生 - c2016级3班等26人</div> 
													</div> 
													<span class="worktime">
														2017-12-18 16:36
														<span class="info-status red">未发送</span>
													</span> 
												</div> 
											</div> 
										</a>
									</div>
								</div>
								<div class="line"></div>
								<div class="weui-loadmore weui-loadmore_line">
								  	<span class="weui-loadmore__tips">暂无数据</span>
								</div>
							</div>
				      	</div>
				      	<div id="tab2" class="weui-tab__bd-item">
				      		<div class="tea-head">
								<span class="tea-select-list-icon"> 
									<span class="searchicon"> 
										<a class="icon iconfont icon-search3 c-green open-popup" href="javascript:;" data-target="#search"></a> 
									</span> 
								</span>
								<div class="selectlist-layout">
									<div class="selectlist-box">
										<span class="select-box c-green b-bottom">作业 <i class="icon iconfont icon-arrLeft-fill"></i> </span>
									</div>
								</div>
								<ul class="select-ul" style="display: none;"> 
									<li class="c-green"> 作业 </li>
									<li class=""> 草稿 </li>
								</ul>
								<div class="select-container" style="display: none;"></div>
							</div>
							<div class="list-layout">
								<div class="line"></div>
								<div class="teacher-list-box glayline">
									<div class="teacher-work-box">
										<a class="teacher-work-head" style="color:#000" href="javascript:"> 
											<div class="titleinfo"> 
												<div class="titleinfo-head"> 
													<div class="titleinfo-head-left fl">  
														<div class="title ml12">修图作业</div> 
														<div class="title-info ml12">学生 - c2016级3班等26人</div> 
													</div> 
													<span class="worktime">2017-12-18 16:36</span> 
												</div> 
											</div> 
										</a>
									</div>
								</div>
								<div class="line"></div>
								<div class="teacher-list-box glayline">
									<div class="teacher-work-box">
										<a class="teacher-work-head" style="color:#000" href="javascript:"> 
											<div class="titleinfo"> 
												<div class="titleinfo-head"> 
													<div class="titleinfo-head-left fl">  
														<div class="title ml12">修图作业</div> 
														<div class="title-info ml12">学生 - c2016级3班等26人</div> 
													</div> 
													<span class="worktime">2017-12-18 16:36</span> 
												</div> 
											</div> 
										</a>
									</div>
								</div>
								<div class="line"></div>
								<div class="teacher-list-box glayline">
									<div class="teacher-work-box">
										<a class="teacher-work-head" style="color:#000" href="javascript:"> 
											<div class="titleinfo"> 
												<div class="titleinfo-head"> 
													<div class="titleinfo-head-left fl">  
														<div class="title ml12">修图作业</div> 
														<div class="title-info ml12">学生 - c2016级3班等26人</div> 
													</div> 
													<span class="worktime">2017-12-18 16:36</span> 
												</div> 
											</div> 
										</a>
									</div>
								</div>
								<div class="line"></div>
								<div class="weui-loadmore weui-loadmore_line">
								  	<span class="weui-loadmore__tips">暂无数据</span>
								</div>
								
							</div>
							
						</div>
				    </div>
				</div>
			</div>
		</div>
	</div>
	<div id="search" class='weui-popup__container'>
    	<div class="weui-popup__overlay"></div>
    	<div class="weui-popup__modal">
			<div class="weui-search-bar weui-search-bar_focusing" id="searchBar">
	      		<form class="weui-search-bar__form" action="#">
			        <div class="weui-search-bar__box">
			          <i class="weui-icon-search"></i>
			          <input type="search" class="weui-search-bar__input" id="searchInput" placeholder="请输入搜索内容" required="">
			          <a href="javascript:" class="weui-icon-clear" id="searchClear"></a>
			        </div>
	      		</form>
	      		<a href="javascript:" class="weui-search-bar__cancel-btn close-popup" id="searchCancel" style="display: block;">取消</a>
	    	</div>
      	</div>
    </div>
	
    <script src="{{ asset('/js/jquery.min.js') }}"></script>
	<script src="{{ asset('/js/fastclick.js') }}"></script>
	<script>
		$(function() {
			FastClick.attach(document.body);
		});
	</script>
	<script src="{{ asset('/js/jquery-weui.min.js') }}"></script>
	<script>
		$('.selectlist-layout').click(function(){
			$('.select-container').toggle();
			$('.select-ul').slideToggle('fast');
		});
		
		$('.select-ul li').click(function(){
			$('.select-container').toggle();
			$('.select-ul').slideToggle('fast');
			$('.select-ul li').removeClass('c-green');
			$(this).addClass('c-green');
			var html = ''+($(this).text())+ '<i class="icon iconfont icon-arrLeft-fill"></i>';
			$('.select-box ').html(html);
		});
		
		$('.teacher-list-box').click(function(){
			console.log(1);
			window.location.href = 'content.html';
		});	
//		$('#searchCancel').click(function(){
//			
//		});
	</script>
</body>
</html>
