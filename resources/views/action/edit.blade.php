@extends('layouts.master')
@section('header')
    <h2>编辑Action</h2>
@endsection
@section('content')
    {!! Form::model($action, ['method' => 'put', 'id' => 'formAction', 'data-parsley-validate' => 'true']) !!}
    @include('action.create_edit')
    {!! Form::close() !!}
@endsection