@extends('layouts.master')
@section('header')
    <h1>添加教职员工/班级</h1>
@endsection
@section('content')
    {!! Form::open(['method' => 'post','id' => 'formEducatorClass','data-parsley-validate' => 'true']) !!}
    @include('educator_class.create_edit')
    {!! Form::close() !!}
@endsection