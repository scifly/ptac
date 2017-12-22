@extends('wechat.layouts.master')
@section('css')
	<link rel="stylesheet" href="{{ asset('/css/wechat/message_center/index.css') }}">
@endsection
@section('content')
		<div class="content home">
			<div class="multi-role">
				<div class="switchschool-item clearfix">
					<div class="switchschool-head"> 
						<div class="title-name" > 消息中心 </div>
						<span class="addworkicon"> 
							<a class="icon iconfont icon-add c-green" href="../public/message_create"></a>
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
@endsection
@section('search')
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
@endsection
@section('script')
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
			window.location.href = '../public/message_show';
		});	
//		$('#searchCancel').click(function(){
//			
//		});
	</script>
@endsection
