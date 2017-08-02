@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $subject->name }}
    </h2>
    <a href="{{ url('subject_modules/edit/' . $subject->id . '') }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('subject_modules/delete/' . $subject->id . '') }}">
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
