@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        监护人/学生关系详情
    </h2>
    <a href="{{ url('custodians_students/edit/' . $custodianStudent->id . '') }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('custodians_students/delete/' . $custodianStudent->id . '') }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $custodianStudent->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>监护人姓名：{{ $custodianStudent->custodian->user->realname }}</p>
    <p>学生姓名：{{ $custodianStudent->student->user->realname }}</p>
    <p>关系：{{ $custodianStudent->relationship }}</p>

@endsection
