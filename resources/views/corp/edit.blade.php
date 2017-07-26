@extends('layouts.master')
@section('header')
    <h2>编辑学校</h2>
@endsection
@section('content')
    {!! Form::model($corp, ['method' => 'put', 'id' => 'formCorp']) !!}
    @include('corp.create_edit')
    {!! Form::close() !!}
@endsection