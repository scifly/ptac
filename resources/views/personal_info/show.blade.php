@extends('layouts.master')
@section('header')
    <h2>
       个人信息
    </h2>
    <a href="{{ url('personal_info/edit/'. $info->id) }}">
        <span class="glyphicon glyphicon-edit"></span>
        编辑资料
    </a>
    <p>
        用户名：
        {{ $info->username }}
    </p>
    <p>
        头像：
        {{ $info->avatar_url }}
    </p>
    <p>
        姓名：
        {{ $info->realname }}
    </p>
    <p>
        性别：
        {{ $info->gender == 1 ?'男':'女'}}
    </p>
    <p>
        @isset($info->wechaatid)
        微信号：
        {{ $info->wechaatid }}
        @endisset
    </p>
    <p>Last edited: {{ $info->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>
        @if ($info->group_id)
            所属组别:
            {{ $group->name}}
        @endif
    </p>
@endsection