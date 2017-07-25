@extends('layouts.master')
@section('header')
    <h2>编辑应用</h2>
@endsection
@section('content')
    {!! Form::model($app, ['method' => 'put', 'id' => 'formApp']) !!}
    @include('app.create_edit')
    {!! Form::close() !!}
@endsection