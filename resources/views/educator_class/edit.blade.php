@extends('layouts.master')
@section('header')
    <h1>编辑科目</h1>
@endsection
@section('content')
    {!! Form::model($educatorClass, ['method' => 'put', 'id' => 'formEducatorClass']) !!}
    @include('educator_class.create_edit')
    {!! Form::close() !!}
@endsection