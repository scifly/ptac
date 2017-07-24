@extends('layouts.master')
@section('header')
    <h1>编辑角色</h1>
@endsection
@section('content')
    {!! Form::model($group, ['url' => '/groups/' . $group->id, 'method' => 'put']) !!}
    @include('admin.config.group.add_edit')
    {!! Form::close() !!}
@endsection