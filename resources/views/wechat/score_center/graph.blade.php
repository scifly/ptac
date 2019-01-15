@extends('layouts.wap')
@section('css')
	<link rel="stylesheet" href="{!! asset('css/wechat/score_center/graph.css') !!}">
@endsection
@section('content')
	<header class="wechat-header">
		<h1 class="wechat-title">成绩中心</h1>
		<p class="wechat-sub-title">
			学生：{!! $student->user->realname . ' : ' . $student->squad->name !!}
		</p>
		{!! Form::hidden('student_id', $student->id, ['id' => 'student_id']) !!}
		{!! Form::hidden('exam_id', $exam->id, ['id' => 'exam_id']) !!}
	</header>
	<div class="tab-bar">
		<div class="tab-item active">
			总分{!! Form::hidden('', -1) !!}
		</div>
		@foreach($subjects as $id => $name)
			<div class="tab-item">
				{!! $name !!}
				{!! Form::hidden('', $id) !!}
			</div>
		@endforeach
	</div>
	<div class="line-table-con class-rank"></div>
	<div class="line-table-con grade-rank"></div>
	<div style="height: 70px;width: 100%;"></div>
	<div class="footerTab" >
		<a class="btnItem footer-active">
			<i class="icon iconfont icon-document"></i>
			<p>详情</p>
		</a>
		<a class="btnItem">
			<i class="icon iconfont icon-renzheng7"></i>
			<p>统计</p>
		</a>
		<div style="clear: both;"></div>
	</div>
@endsection
@section('script')
	<script src="{!! asset('/js/wechat/score_center/graph.js') !!}"></script>
@endsection