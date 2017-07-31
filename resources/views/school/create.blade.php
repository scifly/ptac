@extends('layouts.master')
@section('header')
    <h1>添加新学校</h1>
@endsection
@section('content')
    {!! Form::open(['url' => '/schools', 'method' => 'post', 'id' => 'formSchool']) !!}
    @include('school.create_edit')
    {!! Form::close() !!}
@endsection