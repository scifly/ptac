@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $educator->user->name }}
    </h2>
    <a href="{{ url('educators/edit/' . $educator->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('educators/delete/' . $educator->id ) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $educator->updated_at->diffForHumans() }}</p>
@endsection
@section('content')


    <dl class="dl-horizontal">

        <dt>所属组：</dt>
        @foreach($teams as $v)
            <dd>{{ $v->name }}</dd>
        @endforeach
        <br/>
        <dt>所属学校：</dt>
        <dd>{{ $educator->school->name }}</dd>
        <br/>
        <dt>短息条数：</dt>
        <dd>{{ $educator->sms_quote }}</dd>
        <br/>
    </dl>
@endsection