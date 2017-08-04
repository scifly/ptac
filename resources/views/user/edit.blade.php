@extends('layouts.master')
@section('header')
    <h2>编辑用户信息</h2>
@endsection
@section('content')
    {!! Form::model($user, ['method' => 'put', 'id' => 'formUser', 'data-parsley-validate' => 'true']) !!}
    @include('user.create_edit')
    {!! Form::close() !!}
@endsection