@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $app->name }}
    </h2>
    <a href="{{ url('apps/' . $app->id . '/edit') }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('apps/' . $app->id . '/delete') }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $app->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>名称：{{ $app->name }}</p>
    <p>
    </p>
@endsection