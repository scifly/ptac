@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $article->site_title }}
    </h2>
    <a href="{{ url('wsmarticles/edit/' . $article->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('wsmarticles/delete/' . $article->id ) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $article->updated_at->diffForHumans() }}</p>
@endsection
@section('content')


    <dl class="dl-horizontal">
        <br/>
        <dt>名称：</dt><dd>{{ $article->name }}</dd>
        <br/>
        <dt>所属网站模块：</dt><dd>{{ $article->wapsitemodule->name }}</dd>
        <br/>
        <dt>文章摘要：</dt><dd>{{ $article->summary }}</dd>
        <br/>
        <dt>轮播图：</dt>
        @foreach($medias as $v)
            <dd>
                <img src="../../..{{$v->path}}">
            </dd>
        @endforeach
        <br/>
        <dt>文章内容：</dt><dd>{!! $article->content !!} </dd>
        <br/>
    </dl>
@endsection