@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        学生详情
    </h2>
    <a href="{{ url('students/edit/' . $student->id . '') }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('students/delete/' . $student->id . '') }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $student->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>学生姓名：{{ $student->user->realname }}</p>
    <p>班级名称：{{ $student->squad->name }}</p>
    <p>学号：{{ $student->student_number }}</p>
    <p>卡号：{{ $student->card_number }}</p>
    <p>是否住校：{{ $student->oncampus==1 ? '是' : '否' }}</p>
    <p>生日：{{ $student->birthday }}</p>
    <p>备注：{{ $student->remark }}</p>
@endsection
