@extends('layouts.master')
@section('header')
    <h2>编辑学校</h2>
@endsection
@section('content')
    {!! Form::model($score, ['method' => 'put', 'id' => 'formScore', 'data-parsley-validate' => 'true']) !!}
    @include('score.create_edit')
    {!! Form::close() !!}
@endsection