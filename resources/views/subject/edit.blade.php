@extends('layouts.master')
@section('header')
    <h1>编辑科目</h1>
@endsection
@section('content')
    {!! Form::model($subject, ['url' => '/subject/' . $subject->id, 'method' => 'put']) !!}
    @include('configuration.configuration.partials.subject')
    {!! Form::close() !!}
@endsection