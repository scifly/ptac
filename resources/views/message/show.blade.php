@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        {{ $message->site_title }}
    </h2>
    <a href="{{ url('messages/edit/' . $message->id ) }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('messages/delete/' . $message->id ) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $message->updated_at->diffForHumans() }}</p>
@endsection
@section('content')


    <dl class="dl-horizontal">
        <br/>
        <dt>短信内容：</dt>
        <dd>{{ $message->content }}</dd>
        <br/>
        <dt>业务id：</dt>
        <dd>{{ $message->serviceid }}</dd>
        <br/>
        <dt>页面地址：</dt>
        <dd>{{ $message->url }}</dd>
        <br/>
        <dt>发送者用户：</dt>
        <dd>{{ $message->user->realname }}</dd>
        <br/>
        <dt>接收者用户：</dt>
        @foreach($users as $v)
            <dd>{{ $v->realname }}</dd>
        @endforeach
        <br/>
        <dt>消息类型：</dt>
        <dd>{{ $message->messageType->name }}</dd>
        <br/>
        <dt>已读数量：</dt>
        <dd>{{ $message->read_count }}</dd>
        <br/>
        <dt>消息发送成功数：</dt>
        <dd>{{ $message->received_count }}</dd>
        <br/>
        <dt>接收者数量：</dt>
        <dd>{{ $message->recipient_count }}</dd>
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