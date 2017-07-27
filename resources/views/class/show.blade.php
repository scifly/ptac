@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $squad->name }}
    </h2>
    <a href="{{ url('classes/edit/' . $squad->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('classes/delete/' . $squad->id ) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $squad->updated_at->diffForHumans() }}</p>
@endsection
@section('content')


    <dl class="dl-horizontal">
        <dt>所属年级：</dt><dd>{{ $squad->grade->name }}</dd>
        <br/>
        <dt>教职员工组：</dt>
        @foreach($educators as $v)
            <dd>{{ $v['username'] }}</dd>
        @endforeach
    </dl>
@endsection