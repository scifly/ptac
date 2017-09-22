@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $exam->name }}
    </h2>
    <a href="{{ url('exams/edit/' . $exam->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('exams/delete/' . $exam->id ) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $exam->updated_at->diffForHumans() }}</p>
@endsection
@section('content')


    <dl class="dl-horizontal">
        <dt>备注：</dt>
        <dd>{{ $exam->remark }}</dd>
        <br/>
        <dt>考试类型：</dt>
        <dd>{{ $exam->examType->name }}</dd>
        <br/>
        <dt>考试班级：</dt>
        @foreach($classes as $v)
            <dd>{{ $v->name }}</dd>
        @endforeach
        <br/>
        <dt>考试科目：</dt>
        @foreach($subjects as $v)
            <dd>{{ $v->name }}</dd>
        @endforeach
        <br/>
        <dt>科目满分：</dt>
        <dd>{{ $exam->max_scores }}</dd>
        <br/>
        <dt>科目及格分数：</dt>
        <dd>{{ $exam->pass_scores }}</dd>
        <br/>
        <dt>考试开始日期：</dt>
        <dd>{{ $exam->start_date }}</dd>
        <br/>
        <dt>考试结束日期：</dt>
        <dd>{{ $exam->end_date }}</dd>
        <br/>
    </dl>
@endsection