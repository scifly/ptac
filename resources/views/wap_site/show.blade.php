@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $wapsite->site_title }}
    </h2>
    <a href="{{ url('wapsites/edit/' . $wapsite->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('wapsites/delete/' . $wapsite->id ) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $wapsite->updated_at->diffForHumans() }}</p>
@endsection
@section('content')


    <dl class="dl-horizontal">
        <br/>
        <dt>所属学校：</dt><dd>{{ $wapsite->school->name }}</dd>
        <br/>
        <dt>轮播图：</dt>
        @foreach($medias as $v)
            <dd>
                <img src="../../..{{$v->path}}">
            </dd>
        @endforeach
        <br/>

    </dl>
@endsection