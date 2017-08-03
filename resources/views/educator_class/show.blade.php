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
    <p>教职员工姓名：{{ $educatorClass->educator->user->realname }}</p>
    <p>班级名称：{{ $educatorClass->squad->name }}</p>
    <p>科目：{{ $educatorClass->subject->name }}</p>

@endsection
