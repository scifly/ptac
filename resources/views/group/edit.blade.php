@extends('layouts.master')
@section('header')
    <h1>编辑角色</h1>
@endsection
@section('content')
    {!! Form::model($group, ['method' => 'put', 'id' => 'formGroup']) !!}
    @include('group.create_edit')
    {!! Form::close() !!}
@endsection