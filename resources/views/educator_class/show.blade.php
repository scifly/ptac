@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
       教职员工详情
    </h2>
    <a href="{{ url('educators_classes/edit/' . $educatorClass->id . '') }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('educators_classes/delete/' . $educatorClass->id . '') }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $educatorClass->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>教职员工姓名：{{ $educatorClass->educator->user_id }}</p>
    {{--<p>所属学校：{{ $subject->school->name }}</p>--}}
    {{--<p>是否为副科：{{ $subject->isaux==1 ? '是' : '否' }}</p>--}}
    {{--<p>满分：{{ $subject->max_score }}</p>--}}
    {{--<p>及格分：{{ $subject->pass_score }}</p>--}}
@endsection
