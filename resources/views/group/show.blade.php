@extends('layouts.master')
@section('header')
    <a href="{{ url('/') }}">Back to overview</a>
    <h2>
        角色详情
    </h2>
    <a href="{{ url('groups/edit/' . $group->id . '') }}">
        <span class="glyphicon glyphicon-edit"></span>
        Edit
    </a>
    <a href="{{ url('groups/delete/'.$group->id ) }}">
        <span class="glyphicon glyphicon-trash"></span>
        Delete
    </a>
    <p>Last edited: {{ $group->updated_at->diffForHumans() }}</p>
@endsection
@section('content')
    <p>角色名称：{{ $group->name }}</p>
    <p>备注：{{ $group->remark }}</p>
@endsection
