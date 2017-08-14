@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $module->site_title }}
    </h2>
    <a href="{{ url('wapsitemodules/edit/' . $module->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('wapsitemodules/delete/' . $module->id ) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $module->updated_at->diffForHumans() }}</p>
@endsection
@section('content')


    <dl class="dl-horizontal">
        <br/>
        <dt>名称：</dt><dd>{{ $module->name }}</dd>
        <br/>
        <dt>所属网站：</dt><dd>{{ $module->wapsite->site_title }}</dd>
        <br/>
        <dt>模块图：</dt>
        <dd>
            <img src="../../..{{$module->media->path}}">
        </dd>
        <br/>

    </dl>
@endsection