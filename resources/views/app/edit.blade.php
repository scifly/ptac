@extends('layouts.master')
@section('header')
    <h2>编辑应用</h2>
@endsection
@section('content')
    {!! Form::model($app, ['method' => 'put', 'id' => 'formApp', 'data-parsley-validate' => 'true']) !!}
    @include('app.create_edit')
    {!! Form::close() !!}
@endsection