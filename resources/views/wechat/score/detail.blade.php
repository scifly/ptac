<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>WeUI</title>
	<link rel="stylesheet" href="../lib/weui.min.css">
	<link rel="stylesheet" href="../css/jquery-weui.css">
    <link rel="stylesheet" href="../lib/icon/iconfont.css">
    <style>
body, html {
    height: 100%;
    width: 100%;
    overflow-x: hidden;
}
body{
	margin: 0;
	padding:0;
	background-color: #f2f2f2;
	font-family: "微软雅黑";
}
a{
	color: #333;
}
::-webkit-scrollbar {
width: 0em;
}
::-webkit-scrollbar:horizontal {
height: 0em;
}
.header{height: 90px;width: 90%;margin: 0;padding:0 5%;background-color: #1aad19;}
.header .title{height: 60px;line-height: 60px;text-align: center;font-size: 24px;color: #fff;}
.header .myclass{height: 30px;width: 100%;text-align: center;color:#fff;}

.footerTab{
	position: fixed;
	bottom: 0;
	height: 50px;
	width: 100%;
	background-color: #fff;
	opacity: 0.7;
	border-top: 1px solid #ddd;
}
.footerTab .btnItem{
	display: inline-block;
	width: 50%;
	float: left;
	height: 100%;
	text-align: center;
	position: relative;
}
.footerTab .btnItem i{
	font-size: 24px;
	margin-top: -3px;
}
.footerTab .btnItem p{
	font-size: 14px;
	top: 28px;
	position: absolute;
	width: 100%;
}
.footer-active {
    color: #1aad19;
}
#main{
	width: 100%;
	height: 360px;
}

.main{width: 96 %;margin:10px 2%;}
.tongji-table{width: 100%;font-size: 14px;text-align: center;}
.tongji-table thead tr{color: #fff;background-color:#1aad19;width: 100%;}
.tongji-table tbody tr{background-color: #fff;}
.tongji-table td{word-wrap:break-word;word-break:break-all;}
.tongji-table tbody td{border-bottom: 5px solid #F2F2F2;}
.tongji-table td div{width: 100%;}
.tongji-table td div span{margin:2px 4px;}
.tongji-table td div .subj{float: left;}
.tongji-table td div .score{float: right;}
	</style>
<head>
<body ontouchstart>
	<div class="header">
		<div class="title">
			这是一个标题
		</div>
		
		<div class="myclass">
			一年级一班
		</div>
	</div>
	<div class="weui-search-bar" id="searchBar">
      	<form class="weui-search-bar__form" action="#">
        	<div class="weui-search-bar__box">
	          	<i class="weui-icon-search"></i>
	          	<input type="search" class="weui-search-bar__input" id="searchInput" placeholder="搜索" required="">
	          	<a href="javascript:" class="weui-icon-clear" id="searchClear"></a>
	        </div>
	        <label class="weui-search-bar__label" id="searchText" style="transform-origin: 0px 0px 0px; opacity: 1; transform: scale(1, 1);">
	         	 <i class="weui-icon-search"></i>
	         	 <span>搜索</span>
	        </label>
      	</form>
      	<a href="javascript:" class="weui-search-bar__cancel-btn" id="searchCancel">取消</a>
    </div>
    
	<div class="main">
		<table class="tongji-table" style="width: 100%;" cellspacing="0">
			 <thead>
	            <tr>
	                <td width="40">姓名</td>
	                <td width="40">学号</td>
	                <td width="40">班排</td>
	                <td width="40">年排</td>
	                <td width="40">总分</td>
	                <td width="80">成绩详情</td>
	            </tr>
            </thead>
	            	
            <tbody>
            	<tr class="tongji-item">
            		<td>张三</td>
            		<td>18780011039</td>
            		<td>1</td>
            		<td>1</td>
            		<td>100.00</td>
            		<td>
            			<div>
            				<span class="subj">语文:</span> 
            				<span class="score">85.0</span>
            				<div style="clear: both;"></div>
            			</div>
            			<div>
            				<span class="subj">数学:</span> 
            				<span class="score">85.0</span>
            				<div style="clear: both;"></div>
            			</div>
            			<div>
            				<span class="subj">英语:</span>
            				<span class="score">85.0</span>
            				<div style="clear: both;"></div>
            			</div>
            			<div>
            				<span class="subj">体育:</span>
            				<span class="score">85.0</span>
            				<div style="clear: both;"></div>
            			</div>
            		</td>
            	</tr>
            	<tr class="tongji-item">
            		<td>张三</td>
            		<td>18780011039</td>
            		<td>1</td>
            		<td>1</td>
            		<td>100.00</td>
            		<td>
            			<div>
            				<span class="subj">语文:</span> 
            				<span class="score">85.0</span>
            				<div style="clear: both;"></div>
            			</div>
            			<div>
            				<span class="subj">数学:</span> 
            				<span class="score">85.0</span>
            				<div style="clear: both;"></div>
            			</div>
            			<div>
            				<span class="subj">英语:</span>
            				<span class="score">85.0</span>
            				<div style="clear: both;"></div>
            			</div>
            			<div>
            				<span class="subj">体育:</span>
            				<span class="score">85.0</span>
            				<div style="clear: both;"></div>
            			</div>
            		</td>
            	</tr>
            </tbody>
		</table>
	</div>
	<div style="height: 70px;width: 100%;"></div>
	
	<div class="footerTab" >
		<a class="btnItem footer-active">
			<i class="icon iconfont icon-document"></i>
			<p>详情</p>
		</a>
		<a class="btnItem" href="count.html">
			<i class="icon iconfont icon-renzheng7"></i>
			<p>统计</p>
		</a>
		<div style="clear: both;"></div>
	</div>
	
	
    <script src="../lib/jquery-2.1.4.js"></script>
	<script src="../lib/fastclick.js"></script>
	<script>
		$(function() {
			FastClick.attach(document.body);
		});
	</script>
	<script src="../js/jquery-weui.js"></script>
	<script src="../lib/echarts.common.min.js"></script>
	<script>
		$('.tongji-item').click(function(){
			window.location.href = 'student_score.html';
		});
		
	</script>
</body>
</html>
