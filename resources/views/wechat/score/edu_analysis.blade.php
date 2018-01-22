@extends('wechat.layouts.master')
@section('css')
	<link rel="stylesheet" href="{{ asset('css/wechat/score/analysis.css') }}">
@endsection
@section('content')
	<div class="header">
		<div class="title">
			这是一个标题
		</div>
		<div class="time">
			2018-01-11
		</div>
	</div>
	<div class="main" style="width: 92%;padding: 0 4%;">

		 <div class="subjectItem" id="yuwen">
			<div class="subj-title">
				语文
			</div>
			<div class="subj-tab">
				<a class="tab-item cur" data-type="score">分数统计</a>
				<a class="tab-item" data-type="score-level">分数段统计</a>
				<a class="tab-item" data-type="table">图表统计</a>
			</div>
			<div class="subj-main">
				<div class="show-item score cur">
					<div class="table-title">语文分数统计详情</div>
					<table class="table-count">
						<tr>
							<td class="subtit" width="">统计人数</td>
							<td>5</td>
						</tr>
						<tr>
							<td class="subtit">最高分</td>
							<td>98.00</td>
						</tr>
						<tr>
							<td class="subtit">最低分</td>
							<td>98.00</td>
						</tr>
						<tr>
							<td class="subtit">平均分</td>
							<td>98.00</td>
						</tr>
						<tr>
							<td class="subtit">平均分以上人数</td>
							<td>5</td>
						</tr>
						<tr>
							<td class="subtit">平均分以下人数</td>
							<td>0</td>
						</tr>
					</table>
				</div>
				
				<div class="show-item score-level">
					<div class="table-title">语文分数统计详情</div>
					<table class="table-count">
						<tr>
							<td class="subtit">统计人数</td>
							<td>5</td>
						</tr>
						<tr>
							<td class="subtit">135~150分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">120~135分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">105~120分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">90~105分</td>
							<td>5</td>
						</tr>
						<tr>
							<td class="subtit">80~90分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">70~80分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">60~70分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">60分以下</td>
							<td>0</td>
						</tr>
					</table>
				</div>
				<div class="show-item table">
					<div id="main"></div>
				</div>
			</div>
		</div>
		
		<div class="subjectItem" id="shuxue">
			<div class="subj-title">
				数学
			</div>
			<div class="subj-tab">
				<a class="tab-item cur" data-type="score">分数统计</a>
				<a class="tab-item" data-type="score-level">分数段统计</a>
				<a class="tab-item" data-type="table">图表统计</a>
			</div>
			
			<div class="subj-main">
				<div class="show-item score cur">
					<div class="table-title">数学分数统计详情</div>
					<table class="table-count">
						<tr>
							<td class="subtit" width="">统计人数</td>
							<td>5</td>
						</tr>
						<tr>
							<td class="subtit">最高分</td>
							<td>98.00</td>
						</tr>
						<tr>
							<td class="subtit">最低分</td>
							<td>98.00</td>
						</tr>
						<tr>
							<td class="subtit">平均分</td>
							<td>98.00</td>
						</tr>
						<tr>
							<td class="subtit">平均分以上人数</td>
							<td>5</td>
						</tr>
						<tr>
							<td class="subtit">平均分以下人数</td>
							<td>0</td>
						</tr>
					</table>
				</div>
				
				<div class="show-item score-level">
					<div class="table-title">数学分数统计详情</div>
					<table class="table-count">
						<tr>
							<td class="subtit">统计人数</td>
							<td>5</td>
						</tr>
						<tr>
							<td class="subtit">135~150分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">120~135分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">105~120分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">90~105分</td>
							<td>5</td>
						</tr>
						<tr>
							<td class="subtit">80~90分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">70~80分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">60~70分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">60分以下</td>
							<td>0</td>
						</tr>
					</table>
				</div>
				
				<div class="show-item table">
					<div id="main"></div>
				</div>
				
			</div>
			
		</div>
		
		<div class="subjectItem" id="yingyu">
			<div class="subj-title">
				英语
			</div>
			<div class="subj-tab">
				<a class="tab-item cur" data-type="score">分数统计</a>
				<a class="tab-item" data-type="score-level">分数段统计</a>
				<a class="tab-item" data-type="table">图表统计</a>
			</div>
			
			<div class="subj-main">
				<div class="show-item score cur">
					<div class="table-title">英语分数统计详情</div>
					<table class="table-count">
						<tr>
							<td class="subtit" width="">统计人数</td>
							<td>5</td>
						</tr>
						<tr>
							<td class="subtit">最高分</td>
							<td>98.00</td>
						</tr>
						<tr>
							<td class="subtit">最低分</td>
							<td>98.00</td>
						</tr>
						<tr>
							<td class="subtit">平均分</td>
							<td>98.00</td>
						</tr>
						<tr>
							<td class="subtit">平均分以上人数</td>
							<td>5</td>
						</tr>
						<tr>
							<td class="subtit">平均分以下人数</td>
							<td>0</td>
						</tr>
					</table>
				</div>
				
				<div class="show-item score-level">
					<div class="table-title">英语分数统计详情</div>
					<table class="table-count">
						<tr>
							<td class="subtit">统计人数</td>
							<td>5</td>
						</tr>
						<tr>
							<td class="subtit">135~150分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">120~135分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">105~120分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">90~105分</td>
							<td>5</td>
						</tr>
						<tr>
							<td class="subtit">80~90分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">70~80分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">60~70分</td>
							<td>0</td>
						</tr>
						<tr>
							<td class="subtit">60分以下</td>
							<td>0</td>
						</tr>
					</table>
				</div>
				
				<div class="show-item table">
					<div id="main"></div>
				</div>
				
			</div>
			
		</div>
		
	</div>
	
	<div style="height: 70px;width: 100%;"></div>
	
	<div class="anchor-point">
		<ul>
			<li><a href="#yuwen">语文</a></li>
			<li><a href="#shuxue">数学</a></li>
			<li><a href="#yingyu">英语</a></li>
		</ul>
	</div>
	
	<div class="footerTab" >
		<a class="btnItem" href="detail.html">
			<i class="icon iconfont icon-document"></i>
			<p>详情</p>
		</a>
		<a class="btnItem footer-active">
			<i class="icon iconfont icon-renzheng7"></i>
			<p>统计</p>
		</a>
		<div style="clear: both;"></div>
	</div>
	@endsection
@section('script')
<script src="{{ asset('js/wechat/score/analysis.js') }}"></script>
@endsection
