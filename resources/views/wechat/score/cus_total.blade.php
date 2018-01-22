@extends('wechat.layouts.master')
@section('title')
	<title>成绩中心</title>
@endsection
@section('css')
	<link rel="stylesheet" href="{{ asset('css/wechat/score/cus_total.css') }}">
@endsection
@section('content')
	<div class="header">
		<div class="info">
			<div class="time">
				<div class="subtitle">{{ date('Y-m', strtotime($examDate)) }}</div>
				<div class="days">{{ date('d',strtotime($examDate)) }}日</div>
			</div>
			
			<div class="test">
				<div class="subtitle">考试名</div>
				<div class="testName">
					{{ $examName }}
				</div>
			</div>
		</div>
		<div class="score">
			{{ $data['total']['total_score'] }}分
		</div>
	</div>
	
	<div class="otherinfo">
		<div class="average">
			<div class="byclass">
				<p>{{ $data['total']['avgcla'] }}</p>
				<p class="subtitle">班平均</p>
			</div>
			<div class="byschool">
				<p>{{ $data['total']['avggra'] }}</p>
				<p class="subtitle">年平均</p>
			</div>
		</div>
		<div class="ranke">
			<div class="byclass">
				<p>{{ $data['total']['class_rank'] }}/{{ $data['total']['class_count'] }}</p>
				<p class="subtitle">班排名</p>
			</div>
			<div class="byschool">
				<p>{{ $data['total']['grade_rank'] }}/{{ $data['total']['grade_count'] }}</p>
				<p class="subtitle">年排名</p>
			</div>
		</div>
		
	</div>
	
	<div class="tablemain">
		<div id="main">
			
		</div>
	</div>
	
	<div class="scorelist">
		@foreach($data['single'] as $single)
		<div class="scoreItem">
			<div class="title">{{ $single['sub'] }}</div>
			<div class="myscore"><span class="subtitle">得分</span><span class="scoredata">{{ $single['score'] }}</span></div>
			<div class="avescore"><span class="subtitle">均分</span>{{ $single['avg'] }}</div>
		</div>
		@endforeach
	</div>
	
	<div style="height: 70px;width: 100%;"></div>
	<div class="footerTab" >
		<a class="btnItem " href="subjectItem.html">
			<i class="icon iconfont icon-document"></i>
			<p>单科</p>
		</a>
		<a class="btnItem footer-active" href="allsubject.html">
			<i class="icon iconfont icon-renzheng7"></i>
			<p>综合</p>
		</a>
	</div>
@endsection
@section('script')
<script src="{{ asset('js/wechat/score/cus_total.js') }}"></script>
@endsection
