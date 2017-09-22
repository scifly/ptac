@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $examType->name }}
    </h2>
    <a href="{{ url('exam_types/edit/' . $examType->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('exam_types/delete/' . $examType->id ) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $examType->updated_at->diffForHumans() }}</p>
@endsection
@section('content')


    <dl class="dl-horizontal">
        <dt>备注：</dt>
        <dd>{{ $examType->remark }}</dd>

    </dl>
@endsection