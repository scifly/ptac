@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
       科目次分类详情
    </h2>
    <a href="{{ url('subject_modules/edit/' . $subjectModule->id . '') }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('subject_modules/delete/' . $subjectModule->id . '') }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $subjectModule->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>科目名称：{{ $subjectModule->subject->name }}</p>
    <p>名称：{{ $subjectModule->name }}</p>
    <p>次分类权重：{{ $subjectModule->weight }}</p>
@endsection
