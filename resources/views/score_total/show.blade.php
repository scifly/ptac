@extends('layouts.master')
@section('header')
    <a href="{{ url('/score_totals/index') }}">Back to overview</a>
    <h2>
        姓名：{{ $studentname['realname'] }}
    </h2>
    <p>Last edited: {{ $score_total->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>学号：{{ $score_total->student->student_number }}</p>
    <p>考试名称：{{ $score_total->exam->name }}</p>
    <p>成绩：{{ $score_total->score }}</p>
    <p>计入总成绩科目：@foreach($subjects as $value){{$value['name']." "}}@endforeach</p>
    <p>未计入总成绩科目：@foreach($na_subjects as $value){{$value['name']." "}}@endforeach</p>
    <p>班级排名：{{ $score_total->class_rank }}</p>
    <p>年级排名：{{ $score_total->grade_rank }}</p>
@endsection