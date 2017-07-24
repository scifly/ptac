@extends('layouts.master')
@section('header')
    <h2>编辑应用</h2>
@endsection
@section('content')
    {!! Form::model($app, ['url' => '/apps/update/' . $app->id, 'method' => 'put']) !!}
    @include('configuration.configuration.partials.app')
    {!! Form::close() !!}
@endsection