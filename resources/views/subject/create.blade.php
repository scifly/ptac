@extends('layouts.master')
@section('header')
    <h1>添加新科目</h1>
@endsection
@section('content')
    {!! Form::open(['url' => '/subject', 'method' => 'post','id' => 'formSubject']) !!}
    @include('subject.create_edit')
    {!! Form::close() !!}
@endsection