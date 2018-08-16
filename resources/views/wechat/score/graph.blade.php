@extends('layouts.wap')
@section('css')
	<link rel="stylesheet" href="{{ asset('css/wechat/score/graph.css') }}">
@endsection
@section('content')
	<div class="header">
		<div class="title">学生：{{ $student->user->realname }}</div>
		<div class="myclass">{{ $student->squad->name }}</div>
		{!! Form::hidden('student_id', $student->id, ['id' => 'student_id']) !!}
		{!! Form::hidden('exam_id', $exam->id, ['id' => 'exam_id']) !!}
	</div>
	<div class="tab-bar">
		<div class="tab-item active">
			总分{!! Form::hidden('', -1) !!}
		</div>
		@foreach($subjects as $id => $name)
			<div class="tab-item">
				{{ $name }}
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
	<script src="{{ asset('/js/wechat/score/graph.js') }}"></script>
@endsection