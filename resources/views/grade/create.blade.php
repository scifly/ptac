@extends('layouts.master')
@section('header')
    <h1>添加新年级</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post', 'id' => 'fromGrade', 'data-parsley-validate' => 'true' ]) !!}
    @include('grade.create_edit')
    {!! Form::close() !!}
@endsection