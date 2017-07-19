@extends('layouts.master')
@section('header')
<h1>添加新角色</h1>
@endsection
@section('content')
    {!! Form::open(['url' => '/groups', 'method' => 'post']) !!}
    @include('admin.config.group.add_edit')
    {!! Form::close() !!}
@endsection