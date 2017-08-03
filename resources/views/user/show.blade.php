@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        用户名：
        {{ $user->username }}
    </h2>
    <a href="{{ url('users/edit/'. $user->id) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('users/index') }}">
        <span class="glyphicon glyphicon-edit"></span>
        index
    </a>
    <p>
        头像：
        {{ $user->avatar_url }}
    </p>
    <p>
        姓名：
        {{ $user->realname }}
    </p>
    <p>
        性别：
        {{ $gender }}
    </p>
    <p>
        微信号：
        {{ $user->wechaatid }}
    </p>
    <p>Last edited: {{ $user->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>
        @if ($user->group_id)
            所属组别:
            {{ link_to('groups/show/' . $user->group->id, $user->group->name) }}
        @endif
    </p>
@endsection