@extends('layouts.master')
@section('header')
    <h1>编辑科目</h1>
@endsection
@section('content')
    {!! Form::model($subject, ['method' => 'put', 'id' => 'formSubject']) !!}
    @include('subject.create_edit')
    {!! Form::close() !!}
@endsection