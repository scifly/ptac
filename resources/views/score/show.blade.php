@extends('layouts.master')
@section('header')
    <a href="{{ url('/scores/index') }}">Back to overview</a>
    <h2>
        姓名：{{ $studentname['realname'] }}
    </h2>
    <a href="{{ url('scores/' . 'edit/' . $score->id) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('scores/' . 'delete/' . $score->id ) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $score->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>学号：{{ $score->student->student_number }}</p>
    <p>科目：{{ $score->subject->name }}</p>
    <p>考试名称：{{ $score->exam->name }}</p>
    <p>班级排名：{{ $score->class_rank }}</p>
    <p>年级排名：{{ $score->grade_rank }}</p>
    <p>成绩：{{ $score->score }}</p>
@endsection