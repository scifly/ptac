<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>WeUI</title>
	<link rel="stylesheet" href="{{ URL::asset('css/weui.min.css') }}"/>
	<link rel="stylesheet" href="{{ URL::asset('css/jquery-weui.min.css') }}">
	<link rel="stylesheet" href="{{ URL::asset('css/wechat/icon/iconfont.css') }}">
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
a{
	color: #333;
}
.main{
	height: 100%;
	width: 100%;
	background-color: #fff;
}
.multi-role {
    background: #fff;
    position: relative;
}
.multi-role .switchclass-item {
    padding: 0px;
    padding-right: 0;
    display: -webkit-box;
    line-height: 30px;
    border-bottom: 5px solid #f8f8f8;
    text-align: center;
}
.multi-role .switchclass-item .switchclass-title{
	-webkit-box-flex: 1;position: relative;
} 
.title-name{
	font-size: 18px; 
	color: #686868; 
	width: 100%;
	display: inline-block;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis; 
	float: left;
}
.switchclass-head{
	width: 100%;
}


.kaoqin-tongji {
    width: 100%;
    margin-left:5%;
    /*text-align: center;*/
}
.kaoqin-tongji td {
    display: inline-block;
    width: 32%;
    height: 48px;
    line-height: 48px;
    font-size: 14px;
}
.kaoqin-date-circle {
    float: left;
    margin-top: 2px;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background-color: #ccc;
    margin-top: 17px;
    margin-right: 2px;
}
.okstatus {
    background-color: #83db74;
}
.notstatus {
    background-color: #fdde52;
}
.reststatus {
    background-color: #fc7f4e;
}

.list{
	padding: 15px;
}
.list-item{
	width: 100%;
	border:1px solid #ddd;
	min-height: 120px;
	background-color: #fff;
	border-radius: 5px;
	position: relative;
	color: #333;
	margin-bottom: 15px;
}
.list-item .username,.list-item .parent,.list-item .mobile,.list-item .otherinfo{
	padding:0px 20px;
	font-size: 15px;
}
.list-item .username{
	font-size: 18px;
	margin-top: 8px;
	/*padding: 20px;*/
}
.list-item .parent,.list-item .mobile,.list-item .otherinfo{
	color: #777;
	margin-top: 2px;
}

	</style>
<head>
<body ontouchstart>
	
	<div class="multi-role">
		<div class="switchclass-item clearfix">
			<div class="switchclass-head"> 
				
				<div class="weui-cell">
			        <div class="weui-cell__bd title-name">
			          	<input style="text-align: center;" id="classlist" class="weui-input" type="text" value="一年级1班" readonly="" data-values="一年级1班">
			        </div>
			    </div>
				
				<!--<input class="title-name" id="classlist" type="text" value="一年级一班" readonly="" data-values="一年级一班">-->
			</div>
		</div>
		<div id="main" style="width: 100%;height: 300px;"></div>
		
		<table class="kaoqin-tongji">
			<tr>
				<td>
					<a href="javascript:;" class="open-popup" data-target="#studentlist">
						<div class="kaoqin-date-circle okstatus"></div>
						<span class="pl10">正常:</span>
						<span>14</span>
					</a>
					
				</td>
		
				<td>
					<a href="javascript:;" class="open-popup" data-target="#studentlist">
						<div class="kaoqin-date-circle notstatus"></div>
						<span class="pl10">异常:</span>
						<span>0</span>
					</a>
				</td>
		
				<td>
					<a href="javascript:;" class="open-popup" data-target="#studentlist">
						<div class="kaoqin-date-circle reststatus"></div>
						<span class="pl10">请假:</span>
						<span>0</span>
					</a>
					
				</td>
			</tr>
		</table>
	</div>
	
	<div id="studentlist" class="weui-popup__container">
	  	<div class="weui-popup__overlay"></div>
	  	<div class="weui-popup__modal">
	        <div class="toolbar">
	          	<div class="toolbar-inner">
	            	<a href="javascript:;" class="picker-button close-popup">关闭</a>
	            	<h1 class="title">学生列表</h1>
	          	</div>
	        </div>
	        <div class="modal-content">
	        	<div class="list">
		          	<div class="list-item">
						<div class="list-item-info">
							<div class="username">姓名 : <span>张三</span></div>
							<div class="parent">监护人 : <span>张三他爸</span></div>
							<div class="mobile">手机 : <span>13111111111</span></div>
							<div class="otherinfo">其他信息（打卡时间、请假理由等）</div>
						</div>
					</div>
					
					<div class="list-item">
						<div class="list-item-info">
							<div class="username">姓名 : <span>张三</span></div>
							<div class="parent">监护人 : <span>张三他爸</span></div>
							<div class="mobile">手机 : <span>13111111111</span></div>
							<div class="otherinfo">其他信息（打卡时间、请假理由等）</div>
						</div>
					</div>
					
					<div class="list-item">
						<div class="list-item-info">
							<div class="username">姓名 : <span>张三</span></div>
							<div class="parent">监护人 : <span>张三他爸</span></div>
							<div class="mobile">手机 : <span>13111111111</span></div>
							<div class="otherinfo">其他信息（打卡时间、请假理由等）</div>
						</div>
					</div>
					
					<div class="list-item">
						<div class="list-item-info">
							<div class="username">姓名 : <span>张三</span></div>
							<div class="parent">监护人 : <span>张三他爸</span></div>
							<div class="mobile">手机 : <span>13111111111</span></div>
							<div class="otherinfo">其他信息（打卡时间、请假理由等）</div>
						</div>
					</div>
				</div>
	        </div>
      	</div>
	</div>
	<script src="{{ asset('/js/jquery.min.js') }}"></script>
	<script src="{{ asset('/js/fastclick.js') }}"></script>
	<script src="{{ asset('/js/jquery-weui.min.js') }}"></script>
	<script src="{{ asset('/js/plugins/echarts/echarts.common.min.js') }}"></script>
	<script>
        $(function() {
            FastClick.attach(document.body);
        });
	</script>
	<script src="{{ asset('/js/wechat/attendance_center/edu_index.js') }}"></script>
</body>
</html>
