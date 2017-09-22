@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $messageType->name }}
    </h2>
    <a href="{{ url('message_types/edit/' . $messageType->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('exam_types/delete/' . $messageType->id ) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $messageType->updated_at->diffForHumans() }}</p>
@endsection
@section('content')


    <dl class="dl-horizontal">
        <dt>备注：</dt>
        <dd>{{ $messageType->remark }}</dd>

    </dl>
@endsection