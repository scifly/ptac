@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $subject->name }}
    </h2>
    <a href="{{ url('subject/edit/' . $subject->id . '') }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('subject/delete/' . $subject->id . '') }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $subject->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>科目名称：{{ $subject->name }}</p>
    <p>所属学校：{{ $subject->school->name }}</p>
    <p>是否为副科：{{ $subject->isaux==1 ? '是' : '否' }}</p>
    <p>满分：{{ $subject->max_score }}</p>
    <p>及格分：{{ $subject->pass_score }}</p>
@endsection

{{--<div class="modal fade" id="modal-show-subject">--}}
{{--<div class="modal-dialog">--}}
{{--<div class="modal-content">--}}
{{--<div class="modal-header">--}}
{{--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">--}}
{{--&times;--}}
{{--</button>--}}
{{--<h4 class="modal-title" id="myModalLabel">--}}
{{--科目详情--}}
{{--</h4>--}}
{{--</div>--}}
{{--<div class="modal-body">--}}
{{--<div class="row">--}}
{{--<div class="col-xs-12">--}}
{{--<dl class="dl-horizontal">--}}
{{--<dt>科目名称：</dt>--}}
{{--<dt>所属学校：</dt>--}}
{{--<dt>是否为副科：</dt>--}}
{{--<dt>满分：</dt>--}}
{{--<dt>及格分：</dt>--}}
{{--<dt>状态：</dt>--}}
{{--</dl>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--<div class="modal-footer">--}}
{{--<button type="button" class="btn btn-default" data-dismiss="modal">关闭--}}
{{--</button>--}}
{{--</div>--}}
{{--</div><!-- /.modal-content -->--}}
{{--</div><!-- /.modal -->--}}
{{--</div>--}}