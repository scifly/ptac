@extends('wechat.layouts.master')
@section('css')
	<link rel="stylesheet" href="{{ asset('css/wechat/score/student_score.css') }}">
@endsection
@section('content')
	<div class="header">
		<div class="title">
			学生：{{$student->user->realname}}
		</div>
		<div class="myclass">
			{{$student->squad->name}}
		</div>
		<input type="hidden" value="{{$student->id}}" id="student_id">
		<input type="hidden" value="{{$exam->id}}" id="exam_id">
	</div>
	<div class="tab-bar">
		<div class="tab-item active">
			总分
			<input type="hidden" value="-1" >
		</div>
		@foreach($data as $d)
			<div class="tab-item">
				{{$d->name}}
				<input type="hidden" value="{{$d->id}}" >
			</div>
		@endforeach
	</div>

	<div class="line-table-con class-rank">

	</div>

	<div class="line-table-con grade-rank">

	</div>

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
	<script src="{{asset('/js/wechat/score/show.js')}}"></script>
@endsection

